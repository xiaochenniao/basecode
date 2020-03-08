<?php

class logger {

    public static function log($file_name, $message) {
        $umask = @umask(0);
        $file_name = DATA_DIR . '/logs/' . $file_name . '.log';
        $dir = dirname($file_name);
        if (!is_writable($dir)) {
            if (!@mkdir($dir, 0777, true)) {
                throw new except('dir not writable:' . $dir, 500);
            }
        }
        if (@filesize($file_name) > 1024 * 10240) {
            rename($file_name, $file_name . '.old');
        }
        $message = '[' . date('Y-m-d H:i:s') . '] - ' . $message . "\n";
        $fp = fopen($file_name, 'a');
        if ($fp !== false) {
            flock($fp, LOCK_EX);
            fwrite($fp, $message);
            flock($fp, LOCK_UN);
            fclose($fp);
        }
        @chmod($file_name, 0777);
        @umask($umask);
    }

    public static function loginfo($file_name, $message) {
        $umask = @umask(0);
        $file_name = LOG_DIR . '/' . date('Ymd') . '/' . $file_name . '.log';
        $dir = dirname($file_name);
        if (!is_writable($dir)) {
            if (!@mkdir($dir, 0777, true)) {
                throw new except('dir not writable:' . $dir, 500);
            }
        }
        if (@filesize($file_name) > 1024 * 10240) {
            rename($file_name, $file_name . '.old');
        }
        $message = '[' . date('Y-m-d H:i:s') . '] - ' . $message . "\n";
        $fp = fopen($file_name, 'a');
        if ($fp !== false) {
            flock($fp, LOCK_EX);
            fwrite($fp, $message);
            flock($fp, LOCK_UN);
            fclose($fp);
        }
        @chmod($file_name, 0777);
        @umask($umask);
    }

}
