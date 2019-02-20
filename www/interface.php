<?php
require_once(__DIR__.'/../code/config.php');
//系统配置
$_CONFIG['sys']['entry']='interface/app';
db::connect($_CONFIG['db']);
unset($_CONFIG['db']);
//自定义配置
$_CONFIG['custom']=array(
	'page_list'=>30,//每页显示条数
	'cookie_user'=>md5(__FILE__),//登陆用户的cookie
);

require_once(__DIR__.'/../code/run.php');
