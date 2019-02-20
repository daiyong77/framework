<?php
class area extends common{
	public function __construct(){
		parent::__construct();
		$this->model=new areaModel();

		$this->get=array(
			'fid'=>(int)$_GET['fid']
		);
	}
	public function listAction(){
		$path=$this->model->getFather($this->get['fid']);
		$this->display(array(
			'path'=>$path,
			'list'=>$this->areaList($this->get['fid']),
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
			'fid'=>_int($_POST['fid']),
		);
		$result=$this->model->edit($post);
		if($result['status']){
			$this->success($result['message']);
		}
		$this->error($result['message']);
	}
}

