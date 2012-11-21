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
 * Redis缓存类
 *
 * @category CamelPHP
 * @package  Extend
 * @subpackage Cache
 * @author   dreamans<dreamans@163.com>
 * @since    20121009
 */
class CP_RedisCache{

    /**
     * redis连接参数
     *
     * @access protected
     * @var arrau
     */
    protected $_config = array();

    /**
     * Redis 对象
     *
     * @access protected
     * @var object
     */
    protected $_redis = NULL;

    /**
     * 传入参数
     *
     * @access public
     * @param $cfg
     * @return void
     */
    public function config($cfg = '') {
        $this->_config = !empty($cfg) ? $cfg : Super('Conf')->redisConfig;
    }

    /**
     * 数据写入redis数据库
     *
     * @access public
     * @param $key
     * @param $value
     * @param $life
     * @return int
     */
    public function setCache($key, $value, $life = 0) {
        $this->_connect();
        $value = serialize($value);
        return $this->_redis->setex($key, $life, $value);
    }

    /**
     * 读出缓存数据
     *
     * @access public
     * @param $key
     * @return mixed
     */
    public function getCache($key) {
        $this->_connect();
        $value = $this->_redis->get($key);
        return $value === false ? false : unserialize($value) ;
    }

    /**
     * 连接Redis数据库
     *
     * @access private
     * @return void
     */
    private function _connect() {
        if(empty($this->_redis)) {
            if(!class_exists('Redis')) {
                trigger_error('PHP Extend error: Redis extend class dos not exists');
            }
            $this->_redis = new Redis();
            $this->_redis->connect($this->_config['host'], $this->_config['port']);
        }
    }
}
