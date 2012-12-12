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
 * MySQL驱动类
 *
 * @category CamelPHP
 * @package  Extend
 * @subpackage Mysql
 * @author   dreamans<dreamans@163.com>
 * @since    20120928
 */
class CP_DbMysql{

    /**
     * MySQL连接号
     *
     * @access protected
     * @var resource
     */
    protected $_linkId = NULL;

    /**
     * 配置信息
     *
     * @access protected
     * @var array
     */
    protected $_config = array();

    /**
     * 查询资源句柄
     *
     * @access protected
     * @var query_link
     */
    protected $_queryId = NULL;
    
    /**
     * 影响行号
     *
     * @access protected
     * @var int
     */
    protected $_numRows = 0;

    /**
     * 构造函数
     *
     * @access public
     * @param $table
     * @param $cfg
     * @return void
     */
    public function __construct($table, $cfg) {
        $this->_config = $cfg;
    }

    /**
     * 连接数据库，返回连接资源
     *
     * @access public
     * @return resource
     */
    public function connect() {
        if(!$this->_linkId) {
            $this->_linkId = mysql_connect($this->_config['host'].':'.$this->_config['port'], $this->_config['user'], $this->_config['password'], true);
            if(!$this->_linkId) {
			    trigger_error('Could not connect: ' . mysql_error());
            }
            if(!empty($this->_config['dbname'])) {
                @mysql_select_db($this->_config['dbname'], $this->_linkId);
            }
            $version = mysql_get_server_info($this->_linkId);
            if ($version >= '4.1' && isset($this->_config['charset'])) {
                mysql_query("SET NAMES '". $this->_config['charset'] ."'", $this->_linkId);
            }
            if($version >'5.0.1'){
                mysql_query("SET sql_mode=''",$this->_linkId);
            }
        }
        return $this->_linkId;
    }

    /**
     * 执行SQL查询语句
     *
     * @access public
     * @param $sql
     * @return array
     */
    public function query($sql) {
        $this->connect();
        if(!$this->_linkId ) return false;
        if($this->_queryId) {
            $this->freeResult();
        }
        $this->_queryId = mysql_query($sql, $this->_linkId);
        if(!$this->_queryId ) {
			trigger_error('MySQL query error: ' . mysql_error() . ' ['.$sql.']');
        } else {
            $this->_numRows = mysql_num_rows($this->_queryId);
            $result = array();
            if($this->_numRows >0) {
                while($row = mysql_fetch_assoc($this->_queryId)){
                    $result[] = $row;
                }
                mysql_data_seek($this->_queryId,0);
            }
            return $result;
        }
    }

    /**
     * 执行SQL写入语句
     *
     * @access public
     * @param $sql
     * @return int
     */
    public function execute($sql) {
        $this->connect();
        if(!$this->_linkId ) return false;
        if($this->_queryId) {
            $this->freeResult();
        }
        $result = mysql_query($sql, $this->_linkId);
        if($result == false) {
			trigger_error('MySQL execute error: ' . mysql_error() . ' ['.$sql.']');
        } else {
            $this->_numRows = mysql_affected_rows($this->_linkId);
            $lastInsID = mysql_insert_id($this->_linkId);
            return $lastInsID ? $lastInsID : $this->_numRows;
        }
    }

    /**
     * 选择数据库
     *
     * @access public
     * @param $dbname
     * @return boolean
     */
    public function selectDb($dbname) {
        return mysql_select_db($dbname, $this->_linkId);
    }

    /**
     * SQL语句影响的行数
     *
     * @access public
     * @return int
     */
    public function resultNums() {
        return $this->_numRows;
    }

    /**
     * 释放资源
     *
     * @access public
     * @return void
     */
    public function freeResult() {
        mysql_free_result($this->_queryId);
        $this->_queryId = NULL;
    }

    /**
     * 关闭MySQL连接
     *
     * @access public
     * @return void
     */
    public function close() {
        if($this->_linkId){
            $this->freeResult();
            mysql_close($this->_linkId);
        }
        $this->_linkId = NULL;
    }

}
