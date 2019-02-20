<?php
class my extends common{
	public function welcomeAction(){
		//查询公告
		$notice_type=$this->noticeTypeList();
		$notice=db::findAll('notice',array(),'order by id desc limit 0,20','id');
		//判断是否已读
		$nids=array_keys($notice);
		if($nids){
			$read=db::findAll('notice_read',array('aid'=>$this->admin['id'],'nid|in^'=>'('.implode(',', $nids).')'),'nid');
			$now=strtotime(date('Y-m-d'));
			foreach($notice as $k=>$v){
				$notice[$k]['read']=$read[$k];
				$notice[$k]['type']=$notice_type[$v['tid']];
				//时间显示
				$day=strtotime(date('Y-m-d',strtotime($v['time_create'])));
				if($now==$day){
					$notice[$k]['time_create_txt']='今天';
				}elseif($now-(3600*24)==$day){
					$notice[$k]['time_create_txt']='昨天';
				}elseif($now-(3600*24*6)<=$day){
					$notice[$k]['time_create_txt']='7天之内';
				}else{
					$notice[$k]['time_create_txt']=$v['time_create'];
				}
			}
		}
		$this->display(array(
			'notice'=>$notice
		));
	}
	//公告
	public function noticeAction(){
		$id=(int)$_GET['id'];
		$data=db::find('notice',array('id'=>$id));
		$this->display(array(
			'data'=>$data
		));
	}
	//公告阅读记录
	public function noticeReadAction(){
		$id=(int)$_GET['id'];
		$id=db::insert('notice_read',array(
			'nid'=>$id,
			'aid'=>$this->admin['id']
		));
		if($id){
			$this->success('阅读记录写入成功');
		}else{
			$this->error('阅读记录写入失败');
		}
		
	}
	//修改我的信息
	public function editAction(){
		$this->display(array(
			'data'=>$this->admin
		));
	}
	//修改我的信息
	public function editPost(){
		$post=array(
			'id'=>$this->admin['id'],
			'password'=>_var($_POST['password']),
			'nickname'=>_var($_POST['nickname']),
			'avatar'=>_var($_POST['avatar']),
			'phone'=>_phone($_POST['phone']),
			'mail'=>_mail($_POST['mail']),
		);
		$adminModel=new adminModel();
		$result=$adminModel->edit($post);
		if($result['status']){
			$this->success($result['message']);
		}
		$this->error($result['message']);
	}
}