<?php

/**
 * 字符加密解密类
 */
class keyt {

    protected static $_keys = null;

    protected static function init() {
        if (self::$_keys === null) {
            self::$_keys = array('comm' => '9K5W4H2U', 'en' => array('atc7hfgHqjFlmnopirsbUvwxyzABCDEkGeIJKLMNOPQRSTuVWXYZ0123456d89{_@][-}|>(,`^~:?%<.)/#*+&!;=', 'zqomMAVNJi36QW9eDCY5kbaTyKBvF47IgR0PtZEu1XSUGfwj8nLrpOhHlsx2cd{_@][-}|>(,`^~:?%<.)/#*+&!;='));
        }
    }

    public static function encrypt($code = '') {
        self::init();
        $block = mcrypt_get_block_size('des', 'ecb');
        if (($pad = $block - (strlen($code) % $block)) < $block) {
            $code .= str_repeat(chr($pad), $pad);
        }
        $encrypt = mcrypt_encrypt(MCRYPT_DES, self::$_keys['comm'], $code, MCRYPT_MODE_ECB);
        return base64_encode($encrypt);
    }

    public static function decrypt($str) {
        self::init();
        $str = base64_decode($str);
        $str = mcrypt_decrypt(MCRYPT_DES, self::$_keys['comm'], $str, MCRYPT_MODE_ECB);
        $block = mcrypt_get_block_size('des', 'ecb');
        $pad = ord($str[($len = strlen($str)) - 1]);
        if ($pad && $pad < $block && preg_match('/' . chr($pad) . '{' . $pad . '}$/', $str)) {
            $str = substr($str, 0, strlen($str) - $pad);
        }
        return $str;
    }

    public static function myencrypt($str) {
        if (strlen($str) == 0)
            return false;
        self::init();
        $encrypt_key = self::$_keys['en'][0];
        $decrypt_key = self::$_keys['en'][1];
        for ($i = 0; $i < strlen($str); $i++) {
            for ($j = 0; $j < strlen($encrypt_key); $j++) {
                if ($str[$i] == $encrypt_key[$j]) {
                    $enter.= $decrypt_key[$j];
                    break;
                }
            }
        }
        return $enter;
    }

    public static function mydecrypt($str) {
        self::init();
        $encrypt_key = self::$_keys['en'][0];
        $decrypt_key = self::$_keys['en'][1];
        for ($i = 0; $i < strlen($str); $i++) {
            for ($j = 0; $j < strlen($decrypt_key); $j++) {
                if ($str[$i] == $decrypt_key[$j]) {
                    $enter .= $encrypt_key[$j];
                    break;
                }
            }
        }
        return $enter;
    }

}

?>