<?php
// $mail_config=array('smtp.qq.com','25','1031850847@qq.com','chlxpeadsbthbceb');
// $mail = new mail($mail_config);
// // $mail->from='众网信通'; //称呼(来自于谁发的)
// $mail->setTitle('标题');
// $mail->setContent('内容');
// // $mail->addAttachment(__DIR__.'/../note.txt');//绝对地址
// // $mail->addAttachment(__DIR__.'/../note.txt');//绝对地址
// $mail->sendmail('(代勇)1031850847@qq.com,(小号)792878210@qq.com','(特小号)838640601@qq.com','(被盗号)838640601@qq.com');
namespace tools;

class mail {
    private $mail_host='smtp.qq.com';
    private $mail_port='465';
    private $user='1031850847@qq.com';
    private $pass='chlxpeadsbthbceb';

    public $attachments = array();
    public $from='';//称呼(来自于谁发的)
    private $time_out = 1;//socket超时设置
    private $phpeol="\r\n";
    public function __construct($mail_config=array()) {
        if(!function_exists('imap_8bit')){
            $this->tip('服务器未配置imap_8bit','exit');
        }
        if(count($mail_config)==4){
            $this->mail_host = $mail_config[0];
            $this->mail_port = $mail_config[1];
            $this->user = $mail_config[2];
            $this->pass = $mail_config[3];
        }
    }
    public function setTitle($title){
        $this->title=$title;
    }
    public function setContent($content){
        $this->content=$content;
    }
    public function sendmail($to,$cc='',$bcc=''){
        $bnd = '1_'.$this->random(32);
        $bndp  = '2_'.$this->random(32);
        $hasdata=count($this->attachments);
        //设置邮件头信息
        $content='Date: '.date('r').$this->phpeol;
        if($this->from){
            $from='('.$this->from.')'.$this->user;
        }else{
            $from=$this->user;
        }
        $content.='From: '.$this->mailAddress($from).$this->phpeol;
        $content.='To: '.$this->mailAddress($to).$this->phpeol;
        if ($cc) {
            $content.='Cc: '.$this->mailAddress($cc).$this->phpeol;
        }
        if ($cc) {
            $content.='Bcc: '.$this->mailAddress($bcc).$this->phpeol;
        }
        $content.='Subject: =?utf-8?B?'.base64_encode($this->title).'?='.$this->phpeol;
        $content.='Mime-Version: 1.0'.$this->phpeol;
        $content.='Message-ID: <'.date('YmdHis',time()).rand(1000,9999).$this->clearMail($from).'>'.$this->phpeol;
        $content_type=!$hasdata?'alternative':'mixed';
        $content.= 'Content-Type: multipart/'.$content_type.';'.$this->phpeol.chr(9).'boundary="'.$bnd.'"' . $this->phpeol . $this->phpeol;
        $content.= '--'.$bnd . $this->phpeol;
        if($hasdata){
            $content.= 'Content-Type: multipart/alternative;'.$this->phpeol.chr(9).'boundary="'.$bndp.'"'.$this->phpeol . $this->phpeol. $this->phpeol;
            $content.= '--'.$bndp.$this->phpeol;
        }
        $content .= 'Content-Type: text/plain;'.$this->phpeol.chr(9).'charset="utf-8"' . $this->phpeol;
        $content .= 'Content-Transfer-Encoding: base64' . $this->phpeol . $this->phpeol;
        $content .= $this->toBase64($this->content) . $this->phpeol.$this->phpeol;
        $bnd_dp=!$hasdata?$bnd:$bndp;
        $content .= '--'.$bnd_dp . $this->phpeol;
        $content .= 'Content-Type: text/html;'.$this->phpeol.chr(9).'charset="utf-8"' . $this->phpeol;
        $content .= 'Content-Transfer-Encoding: quoted-printable' . $this->phpeol . $this->phpeol;
        $content .= imap_8bit($this->content) . $this->phpeol;
        $content .= '--'.$bnd_dp.'--' . $this->phpeol;
        if($hasdata){
             $content .=$this->phpeol;
            foreach ($this->attachments as $att) {
                $content .= '--'.$bnd . $this->phpeol.$att;
            }
            $content .= '--'.$bnd.'--' . $this->phpeol;
        }
        $mail=array_unique(array_merge(explode(',',$to),explode(',',$cc),explode(',',$bcc)));
        foreach($mail as $v){
            $v=preg_replace('/\((.*?)\)/','',$v);
            if(!$v)continue;
            $repeat=3;
            while ($repeat>0) {
                $repeat--;
                if($this->toSmtp($from,$v,$content)){
                    $this->tip($v.'发送成功');
                    $repeat=0;
                }else{
                    if($repeat==0){
                        $this->tip($v.'发送失败');
                    }else{
                        $this->tip($v.'发送失败尝试重新发送');
                    }
                }
            }
        }
        
    }
    //提交到smtp
    private function toSmtp($from,$to,$content){
        usleep(100000);//暂停100毫秒
        //sock连接
        if($this->mail_port=='25'){
            $this->sock=@fsockopen($this->mail_host, $this->mail_port, $errno, $errstr, $this->time_out);
        }elseif($this->mail_port=='465'){
            $remoteAddr = "tcp://" . $this->mail_host . ":" . $this->mail_port;
            $this->sock = stream_socket_client($remoteAddr, $errno, $errstr, $this->time_out);
            stream_socket_enable_crypto($this->sock, true, STREAM_CRYPTO_METHOD_SSLv23_CLIENT);
            stream_set_blocking($this->sock, 1);
        }
        if(!$this->sock)return false;
        $gets = fgets($this->sock, 512);
        if (!preg_match('/^[23]/', $gets)) return false;//sock连接错误
        //连接必要操作以及登陆
        if(!$this->smtpPut('HELO PHPmail')) return false;//helo错误
        if(!$this->smtpPut('AUTH LOGIN '.base64_encode($this->user))) return false;//登陆失败
        if(!$this->smtpPut(base64_encode($this->pass))) return false;//验证密码失败
        if(!$this->smtpPut('MAIL FROM:<'.$this->clearMail($from).'>'))return false;//主发邮件地址错误
        if(!$this->smtpPut('RCPT TO:<'.$this->clearMail($to).'>')) return false;//接收邮件地址错误
        if(!$this->smtpPut('DATA')) return false;//设置发送内容失败
        fputs($this->sock,$content.$this->phpeol);
        fputs($this->sock,$this->phpeol.'.'.$this->phpeol);
        $gets = fgets($this->sock, 512);
        if(!(strpos($gets,'250')===0||!$gets)) {
            //echo $gets;
            return false;
        }
        fputs($this->sock,'QUIT'.$this->phpeol);
        fclose($this->sock);
        return true;
    }
    //base64转换
    private function toBase64($data){
        $base64= base64_encode($data);
        $strlen=strlen($base64);
        $newbase64='';
        for($i=0;$i<$strlen;$i=$i+76){
            $newbase64.=substr($base64, $i,76).$this->phpeol;
        }
        return $newbase64;
    }
    //请求邮件
    private function smtpPut($string) {
        fputs($this->sock, $string.$this->phpeol);
        $gets = fgets($this->sock, 512);
        //echo $gets;
        if (!preg_match('/^[23]/', $gets)) {
            fputs($this->sock,'QUIT'.$this->phpeol);
            fclose($this->sock);
            return false;
        }
        return true;
    }
    //添加附件
    public function addAttachment($file, $dispo = 'attachment') {
        $file_df=$file;
        if(strpos(strtoupper(PHP_OS),'WIN')===0){
            $file_df=iconv('utf-8', 'gbk//IGNORE',$file);
        }
        $file_data = (file_exists($file_df)) ? file_get_contents($file_df) : '';
        if ($file_data) {
            $filename = basename($file);
            $filename='=?utf-8?B?'.base64_encode($filename).'?=';
            $parts  = 'Content-Type: application/octet-stream;'.$this->phpeol.chr(9).'name="'.$filename.'"' . $this->phpeol;
            $parts .= 'Content-Transfer-Encoding: base64' . $this->phpeol;
            $parts .= 'Content-Disposition: '.$dispo.';'.$this->phpeol.chr(9).'filename="'.$filename.'"' . $this->phpeol . $this->phpeol;
            $parts .=$this->toBase64($file_data). $this->phpeol;
            $this->attachments[] = $parts;
        }
    }

