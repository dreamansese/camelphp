<?php

defined("C_IN") || die("Access Denied");

class welcome_demoModel{

    var $db;

    public function __construct() {
        $this->db = M()->mysql();
    }

    public function welcome() {
        $opt = array(
            'table' => 'demo',    
        );
        return $this->db->find($opt);
    }
}
