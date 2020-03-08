<?php

/*
 * Copyright (c) 2013
 * �������ع�����
 * $Id: Load.php 1 2013-03-08 19:22:30Z YaoGuoli $
 */

class Load {

    public static function conf($fname, $key = null) {
        $configs = array();
        $default_config_file = CONFIG_DIR . '/' . $fname . '.conf.php';
        if (is_readable($default_config_file)) {
            $configs = require $default_config_file;
            return $key ? $configs[$key] : $configs;
        }
        return $configs;
    }

    public static function lib() {
        $args = func_get_args();
        $file = array_shift($args);
        $lib_file = SERVICE_DIR . '/lib/' . $file . '.php';
        if (is_readable($lib_file)) {
            require_once $lib_file;
            $args_str = array();
            foreach ($args as $key => $v) {
                $args_str[] = "\$args[$key]";
            }
            $class = $file;
            $args_str = implode(',', $args_str);
            eval("\$class = new $class($args_str);");
            return $class;
        }
        return false;
    }

}

?>