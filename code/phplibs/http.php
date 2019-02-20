<?php
class http{
	private static $path_log_curl='cache/logs/curl.log';//项目路径下的日志存放地址
	private static $path_log_socket='cache/logs/socket.log';//项目路径下的日志存放地址
	public static $getcookie='';//获取cookie的路径
	public static $timeout=1;//超时设置
	public static $repeat=3;//请求失败后重复请求次数
	public static $agent=array();//代理array(ip,port,username,password)
	//curl抓取
	//用法:(将覆盖掉$this中设置好的参数)
	// curl('请求地址',array(
	// 	'timeout'=>'超时时间',
	// 	'repeat'=>'请求失败后的重复请求次数',
	// 	'savecookie'=>'请求页面后的cookie保存地址',
	// 	'getcookie'=>'cookie地址',
	// 	'header'=>array('类似于chrome的一条一条的头信息','类似于chrome的一条一条的头信息'),
	// 	'showheader'=>'是否返回头信息'
	// 	'post'=>'post参数最好是url形式'
	// ));
	// 返回:
	// 请求到的值
	public static function curl($url,$data=array()){
		//定义
		if(!isset($data['timeout'])){
			$data['timeout']=self::$timeout;//超时设置
		}
		if(!isset($data['repeat'])){
			$data['repeat']=self::$repeat;//请求失败后重复请求次数
		}
		if(!isset($data['getcookie'])&&self::$getcookie){
			$data['getcookie']=self::$getcookie;
		}
		//设置http头
        $header=array(
            'User-Agent:Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36'
        );
        if(isset($data['header'])){
        	if(self::headerHas($data['header'],'User-Agent')){
        		$header=$data['header'];
        	}else{
        		$header=array_merge($header,$data['header']);
        	}
        }
		if(isset($data['savecookie'])){
			file::put($data['savecookie'],'');
		}
		//日志记录
	    $path_log=file::path(self::$path_log_curl);
	    if(!file_exists($path_log)){
		    file::put($path_log,'#curl log');
		}
	    //执行抓取
	    do{
	        usleep(200000);//暂停200毫秒
	        $message=PHP_EOL.date('Y-m-d H:i:s').'|'.$url;
	        file_put_contents($path_log,$message,FILE_APPEND);
	        $time_begin=microtime(true);
	        //执行抓取
	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, $url); 
	        if(isset($data['showheader'])){
	        	curl_setopt($ch, CURLOPT_HEADER, true); //不返回header部分
	        }else{
	        	curl_setopt($ch, CURLOPT_HEADER, false); //不返回header部分
	    	}
	    	if(self::$agent){
		    	curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);  
				curl_setopt($ch, CURLOPT_PROXY,$agent['ip'].':'.$agent['port']);  //"0.0.0.0:8080"
				if(self::$agent['username']&&self::$agent['password']){
					curl_setopt($ch,CURLOPT_PROXYUSERPWD,self::$agent['username'].':'.self::$agent['password']);  //"username:pwd"
				}
			}
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//不自动输出内容
	        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $data['302']?true:false); // 网页有跳转时使用自动跳转 
	        curl_setopt($ch, CURLOPT_ENCODING,'');//不使用gzip等功能直接获取字符串
	        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
	        //设置超时
	        curl_setopt($ch, CURLOPT_TIMEOUT,$data['timeout']);
	        //https
	        if(strpos($url,'https://')===0){
	            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // https(对认证证书来源的检查)
	            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // https(从证书中检查SSL加密算法是否存在)
	        }
	        //post
	        if(isset($data['post'])){
	            curl_setopt($ch, CURLOPT_POST, true);//post提交
	            curl_setopt($ch, CURLOPT_POSTFIELDS,$data['post']);//post提交
	        }
	        //cookie
	        if(isset($data['getcookie'])){
	            curl_setopt ($ch, CURLOPT_COOKIEFILE,file::path($data['getcookie']));//读取cookie
	        }
	        if(isset($data['savecookie'])){
	            curl_setopt($ch,  CURLOPT_COOKIEJAR,file::path($data['savecookie']));//保存cookie
	        }
	        //返回
	        $content=curl_exec($ch);  
	        $httpcode = curl_getinfo($ch,CURLINFO_HTTP_CODE); 
	        curl_close($ch);
	        $data['repeat']--;//计算循环次数
	        //保存日志
	        $time_limit= sprintf('%.4f',(microtime(true)-$time_begin));
	        file_put_contents($path_log,'|'.$time_limit.'s',FILE_APPEND);
	    } while ( $data['repeat']>0&&$httpcode===0);
	    return $content;
	}
	
	private static function headerHas($data,$string){
		$has=0;
    	foreach($data as $v){
    		if(strpos($v,$string)===0){
    			$has=1;
    			break;
    		}
    	}
    	return $has;
	}
}