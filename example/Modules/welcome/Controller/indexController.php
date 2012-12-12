<?php

defined("C_IN") || die("Access Denied");

class indexController extends commonController {

    public function index() {
        $title = "CamelPHP - PHP开源框架 - 千里之行，始于足下";
        $this->assign('title', $title);
        $this->display();
    }
}
