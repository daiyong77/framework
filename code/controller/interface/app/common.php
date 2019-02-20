<?php
class common{
	public $config;
	public function __construct(){
		$this->config=$GLOBALS['_CONFIG'];
	}
	//返回
	public function display($msg='请求成功',$data=array(),$status=1){
		$this->displayStatus($msg,$data,$status,'请求成功');
	}
	public function success($msg,$data=array(),$status=1){
		$this->displayStatus($msg,$data,$status,'成功');
	}
	public function error($msg,$data=array(),$status=0){
		$this->displayStatus($msg,$data,$status,'失败');
	}
	private function displayStatus($msg,$data,$status,$txt){
		if(is_numeric($data)){
			$status=$data;
			$data=array();
		}
		if(is_array($msg)){
			$data=$msg;
			$msg=$txt;
		}
		$data=array(
	    	'status'=>$status,
	    	'message'=>$msg,
	    	'data'=>$data
	    );
		echo json_encode($data,JSON_UNESCAPED_UNICODE);
	    exit;
	}
}
