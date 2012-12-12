<?php

defined("C_IN") || die("Access Denied");

/**
 * 项目配置文件
 */
return array(

    /**
     * 是否开启测试模式
     */
    'debug' => true,

    /**
     * 默认时区
     */
    'timezone' => 8,

    /**
     * gzip压缩是否开启
     */
    'gzip' => true,

    /**
     * 网页默认编码
     */
    'charset' => 'utf-8',

    /**
     * Url访问方式
     *
     * 支持pathinfo querystring两种方式
     */
    'urlModel' => 1,

    /**
     * 对url进行路由设置,支持正则
     */
    'urlRewrite' => array(
        'welcome' => 'welcome/welcome/index'
    ),

    /**
     * 默认入口模块
     */
    'defModule' => 'welcome',

    /**
     * 应用WEBPATH
     * 若项目入口文件不在根目录下，请在此处定义项目目录
     */
    'webPath' => '/',

    /**
     * 模板常量设置
     * 在模板中可直接引用
     */
    'tplConstant' => array(
        'WWW_STATIC' => '/example/static',
    ),

    /**
     * 日志记录类型 access - 访问日志, error - 错误日志, op - 操作日志
     */
    'logType' => 'access,error,mysql',

    /**
     * 日志最大尺寸, KB
     */
    'logMaxSize' => '5000',

    /**
     * MySQL配置
     */
    'mysqlConfig' => array(
        'driver'   => 'Pdo',
        'host'     => 'localhost',
        'port'     => 3306,
        'user'     => 'root',
        'password' => 'root',
        'dbname'   => 'dbname',
        'tablepre' => 'cp_',
        'charset'  => 'utf8',
    ),

    /**
     * redis配置
     */
    'redisConfig' => array(
        'host' => '127.0.0.1',
        'port' => 6379,
    ),

    /**
     * leveldb配置
     */
    'leveldbConfig' => array(
        'path' => '',
        'opt' => array(),
    ),

    /**
     * memcached配置
     */
    'memcacheConfig' => array(
        'host' => '127.0.0.1',
        'port' => 11211,
    ),

    /**
     * 是否开启伪静态
     */
    'defUrlMode' => false,

    /**
     * 文本缓存配置
     */
    'fileCacheConfig' => array(
        'path' => APP_FILECACHE_PATH,
    ),

    /**
     * Mysql数据库缓存配置
     */
    'mysqlCacheConfig' => array(
        //该选项可省略,省略后默认使用mysqlConfig的配置信息
        'connect' => array(
            'driver'   => 'DbMysqli',
            'host'     => 'localhost',
            'port'     => 3306,
            'user'     => 'root',
            'password' => 'root',
            'dbname'   => 'dbname',
            'tablepre' => 'cp_',
            'charset'  => 'utf8',
        ),
        //缓存表数据结构 key char(32) primary, value mediumblob, life int(10) index
        'table' => 'cache',
    ),

    /**
     * redis缓存配置 [该配置信息可省略,省略后默认使用redisConfig的配置信息]
     */
    'redisCacheConfig' => array(
        'host' => '127.0.0.1',
        'port' => 6379,
    ),

    /**
     * memcache缓存配置 [该配置信息可省略,省略后默认使用memcacheConfig的配置信息]
     */
    'memcacheCacheConfig' => array(
        'host' => '127.0.0.1',
        'port' => 11211,
    ),

    /**
     * cookie设置
     */
    'cookieConfig' => array(
        'path' => '',
        'domain' => '',
        'expire' => 3600,    
    ),

    /**
     * 自动加载
     * 请填写全路径
     */
    'autoLoad' => array(

    ),
);
