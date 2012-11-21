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
 * 分页类
 *
 * @category CamelPHP
 * @package  Extend
 * @subpackage Util
 * @author   dreamans<dreamans@163.com>
 * @since    20120929
 */
class CP_Pages {
    /**
     * 分页栏每页显示的页数
     *
     * @access public
     * @var int
     */
    public $rollPage = 5;
    
    /**
     * 页数跳转时要带的参数
     *
     * @access public
     * @var string
     */
    public $parameter;
    
    /**
     * 默认列表每页显示行数
     *
     * @access public
     * @var int
     */
    public $listRows = 20;
    
    /**
     * 起始行数
     *
     * @access public 
     * @var int
     */
    public $firstRow;
    
    /**
     * 分页总页面数
     *
     * @access protected
     * @var int
     */
    protected $_totalPages;
    
    /**
     * 总行数
     *
     * @access protected
     * @var int
     */
    protected $_totalRows;
    
    /**
     * 当前页数
     *
     * @access protected
     * @var int
     */
    protected $_nowPage;
    
    /**
     * 分页的栏的总页数 
     *
     * @access protected
     * @var protected
     */
    protected $_coolPages;
    
    /**
     * 分页显示定制
     *
     * @access protected
     * @var string
     */
    protected $_config  = array('header'=>'条记录','prev'=>'上一页','next'=>'下一页','first'=>'第一页','last'=>'最后一页','theme'=>' %totalRow% %header% %_nowPage%/%totalPage% 页 %upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

    /**
     * 默认分页变量名
     *
     * @access protected
     * @var string
     */
    protected $_varPage;

    /**
     * 处理分页核心方法
     * 
     * @access public
     * @param int $totalRows  总的记录数
     * @param int $listRows  每页显示记录数
     * @param string $parameter  分页跳转的参数
     * @return void
     */
    public function pages($totalRows,$listRows='',$varpage = 'page',$parameter='') {
        $this->_totalRows = $totalRows;
        $this->parameter = $parameter;
        $this->_varPage = $varpage;
        if(!empty($listRows)) {
            $this->listRows = intval($listRows);
        }
        $this->_totalPages = ceil($this->_totalRows/$this->listRows);     //总页数
        $this->_coolPages  = ceil($this->_totalPages/$this->rollPage);
        $this->_nowPage  = !empty($_GET[$this->_varPage])?intval($_GET[$this->_varPage]):1;
        if(!empty($this->_totalPages) && $this->_nowPage>$this->_totalPages) {
            $this->_nowPage = $this->_totalPages;
        }
        $this->firstRow = $this->listRows*($this->_nowPage-1);
        return $this->_show();
    }

    public function setConfig($name,$value) {
        if(isset($this->_config[$name])) {
            $this->_config[$name]    =   $value;
        }
    }

    /**
     * 计算出分页
     * 
     * @access protected
     * @return string 
     */
    protected function _show() {
        if(0 == $this->_totalRows) return '';
        $p = $this->_varPage;
        $nowCoolPage      = ceil($this->_nowPage/$this->rollPage);
        $url  =  $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?").$this->parameter;
        $parse = parse_url($url);
        if(isset($parse['query'])) {
            parse_str($parse['query'],$params);
            unset($params[$p]);
            $url   =  $parse['path'].'?'.http_build_query($params);
        }
        //上下翻页字符串
        $upRow   = $this->_nowPage-1;
        $downRow = $this->_nowPage+1;
        if ($upRow>0){
            $upPage="<a href='".$url."&".$p."=$upRow'>".$this->_config['prev']."</a>";
        }else{
            $upPage="";
        }

        if ($downRow <= $this->_totalPages){
            $downPage="<a href='".$url."&".$p."=$downRow'>".$this->_config['next']."</a>";
        }else{
            $downPage="";
        }
        // << < > >>
        if($nowCoolPage == 1){
            $theFirst = "";
            $prePage = "";
        }else{
            $preRow =  $this->_nowPage-$this->rollPage;
            $prePage = "<a href='".$url."&".$p."=$preRow' >上".$this->rollPage."页</a>";
            $theFirst = "<a href='".$url."&".$p."=1' >".$this->_config['first']."</a>";
        }
        if($nowCoolPage == $this->_coolPages){
            $nextPage = "";
            $theEnd="";
        }else{
            $nextRow = $this->_nowPage+$this->rollPage;
            $theEndRow = $this->_totalPages;
            $nextPage = "<a href='".$url."&".$p."=$nextRow' >下".$this->rollPage."页</a>";
            $theEnd = "<a href='".$url."&".$p."=$theEndRow' >".$this->_config['last']."</a>";
        }
        // 1 2 3 4 5
        $linkPage = "";
        for($i=1;$i<=$this->rollPage;$i++){
            $page=($nowCoolPage-1)*$this->rollPage+$i;
            if($page!=$this->_nowPage){
                if($page<=$this->_totalPages){
                    $linkPage .= "&nbsp;<a href='".$url."&".$p."=$page'>&nbsp;".$page."&nbsp;</a>";
                }else{
                    break;
                }
            }else{
                if($this->_totalPages != 1){
                    $linkPage .= "&nbsp;<span class='current'>".$page."</span>";
                }
            }
        }
        $pageStr	 =	 str_replace(
            array('%header%','%_nowPage%','%totalRow%','%totalPage%','%upPage%','%downPage%','%first%','%prePage%','%linkPage%','%nextPage%','%end%'),
            array($this->_config['header'],$this->_nowPage,$this->_totalRows,$this->_totalPages,$upPage,$downPage,$theFirst,$prePage,$linkPage,$nextPage,$theEnd),$this->_config['theme']);
        return $pageStr;
    }

}
