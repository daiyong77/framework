<?php
class commonModel{
	private $key='id';
	public function __construct(){

	}
	//按照主键修改或添加数据
	public function editDb($table,$data,$key=''){
		if(!$data)return error('未进行任何修改');
		if(!$key)$key=$this->key;
		if($data[$key]){
			$change=db::update($table,$data,array($key=>$data[$key]));
			if($change){
				return success('修改成功');
			}
			return error('未进行任何修改');
		}else{
			$id=db::insert($table,$data);
			if($id){
				return success('新增成功');
			}
			return error('新增失败');
		}
	}
	//验证字段
	public function checkInput($data){
		if(!$data)return error('请传入参数');
		foreach($data as $k=>$v){
			$fieldVal=$this->fieldVal($k,$v);
			if(!$fieldVal['status']){
				return error($fieldVal['message']);
			}
		}
		return success('验证成功');
	}
	private function fieldVal($k,$v){
		if($v===FIELD_ERROR){
			return error($k.'错误');
		}
		if(!is_array($v)&&strpos($v, FIELD_ERROR.'|')===0){
			return error(str_replace(FIELD_ERROR.'|', '', $v));
		}
		return success('验证成功');
	}

}