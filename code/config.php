<?php
header('Content-type:text/html;charset=utf-8');
date_default_timezone_set('PRC');
error_reporting(E_ALL ^ E_NOTICE);//开启所有错误信息隐藏不重要的
ini_set("display_errors", "On");//开启错误提示
if(PHP_SAPI=='cli'){//命令行下执行
    ini_set('memory_limit', '1G');
    set_time_limit(0);
    //修改文件引用
    list($_GET['c'],$_GET['a'])=explode('/',$argv[1]);
    if(!$_GET['c']||!$_GET['a']){
    	exit('请输入controller或action输入错误格式为: php crontab.php xxx/xxx'.PHP_EOL);
    }
}else{//网页执行
    set_time_limit(3);
}
if(!version_compare(PHP_VERSION,'5.6.27','ge')){
	die('php版本过低，必须 >= 5.6.27');
}
//自动加载库目录
function my_autoloader($class) {
	if(in_array($class,array('db','dir','file','func','http','tpl'))){
		require_once __DIR__.'/phplibs/' . $class . '.php';
	}elseif(strpos($class,'thirdParty\\')===0||strpos($class,'tools\\')===0){
		require_once __DIR__.'/phplibs/' . str_replace('\\','/',$class) . '.php';
	}elseif(strpos($class,'Model')){
		require_once __DIR__.'/model/' . $class . '.php';
	}
}
spl_autoload_register('my_autoloader');
//系统配置
$_CONFIG['sys']=array(
	'http'=>'http://'.$_SERVER['HTTP_HOST'].(strlen(dirname($_SERVER['SCRIPT_NAME']))<=1?'':dirname($_SERVER['SCRIPT_NAME'])).'/',
	'controller'=>isset($_GET['c'])&&preg_match('/^[\w]+$/',$_GET['c'])?$_GET['c']:'index',
	'action'=>isset($_GET['a'])&&preg_match('/^[\w]+$/',$_GET['a'])?$_GET['a']:'index',
	'rewrite'=>0,//服务器是否开启rewite
);
//数据库配置
$_CONFIG['db']=array(
	'connect'=>'mysql:host=127.0.0.1;dbname=project',
	'username'=>'root',
	'password'=>'root',	
	'charset'=>'utf8'
);

