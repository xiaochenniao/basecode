<?php

class dbV2 {

    public $db;

    public function __construct() {

        $db_config = config::get('db');

        $db = Array('host' => $db_config['sources']['main']['host'],
            'username' => $db_config['sources']['main']['user'],
            'password' => $db_config['sources']['main']['pass'],
            'db' => $db_config['sources']['main']['database'],
            'port' => $db_config['sources']['main']['port'],
            'prefix' => '',
            'charset' => $db_config['charset']
        );

        $this->db = new MysqliDb($db);
    }

}
