<?php

define('BIN_DIR', dirname(dirname(__FILE__)));
if (!defined('CORE_DIR'))
    define('CORE_DIR', dirname(BIN_DIR) . DIRECTORY_SEPARATOR . 'library');
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
//
spl_autoload_register('_autoload');

function _autoload($name) {
    if (in_array($name, array('db', 'cache', 'session', 'acl', 'tree', 'mem', 'getopt', 'except', 'debug'))) {
        $file_name = CORE_DIR . DIRECTORY_SEPARATOR . $name . '.php';
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

//set_exception_handler(array('except', 'handler'));

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

class request {

    protected static $_request_uri = null;
    protected static $_script_name = null;
    protected static $_base_path = null;
    protected static $_path_info = null;
    protected static $_module = null;
    protected static $_controller = 'index';
    protected static $_controlleru = 'index';
    protected static $_action = 'index';
    public static $_params = array();

    public static function init() {
        if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() === 1) {

            function _stripslashes($string) {
                if (is_array($string)) {
                    foreach ($string as $key => $val) {
                        $string[$key] = _stripslashes($val);
                    }
                } else {
                    $string = stripslashes($string);
                }
                return $string;
            }

            $_GET = _stripslashes($_GET);
            $_POST = _stripslashes($_POST);
            $_COOKIE = _stripslashes($_COOKIE);
            @set_magic_quotes_runtime(false);
        }
        self::$_params = $_POST + $_GET;
    }

    public static function getScriptName() {
        if (self::$_script_name === null) {
            if (isset($_SERVER['SCRIPT_NAME'])) {
                $script_name = $_SERVER['SCRIPT_NAME'];
            } elseif (isset($_SERVER['PHP_SELF'])) {
                $script_name = $_SERVER['PHP_SELF'];
            } else {
                self::$_script_name = '';
                return self::$_script_name;
            }
            self::$_script_name = substr($script_name, strrpos($script_name, '/') + 1);
            return self::$_script_name;
        }
        return self::$_script_name;
    }

    public static function set($key, $value) {
        if ((null === $value) && isset(self::$_params[$key])) {
            unset(self::$_params[$key]);
        } elseif (null !== $value) {
            self::$_params[$key] = $value;
        }
    }

    public static function get($key, $default = null) {
        if (isset(self::$_params[$key])) {
            return self::$_params[$key];
        }
        return $default;
    }

    public static function gets() {
        return self::$_params;
    }

    public static function get_method() {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function is_get() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    public static function is_post() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    public static function is_accept_gzip() {
        return strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false;
    }

    public static function get_host() {
        return isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    }

    public static function get_port() {
        return (int) $_SERVER['REMOTE_PORT'];
    }

    public static function get_referer() {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    }

    public static function get_ip($t = 'ip') {
        if (getenv('HTTP_X_REAL_IP') && strcasecmp(getenv('HTTP_X_REAL_IP'), 'unknown')) {
            $ip = getenv('HTTP_X_REAL_IP');
        } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $long = ip2long($ip);
        if ($long === false) {
            return false;
        }
        if ($t != 'ip') {
            return $long;
        }
        if (long2ip($long) === $ip) {
            return $ip;
        }
        return false;
    }

    public static function get_module_name() {
        return self::$_module;
    }

    public static function set_module_name($value) {
        self::$_module = strtolower($value);
    }

    public static function getControllerName() {
        return self::$_controller;
    }

    public static function setControllerName($value) {
        $value && self::$_controller = strtolower($value);
    }

    public static function getControllerUName() {
        return self::$_controlleru;
    }

    public static function setControllerUName($value) {
        $value && self::$_controlleru = strtolower($value);
    }

    public static function getActionName() {
        return self::$_action;
    }

    public static function setActionName($value) {
        $value && self::$_action = strtolower($value);
    }

    public static function get_request_uri() {
        if (self::$_request_uri === null) {
            self::$_request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        }
        return self::$_request_uri;
    }

    public static function getBasePath() {
        if (self::$_base_path === null) {
            if (isset($_SERVER['SCRIPT_NAME'])) {
                $base_path = $_SERVER['SCRIPT_NAME'];
            } elseif (isset($_SERVER['PHP_SELF'])) {
                $base_path = $_SERVER['PHP_SELF'];
            } else {
                self::$_base_path = '';
                return self::$_base_path;
            }
            self::$_base_path = rtrim(dirname($base_path), '/');
            return self::$_base_path;
        }
        return self::$_base_path;
    }

    public static function get_path_info() {
        if (self::$_path_info === null) {
            $base_path = self::getBasePath();
            $request_uri = self::get_request_uri();
            if ($pos = strpos($request_uri, '?')) {
                $request_uri = substr($request_uri, 0, $pos);
            }
            self::$_path_info = substr($request_uri, strlen($base_path));
        }
        return self::$_path_info;
    }

    //
    public static function setSubParam() {
        //
    }

}

?>