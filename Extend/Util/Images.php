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
 * 图片处理类
 *
 * @category CamelPHP
 * @package  Extend
 * @subpackage Util
 * @author   dreamans<dreamans@163.com>
 * @since    20121101
 */
class CP_Images {

    /**
     * 获取带路径文件扩展名
     *
     * @access protected
     * @para $filename string
     * @return string
     */
    protected function _getFileExt($fileName){
	    $file = basename($fileName);
	    $tempArr = explode(".", $file);
	    $fileExt = array_pop($tempArr);
	    $fileExt = trim($fileExt);
	    $fileExt = strtolower($fileExt);
	    return $fileExt;
    }
	
    /**
     * 创建缩略图
     *
     * @access public
     * @para $im string 图片路径
     * @para $cover string 是否覆盖原图
     * @para $width int
     * @para $height int
     * @para $quality int 生成缩略图质量
     * @return string
     */
    public function createThumb($im, $width=200, $height=150, $out = '', $quality=95){
        if(!is_readable($im)){
            return false;
	    }
	    $ext = strtolower($this->_getFileExt($im));

	    if(!in_array($ext,array('gif','jpg','jpeg','png'))){
            return false;
	    }
	    //新文件名 带路径
        if(empty($cover)) {
            $newFileName = $im . '.thumb.'.$ext;
        } else {
            $newFileName = $out.basename($im).'_'.$width.'_'.$height.'_.'.$ext;
        }
	    //取得当前图片信息
	    $picSize=getimagesize($im);
	    $imgType = array(1=>'gif', 2=>'jpeg', 3=>'png');
        $iwidth = $picSize[0];
	    $iheight = $picSize[1];
    	$itype    = $imgType[$picSize[2]];
	    $funcCreate = "imagecreatefrom".$itype;
	    if (!function_exists ($funcCreate) && !function_exists('imagecreatetruecolor') && !function_exists('imagecopyresampled')) {
	        return '';
	    }
	    //智能生成缩略图宽高
	    $ws = $iwidth/$width;
	    $hs = $iheight/$height;
	    if($ws>=1 && $hs>=1){
            if($ws>$hs){
		        $w = round($width * $hs);
		        $h = $iheight;
		        $x = round(($iwidth - $w)/2);
		        $y = 0;
            } else {
                $w = $iwidth;
		        $h = round($height * $ws);
		        $x = 0;
		        $y = round(($iheight - $h)/2);
            }
    	} elseif($ws<1 && $hs<1) {
            $w = $iwidth;
            $h = $iheight;
            $x = 0;
            $y = 0;
            $width = $iwidth;
            $height = $iheight;
        } elseif($ws>1 && $hs<1) {
            $w = $width;
            $h = $iheight;
            $height = $iheight;
            $x = ($iwidth - $w)/2;
            $y = 0;
	    } else {
            $w = $iwidth;
            $h = $height;
            $width = $iwidth;
            $x = 0;
            $y = ($iheight - $h)/2;
	    }
		
	    //获取图像
	    $img = @$funcCreate($im);
	    //新建图像
	    $thumb = imagecreatetruecolor($width, $height);
	    //复制图像
	    imagecopyresampled($thumb, $img, 0, 0, $x ,$y, $width, $height, $w, $h);
	    $funcOut = "image" . $itype;
        if($itype == 'jpeg') {
            @$funcOut($thumb,$newFileName,$quality);
        } else {
            @$funcOut($thumb,$newFileName);
        }

    	imagedestroy($thumb);
	    //echo $thumb;
	    if(file_exists($newFileName)){
            return basename($newFileName);
	    }
	    else{
            return false;
	    }
    }

    /**
     * 加水印
     *
     * @access public
     * @para $img string 需要加水印的图片
     * @para $water string 水印图片路径
     * @para $position int 水印位置
     * @para $quality int 水印质量
     * @para $alpha int 不透明度
     * @return boolean
     */
    public function makeWater($img, $water, $position=9, $quality=90, $alpha=100){
	    if(!is_readable($img) || !is_readable($water)){
            return false;
	    }
	    //获取图片信息
	    $imgInfo = getimagesize($img);
	    if($imgInfo[2]<1 && $imgInfo[2]>3){
            return false;
	    }
		
	    $width = $imgInfo[0];
    	$height = $imgInfo[1];
		
	    $waterInfo = getimagesize($water);
	    $waterwidth = $waterInfo[0];
	    $waterheight = $waterInfo[1];
		
	    $itype   = array(1=>'gif', 2=>'jpeg', 3=>'png');
	    $imgType     = $itype[$imgInfo[2]];
	    $waterType   = $itype[$waterInfo[2]];
	    $funcCreateimg   = "imagecreatefrom".$imgType;
	    $funcCreatewater = "imagecreatefrom".$waterType;
		
	    switch($position) {
            case 1:
                $x = +5;
                $y = +5;
                break;
            case 2:
                $x = ($width - $waterwidth)/2;
		        $y = +5;
                break;
            case 3:
		        $x = $width - $waterwidth - 5;
		        $y = +5;
                break;
            case 4:
		        $x = +5;
		        $y = ($height - $waterheight)/2;
                break;
            case 5:
	        	$x = ($width - $waterwidth)/2;
                $y = ($height - $waterheight)/2;
                break;
            case 6:
		        $x = $width - $waterwidth - 5;
		        $y = ($height - $waterheight)/2;
                break;
            case 7:
		        $x = +5;
		        $y = $height - $waterheight -5;
		        break;
            case 8:
		        $x = ($width - $waterwidth)/2;
                $y = $height - $waterheight -5;
                break;
            case 9:
		        $x = $width - $waterwidth - 5;
                $y = $height - $waterheight -5;
                break;
	    }
		
    	if(function_exists($funcCreateimg) && function_exists($func_createwater)){
            $newimg   = @$funcCreateimg($img);
            $newwater = @$funcCreatewater($water);
            imagealphablending($newwater, true);
            imagecopymerge($newimg,$newwater,$x,$y,0,0,$waterwidth,$waterheight,$alpha);
    	} else {
            return false;
    	}
	    $funcOut = "image" . $imgType;
    	@$funcOut($newimg,$img,$quality);
	    imagedestroy($newimg);
	    return true;
    }
}
