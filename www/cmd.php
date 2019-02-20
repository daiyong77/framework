<?php
if(PHP_SAPI!='cli')exit('error'.PHP_EOL);
require_once(__DIR__.'/../code/config.php');

//系统配置
$_CONFIG['sys']['entry']='cmd';
db::connect($_CONFIG['db']);
unset($_CONFIG['db']);

require_once(__DIR__.'/../code/run.php');

