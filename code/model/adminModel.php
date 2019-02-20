<?php
class adminModel extends commonModel{

	public $table='admin';
	public $disable=array(
		1=>'生效中',
		2=>'已失效'
	);

	public function __construct($loginInfo=array()){
		parent::__construct();
		$this->loginInfo=$loginInfo;
	}
	public function getData($id){
		if(!$id) return array();
		$data=db::find($this->table,array('id'=>$id));
		if($data){
			$data['group']=db::find('admin_group',array('id'=>$data['gid']));
		}
		if($this->loginInfo&&$this->loginInfo['group']['lv']<=$data['group']['lv']){
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
			if($this->loginInfo['id']==$id)return error('不能删除自己');
			$admin=$this->getData($id);
			if(!$admin){
				return error('权限不足');
			}
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
		//修改 排除一些不可以修改的越权信息
		if($this->loginInfo&&$data['id']){
			if($this->loginInfo['id']==$data['id']){
				unset($data['gid']);
				unset($data['disable']);
			}else{
				$admin=$this->getData($data['id']);
				if(!$admin){
					return error('不能修改权限比自己大或相等的管理员');
				}
			}
		}
		//不可修改的信息
		if($data['id']){
			unset($data['username']);
		}
		foreach($data as $k=>$v){
			switch ($k) {
				case 'gid':
					if(!$v)return error('请选择管理组');
					$check=db::find('admin_group|id,lv',array('id'=>$v));
					if(!$check){
						return error('管理组不存在');
					}
					//控制权限修改其他管理员
					if($this->loginInfo&&$this->loginInfo['group']['lv']<=$check['lv']){
						return error('不能设置同级别或更高级别管理组');
					}
					break;
				case 'username':
					if(!$v)return error('用户名不能为空');
					$where=array($k=>$v);
					if($data['id']){
						$where['id|!=']=$data['id'];
					}
					$check=db::find($this->table.'|id',$where);
					if($check)return error('用户名重复');
					break;
				case 'phone':
					if($v){
						$where=array($k=>$v);
						if($data['id']){
							$where['id|!=']=$data['id'];
						}
						$check=db::find($this->table.'|id',$where);
						if($check)return error('手机号码重复');
					}
					break;
				case 'mail':
					if($v){
						$where=array($k=>$v);
						if($data['id']){
							$where['id|!=']=$data['id'];
						}
						$check=db::find($this->table.'|id',$where);
						if($check)return error('邮箱号码重复');
					}
					break;
				case 'password':
					if(!$data['id']&&!$v)return error('密码不能为空');
					if($v){
						$data_new['salt']=func::random(5);
						$v=md5($data_new['salt'].$v.$data_new['salt']);
					}
					break;
				case 'nickname':
					if(!$v)$v=$data['username'];
					break;
				case 'disable':
					$disable=array_keys($this->disable);
					if($data['id']&&$v&&!in_array($v, $disable)){
						return error('禁用参数错误');
					}
					if(!in_array($v, $disable)){
						$v=$disable[0];
					}
					break;
			}
			$data_new[$k]=$v;
		}
		//如果密码字段为空则去除
		if(!$data_new['password']){
			unset($data_new['password']);
		}
		//新增时,加入添加时间
		if(!$data_new['id']){
			$data_new['time_create']=date('Y-m-d H:i:s');
		}
		return success($data_new);
	}
}