    //转换成邮件发送的格式
    private function mailAddress($to){
        $to=explode(',', $to);
        foreach($to as $k=>$v){
            if(preg_match('/\((.*?)\)([\w]+)@([\w.]+)/',$v,$match)){
                $match[1]='=?utf-8?B?'.base64_encode($match[1]).'?=';
                $to[$k]=$match[1].' <'.$match[2].'@'.$match[3].'>';
            }else{
                $to[$k]=preg_replace('/([\w]+)@([\w\.]+)/','$1 <$1@$2>',$v);
            }
        }
        $to=str_replace(',',','.$this->phpeol.chr(9),implode(',',$to));
        return $to;
    }
    //清除邮件格式
    private function clearMail($string){
        return preg_replace('/\((.*?)\)/','',$string);
    }
    //提示信息
    private function tip($string,$data=''){
        print_r($string);
        echo $this->phpeol;
        if($data)exit;
    }
    //随机数
    private function random($count = 5,$string='123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
        $random = '';
        for ($i = 0; $i < $count; $i++) {
            if(function_exists('mb_strlen')){
                $scount=mb_strlen($string,'utf-8');
            }else{
                $scount=strlen($string);
            }
            $rand=mt_rand(0,$scount-1);
            if(function_exists('mb_strlen')){
                $random.= mb_substr($string,$rand,1);
            }else{
                $random.= substr($string,$rand,1);
            }
        }
        return $random;
    }

}
