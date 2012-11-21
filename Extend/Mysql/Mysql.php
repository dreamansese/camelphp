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
 * MySQL操作类
 *
 * @category CamelPHP
 * @package  Extend
 * @subpackage Mysql
 * @author   dreamans<dreamans@163.com>
 * @since    20120928
 */
class CP_Mysql {

    /**
     * 数据库实例
     *
     * @access protected
     * @var object|resource
     */
    protected $_db;

    /**
     * 默认数据表名
     *
     * @access protected
     * @var string
     */
    protected $_table;

    /**
     * 表前缀
     *
     * @access protected
     * @var string
     */
    protected $_tablepre;

    /**
     * 构造方法
     *
     * @access public
     * @param $table
     * @param $config
     * @return void
     */
    public function __construct($table, $config) {
        $driver = isset($config['driver']) ? $config['driver']: 'DbMysql';
        Super('Func')->import('Extend.Mysql.'.$driver);
        $dclass = 'CP_'.$driver;
        if(!class_exists($dclass)) {
			trigger_error('Mysql Driver Error: '.$driver.' note exists');
        }
        $this->_tablepre = $config['tablepre'];
        $this->_table = $this->_tablepre.$table;
        $this->_db = new $dclass($table, $config);
    }

    /**
     * 执行查询
     *
     * @access public 
     * @param $sql
     * @return array|false
     */
    public function query($sql) {
        Super('Log')->record($sql, 'mysql');
        return $this->_db->query($sql);
    }

    /**
     * 执行写入
     *
     * @access public
     * @param $sql
     * @return int|false
     */
    public function execute($sql) {
        Super('Log')->record($sql, 'mysql');
        return $this->_db->execute($sql);
    }
    
    /**
     * 给表加前缀
     *
     * @access public
     * @param $table
     * @return string
     */
    public function table($table) {
        return $this->_tablepre.$table;
    }

    /**
     * 返回影响行数
     *
     * @access public
     * @return int
     */
    public function resultNums() {
        $this->_db->resultNums();
    }

    /**
     * 返回一张表数据总条数
     *  
     * @access public
     * @param $param
     * @return int
     */
    public function totNums($param = array()) {
        $param['field'] = 'COUNT(*) as num';
        $rst = $this->find($param);
        return $rst['num'];
    }


    /**
     * 查找第一条记录
     *
     * @access public
     * @param $param
     * @return array
     */
    public function find($param = array()) {
        $param['limit'] = '0,1';
        $rst = $this->select($param);
        return isset($rst[0]) ? $rst[0]: array();
    }

    /**
     * 查询返回一组记录
     *
     * @access public
     * @param $param
     * @return array
     */
    public function select($param = array()) {
        $pa = $this->_param($param, 'select');
        $sql  = 'SELECT ';
        $sql .= $pa['field'] . ' FROM ' . $pa['table'] . ' ';
        $sql .= !empty($pa['where']) ? ' WHERE ' . $pa['where'] : '';
        $sql .= !empty($pa['order']) ? $pa['order'] : '' ;
        $sql .= !empty($pa['limit']) ? $pa['limit'] : '' ;
        return $this->query($sql);
    }

    /**
     * 插入记录
     *
     * @access public
     * @param $param
     * @return int
     */
    public function save($param = array()) {
        $pa = $this->_param($param, 'save');
        $sql = $pa['replace'] ? 'REPLACE INTO ' : 'INSERT INTO ';
        $sql .= '`'.$pa['table'].'`';
        $sql .= $this->_formatSaveData($pa['data']);
        return $this->execute($sql);
    }

    /**
     * 更新记录
     *
     * @access public
     * @param $param
     * @return int
     */
    public function update($param = array()) {
        $pa = $this->_param($param, 'update');
        if(empty($pa['where'])) {
			trigger_error('Mysql Query Error: \'where\' can not empty in mysql->update ');
        }
        $sql = 'UPDATE ';
        $sql .= '`'.$pa['table'].'` ';
        $sql .= 'SET ';
        $sql .= $this->_formatUpdateData($pa['data']);
        $sql .= ' WHERE '.$pa['where'];
        return $this->execute($sql);
    }

    /**
     * 删除记录
     *
     * @access public
     * @param $param
     * @return int
     */
    public function delete($param = array()) {
        $pa = $this->_param($param, 'delete');
        if(empty($pa['where'])) {
			trigger_error('Mysql Query Error: \'where\' can not empty in mysql->delete ');
        }
        $sql = 'DELETE FROM ';
        $sql .= '`'.$pa['table'].'`';
        $sql .= ' WHERE '.$pa['where'];
        return $this->execute($sql);
    }

    /**
     * 切换数据库
     *
     * @access public
     * @param $dbname
     * @return boolean
     */
    public function changeDb($dbname) {
        $this->_db->selectDb($dbname);
    }

