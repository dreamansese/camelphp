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
 * memcacheCache缓存类
 *
 * @category CamelPHP
 * @package  Extend
 * @subpackage Cache
 * @author   dreamans<dreamans@163.com>
 * @since    20121009
 */
class CP_MemcacheCache{

    /*
     * memcache资源
     */
    protected $_memcache;

    /**
     * 参数 
     */
    protected $_config;
    
    /**
     * 传入参数 
     */
    public function config($cfg = '') {
        $this->_config = !empty($cfg) ? $cfg : Super('Conf')->memcacheConfig;
    }

    /**
     * 连接memcache服务器
     */
    private function _connect() {
        if(empty($this->_memcache)) {
            if(!class_exists('Memcache')) {
                trigger_error('PHP Extend error: Memcache extend class dos not exists');
            }
            $this->_memcache = new Memcache();
            $this->_memcache->addServer($this->_config['host'], $this->_config['port']);
        }
    }

    /**
     * 设置缓存
     */
    public function setCache($key, $value, $life = 0) {
        $this->_connect();
        return $this->_memcache->set($key, $value, MEMCACHE_COMPRESSED, $life);
    }

    /**
     * 获取缓存内容
     */
    public function getCache($key) {
        $this->_connect();
        return $this->_memcache->get($key);
    }

    /**
     * 析构 关闭连接 对持久连接无效
     */
    public function __destruct() {
        if(!empty($this->_memcache)) {
            $this->_memcache->close();
        }
    }
}
