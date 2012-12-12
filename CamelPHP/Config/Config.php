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

defined("C_IN") || die("Access Denied");

/**
 * 系统默认配置信息
 *
 * @category CamelPHP
 * @package  Config
 * @author   dreamans<dreamans@163.com>
 * @since    20120928
 */
class CP_Config {

    /**
     * 是否开启调试模式
     *
     * @access public
     * @var boolean
     */
    var $debug = true;

    /**
     * 默认时区
     *
     * @access public
     * @var int
     */
    var $timezone = 8;
    
    /**
     * 是否开启gzip压缩
     *
     * @access public
     * @var boolean
     */
    var $gzip = true;

    /**
     * 网页默认编码
     *
     * @access public
     * @var charset
     */
    var $charset = 'utf-8';
    
    /**
     * 应用WEBPATH
     *
     * @access public
     * @var boolean
     */
    var $webPath = '';

    /**
     * GET接收MODULES参数的默认KEY
     *
     * @access public
     * @var string
     */
    var $moduleKey = 'm';
    
    /**
     * 默认模块
     *
     * @access public
     * @var string
     */
    var $defModule = 'welcome';
    
    /**
     * GET接收Controller参数的默认KEY
     *
     * @access public
     * @var string
     */
    var $controllerKey = 'c';
    
    /**
     * 默认控制器
     *
     * @access public
     * @var string
     */
    var $defController = 'index';
    
    /**
     * GET接收Method参数的默认KEY
     *
     * @access public
     * @var string
     */
    var $methodKey = 'a';
    
    /**
     * 默认控制方法
     *
     * @access public
     * @var string
     */
    var $defMethod = 'index';

    /**
     * URL模式 0 - QuerString; 1 - PATH_INFO
     *
     * @access public
     * @var string
     */
    var $urlModel = 1;
    
    /**
     * Url重定向,仅支持PATH_INFO方式
     *
     * @access public
     * @var string
     */
    var $urlRewrite = array(
        'welcome' => 'welcome/welcome/index'
    );

    /**
     * cookie设置
     *
     * @access public
     * @var array
     */
    var $cookieConfig = array(
        'path' => '/',
        'domain' => '',
        'expire' => 3600,
    );

    /**
     * session过期时间,单位s
     *
     * @access public
     * @var int
     */
    var $sessionGcMaxlifetime = '3600';

    /**
     * session默认路径
     *
     * @access public
     * @var path
     */
    var $sessionPath = APP_SESSION_PATH;

    /**
     * log路径
     *
     * @access public
     * @var path
     */
    var $logPath = APP_LOG_PATH;

    /**
     * 记录log类型
     *
     * @access public
     * @var string
     */
    var $logType = 'access,error';

    /**
     * 日志最大尺寸,单位KB
     *
     * @access public
     * @var int
     */
    var $logMaxSize = '5000';

    /**
     * 是否开启伪静态
     * 
     * @access public
     * @var boolean
     */
    var $defUrlMode = true;
    
    /**
     * MySQL配置
     *
     * @access public
     * @var array
     */
    var $mysqlConfig = array(
        'driver'   => 'DbMysqli',
        'host'     => 'localhost',
        'port'     => 3306,
        'user'     => '',
        'password' => '',
        'dbname'   => '',
        'tablepre' => '',
        'charset'  => '',
    );

    /**
     * redis配置
     *
     * @access public
     * @var array
     */
    var $redisConfig = array(
        'host' => '127.0.0.1',
        'port' => 6379,
    );

    /**
     * leveldb配置
     *
     * @access public
     * @var array
     */
    var $leveldbConfig = array(
        'path' => '',
        'opt' => array(),
    );

    /**
     * memcached配置
     *
     * @access public
     * @var array
     */
    var $memcacheConfig = array(
        'host' => '127.0.0.1',
        'port' => 11211,
    );

    /**
     * 文本缓存配置
     *
     * @access public
     * @var array
     */
    var $fileCacheConfig = array(
        'path' => APP_FILECACHE_PATH,
    );

    /**
     * Mysql数据库缓存配置
     *
     * @access public
     * @var array
     */
    var $mysqlCacheConfig = array(
        //该选项可省略,省略后默认使用mysqlConfig的配置信息
        'connect' => array(
            'driver'   => 'DbMysqli',
            'host'     => 'localhost',
            'port'     => 3306,
            'user'     => 'root',
            'password' => 'root',
            'dbname'   => 'esw',
            'tablepre' => 'cp_',
            'charset'  => 'utf8',
        ),
        //缓存表数据结构 key char(32) primary, value mediumblob, life int(10) index
        'table' => 'cache',
    );

    /**
     * redis缓存配置 [该配置信息可省略,省略后默认使用redisConfig的配置信息]
     *
     * @access public
     * @var array
     */
    var $redisCacheConfig = array(
        'host' => '127.0.0.1',
        'port' => 6379,
    );

    /**
     * memcache缓存配置 [该配置信息可省略,省略后默认使用memcacheConfig的配置信息]
     *
     * @access public
     * @var array
     */
    var $memcacheCacheConfig = array(
        'host' => '127.0.0.1',
        'port' => 11211,
    );

    /**
     * 在控制器实例化之前自动加载文件
     *
     * @access public
     * @var array
     */
    var $autoLoad = array();

}
