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
 * 核心验证类
 *
 * @category CamelPHP
 * @package  Core
 * @author   dreamans<dreamans@163.com>
 * @since    20121019
 */
class CP_Valud {

	/**
     * 根据传入的规则和数据实体,进行验证
     * $rule = array(
     *     array('field', 'rule', 'message'), //一般验证
     *     array('field1', 'eq', 'message', 'field2'), //验证field1, field2是否相等
     * );
	 * 
	 * @access public
	 * @param $rule
	 * @param $data
	 * @return array
	 */
	public function rule($rule, $data) {
		if(is_array($rule)) {
			foreach($rule as $rkey => $rval) {
				if(isset($rval[0]) && isset($rval[1]) && array_key_exists($rval[0], $data)) {
                    if(!$rst = $this->_op($rval, $data)) {
						return isset($rval[2]) ? $rval[2]: false;
                    }
				}
			}
		}
		return true;
	}

	/**
	 * 进行数据验证,并返回验证结果
	 *
	 * @access private
	 * @param $param
	 * @param $data
	 * @return boolean
	 */
    private function _op($param, $data) {
        $method = $param[1];
		switch($method) {
		    case 'email' :
				$rst = preg_match("/^[0-9a-zA-Z]+(?:[\_\-][a-z0-9\-]+)*@[0-9a-zA-Z]+(?:[-.][a-zA-Z0-9]+)*\.[a-zA-Z]+$/i", $data);
				break;
		    case 'number' :
				$rst = preg_match("/^[0-9]+$/i", $data);
				break;
            case 'must' :
                $rst = empty($data[$param['0']]) ? false : true;
                break;
            case 'eq' :
                $data1 = isset($data[$param[0]]) ? $data[$param[0]] : '';
                $data2 = isset($data[$param[3]]) ? $data[$param[3]] : '';
                $rst = ($data1 == $data2);
                break;
            default :
                if(method_exists($this, $method)) {
                    $rst = $this->$method($data);
                } else {
                    $rst = false;
                }
        }
        return $rst;
	}

}
