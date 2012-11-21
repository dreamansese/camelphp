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
 * 核心缓存类
 *
 * @category CamelPHP
 * @package  Core
 * @author   dreamans<dreamans@163.com>
 * @since    20121009
 */
class CP_Cache {

    /**
     * File类型缓存方法
     *
     * @access public
     * @param $table
     * @param $cfg
     * @return object
     */
    public function file($key, $value = NULL, $life = 0) {
        return $this->_op('fileCache', $key, $value, $life);
    }

    /**
     * mysql类型缓存方法
     *
     * @access public
     * @param $key
     * @param $value
     * @param $life
     * @return mixed
     */
    public function mysql($key, $value = NULL, $life = 0) {
        return $this->_op('mysqlCache', $key, $value, $life);
    }

    /**
     * redis类型缓存方法
     *
     * @access public
     * @param $key
     * @param $value
     * @param $life
     * @return mixed
     */
    public function redis($key, $value = NULL, $life = 0) {
        return $this->_op('redisCache', $key, $value, $life);
    }

    /**
     * memcache类型缓存方法
     *
     * @access public
     * @param $key
     * @param $value
     * @param $life
     * @return mixed
     */
    public function memcache($key, $value = NULL, $life = 0) {
        return $this->_op('memcacheCache', $key, $value, $life);
    }

    /**
     * 进行读写缓存操作
     *
     * @access private
     * @param $type
     * @param $key
     * @param $value
     * @param $life
     * @return mixed
     */
    private function _op($type, $key, $value, $life) {
        $cache = $this->_init($type);
        if( $value !== NULL) {
            $rst = $cache->setCache($key, $value, $life);
        } else {
            $rst = $cache->getCache($key);
        }
        return $rst;
    }

    /**
     * 实例化缓存扩展类
     *
     * @access private
     * @param $class
     * @return object
     */
    private function _init($class) {
        static $_cache = array();
        if(!isset($_cache[$class])) {
            $cacheConfig = $class.'Config';
            $cfg = Super('Conf')->{$cacheConfig};
            $cacheClass = ucfirst($class);
            Super('Func')->import('Extend.Cache.'.$cacheClass);
            $className = 'CP_'.$cacheClass;
            if(!class_exists($className)) {
                trigger_error('Cache error: '.$className.' class not exists');
            }
            $_cache[$class] = new $className;
            $_cache[$class]->config($cfg);
        }
        return $_cache[$class];
    }

}
