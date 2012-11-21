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
 * ͼƬ������
 *
 * @category CamelPHP
 * @package  Extend
 * @subpackage Util
 * @author   dreamans<dreamans@163.com>
 * @since    20121101
 */
class CP_Images {

    /**
     * ��ȡ��·���ļ���չ��
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
     * ��������ͼ
     *
     * @access public
     * @para $im string ͼƬ·��
     * @para $cover string �Ƿ񸲸�ԭͼ
     * @para $width int
     * @para $height int
     * @para $quality int ��������ͼ����
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
	    //���ļ��� ��·��
        if(empty($cover)) {
            $newFileName = $im . '.thumb.'.$ext;
        } else {
            $newFileName = $out.basename($im).'_'.$width.'_'.$height.'_.'.$ext;
        }
	    //ȡ�õ�ǰͼƬ��Ϣ
	    $picSize=getimagesize($im);
	    $imgType = array(1=>'gif', 2=>'jpeg', 3=>'png');
        $iwidth = $picSize[0];
	    $iheight = $picSize[1];
    	$itype    = $imgType[$picSize[2]];
	    $funcCreate = "imagecreatefrom".$itype;
	    if (!function_exists ($funcCreate) && !function_exists('imagecreatetruecolor') && !function_exists('imagecopyresampled')) {
	        return '';
	    }
	    //������������ͼ���
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
		
	    //��ȡͼ��
	    $img = @$funcCreate($im);
	    //�½�ͼ��
	    $thumb = imagecreatetruecolor($width, $height);
	    //����ͼ��
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
     * ��ˮӡ
     *
     * @access public
     * @para $img string ��Ҫ��ˮӡ��ͼƬ
     * @para $water string ˮӡͼƬ·��
     * @para $position int ˮӡλ��
     * @para $quality int ˮӡ����
     * @para $alpha int ��͸����
     * @return boolean
     */
    public function makeWater($img, $water, $position=9, $quality=90, $alpha=100){
	    if(!is_readable($img) || !is_readable($water)){
            return false;
	    }
	    //��ȡͼƬ��Ϣ
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
