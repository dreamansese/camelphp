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
 * 验证码生成类
 *
 * @category CamelPHP
 * @package  Extend
 * @subpackage Util
 * @author   dreamans<dreamans@163.com>
 * @since    20121101
 */
class CP_Captcha {
    
    /**
     * 验证码宽度
     * @type int
     */
    private $_width;

    /**
     * 验证码图片高度
     * @type int
     */
    private $_height;
    
    /* - - - - - - - - - - - - - - - - - - - -+/
     * 生成验证码数量
     * @type int
    /+ - - - - - - - - - - - - - - - - - - - -*/
    private $codeNum;
    
    /* - - - - - - - - - - - - - - - - - - - -+/
     * 生成验证码
     * @type int
    /+ - - - - - - - - - - - - - - - - - - - -*/
    private $code;
    
    /* - - - - - - - - - - - - - - - - - - - -+/
     * 生成验证码图片
     * @type source
    /+ - - - - - - - - - - - - - - - - - - - -*/
    private $im;

    /* - - - - - - - - - - - - - - - - - - - -+/
     * 设置验证码基本参数
     * @para $width int
     * @para $height int
     * @para $codeNum int
     * @return void
    /+ - - - - - - - - - - - - - - - - - - - -*/
    public function config($width=100, $height=30, $codeNum=4 , $fontfile = '') {
        $this->width = $width;
        $this->height = $height;
        $this->codeNum = $codeNum;
        $this->fontfile = !empty($fontfile) ? $fontfile : DM_ROOT . 'static/fonts/arial.ttf';
    }

    /* - - - - - - - - - - - - - - - - - - - -+/
     * 输出验证码
     * @return void
    /+ - - - - - - - - - - - - - - - - - - - -*/
    public function showImg() {
        //创建图片
        $this->createImg();
        //设置干扰元素
        $this->setDisturb();
        //设置验证码
        $this->setCaptcha();
        //输出图片
        $this->outputImg();
    }

    /* - - - - - - - - - - - - - - - - - - - -+/
     * 获取验证码
     * @return void
    /+ - - - - - - - - - - - - - - - - - - - -*/
    public function getCaptcha() {
        return $this->code;
    }

    /* - - - - - - - - - - - - - - - - - - - -+/
     * 创建图片
     * @return void
    /+ - - - - - - - - - - - - - - - - - - - -*/
    private function createImg() {
        $this->im = imagecreatetruecolor($this->width, $this->height);
        $bgColor = imagecolorallocate($this->im, rand(234,255), rand(234,255), rand(234,255));
        imagefill($this->im, 0, 0, $bgColor);
    }

    /* - - - - - - - - - - - - - - - - - - - -+/
     * 设置干扰元素
     * @return void
    /+ - - - - - - - - - - - - - - - - - - - -*/
    private function setDisturb() {
        $area = ($this->width * $this->height) / 20;
        $disturbNum = ($area > 250) ? 250 : $area;
        //加入点干扰
        for ($i = 0; $i < $disturbNum; $i++) {
            $color = imagecolorallocate($this->im, rand(200, 255), rand(200, 255), rand(200, 255));
            imagesetpixel($this->im, rand(1, $this->width - 2), rand(1, $this->height - 2), $color);
        }
        //加入弧线
        for ($i = 0; $i <= 5; $i++) {
            $color = imagecolorallocate($this->im, rand(128, 255), rand(125, 255), rand(100, 255));
            imagearc($this->im, rand(0, $this->width), rand(0, $this->height), rand(30, 300), rand(20, 200), 50, 30, $color);
        }
    }

    /* - - - - - - - - - - - - - - - - - - - -+/
     * 生成验证码
     * @return void
    /+ - - - - - - - - - - - - - - - - - - - -*/
    private function createCode() {
        $str = "23456789abcdefghijkmnpqrstuvwxyzABCDEFGHIJKMNPQRSTUVWXYZ";
        for ($i = 0; $i < $this->codeNum; $i++) {
            $this->code .= $str{rand(0, strlen($str) - 1)};
        }
    }

    /* - - - - - - - - - - - - - - - - - - - -+/
     * 设置验证码
     * @return void
    /+ - - - - - - - - - - - - - - - - - - - -*/
    private function setCaptcha() {
        $this->createCode();
        for ($i = 0; $i < $this->codeNum; $i++) {
            $color = imagecolorallocate($this->im, rand(10, 160), rand(100, 160), rand(128, 160));
            $size = 14;
            $x = floor($this->width / $this->codeNum) * $i + 5;
            $y = rand($this->height-2,$this->height-14);
            imagettftext($this->im, $size, 0 ,$x, $y, $color,$this->fontfile ,$this->code{$i});
        }
    }

    /* - - - - - - - - - - - - - - - - - - - -+/
     * 输出验证码
     * @return void
    /+ - - - - - - - - - - - - - - - - - - - -*/
    private function outputImg() {
        if (imagetypes() & IMG_JPG) {
            header('Content-type:image/jpeg');
            imagejpeg($this->im);
        } elseif (imagetypes() & IMG_GIF) {
            header('Content-type: image/gif');
            imagegif($this->im);
        } elseif (imagetype() & IMG_PNG) {
            header('Content-type: image/png');
            imagepng($this->im);
        } else {
            die("Don't support image type!");
        }
    }
}
