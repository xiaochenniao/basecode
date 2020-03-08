<?php

class resource {

    public static function load() {
        $dir = APP_DIR . '/controller';
        $res = self::read_resource();
        ksort($res);
        return $res;
    }

    public static function read_resource() {
        $dir = APP_DIR . '/controller';
        $res = array();
        $dh = @opendir($dir);
        while ($file = @readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullpath = $dir . "/" . $file;
                $controller = substr($file, 0, -4);
                $res[$controller] = self::read_action($fullpath);
            }
        }
        @closedir($dh);
        return $res;
    }

    public static function read_action($file) {
        $code = file_get_contents($file);
        preg_match_all("/function\s+(.*)Action/i", $code, $matchs);
        return $matchs[1];
    }

}

?>