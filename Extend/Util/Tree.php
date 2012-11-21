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
 * Tree生成工具类
 *
 * @category CamelPHP
 * @package  Extend
 * @subpackage Util
 * @author   dreamans<dreamans@163.com>
 * @since    20120929
 */
class CP_Tree {
 
    /**
     * id pid name
     *
     * @access protected
     * @var array
     */
    protected $_arr = array();

    /**
     * 排好序的数组 
     *
     * @access protected
     * @var array
     */
    protected $_res = array();
    
    /**
     * key
     *
     * @access protected
     * @var key
     */
    protected $_id;

    /**
     * pid
     *
     * @access protected
     * @var pid
     */
    protected $_pid;

    /**
     * 传入数组 
     *
     * @access public
     * @param $arr
     * @param $id
     * @param $pid
     * @return void
     */
    public function setArray($arr, $id = 'id', $pid = 'pid') {
        $this->_arr = is_array($arr) ? $arr : array();
        $this->_res = array();
        $this->_id = $id;
        $this->_pid = $pid;
    }

    /**
     * 获取排序后的数组
     *
     * @access public
     * @param $id
     * @param $level
     * @return void
     */
    public function getTree($id = 0, $level = 1) {
        $number = $level;
        $level++;
        $arr = $this->_getChild($id);
        if(is_array($arr)) {
            $count = count($arr);
            foreach($arr as $k => $v) {
                $v['level'] = $number;
                $this->_res[] = $v;
                $this->getTree($v[$this->_id], $level);
            }
        }
    }
    
    /**
     * 判断一个ID是否在另一个ID的child里
     *
     * @access public
     * @param $id
     * @return void
     */
    public function getChildId($id) {
        $res = $this->_getChild($id);
        if(is_array($res)) {
            foreach($res as $v) {
                $this->_res[] = $v[$this->_id];
                $this->getChildId($v[$this->_id]);
            }
        }
    }

    /**
     * 返回结果
     *
     * @access public
     * @return array
     */
    public function getRes() {
        return $this->_res;
    }

    /**
     * 返回下级数组集
     *
     * @access protected
     * @param $id
     * @return boolean|array
     */
    protected function _getChild($id) {
        $arrs = array();
        foreach($this->_arr as $k => $v) {
            if($v[$this->_pid] == $id) {
                $arrs[] = $v;
            }
        }
        return empty($arrs) ? false : $arrs;
    }
}
