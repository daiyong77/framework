<?php
function url($ca='index/index',$param=array(),$enrty='',$enrty2=''){
	$url=array();
	//param
	if(!is_array($param)){
		$enrty=$param;
		$param=array();
	}
	unset($param['c']);
	unset($param['a']);
	$url['param']=array();
	if($param){
		foreach($param as $k=>$v){
			$url['param'][]=$k.'='.$v;
		}
	}
	if(strpos($enrty, '=')){
		$url['param'][]=$enrty;
		$enrty=$enrty2;
	}
	//enrty
	if($enrty){
		$enrty=$enrty.'.php';
	}else{
		$enrty=basename($_SERVER['SCRIPT_NAME']);
	}
	$url['enrty']=$enrty;
	//controller&action
	$ca=explode('/',$ca);
	$url['c']=$ca[0];
	$url['a']=$ca[1];
	//return
	if($GLOBALS['_CONFIG']['sys']['rewrite']){
		$url['param']=$url['param']?'?'.implode('&',$url['param']):'';
		if($url['enrty']=='index.php'){
			return $GLOBALS['_CONFIG']['sys']['http'].$url['c'].'/'.$url['a'].$url['param'];
		}else{
			return $GLOBALS['_CONFIG']['sys']['http'].str_replace('.php','',$url['enrty']).'/'.$url['c'].'/'.$url['a'].$url['param'];
		}
	}else{
		$url['param']=$url['param']?'&'.implode('&',$url['param']):'';
		return $GLOBALS['_CONFIG']['sys']['http'].$url['enrty'].'?c='.$url['c'].'&a='.$url['a'].$url['param'];
	}
}

function success($msg='成功',$data=array(),$status=1){
	if(is_numeric($data)){
		$status=$data;
		$data=array();
	}
	if(is_array($msg)){
		$data=$msg;
		$msg=$txt;
	}
	return array(
		'status'=>$status,
		'message'=>$msg,
		'data'=>$data
	);
}
function error($msg='失败',$data=array(),$status=0){
	if(is_numeric($data)){
		$status=$data;
		$data=array();
	}
	if(is_array($msg)){
		$data=$msg;
		$msg=$txt;
	}
	return array(
		'status'=>$status,
		'message'=>$msg,
		'data'=>$data
	);
}
//输出带回行的html时转换br
function _html($data){
	$data=str_replace(PHP_EOL,'<br/>',$data);
	$data=str_replace(' ','&nbsp;&nbsp;',$data);
	return $data;
}

//判断
const FIELD_ERROR = 'FIELD_ERROR_Hc7uT3qZlP'; 
function _int($data){
	return (int)$data;
}
function _var($data,$max=255){
	$data=trim($data);
	if(strlen($data)>$max){
		return FIELD_ERROR;
	}
	return $data;
}
function _array($data){
	if(is_array($data)){
		foreach($data as $k=>$v){
			if(is_array($v)){
				$v=_array($v);
			}else{
				$v=_var($v);
			}
			if($v===FIELD_ERROR){
				return FIELD_ERROR;
			}
			$data[$k]=$v;
		}
		return $data;
	}
	return array();
}
function _phone($data){
	$data=trim($data);
	if($data){
		if(preg_match('/^1[\d]{10}$/', $data)){
			return $data;
		}else{
			return FIELD_ERROR.'|手机号码格式错误';
		}
	}
	return '';
	
}
function _mail($data){
	$data=trim($data);
	if($data){
		if(preg_match('/^[\w]+@[\w]+\.[\w\.]+$/', $data)){
			return $data;
		}else{
			return FIELD_ERROR.'|邮箱格式错误';
		}
	}
	return '';
	
}
function _txt($data){
	return $data;
}
