<?php

class session {

    protected static $_session_start = false;

    protected static function session_start() {
        if (self::$_session_start === false) {
            session_start();
            self::$_session_start = true;
        }
    }

    public static function set($key, $value) {
        self::session_start();
        if ($value === null) {
            self::del($key);
        }
        $_SESSION[$key] = $value;
        session_write_close();
    }

    public static function get($key) {
        self::session_start();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public static function del($key = null) {
        self::session_start();
        if ($key === null) {
            foreach ($_SESSION as $k => $v) {
                $_SESSION[$k] = null;
            }
        } else {
            $_SESSION[$key] = null;
        }
    }

}
