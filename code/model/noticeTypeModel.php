<?php
class noticeTypeModel extends commonModel{

	public $table='notice_type';

	public function getData($id){
		if(!$id) return array();
		$data=db::find($this->table,array('id'=>$id));
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
		$check=db::find('notice|id',array('tid'=>$id));
		if($check){
			return error('该类型还有公告信息,不能删除');
		}
		if(db::delete($this->table,array('id'=>$id))){
			return success('删除成功');
		}else{
			return error('删除失败');
		}
	}
	private function checkData($data){
		$return=$this->checkInput($data);
		if(!$return['status']) return $return;
		$data_new=array();
		foreach($data as $k=>$v){
			switch ($k) {
				case 'name':
					if(!$v)return error('请填写类型名称');
					$where=array($k=>$v);
					if($data['id']){
						$where['id|!=']=$data['id'];
					}
					$check=db::find($this->table.'|id',$where);
					if($check)return error('类型名称重复');
					break;
			}
			$data_new[$k]=$v;
		}
		return success($data_new);
	}
}