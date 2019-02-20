<?php
class index extends common{
	public function indexAction(){
		$list=array('456');
		$this->display(array(
			'list'=>$list
		));
	}
}