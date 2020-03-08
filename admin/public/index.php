<?php

define('APP_DIR', dirname(dirname(__FILE__)));
define('APP_NAME', 'admin');
//require '../../vendor/autoload.php';
require '../../library/init.php';
framework::set_plugin('plugin_acl');
framework::run();
?>