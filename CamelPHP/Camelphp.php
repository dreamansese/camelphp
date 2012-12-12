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
 * 系统入口文件
 *
 * 定义初始化常量,全局函数,以及启动框架程序
 *
 * @category CamelPHP
 * @package  Camelphp
 * @author   dreamans<dreamans@163.com>
 * @since    20120928
 *

/**
 * 定义入口
 *
 * @var boolean
 */
define('C_IN', true);

/**
 * 框架版本
 *
 * @var string
 */
define('SYS_VERSION', '1.0.0');

/**
 * 系统启动时间
 * 
 * @var time
 */
define('SYS_BTIME', array_sum(explode(' ',microtime())));

/**
 * 系统启动时内存使用情况
 *
 * @var int
 */
if(function_exists('memory_get_usage')) define('SYS_STARTUSEMEMS', memory_get_usage());

/**
 * 判断是否是CLI模式
 * 
 * @var string
 */
define('CLI', PHP_SAPI == 'cli' ? 1 : 0);

/**
 * 系统include_path分割符
 *
 * @var string
 */
if(!defined('PATH_SEPARATOR')) {
    if(substr(PHP_OS, 0, 3) == 'WIN') {
        define('PATH_SEPARATOR', ';');
    }else{
        define('PATH_SEPARATOR', ':');
    }
}

/**
 * 定义框架根目录
 *
 * @var path
 */
define('C_ROOT', dirname(__FILE__));

/**
 * 定义路径分隔符
 *
 * @var path
 */
define('C_DS', DIRECTORY_SEPARATOR);

/**
 * 定义框架核心目录
 *
 * @var path
 */
define('C_CORE', C_ROOT.C_DS.'Core'.C_DS);

/**
 * 框架默认配置文件目录
 *
 * @var path
 */
define('C_CONFIG', C_ROOT.C_DS.'Config'.C_DS);

/**
 * 框架扩展目录
 *
 * @var path
 */
define('C_EXT', C_ROOT.C_DS.'Extend'.C_DS);

/**
 * 项目模板编译目录
 *
 * @var path
 */
define('APP_TPLCOM_PATH', APP_PATH.C_DS.'Data'.C_DS.'Tplcache'.C_DS);

/**
 * 项目日志存放目录
 *
 * @var path
 */
define('APP_LOG_PATH', APP_PATH.C_DS.'Data'.C_DS.'Log'.C_DS);

/**
 * 项目模块/组建执行程序存放目录
 *
 * @var path
 */
define('APP_MODULES_PATH', APP_PATH.C_DS.'Modules'.C_DS);

/**
 * 项目Session默认存放目录
 *
 * @var path
 */
define('APP_SESSION_PATH', APP_PATH.C_DS.'Data'.C_DS.'Session');

/**
 * 项目文本缓存默认存放目录
 *
 * @var path
 */
define('APP_FILECACHE_PATH', APP_PATH.C_DS.'Data'.C_DS.'Cache'. C_DS);

/**
 * 定义MAGIC_QUOTES_GPC
 *
 * @var boolean
 */
if(version_compare(PHP_VERSION,'5.3.0','<') ) {
    @set_magic_quotes_runtime (0);
}
define('MAGIC_QUOTES_GPC', function_exists('get_magic_quotes_gpc') ? get_magic_quotes_gpc() : false);

/**
 * 读取realpath缓存大小
 *
 * @var size
 */
define('REALPATH_CACHE_SIZE', function_exists('realpath_cache_size') ? realpath_cache_size(): 'unknown');

/**
 * 对象聚合函数
 *
 * @param $key
 * @param $obj
 * @return object
 */
function Super($key = NULL, $obj = NULL) {
    static $objs = array();
    if(!$key) {
        return $objs;
    } elseif(!$obj) {
        return isset($objs[$key]) ? $objs[$key]: NULL;
    } else {
        $objs[$key] = $obj; 
    }
}

/**
 * 系统启动函数
 *
 * @param $cfg
 * @return void
 */
function Start($cfg = 'Config.php') {
    require_once C_CORE.'Camelphp.php';
    Camelphp::getCamelphp($cfg)->camelRun();
}
