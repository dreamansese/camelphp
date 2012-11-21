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
 * HTTP操作扩展工具类
 *
 * @category CamelPHP
 * @package  Extend
 * @subpackage Util
 * @author   dreamans<dreamans@163.com>
 * @since    20120929
 */
class CP_Http {

    /**
     * curl方式get访问url
     *
     * @access public
     * @param $url
     * @param $timeout
     * @param $header
     * @return mixed
     */
    public function curlGet($url, $timeout = 10, $header="") {
        $ssl = substr($url, 0, 8) == 'https://' ? true : false;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($ssl) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * curl方式post访问url
     *
     * @access public
     * @param $url
     * @param $data
     * @param $timeout
     * @param $header
     * @return mixed
     */
    public function curlPost($url, $data=array(), $timeout = 10, $header = "") {
        $ssl = substr($url, 0, 8) == 'https://' ? true : false;
        $post_string = http_build_query($data);  
        $ch = curl_init();
        if ($ssl) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查 
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        }
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * socketGet方式访问url
     *
     * @access public
     * @param $url
     * @param $timeout
     * @param $header
     * @return mixed
     */
    public function socketGet($url,$timeout=5,$header="") {
        $url2 = parse_url($url);
        $url2["path"] = isset($url2["path"])? $url2["path"]: "/" ;
        $url2["port"] = isset($url2["port"])? $url2["port"] : 80;
        $url2["query"] = isset($url2["query"])? "?".$url2["query"] : "";
        if(!$fsock = fsockopen($url2["host"], $url2['port'], $errno, $errstr, $timeout)){
            return false;
        }
        $request =  $url2["path"] .$url2["query"];
        $in  = "GET " . $request . " HTTP/1.0\r\n";
        if(false===strpos($header, "Host:"))
        {   
             $in .= "Host: " . $url2["host"] . "\r\n";
        }
        $in .= $header;
        $in .= "Connection: Close\r\n\r\n";
        if(!@fwrite($fsock, $in, strlen($in))){
            @fclose($fsock);
            return false;
        }
        return self::_getHttpContent($fsock);
    }

    /**
     * socket post 方式访问url
     *
     * @access public
     * @param $url
     * @param $data
     * @param $timeout
     * @param $header
     * @return mixed
     */
    public function socketPost($url, $data=array(), $timeout = 10, $header = "") {
        $post_string = http_build_query($data);
        $url2 = parse_url($url);
        $url2["path"] = !isset($url2["path"]) ? "/" : $url2["path"];
        $url2["port"] = !isset($url2["port"]) ? 80 : $url2["port"];
        if(!$fsock = fsockopen($url2["host"], $url2['port'], $errno, $errstr, $timeout)){
            return false;
        }
        $request =  $url2["path"].(isset($url2["query"]) ? "?" . $url2["query"] : "");
        $in  = "POST " . $request . " HTTP/1.0\r\n";
        $in .= "Host: " . $url2["host"] . "\r\n";
        $in .= $header;
        $in .= "Content-type: application/x-www-form-urlencoded\r\n";
        $in .= "Content-Length: " . strlen($post_string) . "\r\n";
        $in .= "Connection: Close\r\n\r\n";
        $in .= $post_string . "\r\n\r\n";
        if(!@fwrite($fsock, $in, strlen($in))) {
            @fclose($fsock);
            return false;
        }
        return self::_getHttpContent($fsock);
    }

    /**
     * file_get_contents函数get数据
     *
     * @access public
     * @param $url
     * @param $timeout
     * @param $header
     * @return mixed
     */
    public function fGet($url, $timeout = 10, $header = "") {
        $opts = array( 
                'http'=>array(
                            'protocol_version'=>'1.1',
                            'method'=>"GET",
                            'timeout' => $timeout ,
                            'header'=> $header
                )
        );
        $context = stream_context_create($opts);    
        return file_get_contents($url, false, $context);
    }

    /**
     * file_get_contents函数post数据
     *
     * @access public
     * @param $url
     * @param $data
     * @param $timeout
     * @param $header
     * @return mixed
     */
    public function fPost($url, $data=array(), $timeout = 10, $header="") {
        $post_string = http_build_query($data);  
        $header.="Content-length: ".strlen($post_string);
        $opts = array( 
                'http'=>array(
                            'protocol_version'=>'1.1',
                            'method'=>"POST",
                            'timeout' => $timeout,
                            'header'=> $header,  
                            'content'=> $post_string
                )
        );
        $context = stream_context_create($opts);    
        return  @file_get_contents($url,false,$context);
    }

    /**
     * 获取通过socket方式get和post页面的返回数据
     *
     * @access private static
     * @param $fsock
     * @return mixed
     */
    private static function _getHttpContent($fsock=null) {
        $out = null;
        while($buff = @fgets($fsock, 2048)) {
             $out .= $buff;
        }
        fclose($fsock);
        $pos = strpos($out, "\r\n\r\n");
        $head = substr($out, 0, $pos);
        $status = substr($head, 0, strpos($head, "\r\n")); 
        $body = substr($out, $pos + 4, strlen($out) - ($pos + 4));
        if(preg_match("/^HTTP\/\d\.\d\s([\d]+)\s.*$/", $status, $matches)){
            if(intval($matches[1]) / 100 == 2) {
                return $body;  
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
