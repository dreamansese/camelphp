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
 * 框架核心Cookie类
 *
 * @category CamelPHP
 * @package  Core
 * @author   dreamans<dreamans@163.com>
 * @since    20120928
 */

class CP_Cookie {

    /**
     * 设置cookie的值
     *
     * @access public
     * @param $key
     * @param $value
     * @param $expire
     * @param $path
     * @param $domain
     * @return boolean
     */
    public function set($key, $value, $expire = NULL, $path = '', $domain = '') {
        $config = Super('Conf')->cookieConfig;
        $expire = ($expire === NULL) ? $config['expire']: $expire;
        $path = !empty($path) ? $path : $config['path'];
        $domain = !empty($domain) ? $domain : $config['domain'];
        return setcookie($key, $value, time() + $expire, $path, $domain);
    }

    /**
     * __set()
     *
     * @access public
     * @param $key
     * @param $value
     * @return void
     */
    public function __set($key, $value) {
        $config = Super('Conf')->cookieConfig;
        setcookie($key, $value, time() + $config['expire'], $config['path'], $config['domain']);
    }

    /**
     * __get()
     *
     * @access public
     * @param $key
     * @return mixed
     */
    public function __get($key) {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key]: NULL ;
    }

}
