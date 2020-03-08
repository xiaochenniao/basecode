<?php

class rdl {

    protected static $_rd = null;

    public static function init() {
        if (!self::$_rd) {
            $server = config::get('redis_local');
            self::$_rd = new Redis();
            self::$_rd->pconnect($server[0], $server[1], 300);
        }
    }

    public static function close() {
        if (self::$_rd) {
            try {
                self::$_rd->close();
            } catch (Exception $e) {
                
            }
            self::$_rd = null;
        }
    }

    public static function keys($key) {
        $retry = 10;
        while (--$retry) {
            try {
                self::init();
                $res = self::$_rd->keys($key);
                return $res;
            } catch (Exception $e) {
                self::close();
                var_dump($e);
                sleep(2);
                continue;
            }
        }
        throw new except('redis error');
    }

    public static function set($key, $value, $expire = null) {
        $retry = 10;
        while (--$retry) {
            try {
                self::init();
                $res = self::$_rd->set($key, $value);
                if ($expire) {
                    self::$_rd->expire($key, $expire);
                }
                return $res;
            } catch (Exception $e) {
                self::close();
                var_dump($e);
                sleep(2);
                continue;
            }
        }
        throw new except('redis error');
    }

    public static function expire($key, $expire = 86400) {
        $retry = 10;
        while (--$retry) {
            try {
                self::init();
                return self::$_rd->expire($key, $expire);
            } catch (Exception $e) {
                self::close();
                var_dump($e);
                sleep(2);
                continue;
            }
        }
        throw new except('redis error');
    }

    public static function get($key) {
        $retry = 10;
        while (--$retry) {
            try {
                self::init();
                $res = self::$_rd->get($key);
                return $res;
            } catch (Exception $e) {
                self::close();
                var_dump($e);
                sleep(2);
                continue;
            }
        }
        throw new except('redis error');
    }

    public static function exists($key) {
        $retry = 10;
        while (--$retry) {
            try {
                self::init();
                $res = self::$_rd->exists($key);
                return $res;
            } catch (Exception $e) {
                self::close();
                var_dump($e);
                sleep(2);
                continue;
            }
        }
        throw new except('redis error');
    }

    public static function lLen($key) {
        $retry = 10;
        while (--$retry) {
            try {
                self::init();
                $res = self::$_rd->lLen($key);
                return $res;
            } catch (Exception $e) {
                self::close();
                var_dump($e);
                sleep(2);
                continue;
            }
        }
        throw new except('redis error');
    }

    public static function blPop($key, $timeout = 5) {
        $retry = 10;
        while (--$retry) {
            try {
                self::init();
                $res = self::$_rd->blPop($key, $timeout);
                if (is_array($res) && isset($res[1])) {
                    return $res[1];
                }
                return $res;
            } catch (Exception $e) {
                self::close();
                var_dump($e);
                sleep(2);
                continue;
            }
        }
        throw new except('redis error');
    }

    public static function brPop($key, $timeout = 5) {
        $retry = 10;
        while (--$retry) {
            try {
                self::init();
                $res = self::$_rd->brPop($key, $timeout);
                if (is_array($res) && isset($res[1])) {
                    return $res[1];
                }
                return $res;
            } catch (Exception $e) {
                self::close();
                var_dump($e);
                sleep(2);
                continue;
            }
        }
        throw new except('redis error');
    }

    public static function lPush($key, $value = null) {
        $retry = 10;
        while (--$retry) {
            try {
                self::init();
                $res = self::$_rd->lPush($key, $value);
                return $res;
            } catch (Exception $e) {
                self::close();
                var_dump($e);
                sleep(2);
                continue;
            }
        }
        throw new except('redis error');
    }

    public static function rPush($key, $value = null) {
        $retry = 10;
        while (--$retry) {
            try {
                self::init();
                $res = self::$_rd->rPush($key, $value);
                return $res;
            } catch (Exception $e) {
                self::close();
                var_dump($e);
                sleep(2);
                continue;
            }
        }
        throw new except('redis error');
    }

    public static function del($key = null) {
        $retry = 10;
        while (--$retry) {
            try {
                self::init();
                if ($key === null) {
                    $res = self::$_rd->flushdb();
                } else {
                    $res = self::$_rd->del($key);
                }
                return $res;
            } catch (Exception $e) {
                self::close();
                var_dump($e);
                sleep(2);
                continue;
            }
        }
        throw new except('redis error');
    }

    public static function sget($key) {
        $retry = 10;
        while (--$retry) {
            try {
                self::init();
                $res = self::$_rd->srandmember($key);
                return $res;
            } catch (Exception $e) {
                self::close();
                var_dump($e);
                sleep(2);
                continue;
            }
        }
        throw new except('redis error');
    }

    public static function sset($key, $value) {
        $retry = 10;
        while (--$retry) {
            try {
                self::init();
                $res = self::$_rd->sadd($key, $value);
                return $res;
            } catch (Exception $e) {
                self::close();
                var_dump($e);
                sleep(2);
                continue;
            }
        }
        throw new except('redis error');
    }

    public static function sisset($key, $value) {
        $retry = 10;
        while (--$retry) {
            try {
                self::init();
                $res = self::$_rd->sismember($key, $value);
                return $res;
            } catch (Exception $e) {
                self::close();
                var_dump($e);
                sleep(2);
                continue;
            }
        }
        throw new except('redis error');
    }

    public static function sdel($key = null, $value = null) {
        if ($value === null) {
            return self::del($key);
        }
        $retry = 10;
        while (--$retry) {
            try {
                self::init();
                $res = self::$_rd->srem($key, $value);
                return $res;
            } catch (Exception $e) {
                self::close();
                var_dump($e);
                sleep(2);
                continue;
            }
        }
        throw new except('redis error');
    }

    public static function sgetall($key) {
        $retry = 10;
        while (--$retry) {
            try {
                self::init();
                $res = self::$_rd->smembers($key);
                return $res;
            } catch (Exception $e) {
                self::close();
                var_dump($e);
                sleep(2);
                continue;
            }
        }
        throw new except('redis error');
    }

    public static function sgetallnum($key) {
        $retry = 10;
        while (--$retry) {
            try {
                self::init();
                $res = self::$_rd->scard($key);
                return $res;
            } catch (Exception $e) {
                self::close();
                var_dump($e);
                sleep(2);
                continue;
            }
        }
        throw new except('redis error');
    }

}

?>
