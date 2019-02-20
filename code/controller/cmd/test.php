<?php
class test extends common{
	//5分钟运行一次
	public function indexAction(){
		print_r($this->config);
	}
	//测试是否结束上一次运行的cmd
	public function testAction(){
		sleep(10000);
		print_r($this->config);
	}
}