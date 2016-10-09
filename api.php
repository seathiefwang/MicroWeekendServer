<?php
error_reporting(E_ERROR ^ E_NOTICE ^ E_WARNING);

/* ///调试、找错时请去掉///前空格
ini_set('display_errors',true);
error_reporting(E_ALL); 
set_time_limit(0);
// */
//网站根路径设置
define('SITE_PATH', dirname(__FILE__));

//默认应用设置为API
//$_GET['app'] = 'api';


define('APP_NAME', 'api');
if (isset($_REQUEST['api_version'])) {
    $api_version = preg_replace('/[^A-Za-z0-9\._-]/', '', $_REQUEST['api_version']);
    define('API_VERSION', $api_version);
    unset($api_version);
} else {
    define('API_VERSION', 'microWeekend');
}

//载入api文件
require SITE_PATH.'/api/mk.php';

Api::run();

/* # The end */