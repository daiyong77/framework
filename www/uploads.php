<?php
require_once(__DIR__.'/../code/config.php');

//系统配置
$_CONFIG['sys']['entry']='uploads';
unset($_CONFIG['db']);
//自定义配置
$_CONFIG['custom']=array(
	'image_ext'=>array('jpg','jpeg','gif','png'),//允许上传图片的后缀名
	'image_path_save'=>__DIR__.'/data/images/',//存储路径
	'image_path_http'=>$_CONFIG['sys']['http'].'data/images/',//存储的可访问路径
	'image_max_width'=>1000,//图片最大宽度
	'image_max_size'=>2,//图片大小(M为单位) 服务器限制为:ini_get('upload_max_filesize')
);

require_once(__DIR__.'/../code/run.php');
