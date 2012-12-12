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
 * PDO驱动类
 *
 * @category CamelPHP
 * @package  Extend
 * @subpackage Mysql
 * @author   dreamans<dreamans@163.com>
 * @since    20121029
 */
class CP_Pdo{

    /**
     * PDO对象
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
            $dsn = 'mysql:dbname='.$this->_config['dbname'].';host='.$this->_config['host'];
            try{
                $this->_linkId = new PDO($dsn, $this->_config['user'], $this->_config['password'], array(PDO::ATTR_PERSISTENT => true));
                $this->_linkId->exec('SET NAMES \''.$this->_config['charset'].'\'');
            } catch (PDOException $e) {
			    trigger_error('MYSQL PDO error: ' . $e->getMessage());
            }
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
        try{
            $this->_linkId->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL); 
            $this->_queryId = $this->_linkId->query($sql);
        } catch(PDOException $e) {
			trigger_error('MySQL PDO query error: ' . $e->getMessage() . ' ['.$sql.']');
        }
        $result = array();
        if($this->_queryId) {
            $this->_queryId->setFetchMode(PDO::FETCH_ASSOC);
            $result = $this->_queryId->fetchAll();
            $this->_numRows = count($result);
        }
        return $result;
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
        try{
            $this->_numRows = $this->_linkId->exec($sql);
        } catch(PDOException $e) {
			trigger_error('MySQL PDO execute error: ' . $e->getMessage() . ' ['.$sql.']');
        }
        $lastInsID = $this->_linkId->lastInsertId();
        return $lastInsID ? $lastInsID : $this->_numRows;
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
        $this->linkId->closeCursor();
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
        }
        $this->_linkId = NULL;
    }

}
