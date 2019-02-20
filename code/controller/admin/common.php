<?php
class common{
	public $config;
	public $admin;
	public function __construct(){
		$this->config=$GLOBALS['_CONFIG'];
		$check=$this->checkLogin();
		if(!$check['status']){
			//载入登陆模板
			$rsa=require_once(file::path('data/rsa.php'));
			$this->display('index_login',array(
				'modulus'=>$rsa['modulus'],
				'exponent'=>$rsa['exponent'],
			));
		}
		$this->admin=$check['data'];
		//权限判断
		$power=$this->config['sys']['controller'].'/'.$this->config['sys']['action'];
		if(!in_array($this->config['sys']['controller'], $this->admin['group']['power'])){
			if(!in_array($power, $this->admin['group']['power'])){
				$this->error('暂无权限');
			}
		}
	}
	//------公告列表信息
	public function noticeTypeList(){
		$list=db::findAll('notice_type',array(),'order by sort desc,id asc','id');
		return $list;
	}
	//------根据fid获取地区列表
	public function areaList($fid=0){
		$list=db::findAll('area',array('fid'=>$fid),'order by sort desc,id asc','id');
		return $list;
	}
	//-------获取管理组列表
	public function adminGroupList(){
		if(!$this->admin)return array();
		$list=db::findAll('admin_group',array('lv|<'=>$this->admin['group']['lv']),'order by sort desc,lv desc,id asc','id');
		return $list;
	}
	//检查是否登陆状态
	public function checkLogin(){
		$admin=db::find('admin',array(
			'token'=>$_COOKIE[$this->config['custom']['cookie_admin']]
		));
		if(!$admin){
			return error('未找到用户信息');
		}
		if($admin['disable']==2){
			return error('您的账户已被禁用');;
		}
		$admin['group']=db::find('admin_group',array('id'=>$admin['gid']));
		$admin['group']['power']=array_merge($this->config['custom']['power_open'],json_decode($admin['group']['power']));
		if($admin['group']['power_all']==2){
			$admin['group']['power']=array_merge($this->config['custom']['power_open'],$this->getFilePowerList());
		}
		return success('已登录',$admin);
	}
	//通用查询
	public function search($table,$search=array(),$sort='id desc',$export=''){
		$page=(int)$_GET['page']?(int)$_GET['page']:1;
		//去除search无用字符
		foreach($search as $k=>$v){
			if(!$v||$v=='%'||$v=='%%'){
				unset($search[$k]);
			}
			if(is_array($v)){
				$v=implode(',', $v);
				$search[$k]=$v?'('.$v.')':'(0)';
			}
		}
		$sort=' order by '.$sort.' ';

		if(!is_object($export)){//返回查询结果
			//查询page
			$count=db::find($table.'|count(id)',$search);
			$page_array=$this->page($page,$this->config['custom']['page_list'],$count);
			//查询列表
			if($page>$page_array['all'])$page=$page_array['all'];
			$limit=' limit '.($page-1)*$this->config['custom']['page_list'].','.$this->config['custom']['page_list'].' ';
			$list=db::findAll($table,$search,$sort.$limit);
			return $list;
		}else{//导出
			list($title,$list_title,$list_body)=$export($search,$sort);
			for($i=0;$i>-1;$i=$i+5000){
				$data=array();
				$list=db::findAll($table,$search,$sort.'limit '.$i.',5000');
				if($i==0&&!$list)$this->error('需要导出的数据为空');
				if(!$list)break;
				if($i==0){
					$data[-1]=$list_title;
				}
				foreach($list as $v){
					$data[]=$list_body($v);
				}
				file::exportCSV($title,$data,$i);
			}
			exit;
		}
	}
	//分页
	//page(当前页(可随便传),每页显示,总条数)  必须全为数字
	//return array
	private function page($page_now,$page_count,$count){
		if($page_now<1)$page_now=1;
		$page_all=@ceil($count/$page_count);//总页数
		if($page_all<1)$page_all=1;
		if($page_now>$page_all)$page_now=$page_all;
		$page_prev=$page_now-1<1?1:$page_now-1;
		$page_next=$page_now+1>$page_all?$page_all:$page_now+1;
		$page=array(
			'all'=>$page_all,//总页数
			'now'=>$page_now,//当前页
			'count'=>$count,//总数量
			'prev'=>$page_prev,//上一页
			'next'=>$page_next
		);
		$this->page=$page;
		return $page;
	}
	//获取权限字段
	public function getFilePowerList(){
		$list=array();
		$array=$this->getFilePower();
		foreach($array as $k=>$v){
			foreach($v as $v2){
				$list[]=$k.'/'.$v2;
			}
		}
		return $list;
	}
	public function getFilePower(){
		$list=dir::getFileList('controller/'.$this->config['sys']['entry'].'/');
		$power=array();
		foreach($list as $v){
			$code=file::get($v);
			preg_match_all('/public[ ]+function[ ]+([\w]+)(Action|Post)\(/', $code, $matches);
			$v=str_replace('.php','',str_replace('controller/'.$this->config['sys']['entry'].'/', '', $v));
			if($matches[1]){
				$power[$v]=array_unique($matches[1]);
			}
		}
		foreach($this->config['custom']['power_open'] as $v){
			$v=explode('/',$v);
			if(!$v[1]){
				unset($power[$v[0]]);
			}elseif($power[$v[0]]){
				foreach($power[$v[0]] as $k2=>$v2){
					if($v[1]==$v2){
						unset($power[$v[0]][$k2]);
					}
				}
			}
		}
		return $power;
	}
	//模板显示
	public function display($tpl='',$data=array()){
		if(is_array($tpl)){
			$data=$tpl;
			$tpl='';
		}
		//参数
		if($this->admin&&!$data['admin']){
			$data['admin']=$this->admin;
		}
		if($this->config&&!$data['config']){
			$data['config']=$this->config;
		}
		if($this->get&&!$data['get']){
			$data['get']=$this->get;
			if(isset($_GET['page'])&&$_GET['page']){
				$data['get']['page']=$_GET['page'];
			}
		}
		if($this->page&&!$data['page']){
			$data['page']=$this->page;
		}
		//显示
		if($this->isajax()){
			echo json_encode(array(
		    	'status'=>1,
		    	'message'=>'请求成功',
		    	'data'=>$data
		    ),JSON_UNESCAPED_UNICODE);
		    exit;
		}else{
			if(!$tpl){
				$sys=$this->config['sys'];
				$tpl=$sys['controller'];
				if($sys['action']!='index'){
					$tpl.='_'.$sys['action'];
				}
			}
			tpl::display($tpl,$data);
		}
	}
	public function success($msg,$data=array(),$status=1){
		$data=$this->displayStatus($msg,$data,$status,'成功');
		tpl::$base='';
	   	tpl::display('common/success',$data);
	}
	public function error($msg,$data=array(),$status=0){
		$data=$this->displayStatus($msg,$data,$status,'失败');
		tpl::$base='';
	   	tpl::display('common/error',$data);
	}
	private function displayStatus($msg,$data,$status,$txt){
		if(is_numeric($data)){
			$status=$data;
			$data=array();
		}
		if(is_array($msg)){
			$data=$msg;
			$msg=$txt;
		}
		$data=array(
	    	'status'=>$status,
	    	'message'=>$msg,
	    	'data'=>$data
	    );
		if($this->isajax()){ 
			echo json_encode($data);
		    exit;
		}
		return $data;
	}
	private function isajax(){
		//手动固定是ajax
		if(isset($_GET['isajax'])&&$_GET['isajax']==1){
			return true;
		}
		//ajax请求
		if(isset($_SERVER["HTTP_X_REQUESTED_WITH"])&&strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])=="xmlhttprequest"){
			return true;
		}
		return false;
	}
}
