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
 * 核心模型类
 *
 * @category CamelPHP
 * @package  Core
 * @author   dreamans<dreamans@163.com>
 * @since    20120928
 */
class CP_Model {

    /**
     * Mysql数据库类实例化
     *
     * @access public
     * @param $table
     * @param $cfg
     * @return object
     */
    public function mysql($table = '', $cfg = '') {
        if(!$cfg) {
            $cfg = Super('Conf')->mysqlConfig;
        }
        Super('Func')->import('Extend.Mysql.Mysql');
        if(!class_exists('CP_Mysql')) {
            trigger_error('Db error: Mysql class not exists');
        }
        return new CP_Mysql($table, $cfg);
    }

    /**
     * Redis实例化
     *
     * @access public
     * @param $cfg
     * @return object
     */
    public function redis($cfg = '') {
        static $redis;
        if(empty($redis)) {
            if(!$cfg) {
                $cfg = Super('Conf')->redisConfig;
            }
            if(!class_exists('Redis')) {
                trigger_error('PHP Extend error: Redis extend class dos not exists');
            }
            $redis = new Redis();
            $redis->connect($cfg['host'], $cfg['port']);
        }
        return $redis;
    }

    /**
     * levelDB实例化
     * 
     * @access public
     * @param $cfg
     * @return object
     */
    public function leveldb($cfg = '') {
        static $leveldb = array();
        if(!$cfg) {
            $cfg = Super('Conf')->leveldbConfig;
        }
        $key = md5(json_encode($cfg));
        if(!isset($leveldb[$key])) {
            if(!class_exists('LevelDB')) {
                trigger_error('PHP Extend error: LevelDB extend class dos not exists');
            }
            $opt = isset($cfg['opt']) ? $cfg['opt'] : array();
            $leveldb[$key] = new LevelDB($cfg['path'], $opt);
        }
        return $leveldb[$key];
    }

    /**
     * Memcache实例化
     *
     * @access public
     * @param $cfg
     * @return object
     */
    public function memcache($cfg = '') {
        static $memcache;
        if(empty($memcache)) {
            if(!class_exists('Memcache')) {
                trigger_error('PHP Extend error: Memcache extend class dos not exists');
            }
            if(!$cfg) {
                $cfg = Super('Conf')->memcacheConfig;
            }
            $memcache = new Memcache();
            $memcache->connect($cfg['host'], $cfg['port']);
        }
        return $memcache;
    }

    /**
     * 实例化项目模型文件
     * M('@.admin') 代表调用当前模块中Model目录中的./Modules/项目名/Model/adminModel.php模型
     *
     * @access public
     * @param $file
     * @param $ext
     * @return object
     */
    public function load($model) {
        static $_model = array();
        if(!isset($_model[$model])) {
            $modelArr = explode('.', $model);
            if(count($modelArr) > 1) {
                //当前项目Model
                if($modelArr[0] == '@') {
                    $modelPath = APP_MODULES_PATH . C_MODULE . C_DS . 'Model'.C_DS . $modelArr[1] . 'Model.php';
                    $modelName = C_MODULE.'_'.$modelArr[1].'Model';
                } else {
                    $modelPath = APP_MODULES_PATH . $modelArr[0] . C_DS . 'Model' . C_DS . $modelArr[1] . 'Model.php';
                    $modelName = $modelArr[0].'_'.$modelArr[1].'Model';
                }
                //引入父模型,若存在
                $parentPath = APP_PATH . C_DS . 'Model' . C_DS . $modelArr[1] . 'Model.php';
                Super('Func')->requireFile($parentPath);
            } else {
                $modelPath = APP_PATH . C_DS . 'Model' . C_DS . $model . 'Model.php';
                $modelName = $model.'Model';
            }
            if(!Super('Func')->requireFile($modelPath)) {
                trigger_error(" Model file {$modelPath} dos not exists.");
            }
            if(!class_exists($modelName)) {
                trigger_error(" Model class {$modelName} dos not exists.");
            }
            $_model[$model] = new $modelName;
        }
        return $_model[$model];
    }
}
