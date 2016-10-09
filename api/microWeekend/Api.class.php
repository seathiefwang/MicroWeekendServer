<?php

class Api 
{
    public $user_name;
    public $error;
    public $db;
    
    public function __construct($location = false) {
        
        
        $this->db = Db::init();
        //控制器初始化
        if (MODULE_NAME !=='User'&&ACTION_NAME!=='login'&&ACTION_NAME!=='register') {
            $this->verifyUser();
        }
        if (method_exists($this, '_initialize')) {
            $this->_initialize();
        }
    }
    
    public function data() {
        $this->user_name = $_REQUEST['user_name'] ? $_REQUEST['user_name'] : '';
    }
    
    public function input($k) {
        return isset($_REQUEST[$k]) ? (is_array($_REQUEST[$k]) ? $_REQUEST[$k] : trim($_REQUEST[$k])) : null;
    }
    
    /**
     * verifyUser
     * @return 
     */
    protected function verifyUser() {
        $uname = $this->input('user_name');
        $uuid = $this->input('uuid');
        
        if ($uuid == null||!$this->db->query("select user_id from users where user_name='$uname' and uuid='$uuid'")) {
            return $this->error(10010, "uuid is error");
        }      
    }

    //返回错误信息
    public static function error($code, $msg = '') {       
        $message['code'] = $code;
        switch ($code) {
            case 0:
                $message['msg'] = '操作失败';break;
            case 10011:
                $message['msg'] = '密码错误';break;
            case 10012:
                $message['msg'] = '用户名错误';break;
            case 10013:
                $message['msg'] = '用户名已存在';break;
            case 20010:
                $message['msg'] = '插入数据库出错';break;
            default:
                $message['msg'] = '操作失败';break;
        }
        
        if (is_array($msg)) {
            $message = array_merge($message, $msg);
        } elseif ($msg != '') {
            $message['msg'] = $msg;
        }

        //格式化输出
        if (isset($_REQUEST['format']) && $_REQUEST['format'] == 'test') {
            //测试输出
            var_dump($message);
            exit;
        } else {
            exit(json_encode($message));
        }
    }

    //返回成功信息
    public static function success($msg = '') {
        $message['msg'] = '操作成功';
        $message['code'] = 1;
        if (is_array($msg)) {
            $message = array_merge($message, $msg);
        } elseif ($msg != '') {
            $message['msg'] = $msg;
        }

        //格式化输出
        if (isset($_REQUEST['format']) && $_REQUEST['format'] == 'test') {
            //测试输出
            var_dump($message);
            exit;
        } else {
            exit(json_encode($message));
        }
    }

    //返回错误信息
    public static function getError() {
        return $this->error;
    }

    /**
     * 运行控制器
     * @access public
     */
    public static function run() {

        // 设定错误和异常处理
        set_error_handler(array('Api', 'appError'));
        set_exception_handler(array('Api', 'appException'));

        // Session��ʼ��
        //if (!session_id()) {
        //    session_start();
        //}

        if (constant('API_VERSION')) {
            $class_file = SITE_PATH.'/api/'.API_VERSION.'/'.MODULE_NAME.'Api.class.php';
        } else {
            $class_file = SITE_PATH.'/api/micro_weekend/'.MODULE_NAME.'Api.class.php';
        }

        if (!file_exists($class_file)) {
            $message['msg'] = '接口不存在';
            $message['code'] = 404;
            API::error($message);
        }

        //执行当前操作
        include $class_file;
        $className = MODULE_NAME.'Api';
        $module = new $className();
        $action = ACTION_NAME;
        $data = call_user_func(array(&$module, $action));

        //格式化输出
        if ($_REQUEST['format'] == 'php') {
            //输出php格式
            echo var_export($data);
        } elseif ($_REQUEST['format'] == 'test') {
            //测试输出
            var_dump($data);
        } else {
            header('Content-Type:application/json');
            echo json_encode($data);
        }

        return ;
    }

    /**
     * app异常处理
     * @access public
     */
    public static function appException($e) {
        die('system_error:'.$e->__toString());
    }

    /**
     * 自定义错误处理
     * @access public
     * @param int    $errno   错误类型
     * @param string $errstr  错误信息
     * @param string $errfile 错误文件
     * @param int    $errline 错误行数
     */
    public static function appError($errno, $errstr, $errfile, $errline) {
        switch ($errno) {
          case E_ERROR:
          case E_USER_ERROR:
            $errorStr = "[$errno] $errstr ".basename($errfile)." 第 $errline 行.";
            //if(C('LOG_RECORD')) Log::write($errorStr,Log::ERR);
            echo $errorStr;
            break;
          case E_STRICT:
          case E_USER_WARNING:
          case E_USER_NOTICE:
          default:
            $errorStr = "[$errno] $errstr ".basename($errfile)." 第 $errline 行.";
            //Log::record($errorStr,Log::NOTICE);
            break;
      }
    }
}
