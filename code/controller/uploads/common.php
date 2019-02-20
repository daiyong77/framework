<?php
class common{
	public $config;
	public function __construct(){
		$this->config=$GLOBALS['_CONFIG'];
	}
	//获取文件夹名称
	public function getDir($dir){//绝对地址
		$dir_new=$dir;
		if (!is_dir($dir)) mkdir($dir, 0777, true);
		if($this->getFileCounts($dir)>=300){
			for($i=1;$i++;$i>-1){
				$dir_new=$dir.'_'.$i;
				if (!is_dir($dir_new)) mkdir($dir_new, 0777, true);
				if($this->getFileCounts($dir_new)<300){
					break;
				}
			}
		}
		return $dir_new.'/';
	}
	//获取文件夹下的文件数量
	private function getFileCounts($dir){//绝对地址
		$handle = opendir($dir);
		$i = 0;
		while(false !== $file=(readdir($handle))){
			if($file !== '.' && $file != '..'){
				$i++;
			}
		}
		closedir($handle);
		return $i;
	}
	//返回
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
