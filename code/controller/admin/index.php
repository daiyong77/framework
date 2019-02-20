<?php
class index extends common{
	public function __construct(){
		//不执行上一个construct的登陆判断
		$this->config=$GLOBALS['_CONFIG'];
	}
	public function indexAction(){
		$check=$this->checkLogin();
		if(!$check['status']){
			$rsa=require_once(file::path('data/rsa.php'));
			$this->display('index_login',array(
				'modulus'=>$rsa['modulus'],
				'exponent'=>$rsa['exponent'],
			));
		}
		tpl::$base='';
		$this->display('index_frame',array(
			'admin'=>$check['data']
		));
	}
	public function loginAction(){
		$check=$this->checkLogin();
		if($check['status']){
			header('location:'.url('index/index'));
			exit;
		}
		$rsa=require_once(file::path('data/rsa.php'));
		$this->display('index_login',array(
			'modulus'=>$rsa['modulus'],
			'exponent'=>$rsa['exponent'],
		));
	}
	public function loginoutAction(){
		setcookie($this->config['custom']['cookie_admin'],'',time()-1,'/');
		header('location:'.url('index/login'));
		exit;
	}
	public function loginPost(){
		$post=array(
			'username'=>trim($_POST['username']),
			'password'=>trim($_POST['password']),
			'save'=>(int)$_POST['save']
		);
		if(strlen($post['username'])<250||strlen($post['password'])<250){
			$this->error('非法请求');
		}
		//rsa解密判断用户
		$encrypt_data = pack('H*', $post['username']); //对十六进制数据进行转换
		$rsa=require_once(file::path('data/rsa.php'));
		openssl_private_decrypt($encrypt_data, $post['username'], $rsa['private']); //解密数据
		if(!$post['username']||!$post['password']){
			$this->error('用户名和密码不能为空');
		}
		$admin=array();
		if(preg_match('/^1[\d]{10}$/',$post['username'])){
			$admin=db::find('admin',array('phone'=>$post['username']));
		}elseif(preg_match('/^[\w]+@[\w]+\.[\.\w]+$/',$post['username'])){
			$admin=db::find('admin',array('mail'=>$post['username']));
		}
		if(!$admin){
			$admin=db::find('admin',array('username'=>$post['username']));
		}
		if(!$admin){
			$this->error('不存在该用户');
		}
		//rsa解密判断密码
		$encrypt_data = pack('H*', $post['password']); //对十六进制数据进行转换
		openssl_private_decrypt($encrypt_data, $post['password'], $rsa['private']); //解密数据
		if($admin['password']!=md5($admin['salt'].$post['password'].$admin['salt'])){
			$this->error('密码错误');
		}
		if($admin['disable']==2){
			$this->error('您已被禁用');
		}
		//重置admin信息
		$admin['token']=md5($admin['username'].$admin['password'].time().func::random(5));
		$admin['group']=db::find('admin_group',array('id'=>$admin['gid']));
		$adminModel=new adminModel();
		$adminModel->edit(array(
			'id'=>$admin['id'],
			'token'=>$admin['token']
		));
		$expire=0;
		if($post['save']){
			$expire=time()+3600*24*30;
		}
		setcookie($this->config['custom']['cookie_admin'],$admin['token'],$expire,'/');
		$this->success('登录成功',array(
			'admin'=>$admin,
		));
	}
}
