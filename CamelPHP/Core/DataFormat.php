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
 * 数据调用基类，返回相应的数据格式
 *
 * @category CamelPHP
 * @package  Core
 * @author   dreamans<dreamans@163.com>
 * @since    20121101
 */
class CP_DataFormat {

    /**
     * 需要返回的字符串或数组
     */
    var $data = NULL;

    /**
     * 实例化类 
     */
    public static function getDataFormat() {
        static $obj = NULL;
        if(empty($obj)) {
            $obj = new CP_DataFormat();
        }
        return $obj;
    }

    /**
     * 输出经过格式化的数据 
     * 
     * @param mix $value 需要格式化的数据
     * @param string $type 格式化方式 json xml
     * @return string 
     */
    public function display($value, $type = 'json') {
        $this->data = $value;
        if(!method_exists($this, $type)) {
            trigger_error(" Display Format type '{$type}' dos not exists.");
        }
        return $this->$type();
    }

    /**
     * 转换成json格式
     * @return json
     */
    public function json() {
        return json_encode($this->data);
    }
    
    /**
     * 转换成xml格式 
     */
    public function xml( $charset = 'utf-8', $root = 'root') {
        $xml = "<?xml version=\"1.0\" encoding=\"" . $charset . "\"?>";
        $xml .= "<".$root.">";
        if(is_array($this->data)) {
            $xml .= $this->_xmlDataFormat($this->data);
        }else{
            $xml .= "<data><![CDATA[" . $this->data . "]]></data>";
        }
        $xml .= "</".$root.">";
        return $xml;
    }

    /**
    * Xml相关函数
    */
   private function _xmlDataFormat($arr) {
        $xml = '';
        if(is_array($arr) || is_object($arr)) {
            foreach($arr as $k => $v) {
                $k = is_numeric($k) ? 'item' : $k;
                $xml .= "<" . $k . ">";
                $xml .= $this->_xmlDataFormat($v);
                $xml .= "</" . $k . ">";
            }
        }else{
            if(strlen($arr) < 255 && !count(array_intersect(str_split($arr),array('<','>','&','\'','"')))) {
                $xml = $arr ;
            }else{
                $xml = "<![CDATA[" . $arr . "]]>";
            }
        }
        return $xml;
    }
}
