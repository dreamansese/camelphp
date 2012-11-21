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
 * 模板编译类
 *
 * @category CamelPHP
 * @package  Core
 * @author   dreamans<dreamans@163.com>
 * @since    20120928
 */
class CP_Template {

    /**
     * 模板路径
     * 
     * @access protected
     * @var path
     */
    protected $_tmp_file_path;

    /**
     * 模板编译路径
     *
     * @access protected
     * @var path
     */
    protected $_com_file_path;

    /**
     * 编译模板
     *
     * @access public
     * @param $tpl
     * @return path
     */
    public function template($tpl) {
		$tplarr = explode(':',$tpl);
		$tplarr = array_filter($tplarr);
		$tpla = !empty($tplarr) ? array_pop($tplarr): C_ACTION;
        $tplc = !empty($tplarr) ? array_pop($tplarr): C_CONTROLLER;
        $tplm = !empty($tplarr) ? array_pop($tplarr): C_MODULE;
        //模板文件
        $tplPath = APP_MODULES_PATH . (!empty($tplm) ? $tplm .C_DS: '') . 'View';
        $this->_tmp_file_path = $tplPath . C_DS . $tplc . C_DS . $tpla . '.tpl.php';

        //编译后的文件
        $this->_com_file_path = APP_TPLCOM_PATH . ($tplm ? $tplm . '_': '').$tplc.'_'.$tpla.'.cache.php';

        $temp_time = is_file($this->_tmp_file_path) ? filemtime($this->_tmp_file_path) : 0;
        $com_time  = is_file($this->_com_file_path) ? filemtime($this->_com_file_path) : 0;
        //模板不存在
        if(!$temp_time) trigger_error('template file ' . $this->_tmp_file_path . ' not exists');
        if($temp_time > $com_time) {
            if(is_file($this->_tmp_file_path)) {
                if(($tmp_content = Super('Func')->readFile($this->_tmp_file_path)) === false){
				    trigger_error('template file ' . $this->_tmp_file_path . ' failed to open');
                }
            } else {
                //模板不存在
			    trigger_error('template file ' . $this->_tmp_file_path . ' not exists');
            }
            $com_content = $this->compile($tmp_content);
            Super('Func')->makeDir(dirname($this->_com_file_path));
            Super('Func')->writeFile($this->_com_file_path, $com_content);
        }
        return $this->_com_file_path;
    }

    /**
     * 核心编译方法
     *
     * @access public
     * @return string
     */
    public function compile($tmp_content) {
        //替换模板文件访问限制代码
        $tmp_content = str_replace("<?exit?>","",$tmp_content);
        //去除tab符
        $tmp_content = preg_replace("/([\n\r]+)\t+/s", "\\1", $tmp_content);
        //统一标记符
        $tmp_content = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $tmp_content);
        //换行符
        $tmp_content = str_replace("{LF}", "<?=\"\\n\"?>", $tmp_content);
        //替换<?= 标签
        $tmp_content = preg_replace("/\{(\\\$[a-zA-Z0-9_\-\>\[\]\'\"\$\.\x7f-\xff]+)\}/s", "<?php echo \\1?>", $tmp_content);
        //替换模板常量标签
        $tmp_content = preg_replace("/\{([A-Z_]+)\}/s", "<?php echo T('\\1'); ?>", $tmp_content);
        //替换php开始标签
        $tmp_content = preg_replace("/[\n\r\t]*\{php\}/s", "<?php", $tmp_content);
        //替换php结束标签
        $tmp_content = preg_replace("/[\n\r\t]*\{\/php\}/s", "?>", $tmp_content);
        //echo 打印
        $tmp_content = preg_replace("/[\n\r\t]*\{echo\s+(.+?)\}[\n\r\t]*/s", "<?php echo \\1; ?>", $tmp_content);
        //if
        $tmp_content = preg_replace("/[\n\r\t]*\{if\s+(.+?)\}[\n\r\t]*/is", "<?php if(\\1) { ?>", $tmp_content);
        //else
        $tmp_content = preg_replace("/[\n\r\t]*\{else\}[\n\r\t]*/is", "<?php } else { ?>", $tmp_content);
        //endif
        $tmp_content = preg_replace("/[\n\r\t]*\{\/if\}[\n\r\t]*/is", "<?php } ?>", $tmp_content);
        //elseif
        $tmp_content = preg_replace("/[\n\r\t]*\{elseif\s+(.+?)\}[\n\r\t]*/is", "<?php } elseif(\\1) { ?>", $tmp_content);
        //foreach $val
        $tmp_content = preg_replace("/[\n\r\t]*\{foreach\s+(\S+)\s+(\S+)\}[\n\r\t]*/is", "<?php if(is_array(\\1)) foreach(\\1 as \\2) { ?>", $tmp_content);
        //foreach $k $v
        $tmp_content = preg_replace("/[\n\r\t]*\{foreach\s+(\S+)\s+(\S+)\s+(\S+)\}[\n\r\t]*/is", "<?php if(is_array(\\1)) foreach(\\1 as \\2 => \\3) { ?>", $tmp_content);
        //end foreach
        $tmp_content = preg_replace("/\{\/foreach\}/i", "<?php } ?>", $tmp_content);
        //对模板中使用的函数进行替换
		$tmp_content = preg_replace("/\{([a-zA-Z\:\_]+)\((.+?)\)\}/is", "<?php echo \\1(\\2); ?>", $tmp_content);
		//执行PHP语句
		$tmp_content = preg_replace("/\{php\s+(.+?)\}/is", "<?php \\1; ?>", $tmp_content);
		//对文件引入标签进行替换
        $tmp_content = preg_replace("/[\n\r\t]*\{include\s+(.+?)\}[\n\r\t]*/s", '<?php include $t->Template("\\1"); ?>', $tmp_content);
        return $tmp_content;
    }
}
