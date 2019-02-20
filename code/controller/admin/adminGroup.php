<?php
class adminGroup extends common{
	public function __construct(){
		parent::__construct();
		$this->model=new adminGroupModel($this->admin);
	}
	public function listAction(){
		$this->display(array(
			'list'=>$this->adminGroupList(),
		));
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
			'data'=>$data,
			'lv'=>$this->model->lv,
			'filePower'=>$this->getFilePower()
		));
	}
	public function editSortPost(){
		foreach($_POST['sort'] as $v){
			$post=array(
				'id'=>_int($v['id']),
				'sort'=>_int($v['sort'])
			);
			$return=$this->model->edit($post);
			if(!$return){
				$this->error($v['id'].'排序失败,请重新提交');
			}
		}
		$this->success('排序成功');
	}
	public function editPost(){
		$post=array(
			'id'=>_int($_POST['id']),
			'name'=>_var($_POST['name']),
			'power'=>_array($_POST['power']),
			'lv'=>_int($_POST['lv']),
		);
		$result=$this->model->edit($post);
		if($result['status']){
			$this->success($result['message']);
		}
		$this->error($result['message']);
	}
}

