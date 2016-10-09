<?php

if (!defined('SITE_PATH')) {
    exit();
}

@ini_set('magic_quotes_runtime', 0);

/* # 设置时区 */
if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set('Asia/Shanghai');
}

//$time_include_start = microtime(true);
//$mem_include_start = memory_get_usage();

//设置全局变量mk
$mk['_debug'] = false;        //调试模式
$mk['_define'] = array();    //全局常量
$mk['_config'] = array();    //全局配置
//$mk['_access'] = array();    //访问配置
//$mk['_router'] = array();    //路由配置


mkdefine('API_PATH',    SITE_PATH.'/api/'.API_VERSION);
mkdefine('LIB_PATH',    SITE_PATH.'/api/lib/');

if (!isset($_REQUEST['app']) && !isset($_REQUEST['mod']) && !isset($_REQUEST['act'])) {
    $mk['_app'] = 'micro_weekend';
    $mk['_mod'] = 'UserApi';
    $mk['_act'] = 'login';
} else {
    $mk['_app'] = isset($_REQUEST['app']) && !empty($_REQUEST['app']) ? $_REQUEST['app'] : 'micro_weekend';
    $mk['_mod'] = isset($_REQUEST['mod']) && !empty($_REQUEST['mod']) ? $_REQUEST['mod'] : 'UserApi';
    $mk['_act'] = isset($_REQUEST['act']) && !empty($_REQUEST['act']) ? $_REQUEST['act'] : 'login';
}
//APP的常量定义
mkdefine('APP_NAME', $mk['_app']);
mkdefine('MODULE_NAME', $mk['_mod']);
mkdefine('ACTION_NAME', $mk['_act']);

//载入扩展函数库
mkload(SITE_PATH.'/api/lib/Functions.php');
//
mkload(SITE_PATH.'/api/db/Db.php');


//注册AUTOLOAD方法
if (function_exists('spl_autoload_register')) {
    spl_autoload_register('mkautoload');
}


/* 核心方法 */

/**
 * 载入文件 去重\缓存.
 * @param  string $filename 载入的文件名
 * @return bool
 */
function mkload($filename)
{
    static $_importFiles = array();    //已载入的文件列表缓存

    $key = strtolower($filename);

    if (!isset($_importFiles[$key])) {
        if (is_file($filename)) {
            require_once $filename;
            $_importFiles[$key] = true;
        } elseif (file_exists(API_PATH.'/'.$filename.'.class.php')) {
            require_once API_PATH.'/'.$filename.'.class.php';
            $_importFiles[$key] = true;
        } else {
            $_importFiles[$key] = false;
        }
    }

    return $_importFiles[$key];
}

/**
 * 系统自动加载函数
 * @param string $classname 对象类名
 */
function mkautoload($classname)
{

    // 检查是否存在别名定义
    if (mkload($classname)) {
        return ;
    }

    return ;
}

/**
 * 定义常量,判断是否未定义.
 *
 * @param  string $name  常量名
 * @param  string $value 常量值
 * @return string $str 返回常量的值
 */
function mkdefine($name, $value)
{
    global $mk;
    //定义未定义的常量
    if (!defined($name)) {
        //定义新常量
        define($name, $value);
    } else {
        //返回已定义的值
        $value = constant($name);
    }
    //缓存已定义常量列表
    $mk['_define'][$name] = $value;

    return $value;
}
