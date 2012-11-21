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
 * FileCache文件缓存类
 *
 * @category CamelPHP
 * @package  Extend
 * @subpackage Cache
 * @author   dreamans<dreamans@163.com>
 * @since    20121009
 */
class CP_FileCache{

    /**
     * 配置信息
     *
     * @access protected
     * @var array
     */
    protected $_config = array();

    /**
     * 缓存键
     *
     * @access protected
     * @var string
     */
    protected $_key;

    /**
     * 缓存内容
     * 
     * @access protected
     * @var string
     */
    protected $_value;

    /**
     * 存在周期
     *
     * @access protected
     * @var int
     */
    protected $_life;

    /**
     * 缓存文件
     *
     * @access protected
     * @var path
     */
    protected $_path;

    /**
     * 配置信息
     * 
     * @access public
     * @param $cfg
     * @return void
     */
    public function config($cfg) {
        $this->_config = $cfg;
    }

    /**
     * 执行写入缓存
     *
     * @access public
     * @param $key
     * @param $value
     * @param $life
     * @return boolean
     */
    public function setCache($key, $value, $life) {
        $this->_setParam($key, $value, $life);
        return $this->_writeCache();
    }

    /**
     * 获取缓存
     * 
     * @access public
     * @param $key 
     * @return mixed
     */
    public function getCache($key) {
        $this->_setParam($key);
        return $this->_readCache();
    }

    /**
     * 删除缓存文件
     * 
     * @access public
     * @param string $key
     * @return void 
     */
    public function delCache($key = '') {
        $this->_setParam($key);
        $this->_delCache();
    }

    /**
     * 设置参数
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
        $this->_path = $this->_config['path'].$this->_key.'.php';
    }

    /**
     * 写缓存到文件
     * 
     * @access private
     * @return boolean 
     */
    private function _writeCache() {
        $life = $this->_life == 0 ? 0 : $this->_life + time();
        $value = "<?exit('Denied!')?>" . $life . 'DM//-->' . $this->_value;
        return Super('Func')->writeFile($this->_path, $value);
    }

    /**
     * 从文件读出缓存
     * 
     * @access private
     * @return mixed
     */
    private function _readCache() {
        $value = Super('Func')->readFile($this->_path);
        //读取文件失败 返回false
        if(empty($value)) {
            return false;
        }
        $value = str_replace("<?exit('Denied!')?>",'',$value);
        preg_match('/^(\d+)DM\/\/\-\-\>/is', $value, $match);
        $this->_life = isset($match[1]) ? $match[1] : 0 ;

        //缓存失效 删除过期缓存文件 返回false
        if($this->_life < time()) {
            $this->_delCache();
            return false;
        }
        $replace = isset($match[0]) ? $match[0] : '' ;
        $value = str_replace($replace, '', $value);
        return unserialize($value);
    }

    /**
     * 删除缓存文件
     *
     * @access private
     * @return void
     */
    private function _delCache() {
        if(!empty($this->_key)) {
            is_file($this->_path) && unlink($this->_path);
        }
    }
}
