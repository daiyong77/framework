<?php
//验证码识别
namespace thirdParty;
class cjy{
	private $username='17607137932';//超级鹰账号
	private $password='123456';//超级鹰密码
	private	$softid= '894190';	//超级鹰 软件ID 用户中心>软件ID 可以生成
	private $url_verify='http://upload.chaojiying.net/Upload/Processing.php';//验证地址
	private $url_money='http://code.chaojiying.net/Upload/GetScore.php';//获取剩余提分
	private $url_error='http://code.chaojiying.net/Upload/ReportError.php';//错误提分
	private $id='';//记录提分id
	//codetype码图类型
	//1004~1020 1~20位英文数字
	//2001~2007 1~7位纯汉字
	//查看更多类型 https://www.chaojiying.com/price.html
	public function toTxt($image,$codetype='1902',$timeout=10){
		$fields = array( 
			'user'=>$this->username,
			'pass2'=>md5($this->password),
			'softid'=>$this->softid ,
			'codetype'=>$codetype ,
			'userfile'=> new \CURLFile(realpath(\file::path($image)))
		);
		$data=\http::curl($this->url_verify,array(
			'header'=>array(
				'Expect:'
			),
			'post'=>$fields,
			'timeout'=>$timeout
		));
		$data=@json_decode($data,true);
		if($data['err_no']===0||$data['err_no']==='0'){
			$this->id=$data['pic_id'];
			return array('status'=>1,'message'=>'识别成功','data'=>$data['pic_str']);
		}else{
			return array('status'=>0,'message'=>'识别失败','data'=>$data);
		}
	}
	public function getMoney(){
		return \http::curl($this->url_money,array(
			'post'=>array(
				'user'=>$this->username,
				'pass2'=>md5($this->password)
			)
		));
	}
	public function error(){
		if(!$this->id)return;
		$fields = array( 
			'user'=>$this->username,
			'pass2'=>md5($this->password),
			'softid'=>$this->softid ,
			'id'=>$this->id
		);
		\http::curl($this->url_error,array(
			'post'=>$fields
		));
	}
}
