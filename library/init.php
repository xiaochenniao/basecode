<?php

/**
 * php轻量级框架 结合bae.baidu.com平台使用
 *
 * @author xiaochenniao@163.com
 * @category php framework
 * @copyright Copyright(c) 2020
 * @version 0.2
 */
if (!defined('APP_DIR')) {
    exit("Not defined APP_DIR, Please define APP_DIR at public/index.php");
}
if (!defined('APP_NAME')) {
    exit("Not defined APP_NAME, Please define APP_NAME at public/index.php");
}

//框架版本
define('CORE_VERSION', '0.2');
define('CORE_DIR', dirname(__FILE__));
if (!defined('CROND_DIR'))
    define('CROND_DIR', dirname(CORE_DIR) . DIRECTORY_SEPARATOR . 'bin');
if (!defined('CONTROLLER_DIR'))
    define('CONTROLLER_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'controller');
if (!defined('SERVICE_DIR'))
    define('SERVICE_DIR', dirname(CORE_DIR) . DIRECTORY_SEPARATOR . 'service');
if (!defined('LIBRARY_DIR'))
    define('LIBRARY_DIR', dirname(CORE_DIR) . DIRECTORY_SEPARATOR . 'library');
if (!defined('TEMPLATE_DIR'))
    define('TEMPLATE_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'template');
if (!defined('DATA_DIR'))
    define('DATA_DIR', dirname(CORE_DIR) . DIRECTORY_SEPARATOR . 'data');
if (!defined('LOG_DIR'))
    define('LOG_DIR', '/data/logs/' . APP_NAME);
if (!defined('CONFIG_DIR'))
    define('CONFIG_DIR', dirname(CORE_DIR) . DIRECTORY_SEPARATOR . 'config');
if (!defined('ATTACHMENT'))
    define('ATTACHMENT', 'attachment');
if (!defined('UPLOAD_DIR'))
    define('UPLOAD_DIR', DATA_DIR . DIRECTORY_SEPARATOR . 'attachment');
require '../../vendor/autoload.php';
require_once LIBRARY_DIR . '/common/common.fun.php';
require_once LIBRARY_DIR . '/dbV2.php';
require_once CONTROLLER_DIR . '/base.php';


//开启缓冲区
ob_start();

//注册自动加载
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

//初始化配置文件
config::load();

//是否开启错误提示
if (config::get('base.is_debug', 0)) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL & ~E_NOTICE);
}

set_exception_handler(array('except', 'handler'));

class framework {

    protected static $_plugin = null;

    public static function set_plugin($plugin) {
        self::$_plugin = $plugin;
    }

    public static function do_plugin($method) {
        if (self::$_plugin !== null) {
            if (is_string(self::$_plugin)) {
                self::$_plugin = new self::$_plugin;
            }
            if (method_exists(self::$_plugin, $method)) {
                self::$_plugin->$method();
            }
        }
    }

    public static function run() {
        request::init();
        router::parse_url();
        self::do_plugin('pre_dispatch');
        dispatcher::run();
        if (view::has_template() === false) {
            $template = request::getControllerName() . '_' . request::getActionName() . '.tpl';
            if (request::get_module_name()) {
                $template = request::get_module_name() . '/' . $template;
            }
            view::display($template);
        }
        response::send();
    }

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
        $defined_config_file = CONFIG_DIR . '/defined.conf.php';
        if (is_readable($defined_config_file)) {
            require $defined_config_file;
        }
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

class dispatcher {

    private static $_dispatch_num = 0;

    public static function run() {
        self::_dispatch();
    }

    private static function _dispatch() {
        self::$_dispatch_num++;
        $loadController = false;
        $module_name = request::get_module_name();
        $controller_name = request::getControllerName();
        if ($controller_name == 'base') {
            throw new except('controller ' . $controller_name . ' not found', 404);
        }
        $controller_dir = CONTROLLER_DIR . ($module_name ? '/' . $module_name : '');
        $load_file = $controller_dir . '/' . $controller_name . '.php';
        if (is_readable($load_file)) {
            require_once $load_file;
            $class_name = $controller_name . '_controller';
        } else {
            throw new except('controller ' . $controller_name . ' not found', 404);
        }
        $action = request::getActionName();
        $action = $action . 'Action';
        $controller = new $class_name();
        $controller->$action();
    }

}

abstract class controller {

