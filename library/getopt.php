<?php

class getopt {

    protected static $_pramas = null;

    public static function get($key, $default = null) {
        if (self::$_pramas === null) {
            self::$_pramas = array();
            if (!isset($_SERVER['argv'])) {
                return null;
            }
            $argv = array_slice($_SERVER['argv'], 1);
            while (count($argv) > 0) {
                if (substr($argv[0], 0, 2) == '--') {
                    $optionWithParam = ltrim(array_shift($argv), '-');
                    list($flag, $param) = explode('=', $optionWithParam, 2);
                    self::$_pramas[$flag] = $param;
                } elseif (substr($argv[0], 0, 1) == '-' && ('-' != $argv[0] || count($argv) > 1)) {
                    $flag = ltrim(array_shift($argv), '-');
                    $param = array_shift($argv);
                    self::$_pramas[$flag] = $param;
                } else {
                    self::$_pramas[] = array_shift($argv);
                }
            }
        }
        return isset(self::$_pramas[$key]) ? self::$_pramas[$key] : $default;
    }

}
