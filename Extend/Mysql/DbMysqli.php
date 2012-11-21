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
 * MySQLi驱动类
 *
 * @category CamelPHP
 * @package  Extend
 * @subpackage Mysql
 * @author   dreamans<dreamans@163.com>
 * @since    20120928
 */
class CP_DbMysqli{

    /**
     * MySQL连接ID
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
     * 查询资源链接
     *
     * @access protected
     * @var query_id
     */
    protected $_queryId = NULL;

    /**
     * 影响行数
     *
     * @access protected
     * @var int
     */
    protected $_numRows = 0;

    /**
     * 构造方法
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
     * 连接数据库
     *
     * @access public
     * @return objects
     */
    public function connect() {
        if(!$this->_linkId) {
            $this->_linkId = mysqli_connect($this->_config['host'], $this->_config['user'], $this->_config['password'], $this->_config['dbname'], $this->_config['port']);
            if(!$this->_linkId) {
			    trigger_error('Could not connect: ' . mysqli_content_error());
            }
            mysqli_set_charset($this->_linkId, $this->_config['charset']);
        }
        return $this->_linkId;
    }

    /**
     * 执行查询语句
     *
     * @access public
     * @param $sql
     * @return array|boolean
     */
    public function query($sql) {
        $this->connect();
        if(!$this->_linkId ) return false;
        if($this->_queryId) {
            $this->freeResult();
        }
        $this->_queryId = mysqli_query($this->_linkId, $sql);
        if(!$this->_queryId ) {
			trigger_error('MySQL query error: ' . mysqli_error($this->_linkId) . ' ['.$sql.']');
        } else {
            $this->_numRows = mysqli_num_rows($this->_queryId);
            $result = array();
            if($this->_numRows >0) {
                while($row = mysqli_fetch_assoc($this->_queryId)){
                    $result[] = $row;
                }
                mysqli_data_seek($this->_queryId,0);
            }
            return $result;
        }
    }

    /**
     * 执行写入语句
     *
     * @access public
     * @param $sql
     * @return int|false
     */
    public function execute($sql) {
        $this->connect();
        if(!$this->_linkId ) return false;
        if($this->_queryId) {
            $this->freeResult();
        }
        $result = mysqli_query($this->_linkId, $sql);
        if($result == false) {
			trigger_error('MySQL execute error: ' . mysqli_error($this->_linkId) . ' ['.$sql.']');
        } else {
            $this->_numRows = mysqli_affected_rows($this->_linkId);
            $lastInsID = mysqli_insert_id($this->_linkId);
            return $lastInsID ? $lastInsID : $this->_numRows;
        }
    }

    /**
     * 返回影响行数
     *
     * @access public
     * @return int
     */
    public function resultNums() {
        return $this->_numRows;
    }

    /**
     * 释放query资源
     *
     * @access public
     * @return void
     */
    public function freeResult() {
        mysqli_free_result($this->_queryId);
        $this->_queryId = NULL;
    }

    /**
     * 关闭数据库连接
     * 
     * @access public
     * @return void
     */
    public function close() {
        if($this->_linkId){
            $this->freeResult();
            mysqli_close($this->_linkId);
        }
        $this->_linkId = NULL;
    }

}
