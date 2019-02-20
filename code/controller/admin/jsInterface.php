<?php
//js ajax获取的信息(如:地区,快递,分类等) 入口文件设置所有权限
class jsInterface extends common{
	//根据fid获取地区
	public function getAreaByFidAction(){
		$fid=(int)$_GET['fid'];
		$data=$this->areaList($fid);
		//转换排序
		$newdata=array();
		foreach($data as $v){
			$newdata[]=$v;
		}
		$this->success($newdata);
	}
	
}