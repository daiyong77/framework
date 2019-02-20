<?php
class adminGroupModel extends commonModel{

	public $table='admin_group';
	private $power_all=array(1,2);
	public $lv=array(1,2,3,4,5,6,7,8,9,10);

	public function __construct($loginInfo=array()){
		parent::__construct();
		$this->loginInfo=$loginInfo;
		//如果登陆则修改权限等级
		if($loginInfo){
			foreach($this->lv as $k=>$v){
				if($v >= $loginInfo['group']['lv']){
					unset($this->lv[$k]);
				}
			}
		}
	}

	public function getData($id){
		if(!$id)return array();
		$data=db::find($this->table,array('id'=>$id));
		$data['power']=json_decode($data['power']);
		if($this->loginInfo&&$this->loginInfo['group']['lv']<=$data['lv']){
			return array();
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
		if($this->loginInfo){
			$group=$this->getData($id);
			if(!$group){
				return error('不能删除权限比自己大或相等的管理组');
			}
		}
		$check=db::find('admin|id',array('gid'=>$id));
		if(!$check){
			if(db::delete($this->table,array('id'=>$id))){
				return success('删除成功');
			}else{
				return error('删除失败');
			}
		}
		return error('该管理组下还有管理员不能删除');
	}
	private function checkData($data){
		$return=$this->checkInput($data);
		if(!$return['status']) return $return;
		$data_new=array();
		foreach($data as $k=>$v){
			switch ($k) {
				case 'name':
					if(!$v)return error('用户组名称不能为空');
					$where=array($k=>$v);
					if($data['id']){
						$where['id|!=']=$data['id'];
					}
					$check=db::find($this->table.'|id',$where);
					if($check)return error('用户组名称重复');
					break;
				case 'power_all':
					if($data['id']&&$v&&!in_array($v, $this->power_all)){
						return error('系统内全部权限选择权限错误');
					}
					if(!in_array($v, $this->power_all)){
						$v=$this->power_all[0];
					}
					break;
				case 'power':
					if(!is_array($v)){
						return error('权限字段错误');
					}
					$v=json_encode($v);
					break;
				case 'lv':
					if(!$v)return error('请选择管理组等级');
					if($data['id']&&$v&&!in_array($v, $this->lv)){
						return error('管理组等级错误');
					}
					if(!in_array($v, $this->lv)){
						$v=$this->lv[0];
					}
					break;
			}
			$data_new[$k]=$v;
		}
		return success($data_new);
	}
}