<?php

class mem {

    protected static $_mem = null;
    protected static $_expires = 86400;

    public static function init($servers = array()) {
        if (self::$_mem === null || !empty($servers)) {
            if (class_exists('memcached')) {
                self::$_mem = new Memcached;
                if (empty($servers)) {
                    $servers = config::get('memcached');
                }
                self::$_mem->addServers($servers);
            } elseif (class_exists('memcache')) {
                self::$_mem = new Memcache;
                if (empty($servers)) {
                    $servers = config::get('memcached');
                }
                foreach ($servers as $server) {
                    self::$_mem->addServer($server[0], $server[1]);
                }
            } else {
                throw new except('no memcache install');
            }
        }
    }

    public static function get($key) {
        self::init();
        return self::$_mem->get($key);
    }

    public static function set($key, $value = null, $expires = null) {
        self::init();
        $expires = $expires ? $expires : self::$_expires;
        return self::$_mem->set($key, $value, $expires);
    }

    public static function del($key = null) {
        self::init();
        if ($key === null) {
            return self::$_mem->flush();
        } else {
            return self::$_mem->delete($key);
        }
    }

}

?>
