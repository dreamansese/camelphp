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
 * 框架核心控制器基类
 *
 * @category CamelPHP
 * @package  Core
 * @author   dreamans<dreamans@163.com>
 * @since    20120928
 */
class CP_Controller extends CP_Base{

    /**
     * 视图实例
     *
     * @var object
     */
    protected $_view;

    /**
     * 构造方法
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->_view = $this->Func->objects('Core.View');
    }

    /**
     * 模板调用方法
     * 
     * @access public
     * @param $tpl
     * @return void
     */
    public function display($tpl = '', $content = '', $type = '', $out = false) {
        return $this->_view->display($tpl, $content, $type, $out);
    }

    /**
     * 输出文件
     *
     * @access public
     * @param $filename
     * @param $showname
     * @param $expire
     * @return file
     */
    public function displayFile($filename, $showname = '', $expire = 1800) {
        return $this->_view->displayFile($filename, $showname, $expire);
    }

    /**
     * 变量传递方法
     *
     * @access public
     * @param $key
     * @param $val
     * @return void
     */
    public function assign($key, $val = '') {
        $this->_view->assign($key, $val);
    }

    /**
     * 判断是否是Ajax请求
     *
     * @access public
     * @return boolean
     */
	public function isAjax() {
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return true;
        }
		if(!empty($_REQUEST[AJAX_SUBMIT_KEY])) {
			return true;
		}
        return false;
	}

    /**
     * 判断是否是POST请求
     *
     * @access public
     * @return boolean
     */
    public function isPost() {
		if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
            return true;
		} else {
			return false;
		}
    }
}
