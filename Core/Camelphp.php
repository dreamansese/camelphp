<?php

/**
 * CamelPHP [A journey always starts with the first step]
 *
 * CamelPHP is an open source framework for PHP
 *
 * @author    dreamans<dreamans@163.com>
 * @copyright Copyright (C) 2012 camelphp.com All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0.txt
 */

//----------------------------------------------------------------

/**
 * 框架核心启动类
 *
 * @category CamelPHP
 * @package  Core
 * @author   dreamans<dreamans@163.com>
 * @since    20120928
 */
class Camelphp{

    /**
     * 项目配置信息
     *
     * @access protected
     * @var array
     */
    protected $_config;

    /**
     * 实例化Camelphp
     *
     * @access public static
     * @return object
     */
    public static function getCamelphp($cfg) {
        $camel = new Camelphp($cfg);
        return $camel;
    }

    /**
     * 引入项目配置信息
     *
     * @access public
     * @param $cfg
     * @return void
     */
    public function __construct($cfg) {
        if(is_array($cfg)) {
            $this->_config = $cfg;
        } else {
            $appConfigPath = APP_PATH.C_DS.'Config'.C_DS.$cfg;
            if(is_file($appConfigPath)) {
                $this->_config = include $appConfigPath;
            }
        }
    }

    /**
     * 总调度方法
     * 注册类库到系统中
     *
     * @access public
     * @return void
     */
    public function camelRun() {
        //初始化系统,开启缓存
        $this->init();
        //对用户输入进行安全过滤
        $this->safeFilter();
        //进行路由设置
        $this->route();
        //开启session功能
        $this->session();
        //开启cookie功能
        $this->cookie();
        //开启globals
        $this->globals();
        //开启缓存功能
        $this->cache();
        //初始化模型操作
        $this->model();
        //执行控制器程序
        $this->controller();
    }

    /**
     * 控制器调度
     *
     * @access public
     * @return void
     */
    public function controller() {
        //记录访问日志
        Super('Log')->record();
        //记录post日志
        Super('Log')->record('', 'post');
        //引入核心控制器类
        Super('Func')->import('Core.Controller');
        //控制器初始化之前引入自动加载文件
        $this->autoLoad();
        Super('Func')->controller();
    }

