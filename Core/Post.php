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
 * POST类
 *
 * @category CamelPHP
 * @package  Core
 * @author   dreamans<dreamans@163.com>
 * @since    20120928
 */
class CP_Post {

    /**
     * __set()
     *
     * @access public
     * @param $key
     * @param $value
     * @return void
     */
    public function __set($key, $value) {
        $_POST[$key] = $value;
    }

    /**
     * __get()
     *
     * @access public
     * @param $key
     * @return string|array
     */
    public function __get($key) {
        return isset($_POST[$key]) ? $_POST[$key]: '' ;
    }
}
