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
 * 框架核心SESSION类
 *
 * @category CamelPHP
 * @package  Core
 * @author   dreamans<dreamans@163.com>
 * @since    20120928
 */

class CP_Session {

    /**
     * 开启Session
     *
     * @access public
     * @return void
     */
    public function start() {
        $this->__setHandler();
        session_start();
    }

    /**
     * 初始化Session
     *
     * @access private
     * @return void
     */
    private function __setHandler() {
        ini_set('session.save_handler', 'files');
        $mtime = Super('Conf')->sessionGcMaxlifetime;
        $spath = Super('Conf')->sessionPath;
        $mtime && session_cache_expire(Super('Conf')->sessionGcMaxlifetime);
        $spath && session_save_path(Super('Conf')->sessionPath);
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
        $_SESSION[$key] = $value;
    }

    /**
     * __get()
     *
     * @access public
     * @param $key
     * @return mixed
     */
    public function __get($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key]: NULL ;
    }

    /**
     * 销毁Session
     *
     * @access public
     * @return boolean
     */
    public function destroy() {
        session_destroy();
    }

}