    protected $logininfo = null;
    protected $_pageurl = null;

    function __construct() {
        //$this->logininfo = F::logininfo(null,APP_NAME);
        $this->init();
        $this->inia(); // adver
        $this->inim(); // media
    }

    function init() {
        
    }

    function inia() {
        
    }

    function inim() {
        
    }

    function __call($method_name, $args) {
        if ('Action' == substr($method_name, -6)) {
            $action = substr($method_name, 0, strlen($method_name) - 6);
            throw new except(sprintf('action "%s" does not exist', $action), 404);
        }
        throw new except(sprintf('method "%s" does not exist', $method_name), 500);
    }

    protected function _forward($action, $controller = null) {
        if (null !== $controller) {
            request::setControllerName($controller);
        }
        request::setActionName($action);
        dispatcher::run();
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

    public static function get_ip() {
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

    //主要处理二级域名使用
    public static function setSubParam() {
        //待添加
    }

}

class response {

    protected static $_response = array(
        'status_code' => 200,
        'status_phrase' => 'OK',
        'headers' => array(),
        'cookies' => array(),
    );

    public static function setHeader($name, $value) {
        self::$_response['headers'][$name] = $value;
    }

    public static function redirect($url) {
        $url = router::build_url($url);
        self::setHeader('Location', $url);
        self::send();
    }

    public static function setCookie($name, $value, $seconds = 0, $path = '/', $domain = '', $is_secure = false, $http_only = false) {
        if ($seconds !== 0) {
            $seconds = time() + $seconds;
        }
        $path = request::getBasePath() . $path;
        if ($domain === '') {
            $domain = config::get('base.cookie_domain', '');
        }
        $cookie = array('name' => $name, 'value' => $value, 'expire' => $seconds, 'path' => $path, 'domain' => $domain, 'secure' => $is_secure, 'http_only' => $http_only);
        self::$_response['cookies'][] = $cookie;
    }

    protected static function clear() {
        for ($i = 0, $n = ob_get_level(); $i < $n; $i++) {
            ob_end_clean();
        }
    }

    public static function send() {
        $content = ob_get_clean();
        self::clear();
        foreach (self::$_response['headers'] as $name => $value) {
            header($name . ': ' . $value);
        }
        foreach (self::$_response['cookies'] as $cookie) {
            setcookie($cookie['name'], $cookie['value'], $cookie['expire'], $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['http_only']);
        }

        if (config::get('base.gzip', false) && request::is_accept_gzip()) {
            ob_start('ob_gzhandler');
        } else {
            ob_start();
        }
        echo $content;
        ob_end_flush();
        exit;
    }

}

class view {

    protected static $_engine = null;
    protected static $_params = array();
    protected static $_template = false;

    public static function set_template($template) {
        self::$_template = $template;
    }

    public static function has_template() {
        return !empty(self::$_template);
    }

    public static function e($string) {
        return self::toggle_escape($string, 'htmlspecialchars');
    }

    public static function r($string) {
        return self::toggle_escape($string, 'htmlspecialchars_decode');
    }

    public static function toggle_escape($string, $toggle_handler) {
        if (is_array($string)) {
            $toggled_value = array();
            foreach ($string as $key => $value) {
                $toggled_value[self::toggle_escape($key, $toggle_handler)] = self::toggle_escape($value, $toggle_handler);
            }
        } elseif (is_string($string)) {
            $toggled_value = $toggle_handler($string, ENT_QUOTES);
        } else {
            $toggled_value = $string;
        }
        return $toggled_value;
    }

    public static function get($key) {
        return r_get($key);
    }

    public static function getParams() {
        return self::$_params;
    }

    public static function set($key, $value = true, $is_escape = true) {
        if (is_array($key)) {
            foreach ($key as $_key => $_val) {
                self::$_params[$_key] = $value ? self::e($_val) : $_val;
            }
        } else {
            self::$_params[$key] = $is_escape ? self::e($value) : $value;
        }
    }

    public static function get_engine() {
        if (self::$_engine === null) {
            require_once CORE_DIR . '/third/smarty/Smarty.class.php';
            $smarty = new Smarty();
            $smarty->left_delimiter = "<!--{";
            $smarty->right_delimiter = "}-->";
            $smarty->template_dir = TEMPLATE_DIR;
            $smarty->compile_dir = DATA_DIR . '/template_compile/' . md5(TEMPLATE_DIR);
            $smarty->registerPlugin('modifier', 'e', array('view', 'e'));
            $smarty->registerPlugin('modifier', 'r', array('view', 'r'));
            $smarty->registerPlugin('modifier', 'get', array('view', 'get'));
            $smarty->registerPlugin('modifier', 'url', array('view', 'url'));
            $smarty->registerPlugin('function', 'url', array('view', 'url'));
            $smarty->registerPlugin('function', 'pager', array('view', 'pager'));
            $smarty->registerPlugin('function', 'formhash', array('view', 'formhash'));
            self::$_engine = $smarty;
        }
        return self::$_engine;
    }

    public static function display($template = null) {
        self::set_template(true);
        if ($template === null) {
            if (request::getActionName() == 'index') {
                $template = request::getControllerName() . '.tpl';
            } else {
                $template = request::getControllerName() . '_' . request::getActionName() . '.tpl';
            }
            if (request::get_module_name()) {
                $template = request::get_module_name() . '/' . $template;
            }
        }
        $engine = self::get_engine();
        $engine->assign(self::$_params);
        $engine->display($template);
    }

    public static function url($string = '') {
        if ($string == '_fullurl_') {
            return router::build_url(r_gets());
        }
        return router::build_url($string);
    }

    public static function pager($params, &$smarty) {
        $pager = $params['pager'];
        unset($params['pager']);
        $total = $pager['record_count'];
        $page_total = ceil($total / $pager['page_size']);
        $page = max(1, min($pager['page'], $page_total));
        $num = min(10, $page_total);
        $offset = min(2, $num);
        $pagefrom = min($page_total - $num + 1, max($page - $offset, 1));
        $pageto = min($pagefrom + $num - 1, $page_total);
        $params['page'] = '{page}';
        $params = $params + r_gets();
        $page = array(
            'num' => $num,
            'offset' => $offset,
            'pagefrom' => $pagefrom,
            'pageto' => $pageto,
            'page_total' => $page_total,
            'page' => $page,
            'total' => $total,
            'url' => router::build_url($params)
        );
        $smarty->assign('page', $page);
        return $smarty->fetch('global/sheet_page.tpl');
    }

    public static function formhash() {
        $formhash = substr(md5(time() + rand(0, 1000)), 0, 8);
        @setcookie('formhash-' . request::getControllerUName(), $formhash, 0, '/');
        return $formhash;
    }

}

//路由
class router {

    public static function parse_url() {
        //request::setControllerName(request::get('controller'));
        //request::setActionName(request::get('action'));
        $path = request::get_path_info();
        $params = array();
        $path = trim($path, '/');
        if ($path != '') {
            $tmp = explode('/', substr($path, 0, strrpos($path, '.')));
            $route = Load::conf('route');
            $route = $route[APP_NAME];
            if (isset($route['module']) && !empty($route['module']) && in_array($tmp[0], array_keys($route['module']))) {
                if (count($tmp) < 2) {
                    $ucontroller = 'index';
                } else {
                    $ucontroller = strrpos($tmp[1], '.') > 0 ? substr($tmp[1], 0, strrpos($tmp[1], '.')) : $tmp[1];
                }
            } else {
                $ucontroller = strrpos($tmp[0], '.') > 0 ? substr($tmp[0], 0, strrpos($tmp[0], '.')) : $tmp[0];
            }
            $route_modules = isset($route['module']) ? (array) $route['module'] : array();
            $rules = (array) $route['rule'];
            foreach ($rules as $rule) {
                $newpath = preg_replace("/$rule[0]/i", $rule[1], $path);
                if ($newpath != $path) {
                    $path = $newpath;
                    break;
                }
            }
            if ($pos = strrpos($path, '.')) {
                $path = substr($path, 0, $pos);
            }
            $path = explode('/', $path);
            if (count($path) > 3) {
                throw new except('Path depth validation Failure', 404);
            }

            $setup = $params['controller'] = str_replace("-", "", $path[0]);
            $params['action'] = isset($path[1]) ? $path[1] : '';
            if (!empty($route_modules[$setup])) {
                $params['module'] = $setup;
                $params['controller'] = $path[1];
                $params['action'] = $path[2];
            }
        }
        foreach ($params as $param => $value) {
            if ($param === 'module') {
                request::set_module_name($value);
            } elseif ($param === 'controller') {
                request::setControllerName($value);
                request::setControllerUName($ucontroller ? $ucontroller : $value);
            } elseif ($param === 'action') {
                $arr = strtolower($value);
                $arr = explode('-', $arr);
                $actionName = array_shift($arr);
                request::setActionName($actionName);
                if ($params['controller'] == 'email') {
                    request::set('tpl', substr(strtolower($value), 10));
                } else {
                    if ($len = count($arr)) {
                        for ($i = 0; $i < $len; $i = $i + 2) {
                            $key = $arr[$i];
                            $val = $arr[$i + 1];
                            request::set($arr[$i], $val);
                        }
                    }
                }
            }
        }
        request::setSubParam();
    }

    public static function build_url($string) {
        $base_path = request::getBasePath();
        if (empty($string)) {
            return $base_path;
        }

        if (is_string($string)) {
            $url_parse = parse_url($string);
            if (!empty($url_parse['host'])) {
                return $string;
            }
            if (isset($url_parse['query'])) {
                parse_str($url_parse['query'], $params);
            } else {
                $params = array();
            }
            if (isset($url_parse['path'])) {
                $path = trim($url_parse['path'], '/');
                $newpath = '/' . $path;
                if ($path && strpos($path, ".") === false) {
                    $newpath .= ".do";
                }
                $base_path .= $newpath;
            }
        } elseif (is_array($string)) {
            $params = $string;
            $params['module'] = isset($params['module']) ? $params['module'] : request::get_module_name();
            $params['controller'] = isset($params['controller']) ? $params['controller'] : request::getControllerUName();
            $params['action'] = isset($params['action']) ? $params['action'] : request::getActionName();

            if (substr($params['controller'], 0, 11) == 'javascript:') {
                $params['controller'] = substr($params['controller'], 11);
            }
            if (substr($params['action'], 0, 11) == 'javascript:') {
                $params['action'] = substr($params['action'], 11);
            }
            $nobase = false;
            if ($params['module']) {
                $base_path .= '/' . $params['module'];
                unset($params['module']);
                $nobase = true;
            }
            if ($params['controller']) {
                if ($params['controller'] != 'index' && $params['action'] != 'index') {
                    $base_path .= '/' . $params['controller'];
                }
                unset($params['controller']);
                $nobase = true;
            }
            if ($params['action']) {
                if ($params['action'] != 'index') {
                    $base_path .= '/' . $params['action'];
                }
                unset($params['action']);
                $nobase = true;
            }
            if (!$nobase) {
                return $base_path;
            } else {
                $base_path .= '.do';
            }
        } else {
            return $base_path;
        }
        $url = array();
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                if (!($value === null || $value === '')) {
                    if (substr($value, 0, 11) == 'javascript:') {
                        $url[] = $key . '=' . substr($value, 11);
                    } else {
                        $url[] = $key . '=' . rawurlencode($value);
                    }
                }
            }
        }
        return $base_path . ($url ? '?' . implode("&", $url) : '');
    }

}
