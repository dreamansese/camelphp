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
 * 上传类
 *
 * @category CamelPHP
 * @package  Extend
 * @subpackage Util
 * @author   dreamans<dreamans@163.com>
 * @since    20120929 
 */
class CP_Upload {
    
    /**
     * 允许上传的扩展名
     *
     * @access protected
     * @var string
     */
    protected $_allow_ext;
    
    /**
     * 允许上传的最大尺寸KB
     *
     * @access protected
     * @var int
     */
    protected $_maxsize;
    
    /**
     * 上传附件根目录
     *
     * @access protected
     * @var string 
     */
    protected $_path;
    
    /**
     * 错误信息 
     * 0 - 无错误
     * 1 - 扩展名不合法
     * 2 - 目录创建失败
     * 3 - 文件移动失败
     * 4 - 文件尺寸过大
	 * 9 - $_FILES数组不存在
     *
     * @access protected
     * @var int
     */
    protected $_errno = 0;

    /**
     * 配置信息
     * 
     * @param string $ext
     * @param int $size 
     * @return void
     */
    public function config($cfg) {
        $this->_allow_ext = $cfg['ext'];
        $this->_maxsize = $cfg['size'];
        $this->_path = $cfg['path'];
    }

    /**
     * 上传文件主方法
     * 
     * @access public
     * @param string $filedata
     * @return array 
     */
    public function uploadFile($filedata) {
		if(empty($_FILES[$filedata])) {
			return array('errno' => 9);
		}
        //获取上传文件基本信息
        $info = $this->_getFileInfo($filedata);

        //检查文件的合法性
        $this->_checkFile($info['ext']);
        
        //检查文件尺寸是否合法
        $this->_checkSize($info['size']);
        
        //创建目录
        !$this->_errno && ($dir = $this->_createDir());

        //生成文件
        !$this->_errno && ($filename = $this->_createFilename($info['ext']));
        
        //移动目录
        !$this->_errno && $this->_movefile($info['tmpname'], $dir['realpath'] . $filename);
        
        $newinfo = array();
        if(!$this->_errno) {
            $newinfo = array(
                'ext' => $info['ext'],
                'realpath' => $dir['realpath'] . $filename,
                'path' => $dir['path'] . $filename,
                'size' => $info['size'],
				'errno' => 0,
            );
        }else{
            $newinfo['errno'] = $this->_errno;
        }
        return $newinfo;
    }
    
    /**
     * 移动文件到目标目录
     * 
     * @access protected
     * @param string $tmpfile
     * @param string $newfile
     * @return void 
     */
    protected function _movefile($tmpfile, $newfile) {
        if (function_exists('move_uploaded_file') && move_uploaded_file($tmpfile, $newfile)) {
            
        } elseif (@rename($tmpfile, $newfile)){
            
        } elseif (copy($tmpfile, $newfile)){
            
        } else {
            $this->error = 3;
        }
	    //@unlink($tmpfile);
    }
    
    /**
     * 检查上传文件尺寸
     * 
     * @access protected
     * @param int $size 
     * @return void
     */
    protected function _checkSize($size) {
        if(($size > $this->_maxsize * 1024) || $size/1024/1024 > intval(ini_get('upload_max_filesize'))) {
            $this->_errno = 4;
        }
    }

    /**
     * 获取上传文件相关信息
     * 
     * @access protected
     * @param string $filedata 
     * @return array
     */
    protected function _getFileInfo($filedata) {
        $info = array();
        $info['name'] = $_FILES[$filedata]['name'];
        $info['tmpname'] = $_FILES[$filedata]['tmp_name'];
        $info['size'] = $_FILES[$filedata]['size'];
        $info['ext'] = pathinfo($info['name'], PATHINFO_EXTENSION);
        return $info;
    }

    /**
     * 检查上传文件是否是图片
     * 此方法需要php_exif模块支持
     * 若图片格式为gif、jpg、png、bmp时返回true，负责返回false
     * 
     * @access public
     * @param string $filename
     * @return boolean 
     */
    public function checkImg($filename) {
        $isimage = false;
        $pic_info = exif_imagetype($filename);
        if(in_array($pic_info, array('IMAGETYPE_GIF','IMAGETYPE_JPEG','IMAGETYPE_PNG','IMAGETYPE_BMP'))) $isimage = true;
        return $isimage;
    }

    /**
     * 检查文件扩展名是否合法
     * 
     * @access protected
     * @param string $ext
     * @param boolean $isimage
     * @return boolean 
     */
    protected function _checkFile($ext) {
        $allow_ext = explode(',', $this->_allow_ext);
        if(in_array($ext, $allow_ext)) {
            return true;
        }else{
            $this->_errno = 1;
        }
    }
    
    /**
     * 创建目录
     * 
     * @access protected
     * @param boolean $index
     * @return array 
     */
    protected function _createDir($index=true) {
        $dir = array();
        $dir['path'] = date("Y/m/d/",time());
        $dir['realpath'] = $this->_path . $dir['path'];
        $status = true;
        if(!is_dir($dir['realpath'])){
            $status = mkdir($dir['realpath'], 0777 ,true);
            $index && @touch($dir['realpath'].'/index.html');
        }
        if(!$status){
            $this->_errno = 2;
        }
        return $dir;
    }
    
    /**
     * 生成文件名
     * 
     * @access protected
     * @param string $ext
     * @return string 
     */
    protected function _createFilename($ext) {
        return rand(10000,99999) . substr(md5(time()), 0, 16) . '.' . $ext;
    }
}
