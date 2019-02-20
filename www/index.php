<?php
require_once(__DIR__.'/../code/config.php');

//系统配置
$_CONFIG['sys']['entry']='admin';
db::connect($_CONFIG['db']);
unset($_CONFIG['db']);
//模板
tpl::$path_cache='cache/view/'.$_CONFIG['sys']['entry'].'/';//html缓存目录
tpl::$style='view/'.$_CONFIG['sys']['entry'].'/';;//html文件目录
tpl::$base='view/'.$_CONFIG['sys']['entry'].'/common/base.html';//html base目录
tpl::$replace=array(//html替换目录
	'__STATIC__/'=>$_CONFIG['sys']['http'].'static/'.$_CONFIG['sys']['entry'].'/',
	'__SOURCE__/'=>$_CONFIG['sys']['http'].'source/',
);
//自定义配置
$_CONFIG['custom']=array(
	'page_list'=>30,//每页显示条数
	'cookie_admin'=>md5(__FILE__),//登陆用户的cookie
	'power_open'=>array('index','my','jsInterface')//允许任何人访问的页面
);

require_once(__DIR__.'/../code/run.php');

