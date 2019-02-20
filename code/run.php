<?php
//载入执行文件
require_once(__DIR__.'/vendor/autoload.php');
require_once(__DIR__.'/func.php');
require_once(__DIR__.'/controller/'.$_CONFIG['sys']['entry'].'/common.php');
require_once(__DIR__.'/controller/'.$_CONFIG['sys']['entry'].'/'.$_CONFIG['sys']['controller'].'.php');

if($_POST){
	$_CONFIG['sys']['mode']='Post';
}else{
	$_CONFIG['sys']['mode']='Action';
}

$controller=new $_CONFIG['sys']['controller']();
$action=$_CONFIG['sys']['action'].$_CONFIG['sys']['mode'];
$controller->$action();
