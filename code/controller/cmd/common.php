<?php
class common{
	public $config;
	public function __construct(){
		$this->config=$GLOBALS['_CONFIG'];
		if(PHP_OS=='Linux'){
			$this->linuxCmdOut();
		}
	}
	public function tip($msg,$code=''){
		echo $msg.PHP_EOL;
		if($code){
			exit;
		}
	}
	//测试过的版本CentOS release 6.9 (Final)
	private function linuxCmdOut(){
		$sys=shell_exec("ps aux|grep 'crontab.php ".$this->config['sys']['controller']."/".$this->config['sys']['action']."'");
		$sys=explode(PHP_EOL,$sys);
		$thread=array();
		foreach($sys as $k=>$v){
			if($v&&!strpos($v,'grep')){
				preg_match('/^[\w]+[ ]+([\d]+)[ ]+/',$v,$match);
				$thread[$match[1]]=$match[1];
			}
		}
		unset($thread[max($thread)]);
		foreach($thread as $k=>$v){
			shell_exec("kill ".$v);
		}
	}
}
