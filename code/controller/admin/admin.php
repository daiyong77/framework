<?php
class admin extends common{
	public function __construct(){
		parent::__construct();
		$this->model=new adminModel($this->admin);

		$this->get=array(
			'username'=>trim($_GET['username']),
			'phone'=>trim($_GET['phone']),
			'mail'=>trim($_GET['mail']),
			'gid'=>(int)$_GET['gid'],
			'disable'=>(int)$_GET['disable'],
		);

	}
	public function listAction($export=''){
		$group=$this->adminGroupList();
		$list=$this->search($this->model->table,array(
			'username|like'=>'%'.$this->get['username'].'%',
			'phone|like'=>'%'.$this->get['phone'].'%',
			'mail|like'=>'%'.$this->get['mail'].'%',
			'gid'=>$this->get['gid'],
			'disable'=>$this->get['disable'],
			'gid|in^'=>array_keys($group),
		),'sort desc,id asc',$export);
		$this->display(array(
			'group'=>$group,
			'disable'=>$this->model->disable,
			'list'=>$list,
		));
	}
	public function listExportAction(){
		$this->group=$this->adminGroupList();
		$this->listAction(function($where,$orderby){
			$title=array('id','用户名','管理组','电话','邮箱','当前状态');
			$line=function($line){
				return array($line['id'],$line['username'],$this->group[$line['gid']]['name'],$line['phone'],$line['mail'],$this->model->disable[$line['disable']]);
			};
			return array('管理员列表数据.csv',$title,$line);
		});
	}
	public function deleteAction(){
		$id=(int)$_GET['id'];
		$result=$this->model->delete($id);
		if($result['status']){
			$this->success($result['message']);
		}else{
			$this->error($result['message']);
		}
	}
	public function editAction(){
		$id=(int)$_GET['id'];
		$data=$this->model->getData($id);
		$this->display(array(
			'group'=>$this->adminGroupList(),
			'data'=>$data
		));
	}
	public function editPost(){
		$post=array(
			'id'=>_int($_POST['id']),
			'gid'=>_int($_POST['gid']),
			'username'=>_var($_POST['username']),
			'password'=>_var($_POST['password']),
			'nickname'=>_var($_POST['nickname']),
			'avatar'=>_var($_POST['avatar']),
			'phone'=>_phone($_POST['phone']),
			'mail'=>_mail($_POST['mail']),
			'sort'=>_int($_POST['sort']),
			// 'disable'=>_int($_POST['disable']),
		);
		$result=$this->model->edit($post);
		if($result['status']){
			$this->success($result['message']);
		}
		$this->error($result['message']);
	}
	public function editDisableAction(){
		$get=array(
			'id'=>_int($_GET['id']),
			'disable'=>_int($_GET['disable']),
		);
		$result=$this->model->update($get);
		if($result['status']){
			$this->success($result['message']);
		}
		$this->error($result['message']);
	}
}

