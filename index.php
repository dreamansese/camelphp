<?php

/**
 * example 入口文件
 */

define('APP_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR . 'example');

require dirname(__FILE__).'/CamelPHP/Camelphp.php';

/**
 * 开启应用
 *
 * Start函数可传入参数进行启动
 * Start($config = array());
 * 参数为空则默认调用APP_PATH中Config目录下的Config.php配置文件
 */
Start();
