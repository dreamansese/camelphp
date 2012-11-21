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
 * 定义短函数,方便应用开发者调用
 *
 * @category CamelPHP
 * @package  Core
 * @author   dreamans<dreamans@163.com>
 * @since    20121017
 */

/**
 * 配置信息快速返回
 *
 * @param $key
 * @return mixed
 */
function C($key = '') {
    return !empty($key) ? Super('Conf')->{$key} : Super('Conf');
}

/**
 * 执行Super('Func')->url()
 *
 * @param $url
 * @return url
 */
function Url($url, $domain = 'false') {
    return Super('Func')->url($url, $domain);
}

/**
 * Post
 *
 * @param $key
 * @return mixed
 */
function Post($key = '', $type = '') {
    return !empty($key) ? Super('Func')->filterString(Super('Post')->{$key}, $type): Super('Post') ;
}

/**
 * Get
 *
 * @param $key
 * @return mixed
 */
function Get($key = '', $type = '') {
    return !empty($key) ? Super('Func')->filterString(Super('Get')->{$key}, $type) : Super('Get') ;
}

/**
 * $_REQUEST
 *
 * @param $key
 * @return mixed
 */
function Req($key, $type = '') {
    if(!Post($key)) {
        return Get($key, $type);
    } else {
        return Post($key, $type);
    }
}

/**
 * 调用模型
 * 调用当前模块模型 M('@.modelName');
 * 调用项目总模型 M('modelName');
 * 调用其他模块模型 M('moduleName.modelName');
 *
 * @param $name
 * @param $module
 * @return model
 */
function M($name = '') {
    return !empty($name) ? Super('Model')->load($name) : Super('Model');
}

/**
 * 文件读写操作
 *
 * @param $path
 * @param $data
 * @param $mode
 * @return mixed
 */
function F($path, $data = NULL, $mode = '') {
    if($data == NULL) {
        return Super('Func')->readFile($path, $mode = 'r');
    } else {
        return Super('Func')->writeFile($path, $data, $mode = 'wb');
    }
}

/**
 * 全局变量 $GLOBALS
 *
 * @param $key
 * @param $value
 * @return mixed
 */
function G($key = NULL, $value = NULL) {
    if($key == NULL && $value == NULL) {
        return Super('Globals');
    } elseif($value == NULL) {
        return Super('Globals')->{$key};
    } else {
        return Super('Globals')->{$key} = $value;
    }
}

/**
 * Session存取
 *
 * @param $key
 * @param $value
 */
function Session($key = NULL, $value = NULL) {
    if($key == NULL && $value == NULL) {
        return Super('Session');
    } elseif($value == NULL) {
        return Super('Session')->{$key};
    } else {
        return Super('Session')->{$key} = $value;
    }
}

/**
 * Cookie存取
 *
 * @param $key
 * @param $value
 * @param $timeout
 * @return mixed
 */
function Cookie($key = NULL, $value = NULL, $timeout = NULL) {
    if($key == NULL && $value == NULL) {
        return Super('Cookie');
    } elseif($value == NULL) {
        return Super('Cookie')->{$key};
    } elseif($timeout == NULL) {
        return Super('Cookie')->{$key} = $value;
    } else {
        return Super('Cookie')->set($key, $value, $timeout);
    }
}

/**
 * Cache存取
 *
 * @param $key
 * @param $value
 */
function Cache($type, $key, $value = NULL, $life = 0) {
    return Super('Cache')->{$type}($key, $value, $life);
}

/**
 * 加载核心类库并实例化
 *
 * @param $class
 * @param $ext
 * @return object
 */
function Objects($class, $newObj = false) {
    return Super('Func')->objects($class, $newObj);
}

/**
 * 加载应用类库
 *
 * @param $class
 * @return object
 */
function Load($class, $ext = 'class.php') {
    return Super('Func')->loadlib($class, $ext);
}

/**
 * 引入应用类库不实例化
 *
 * @param $class
 * @return boolean
 */
function Import($class, $ext = 'class.php') {
    return Super('Func')->lib($class, $ext);
}

/**
 * 验证函数
 *
 * @param $rule
 * @param $data
 * @return mixed
 */
function V($rule, $data) {
    return Super('Func')->valud($rule, $data);
}

/**
 * 读取模板常量
 *
 * @param $key
 * @return mixed
 */
function T($key) {
    return Super('Func')->tplConstant($key);
}

/**
 * 过滤字符串
 *
 * @param $string 
 * @param $type
 *      hsc - 预定义的字符转换为 HTML 实体
 *      hscd - 预定义的 HTML 实体转换为字符
 *      hed - HTML 实体转换为字符
 *      he - 字符转换为 HTML 实体
 *      st - 剥去 HTML、XML 以及 PHP 的标签
 *      nb - 在字符串中的每个新行 (\n) 之前插入 HTML 换行符 (<br />)
 *      int - 转换成数字
 * @return string
 */
function Filter($string, $type = 'hsc') {
    return Super('Func')->filterString($string, $type);
}


/**
 * 过滤字符串
 *
 * @param $path
 * @param $index
 * @return boolean
 */
function Dir($path, $index = true) {
    return Super('Func')->makeDir($path, $index);
}