    /**
     * 核心初始化方法
     *
     * @access public
     * @return void
     */
    public function init() {
        //require system config and functions
        $file = array(
            C_CORE.'Base.php',
            C_CONFIG.'Config.php',
            C_CORE.'Function.php',
        );
        foreach($file as $fpath) {
            require $fpath;
        }
        Super('Conf', new CP_Config());
        //引入项目配置文件
        if(is_array($this->_config)) {
            foreach($this->_config as $cKey => $cVal) {
                Super('Conf')->{$cKey} = $cVal;
            }
        }
        Super('Func', new CP_Function());
        Super('Log', Super('Func')->objects('Core.Log'));

        //自定义错误处理方法
        set_error_handler(array($this, 'errorDebug'));

        //自定义异常处理方法
        set_exception_handler(array($this, 'exceptionDebug'));

        //引入短函数
        Super('Func')->import('Core.ShortFunc');

        //timezone setting
        if(function_exists('date_default_timezone_set')) {
            $timezone = Super('Conf')->timezone * -1;
            @date_default_timezone_set("Etc/GMT".$timezone);
        }
        if(Super('Conf')->gzip && function_exists('ob_gzhandler') && isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER[ 'HTTP_ACCEPT_ENCODING'], 'gzip ')!==false) {
            ob_start('ob_gzhandler');
        } else {
            ob_start();
        }
        //Close absolute output
        ob_implicit_flush(0);
    }

    /**
     * 引入自动加载文件
     *
     * @access public
     * @return void
     */
    public function autoLoad() {
        //控制器初始化之前引入自动加载文件
        $autoLoad = Super('Conf')->autoLoad;
        if(is_array($autoLoad)) {
            foreach($autoLoad as $autoFile) {
                Super('Func')->requireFile($autoFile);
            }
        }
    }

    /**
     * 过滤输入
     *
     * @access public
     * @return void
     */
    public function safeFilter() {
        $_POST = Super('Func')->addSlashes($_POST);
        $_GET = Super('Func')->addSlashes($_GET);
        $_COOKIE = Super('Func')->addSlashes($_COOKIE);
        $_FILES = Super('Func')->addSlashes($_FILES);
    }

    /**
     * 路由分析
     *
     * @access public
     * @return void
     */
    public function route() {
        Super('Get', Super('Func')->objects('Core.Get'));
        Super('Post', Super('Func')->objects('Core.Post'));
        Super('Func')->routeUrl();
    }

    /**
     * Session设置
     *
     * @access public
     * @return void
     */
    public function session() {
        Super('Session', Super('Func')->objects('Core.Session'));
        Super('Session')->start();
    }

    /**
     * 初始化模型
     *
     * @access public
     * @return void
     */
    public function model() {
        Super('Model', Super('Func')->objects('Core.Model'));
    }

    /**
     * Cache初始化
     *
     * @access public
     * @return void
     */
    public function cache() {
        Super('Cache', Super('Func')->objects('Core.Cache'));
    }

    /**
     * Cookie初始化
     * 
     * @access public
     * @return void
     */
    public function cookie() {
        Super('Cookie', Super('Func')->objects('Core.Cookie'));
    }

    /**
     * Globals初始化
     * 
     * @access public
     * @return void
     */
    public function globals() {
        Super('Globals', Super('Func')->objects('Core.Globals'));
    }

    /**
     * 自定义exception
     *
     */
    public function exceptionDebug($e) {
        $str = '<b>Exception:</b> [ID '.$e->getCode().'] <font style="color:#cc0000;font-size:18px">'.$e->getMessage().'</font> ( Line: '.$e->getLine() .' of '.$e->getFile().' )<br/>';
        $this->debugInfo($str, $e->getTrace());
    }

    /**
     * 自定义error
     *
     * @access public
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     * @return die
     */
    public function errorDebug($errno, $errstr, $errfile, $errline) {
        $errlevel = 'ERROR';
        switch($errno) {
            case E_NOTICE :
                $errlevel = 'E_NOTICE';
                break;
            case E_WARNING :
                $errlevel = 'E_WARNING';
                break;
            case E_USER_ERROR :
                $errlevel = 'E_USER_ERROR';
                break;
            case E_USER_WARNING :
                $errlevel = 'E_USER_WARNING';
                break;
            case E_USER_NOTICE :
                $errlevel = 'E_USER_NOTICE';
                break;
            case E_STRICT :
                $errlevel = 'E_STRICT';
                break;
            case E_COMPILE_WARNING :
                $errlevel = 'E_COMPILE_WARNING';
        }
        //record error log
        $errmessage = "{$errlevel} [ERRID:{$errno}] $errstr ( Line:{$errline} of '{$errfile}')";
        Super('Log')->record($errmessage, 'error');
        //判断是否输出错误信息
        $debug = Super('Conf')->debug;
        if(!$debug) {
            die('PHP Error');
        }
        $str = '<b>'.$errlevel.':</b> [ID '.$errno.'] <font style="color:#cc0000;font-size:18px">'.$errstr.'</font> ( Line: '.$errline .' of '.$errfile.' )<br/>';
        $backTrace = debug_backtrace();
        $this->debugInfo($str, $backTrace);
    }

    /**
     * debug信息输出
     *
     * @access public
     * @param $message
     * @return die
     */
    public function debugInfo($message, $backTrace) {
        $str  = '<html><head><title>Error Debug</title></head><body>';
        $str .= '<div style="font-size:14px; text-align:left; border-bottom:1px solid #9cc9e0;padding:10px;color:#000;font-family:Arial, Helvetica,sans-serif;">';
        $str .= $message;
        $str .= '</div>';
        krsort($backTrace);
        $str .= '<div style="font-size:13px;line-height:1.8; padding:10px; font-family:Arial, Helvetica,sans-serif;">';
        $str .= '<b>Code Backtrace:</b>';
        $numline = 1;
        $str .= '<table cellpadding="1" cellspacing="1" width="100%" style="background: #cdcdcd; font: 11pt Menlo,Consolas,"Lucida Console"">';
        $str .= '<tr style="background-color: #efefef;"><td width="5%">No.</td><td width="45%">File</td><td width="5%">Line</td><td width="45%">Code</td></tr>';
        foreach($backTrace as $k => $v) {
            $str .= '<tr style="background-color: #FFFFCC;"><td>'.$numline++.'</td><td>'.(isset($v['file']) ? $v['file']: '').'</td><td>'.(isset($v['line']) ? $v['line']: '') .'</td><td>' . (isset($v['class']) ? $v['class'] : '') . (isset($v['type']) ? $v['type'] : '').(isset($v['function']) ? $v['function'] : '') . (!empty($v['args']) ? '('.json_encode($v['args']).')' : '()') . '</td></tr>';
        }
        $str .= '</table>';
        $str .= '<p>Memory used '. (memory_get_usage()/1024/1024 - SYS_STARTUSEMEMS/1024/1024) . ' MB - - ';
        $str .= 'Runtime '.(array_sum(explode(' ',microtime())) - SYS_BTIME).' S - - ';
        $str .= 'Realpath cache size '.(REALPATH_CACHE_SIZE/1024).'KB - - ';
        $str .= 'PHP version '.PHP_VERSION.' - - ';
        $str .= 'Camelphp version '.SYS_VERSION.'</p>';
        $str .= '</div>';
        $str .= '</body></html>';
        die($str);
    }
}
