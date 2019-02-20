<?php
class notice extends common{
	public function __construct(){
		parent::__construct();
		$this->model=new noticeModel();

		$this->get=array(
			'tid'=>(int)$_GET['tid'],
		);

	}
	public function listAction($export=''){
		$list=$this->search($this->model->table,array(
			'tid'=>$this->get['tid'],
		));
		
		$this->display(array(
			'list'=>$list,
			'type'=>$this->noticeTypeList()
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
			'type'=>$this->noticeTypeList()
		));
	}
	public function editPost(){
		$post=array(
			'id'=>_int($_POST['id']),
			'tid'=>_int($_POST['tid']),
			'title'=>_var($_POST['title']),
			'body'=>_txt($_POST['body']),
		);
		$result=$this->model->edit($post);
		if($result['status']){
			$this->success($result['message']);
		}
		$this->error($result['message']);
	}
}

