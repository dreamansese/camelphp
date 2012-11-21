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
 * 处理HTTP请求类
 *
 * @category CamelPHP
 * @package  Extend
 * @subpackage Util
 * @author   dreamans<dreamans@163.com>
 * @since    20120929
 */
class CP_Request {

    /**
     * 客户端请求IP
     *
     * @access public
     * @return ip
     */
    public function ip() {
        if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return preg_match('/[\d\.]{7,15}/', $ip, $matches) ? $matches[0] : '';
    }

    /**
     * 解析url
     *
     * @access public
     * @return url
     */
    public function parseUrl() {
        $script_name = $_SERVER["SCRIPT_NAME"];//获取当前文件的路径
        $url = $_SERVER["REQUEST_URI"];//获取完整的路径，包含"?"之后的字符串
        //去除url包含的当前文件的路径信息
        if($url && @strpos($url,$script_name,0) !== false) {
            $url = substr($url,strlen($script_name));
        } else {
            $script_name = str_replace(basename($_SERVER["SCRIPT_NAME"]), '', $_SERVER["SCRIPT_NAME"]);
            if($url && @strpos($url,$script_name,0) !== false) {
                $url=substr($url,strlen($script_name));
            }
        }
        return $url;
    }

    /**
     * 客户端 User Agent
     *
     * @access public
     * @return string
     */
    public function userAgent() {
        return (!isset($_SERVER['HTTP_USER_AGENT'])) ? FALSE : $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * 获取当前请求完整url
     *
     * @access public
     * @return url
     */
    public function getThisUrl() {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? safe_replace($_SERVER['PHP_SELF']) : safe_replace($_SERVER['SCRIPT_NAME']);
        $path_info = isset($_SERVER['PATH_INFO']) ? safe_replace($_SERVER['PATH_INFO']) : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? safe_replace($_SERVER['REQUEST_URI']) : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.safe_replace($_SERVER['QUERY_STRING']) : $path_info);
        return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
    }
}
