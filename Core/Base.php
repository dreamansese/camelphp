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
 * 框架基类
 *
 * 继承Super函数中所有对象
 *
 * @category CamelPHP
 * @package  Core
 * @author   dreamans<dreamans@163.com>
 * @since    20120928
 */
class CP_Base {

    /**
     * 构造方法
     *
     * @access public
     * @return void
     */
    public function __construct() {
        $s = Super();
        foreach($s as $k => $v) {
            $this->$k = $v;
        }
    }
}
