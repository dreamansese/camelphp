<?php

defined("C_IN") || die("Access Denied");

class welcomeModel {

    var $db;

    public function __construct() {
        $this->db = M()->mysql();
    }

    public function welcome() {
        return $this->db->find();
    }

}
