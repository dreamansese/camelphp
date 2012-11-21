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
 * 字符串操作扩展工具类
 *
 * @category CamelPHP
 * @package  Extend
 * @subpackage Util
 * @author   dreamans<dreamans@163.com>
 * @since    20120928
 */
class CP_String {

    /**
     * 过滤引号
     *
     * @access public
     * @param $string
     * @return string
     */
    public function stripQuotes($string) {
        return str_replace(array('"', "'"), '', $string);
    }

    /**
     * 将引号替换成HTML实体
     * 
     * @access public
     * @param $string
     * @return string
     */
    public function quotesToHtmlEntity($string) {
		return str_replace(array("\'","\"","'",'"'), array("&#39;","&quot;","&#39;","&quot;"), $string);
    }

    /**
     * 将尖括号替换成HTML实体
     *
     * @access public
     * @param $string
     * @return string
     */
    public function bracketToHtmlEntity($string) {
		return str_replace(array('<','>'), array("&lt;","&gt;"), $string);
    }

    /**
     * 生成指定长度随机字符串
     *
     * @access public
     * @param $len
     * @param $type all,number,alpha
     * @return string
     */
    public function randomString($len = 8, $type = 'all') {
        switch($type) {
            case 'all' :
                $base = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                break;
            case 'number' :
                $base = '0123456789';
                break;
            case 'alpha' :
                $base = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            default :
                $base = md5(rand());
                break;
        }
        $string = '';
        for($i = 0, $i < $len, $i++) {
            $string .= substr($base, rand(0, strlen($base)-1), 1);
        }
        return $string;
    }

    /**
     * 过滤html,xml,php等标签
     *
     * @access public
     * @param $string
     * @param $allow
     * @return string
     */
    public function filterTags($string, $allow = '') {
        return strip_tags($string, $allow);
    }

    /**
     * 截取指定长度字符串
     *
     * @access public
     * @param $str
     * @param $length
     * @param $start
     * @param $charset
     * @param $suffix
     * @return string
     */
    public function cutStr($str, $length, $start=0, $charset="", $suffix=true) {
        if(empty($charset)) $charset = Super('Conf')->charset;
        $str = $this->filterTags($str);
        $str = trim($str);
        switch($charset) {
            case 'utf-8':$char_len=3;break;
            case 'UTF-8':$char_len=3;break;
            case 'UTF8':$char_len=3;break;
            default:$char_len=2;
        }
        //小于指定长度，直接返回
        if(strlen($str)<=($length*$char_len)) {   
            return $str;
        }
        if(function_exists("mb_substr")) {   
            $slice= mb_substr($str, $start, $length, $charset);
        } else if(function_exists('iconv_substr')) {
            $slice=iconv_substr($str,$start,$length,$charset);
        } else { 
            $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("",array_slice($match[0], $start, $length));
        }
        if($suffix) return $slice."…";
        return $slice;
    }

    /**
     * 字符过滤,只保留字母
     *
     * @access public
     * @param $string
     * @return string
     */
    public function onlyLetter($string) {
        return preg_replace('/[^a-zA-Z]*/is', '', $string);
    }

    /**
     * 检测字符串是否是utf8编码
     *
     * @access public
     * @param $string
     * @return boolean
     */
    public function isUtf8($string) {
        return preg_match('%^(?:[\x09\x0A\x0D\x20-\x7E] 
            | [\xC2-\xDF][\x80-\xBF] 
            | \xE0[\xA0-\xBF][\x80-\xBF] 
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} 
            | \xED[\x80-\x9F][\x80-\xBF]
            | \xF0[\x90-\xBF][\x80-\xBF]{2}
            | [\xF1-\xF3][\x80-\xBF]{3} 
            | \xF4[\x80-\x8F][\x80-\xBF]{2}
        )*$%xs', $string);
    }

    /**
     * 字符转换
     *
     * @access public
     * @param $fContents
     * @param $from
     * @param $to
     * @return string
     */
    public function changeCharset($fContents, $from='gbk', $to='utf-8') {
        $from  = strtoupper($from) == 'UTF8' ? 'utf-8':$from;
        $to = strtoupper($to) == 'UTF8' ? 'utf-8':$to;
        if( strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents)) ){
            return $fContents;
        }
        if(is_string($fContents) ) {
            if(function_exists('mb_convert_encoding')){
                return mb_convert_encoding ($fContents, $to, $from);
            }elseif(function_exists('iconv')){
                return iconv($from,$to,$fContents);
            }else{
                return $fContents;
            }
        } else {
            return $fContents;
        }
    }

    /**
     * 生成唯一的值
     *
     * @access public
     * @return string
     */
    public function sUniqid() {
        return md5(uniqid(rand(), true));
    }

    /**
     * 转义预定义字符
     *
     * @access public
     * @param $string
     * @return string
     */
    public function sAddslashes($string) {
        return addslashes($string);
    }

    /**
     * 反转义预定义字符
     * 
     * @access public
     * @param $string
     * @return string
     */
    public function sStripslashes($string) {
        return stripslashes($string);
    }
}
