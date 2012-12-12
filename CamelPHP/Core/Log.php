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
 * 日志类
 *
 * @category CamelPHP
 * @package  Core
 * @author   dreamans<dreamans@163.com>
 * @since    20120928
 */
class CP_Log {

    /**
     * 日志调度方法,选择记录日志类型
     *
     * @access public
     * @param $message
     * @param $mode
     * @return void
     */
    public function record($message = '', $mode = 'access') {

        $config = Super('Conf')->logType;
        $logpath = Super('Conf')->logPath;
        $logmaxsize = Super('Conf')->logMaxSize;
        if(!in_array($mode, explode(',', $config))) {
            return false;
        }
        $modemethod = '_' . $mode;
        $path = $logpath . $mode . '.log';
        //按尺寸存放,首先检验,备份达到尺寸日志文件
        if(is_file($path) && filesize($path) > $logmaxsize * 1024) {
            $bakPath = APP_LOG_PATH . $mode . C_DS;
            Super('Func')->makeDir($bakPath);
            $bakFle = $bakPath . time() . '-' . basename($path);
            rename($path, $bakFle);
        }
        Super('Func')->makeDir(dirname($path));
        self::$modemethod($message, $path);
    }

    /**
     * 记录访问日志
     *
     * @access protected
     * @param $message
     * @param $file_path
     * @return void
     */
    protected static function _access($message = '', $file_path) {
        $data['time'] = date('Y/m/d H:i:s', time()) . ' - -';
        $data['ip'] = Super('Func')->ip();
        $data['method'] = $_SERVER['REQUEST_METHOD'];
        $data['uri'] = $_SERVER['REQUEST_URI'];
        $data['refer'] = 'Refer - '. (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');

        $datas = implode(' ', $data) . "\r\n";
        Super('Func')->writeFile($file_path, $datas, 'a');
    }

    /**
     * 记录操作日志
     *
     * @access protected
     * @param $message
     * @param $file_path
     * @return void
     */
    protected static function _post($message = '', $file_path) {
        $data['time'] = date('Y/m/d H:i:s', time()) . ' - -';
        $data['ip'] = Super('Func')->ip();
        $data['route_m'] = 'Path: ' . C_PATH;
        $data['route_c'] = 'Controller: ' . C_CONTROLLER;
        $data['route_a'] = 'Method: ' . C_METHOD;
        $data['message'] = $message;
        $data['method'] = $_SERVER['REQUEST_METHOD'];
        $data['uri'] = $_SERVER['REQUEST_URI'];
        $data['post'] = "POSTDATA:" . (!empty($_POST) ? json_encode($_POST): 'empty');
        $datas = implode(' ', $data)."\r\n" ;
        Super('Func')->writeFile($file_path, $datas, 'a');
    }

    /**
     * 记录错误日志
     *
     * @access protected
     * @param $message
     * @param $file_path
     * @return void
     */
    protected static function _error($message, $file_path) {
        $data['time'] = date('Y/m/d H:i:s', time()) . ' - -';
        $data['ip'] = Super('Func')->ip();
        $data['message'] = $message;
        $datas = implode(' ', $data)."\r\n" ;
        Super('Func')->writeFile($file_path, $datas, 'a');
    }

    /**
     * Mysql执行日志
     *
     * @access protected
     * @param $message
     * @param $file_path
     * @return void
     */
    protected static function _mysql($message, $file_path) {
        $data['time'] = date('Y/m/d H:i:s', time()) . ' - -';
        $data['ip'] = Super('Func')->ip();
        $data['SQL'] = $message;
        $datas = implode(' ', $data)."\r\n" ;
        Super('Func')->writeFile($file_path, $datas, 'a');
    }
}
