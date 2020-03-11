<?php

/**
 * swoole
 * Description of init
 * @author xiaochenniao <374270458@qq.com>
 * Created on : 2020-3-10
 */
define('SWOOLE_DIR', dirname(dirname(__FILE__)));
if (!defined('CORE_DIR'))
    define('CORE_DIR', dirname(SWOOLE_DIR) . DIRECTORY_SEPARATOR . 'library');
if (!defined('SERVICE_DIR'))
    define('SERVICE_DIR', dirname(CORE_DIR) . DIRECTORY_SEPARATOR . 'service');
if (!defined('DATA_DIR'))
    define('DATA_DIR', dirname(CORE_DIR) . DIRECTORY_SEPARATOR . 'data');
if (!defined('CONFIG_DIR'))
    define('CONFIG_DIR', dirname(CORE_DIR) . DIRECTORY_SEPARATOR . 'config');
if (!defined('ATTACHMENT'))
    define('ATTACHMENT', 'attachment');
if (!defined('UPLOAD_DIR'))
    define('UPLOAD_DIR', DATA_DIR . DIRECTORY_SEPARATOR . 'attachment');
if (!defined('TASK_DIR'))
    define('TASK_DIR', SWOOLE_DIR . DIRECTORY_SEPARATOR . 'task');
//
spl_autoload_register('_autoload');

function _autoload($name) {
    if (in_array($name, array('db', 'cache', 'session', 'acl', 'tree', 'mem', 'getopt', 'except', 'debug'))) {
        $file_name = CORE_DIR . DIRECTORY_SEPARATOR . $name . '.php';
    } else if (strstr($name, 'task_')) {
        $file_name = TASK_DIR . DIRECTORY_SEPARATOR . $name . '.php';
    } else {
        $file_name = SERVICE_DIR . DIRECTORY_SEPARATOR . $name . '.php';
    }
    if (is_readable($file_name)) {
        require_once $file_name;
    }
}

//
config::load();

//
if (config::get('base.is_debug', 0)) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL & ~E_NOTICE);
}

class config {

    protected static $_configs = array();

    public static function get($key, $default_value = null) {
        $config = self::$_configs;
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            foreach ($keys as $next_key) {
                if (!isset($config[$next_key])) {
                    return $default_value;
                }
                $config = $config[$next_key];
            }
        } elseif (!isset($config[$key])) {
            $config = $default_value;
        } else {
            $config = $config[$key];
        }
        return $config;
    }

    public static function load() {
        $default_config_file = CONFIG_DIR . '/default.conf.php';
        if (is_readable($default_config_file)) {
            self::$_configs = require $default_config_file;
        }
        $custom_config_file = CONFIG_DIR . '/custom.conf.php';
        if (is_readable($custom_config_file)) {
            $custom_configs = require $custom_config_file;
            if (is_array($custom_configs)) {
                if (!function_exists('array_replace_recursive')) {

                    function array_replace_recursive($array, $array1) {

                        function recurse($array, $array1) {
                            foreach ($array1 as $key => $value) {
                                if (!isset($array[$key]) || (isset($array[$key]) && !is_array($array[$key]))) {
                                    $array[$key] = array();
                                }
                                if (is_array($value)) {
                                    $value = recurse($array[$key], $value);
                                }
                                $array[$key] = $value;
                            }
                            return $array;
                        }

                        $args = func_get_args();
                        $array = $args[0];
                        if (!is_array($array)) {
                            return $array;
                        }
                        for ($i = 1; $i < count($args); $i++) {
                            if (is_array($args[$i])) {
                                $array = recurse($array, $args[$i]);
                            }
                        }
                        return $array;
                    }

                }
            }
            self::$_configs = array_replace_recursive(self::$_configs, $custom_configs);
        }
    }

}
