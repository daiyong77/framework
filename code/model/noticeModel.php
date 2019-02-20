<?php
class noticeModel extends commonModel{

	public $table='notice';

	public function getData($id){
		if(!$id) return array();
		$data=db::find($this->table,array('id'=>$id));
		if($data){
			$data['type']=db::find('notice_type',array('id'=>$data['tid']));
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
	public function delete($id){
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
				case 'tid':
					if(!$v)return error('请选择公告类型');
					$check=db::find('notice_type|id',array('id'=>$v));
					if(!$check){
						return error('公告类型不存在');
					}
					break;
				case 'title':
					if(!$v)return error('标题不能为空');
					break;
				case 'body':
					if(!$v)return error('内容不能为空');
					break;
			}
			$data_new[$k]=$v;
		}
		//新增时,加入添加时间
		if(!$data_new['id']){
			$data_new['time_create']=date('Y-m-d H:i:s');
		}
		return success($data_new);
	}
}