<?php
//虚拟手机号与短信获取(该接口经常坏掉)
//使用的时候必须先getToken 并且判断token是否获取到
namespace thirdParty;
class ixinsms{
	public $username='dailingang';
	public $password='zwxt6899';
	private $phone;
	private $token;
	public function getToken(){
		$token=\http::curl('http://api.ixinsms.com/api/do.php?action=loginIn&name='.$this->username.'&password='.$this->password);
		$token=explode('|',$token);
		if($token[0]==1){
			$this->token=$token[1];
			return array('status'=>1,'message'=>'获取登录信息成功','data'=>$this->token);;
		}else{
			return array('status'=>0,'message'=>'获取登录信息失败','data'=>array());
		}
	}
	public function getPhone(){
		$phone=\http::curl('http://api.ixinsms.com/api/do.php?action=getPhone&sid=1177&token='.$this->token);
		$phone=explode('|',$phone);
		if($phone[0]==1){
			$this->phone=$phone[1];
			return array('status'=>1,'message'=>'获取手机号码成功','data'=>$this->phone);;
		}
		return array('status'=>0,'message'=>'获取手机号码失败','data'=>array());
	}
	public function getCode($preg=''){//$preg='/([\d]+)/'匹配的正则
		$message=\http::curl('http://api.ixinsms.com/api/do.php?action=getMessage&sid=1177&phone='.$this->phone.'&token='.$this->token);
		$message=explode('|',$message);
		if($message[0]==1){
			if($preg){
				preg_match($preg,$message[1],$match);
				return array('status'=>1,'message'=>'获取验证码成功','data'=> $match[1]);
			}
			return array('status'=>1,'message'=>'获取验证码成功','data'=> $message[1]);
		}
		return array('status'=>0,'message'=>'获取验证码失败','data'=>array());
	}
	public function getMoney(){
		return \http::curl('http://api.ixinsms.com/api/do.php?action=getSummary&token='.$this->token);
	}
}