    /**
     * 格式化save数据内容
     *
     * @access protected
     * @param $data
     * @return sql
     */
    protected function _formatSaveData($data) {
        $sql = '';
        if(is_array($data)) {
            $fields = $vals = $dots = '';
            foreach($data as $key => $val) {
                $fields .= $dots . '`'.$key.'`';
                $vals .= $dots . '\''.$val.'\'';
                $dots = ',';
            }
            $sql = '('.$fields.') VALUES('.$vals.')';
        }
        return $sql;
    }

    /**
     * 格式化update数据内容
     *
     * @access protected
     * @param $data
     * @return sql
     */
    protected function _formatUpdateData($data) {
        $sql = $dots = '';
        if(is_array($data)) {
            foreach($data as $key => $val) {
                $sql .= $dots . '`'.$key.'` = \''.$val.'\'';
                $dots = ',';
            }
        }
        return $sql;
    }

    /**
     * 格式化WHERE语句
     *
     * @access protected
     * @param $condition
     * @return sql
     */
    protected function _where($condition) {
        if(is_array($condition)) {
            $where = $dot = '';
            foreach($condition as $key => $val) {
                $where .= $dot . $key . ' = \'' . $val . '\'';
                $dot = ' AND ';
            }
        }else{
            $where = !empty($condition) ? $condition : '1';
        }
        return $where;
    }

    /**
     * 当WHERE为空时
     *
     * @access protected
     * @return sql
     */
    protected function _whereEmpty($table) {
        //暂停使用自动根据主键生成where条件
        return false;
        $where = '';
        $pk = $this->_getPk($table);
        if($pk !== false) {
            $val = Super('Get')->$pk;
            if(!empty($val)) {
                $where = "`{$pk}` = '{$val}'";
            }
        }
        return $where;
    }

    /**
     * 获取主键
     *
     * @access protected
     * @return primary|false
     */
    protected function _getPk($table) {
        $fields = $this->_getFields($table);
        if(is_array($fields)) {
            foreach($fields as $key => $val) {
                if($val['primary']) return $key;
            }
        }
        return false;
    }

    /**
     * 获取字段信息
     *
     * @access protected
     * @return array
     */
    protected function _getFields($table) {
        $sql = 'SHOW COLUMNS FROM `'.$table.'`';
        $rst = $this->query($sql);
        $info = array();
        if($rst) {
            foreach($rst as $key => $val) {
                $info[$val['Field']] = array(
                    'name' => $val['Field'],
                    'primary' => (strtolower($val['Key']) == 'pri'),
                );
            }
        }
        return $info;
    }

    /**
     * 格式化通用参数
     *
     * @access protected
     * @param $param
     * @param $mode
     * @return array
     */
    protected function _param($param, $mode) {
        $param['table'] = isset($param['table']) ? $this->table($param['table']) : $this->_table;
        $para = '_param'.ucfirst($mode);
        $pa = $this->$para($param);
        return $pa;
    }

    /**
     * 格式化select参数
     *
     * @access protected
     * @param $param
     * @return array
     */
    protected function _paramSelect($param) {
        $param['field'] = isset($param['field']) ? $param['field'] : '*';
        $param['where'] = isset($param['where']) ? $this->_where($param['where']) : $this->_whereEmpty($param['table']);
        $param['limit'] = isset($param['limit']) ? ' LIMIT '.$param['limit']: '';
        $param['order'] = isset($param['order']) ? ' ORDER BY '.$param['order']: '';
        return $param;
    }

    /**
     * 格式化save参数
     *
     * @access protected
     * @param $param
     * @return array
     */
    protected function _paramSave($param) {
        $param['data'] = isset($param['data']) ? $param['data']: $this->_data();
        $param['replace'] = isset($param['replace']) ? true : false;
        return $param;
    }

    /**
     * 格式化update参数
     *
     * @access protected
     * @param $param
     * @return array
     */
    protected function _paramUpdate($param) {
        $param['where'] = isset($param['where']) ? $this->_where($param['where']) : $this->_whereEmpty($param['table']);
        $param['data'] = isset($param['data']) ? $param['data']: $this->_data();
        return $param;
    }

    /**
     * 格式化delete参数
     *
     * @access protected
     * @param $param
     * @return array
     */
    protected function _paramDelete($param) {
        $param['where'] = isset($param['where']) ? $this->_where($param['where']) : $this->_whereEmpty($param['table']);
        return $param;
    }

    /**
     * 根据POST获取数据
     *
     * @access protected
     * @return array
     */
    protected function _data() {
        $data = array();
        $fields = $this->_getFields();
        if(is_array($fields)) {
            foreach($fields as $key => $val) {
                if(!Super('Post')->$key) {
                    $data[$key] = Super('Post')->$key;
                }
            }
        }
        return $data;
    }
}
