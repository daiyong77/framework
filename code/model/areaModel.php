<?php
class areaModel extends commonModel{

	public $table='area';

	//获取父级信息
	public function getFather($id){
		$all=array();
		$data=db::find($this->table,array('id'=>$id));
		if($data)$all[]=$data;
		if($data['fid']){
			$all=array_merge($this->getFather($data['fid']),$all);
		}
		return $all;
	}
	public function getData($id){
		if(!$id) return array();
		$data=db::find($this->table,array('id'=>$id));
		if($data){
			$data['father']=db::find($this->table,array('id'=>$data['fid']));
		}
		return $data;
	}
	public function edit($data){
		$return=$this->checkData($data);
		if($return['status']){
			$return=$this->editDb($this->table,$return['data']);
		}
		return $return;
	}
	public function update($data){
		if(!$data['id']){
			return error('id不能为空');
		}
		return $this->edit($data);
	}
	public function delete($id,$child=''){
		$count=0;
		$check=db::findAll($this->table.'|id',array('fid'=>$id));
		foreach($check as $v){
			$count+=$this->delete($v,'child');
		}
		if(db::delete($this->table,array('id'=>$id))){
			if($child)return $count+1;
			return success('成功删除'.($count+1).'个地区');
		}else{
			if($child)return $count;
			return error('删除失败'.($count?',但删除了'.$count.'个子级':''));
		}
	}
	private function checkData($data){
		$return=$this->checkInput($data);
		if(!$return['status']) return $return;
		$data_new=array();
		//不可修改的信息
		if($data['id']){
			unset($data['fid']);
		}
		foreach($data as $k=>$v){
			switch ($k) {
				case 'name':
					if(!$v)return error('请填写地区名称');
					$where=array($k=>$v,'fid'=>(int)$data['fid']);
					if($data['id']){
						$where['id|!=']=$data['id'];
					}
					$check=db::find($this->table.'|id',$where);
					if($check)return error('地区名称重复');
					break;
			}
			$data_new[$k]=$v;
		}
		return success($data_new);
	}
}