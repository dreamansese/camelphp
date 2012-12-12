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
 * Mysql数据库缓存类
 *
 * @category CamelPHP
 * @package  Extend
 * @subpackage Cache
 * @author   dreamans<dreamans@163.com>
 * @since    20121009
 */
class CP_MysqlCache{

    /**
     * 参数
     *
     * @access protected
     * @var arrau
     */
    protected $_config = array();

    /**
     * 数据表KEY字段
     *
     * @access protected
     * @var string
     */
    protected $_key = '';
    
    /**
     * 数据表VALUE字段
     *
     * @access protected
     * @var string
     */
    protected $_value = '';

    /**
     * LIFE缓存周期字段
     *
     * @access protected
     * @var string
     */
    protected $_life = '';

    /**
     * 数据库连接ID
     *
     * @access protected
     * @var object
     */
    protected $_dblink = NULL;

    /**
     * 传入参数
     *
     * @access public
     * @param $cfg
     * @return void
     */
    public function config($cfg) {
        $this->_config = $cfg;
    }

    /**
     * 写入
     *
     * @access public
     * @param $key
     * @param $value
     * @param $life
     * @return int
     */
    public function setCache($key, $value, $life = 0) {
        $this->_conndb();
        $this->_setParam($key, $value, $life);
        $data = array(
            'key' => $this->_key,
            'value' => $this->_value,
            'life' => $this->_life + time(),
        );
        return $this->_dblink->save(array('data' => $data, 'replace' => true));
    }

    /**
     * 读出
     *
     * @access public
     * @param $key
     * @return mixed
     */
    public function getCache($key) {
        $this->_conndb();
        $this->_setParam($key);
        $param['where'] = '`key` = "'.$this->_key.'" AND `life` >= '.time();
        $res = $this->_dblink->find($param);
        if(isset($res['value'])) {
            return unserialize($res['value']);
        }
        //缓存过期，删除缓存 返回false
        $this->delCache($key);
        return false;
    }

    /**
     * 删除
     *
     * @access public
     * @param $key
     * @return int
     */
    public function delCache($key) {
        $param['where'] = ' `key` = "'.$this->_key.'"';
        return $this->_dblink->delete($param);
    }

    /**
     * 连接数据库
     *
     * @access private
     * @return void
     */
    private function _conndb() {
        if(empty($this->_dblink)) {
            Super('Func')->import('Extend.Mysql.Mysql');
            $table = $this->_config['table'];
            $cfg = isset($this->_config['connect']) ? $this->_config['connect']: Super('Conf')->mysqlConfig;
            $this->_dblink = new CP_Mysql($table, $cfg);
        }
    }

    /**
     * 处理参数
     *
     * @access private
     * @param $key
     * @param $value
     * @param $life
     * @return void
     */
    private function _setParam($key, $value = '', $life = 0) {
        $this->_key = md5($key);
        $this->_value = serialize($value);
        $this->_life = $life;
    }
}
