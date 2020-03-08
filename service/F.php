<?php

/**
 * 公用函数库
 */
class F {

    //登录成功
    public static function login($data, $m = '') {
        session::set(self::formatm($m) . 'logininfo', $data);
    }

    //退出登录
    public static function logout($m = '') {
        session::del(self::formatm($m) . 'logininfo');
    }

    //获取登录信息
    public static function logininfo($key = null, $m = '') {
        $logininfo = session::get(self::formatm($m) . 'logininfo');
        if ($key) {
            return $logininfo[$key];
        }
        return $logininfo;
    }

    //判断用户登录状态
    public static function islogin($m = '') {
        $logininfo = self::logininfo(null, $m);
        if ($logininfo['id'] > 0 && $logininfo['login'] == 1) {
            return true;
        }
        return false;
    }

    //写入登录值
    public static function setlogininfo($key, $val, $m = '') {
        $logininfo = self::logininfo(null, $m);
        $logininfo[$key] = $val;
        self::login($logininfo, $m);
    }

    public static function formatm($m = '') {
        $m = !$m ? APP_NAME : $m;
        return $m == 'index' ? '' : $m;
    }

    //验证表单HASH值是否正确 防止重复提交
    public static function checkformhash($hash = '', $controller = null) {
        if (!$hash) {
            return false;
        }
        $controller = $controller ? $controller : request::getControllerUName();
        if ($hash != $_COOKIE['formhash-' . $controller]) {
            return false;
        }
        setcookie('formhash-' . $controller, '', -86400, '/');
        return true;
    }

    //生成加密 密码
    public static function md5($string, $key = "SuperPhp") {
        return md5($key . $string);
    }

    //生成双md5加密密码
    public static function md5md5($string) {
        return self::md5(md5($string));
    }

    //将数组中的指定元素转化成Int型
    public static function formatInt($data = array(), $intkey = array()) {
        if (!empty($intkey)) {
            foreach ($intkey as $key) {
                $data[$key] = isset($data[$key]) ? floatval($data[$key]) : 0;
            }
        }
        return $data;
    }

    /*
     * 生成sql  中 的 Where $s为要进入查询的数组，$sc为判断是否进入查询的条件，$ss是用来做查询替换的
     */

    public static function FormatSearchFields($s = array(), $sc = array(), $ss = array()) {
        if (empty($s)) {
            return array('where' => null, 'args' => array(), 'order' => '');
        }
        $where = $args = array();
        $order = '';
        if ($s['orderby']) {
            $order = $s['orderby'];
            unset($s['orderby']);
            if ($s['ordertype']) {
                $order .= ' ' . $s['ordertype'];
                unset($s['ordertype']);
            }
        }
        foreach ($s as $k => $v) {
            if (self::SearchCheckValue($sc, $k, $v)) {
                if (isset($ss[$k])) {
                    if ($ss[$k] != false) {
                        if (strpos($ss[$k], '[V]')) {
                            $where[] = str_replace('[V]', '?', $ss[$k]);
                            $args[] = $v;
                        } else {
                            $where[] = $ss[$k];
                        }
                    }
                } else {
                    $where[] = $k . "=?";
                    $args[] = $v;
                }
            }
        }
        return array('where' => implode(" AND ", $where), 'args' => $args, 'order' => $order);
    }

    public static function SearchCheckValue($sc = array(), $k = null, $v = '') {
        if (is_numeric($v)) {
            if (isset($sc[$k]) && $sc[$k]) {
                return true;
            } elseif ($v != 0) {
                return true;
            }
            return false;
        }
        if ($v) {
            return true;
        }
        return false;
    }

    //获得用户真实IP地址
    public static function onlineip() {
        if (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $onlineip = getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $onlineip = $_SERVER['REMOTE_ADDR'];
        }
        return $onlineip;
    }

    //转义
    function myaddslashes($string = '') {
        if ($string) {
            return addslashes($string);
        }
        return '';
    }

    //\'转换成'
    public static function slashes($data = array()) {
        if (!$data)
            return $data;
        if (!is_array($data)) {
            return stripslashes($data);
        }
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $data[$k] = self::slashes($v);
            } else {
                if (is_string($v)) {
                    $data[$k] = stripslashes($v);
                }
            }
        }
        return $data;
    }

    public static function jsonString($str) {
        return preg_replace("/([\\\\\/'])/", '\\\$1', $str);
    }

    //阿拉伯数字转汉字  目前只对百以内的数字输出正确
    public static function NumToCN($num = 0) {
        $cns = array('零', '一', '二', '三', '四', '五', '六', '七', '八', '九');
        $cns2 = array('', '十', '百', '千', '万');
        $cn = '';
        if ($num > 9) {
            $n = strlen($num);
            for ($i = $n; $i > 0; $i--) {
                $k = substr($num, 0 - $i, 1);
                if ($i == 1) {
                    if ($k > 0)
                        $cn .= $cns[$k];
                }
                elseif ($i == 2 && $k == 1) {
                    $cn .= $cns2[$i - 1];
                } else {
                    $cn .= $cns[$k] . $cns2[$i - 1];
                }
            }
        } else {
            $cn = $cns[$num];
        }
        return $cn;
    }

    //阿拉伯数字转字母
    public static function NumToLetter($num = 0) {
        $num = $num % 26;
        $cns = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        return isset($cns[$num]) ? $cns[$num] : $num;
    }

    //获取随即字符
    public static function getRandomstr($num = 6, $type = 'all', $allowzero = true) {
        $arr['number'] = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        $arr['lowwer'] = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
        $arr['upper'] = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        $arr['other'] = array('@', '#', '$', '%', '!');
        $rans = array();
        if (!$allowzero)
            array_shift($arr['number']);
        if ($type == 'all') {
            $rans = array_merge($arr['number'], $arr['lowwer'], $arr['upper'], $arr['other']);
        } else {
            $type = explode(',', $type);
            foreach ($type as $v) {
                $rans = array_merge($rans, $arr[$v]);
            }
        }
        $n = count($rans) - 1;
        $str = '';
        for ($i = 0; $i < $num; $i++) {
            $str .= $rans[rand(0, $n)];
        }
        return $str;
    }

    //四舍五入
    public static function nformat($number = 0, $len = 0) {
        return number_format($number, $len, '.', '');
    }

    //数组转URL字串
    public static function array2parm($arr = array(), $notkey = '') {
        $parms = array();
        if (!empty($arr)) {
            foreach ($arr as $k => $v) {
                if ($v !== '' && $notkey != $k) {
                    $parms[] = $k . '=' . urlencode($v);
                }
            }
        }
        return implode('&', $parms);
    }

    //截取字符串
    public static function getstr($string, $length, $ext = '', $charset = 'utf-8') {
        if ($length && strlen($string) > $length) {
            //截断字符
            $wordscut = '';
            if ($charset == 'utf-8') {
                //utf8编码
                $n = 0;
                $tn = 0;
                $noc = 0;
                while ($n < strlen($string)) {
                    $t = ord($string[$n]);
                    if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                        $tn = 1;
                        $n++;
                        $noc++;
                    } elseif (194 <= $t && $t <= 223) {
                        $tn = 2;
                        $n += 2;
                        $noc += 2;
                    } elseif (224 <= $t && $t < 239) {
                        $tn = 3;
                        $n += 3;
                        $noc += 2;
                    } elseif (240 <= $t && $t <= 247) {
                        $tn = 4;
                        $n += 4;
                        $noc += 2;
                    } elseif (248 <= $t && $t <= 251) {
                        $tn = 5;
                        $n += 5;
                        $noc += 2;
                    } elseif ($t == 252 || $t == 253) {
                        $tn = 6;
                        $n += 6;
                        $noc += 2;
                    } else {
                        $n++;
                    }
                    if ($noc >= $length) {
                        break;
                    }
                }
                if ($noc > $length) {
                    $n -= $tn;
                }
                $wordscut = substr($string, 0, $n);
            } else {
                for ($i = 0; $i < $length - 1; $i++) {
                    if (ord($string[$i]) > 127) {
                        $wordscut .= $string[$i] . $string[$i + 1];
                        $i++;
                    } else {
                        $wordscut .= $string[$i];
                    }
                }
            }
            $string = $wordscut . $ext;
        }
        return trim($string);
    }

    //使用htmlspecialchars递归的处理数组
    public static function htmlspecialchars($string) {
        if (is_array($string)) {
            foreach ($string as $key => $val) {
                $string[$key] = self::htmlspecialchars($val);
            }
        } else {
            $string = htmlspecialchars(trim($string), ENT_QUOTES);
        }
        return $string;
    }

    //使用stripslashes递归的处理数组
    public static function dstripslashes($string) {
        if (is_array($string)) {
            foreach ($string as $key => $val) {
                $string[$key] = self::dstripslashes($val);
            }
        } else {
            $string = stripslashes($string);
        }
        return $string;
    }

    //创建一个目录
    public static function mkdir($dir, $mode = 0777, $recursive = true) {
        $umask = @umask(0);
        @mkdir($dir, $mode, $recursive);
        @umask($umask);
    }

    //递归的删除一个目录
    public static function deldir($dir) {
        if (is_dir($dir)) {
            $dh = @opendir($dir);
            while ($file = @readdir($dh)) {
                if ($file != "." && $file != "..") {
                    $fullpath = $dir . "/" . $file;
                    if (!is_dir($fullpath)) {
                        @unlink($fullpath);
                    } else {
                        self::deldir($fullpath);
                    }
                }
            }
            @closedir($dh);
            return @rmdir($dir);
        } elseif (is_file($dir)) {
            return @unlink($dir);
        } else {
            return false;
        }
    }

    //字符加密
    public static function passport_encrypt($txt, $key = 'twk') {
        srand((double) microtime() * 1000000);
        $encrypt_key = md5(rand(0, 32000));
        $ctr = 0;
        $tmp = '';
        for ($i = 0; $i < strlen($txt); $i++) {
            $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
            $tmp .= $encrypt_key[$ctr] . ($txt[$i] ^ $encrypt_key[$ctr++]);
        }
        return base64_encode(self::passport_key($tmp, $key));
    }

    //字符解密
    public static function passport_decrypt($txt, $key = 'twk') {
        $txt = self::passport_key(base64_decode($txt), $key);
        $tmp = '';
        for ($i = 0; $i < strlen($txt); $i++) {
            $md5 = $txt[$i];
            $tmp .= $txt[++$i] ^ $md5;
        }
        return $tmp;
    }

    //加密解密算法
    function passport_key($txt, $encrypt_key) {
        $encrypt_key = md5($encrypt_key);
        $ctr = 0;
        $tmp = '';
        for ($i = 0; $i < strlen($txt); $i++) {
            $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
            $tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
        }
        return $tmp;
    }

    //验证字符是否无中文
    public static function nocn($str = '') {
        $str = trim($str);
        if (!$str) {
            return false;
        }
        if (preg_match("/^[a-zA-Z0-9\_\-]+$/i", $str)) {
            return true;
        }
        return false;
    }

    //判断字符串是否为UTF-8编码
    public static function isutf8($str) {
        if (preg_match("/^([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){1}/", $str) == true || preg_match("/([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){1}$/", $str) == true || preg_match("/([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){2,}/", $str) == true) {
            return true;
        } else {
            return false;
        }
    }

    //GB转UTF8
    public static function gb2utf8($str) {
        if (!$str)
            return '';
        if (!self::isutf8($str)) {
            return function_exists('iconv') ? iconv('GBK', 'UTF-8', $str) : mb_convert_encoding($str, "GBK", "UTF-8");
        } else {
            return $str;
        }
    }

    //UTF8转GB
    public static function utf82gb($str) {
        if (!$str)
            return '';
        if (self::isutf8($str)) {
            return function_exists('iconv') ? iconv('UTF-8', 'GBK', $str) : mb_convert_encoding($str, "UTF-8", "GBK");
        } else {
            return $str;
        }
    }

    /**
     * 多个数组元素值无重复合并
     * 注意：只适合一维数组
     * @return unknown
     */
    public static function array_values_merge() {
        $argc = func_num_args();
        if ($argc == 0) {
            return false;
        } else if ($argc == 1) {
            $arg1 = func_get_arg(0);
            if (is_array($arg1)) {
                return array_values(array_unique($arg1));
            } else {
                return array($arg1);
            }
        } else {
            $arg_list = func_get_args();
            $arr = array();
            for ($i = 0; $i < $argc; $i++) {
                $arr = array_merge($arr, $arg_list[$i]);
            }
            return array_values(array_unique($arr));
        }
    }

    // 客户端浏览器判断
    public static function my_get_browser() {
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 9.0')) {
            return 'IE9';
        }
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 8.0')) {
            return 'IE8';
        }
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 7.0')) {
            return 'IE7';
        }
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0')) {
            return 'IE6';
        }
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox')) {
            return 'ff';
        }
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome')) {
            return 'chrome';
        }
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Safari')) {
            return 'safari';
        }
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Opera')) {
            return 'opera';
        }
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], '360SE')) {
            return '360se';
        }
        return false;
    }

    // 手机号格式处理 格式：131****8888
    public static function getmobile($mobile) {
        if (!$mobile || !preg_match('/[1][3-8][0-9]{9}/i', $mobile)) {
            return false;
        } else {
            $pattern = "/(1\d{1,2})\d\d(\d{0,2})/";
            $replacement = "\$1****\$3";
            $res = preg_replace($pattern, $replacement, $mobile);
            return $res;
        }
    }

    // 邮箱格式处理 格式：188****@****.com
    public static function getemail($email) {
        if (!$email || !preg_match('/^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/', $email)) {
            return false;
        } else {
            // $pattern = "/^([\w\-\.]{1,3})([\w\-\.]+)@[\w\-\.]+(\.\w+)+$/";
            // $replacement = "$1****@****$3";
            // $res=preg_replace($pattern, $replacement, $email);   
            $res = substr($string, 0, 3) . "***@***." . substr($email, strrpos($email, '.') + 1);
            return $res;
        }
    }

    // 字符数统计（有换行时的统计）-wu
    public static function countstrn($str, $coding = 'utf8') {
        $arr_str = explode("\n", $str);
        $num = 0;
        foreach ($arr_str as $key => $val) {
            $num += mb_strlen($val, $coding);
        }
        return $num;
    }

    /**
     * curl 多线程
     * @param array $array 并行网址
     * @param int $timeout 超时时间
     * @return array
     */
    public static function curl_multi($array, $timeout) {
        $res = array();
        $mh = curl_multi_init(); //创建多个curl语柄
        foreach ($array as $k => $url) {
            $conn[$k] = curl_init($url);
            curl_setopt($conn[$k], CURLOPT_TIMEOUT, $timeout); //设置超时时间
            curl_setopt($conn[$k], CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
            curl_setopt($conn[$k], CURLOPT_MAXREDIRS, 7); //HTTp定向级别
            curl_setopt($conn[$k], CURLOPT_HEADER, 0); //这里不要header，加块效率
            curl_setopt($conn[$k], CURLOPT_FOLLOWLOCATION, 1); // 302 redirect
            curl_setopt($conn[$k], CURLOPT_RETURNTRANSFER, 1);
            curl_multi_add_handle($mh, $conn[$k]);
        }

        // 执行批处理句柄
        $active = null;
        do {
            $mrc = curl_multi_exec($mh, $active); //当无数据，active=true
        } while ($mrc == CURLM_CALL_MULTI_PERFORM); //当正在接受数据时
        while ($active && $mrc == CURLM_OK) {//当无数据时或请求暂停时，active=true
//        if(curl_multi_select($mh) != -1){
            do {
                $mrc = curl_multi_exec($mh, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
//        }
        }

        foreach ($array as $k => $url) {
            curl_error($conn[$k]);
            $res[$k]['content'] = curl_multi_getcontent($conn[$k]); //获得返回信息
            $res[$k]['http_code'] = curl_getinfo($conn[$k], CURLINFO_HTTP_CODE); //获得返回信息
            $header[$k] = curl_getinfo($conn[$k]); //返回头信息
            curl_close($conn[$k]); //关闭语柄
            curl_multi_remove_handle($mh, $conn[$k]); //释放资源
        }

        curl_multi_close($mh);
        return $res;
    }

    /**
     * Curl Request
     *
     * @param        $url
     * @param string $data
     * @param int    $timeOut
     * @param array  $headers
     * @param string $port
     * @param string $basic_key
     *
     * @return bool|mixed
     */
    public static function curlRequest($url, $data = '', $timeOut = 10, $headers = [], $port = '', $basic_key = '') {

        $start_time = microtime(true);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);

        // HTTPS
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

        // USER AGENT
        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        }
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);

        // REQUEST METHOD
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $type = 'POST';
        } else {
            $type = 'GET';
        }

        // HEADER REQUEST
        if (!empty($headers)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }

        // PORT
        if (!empty($port)) {
            curl_setopt($curl, CURLOPT_PORT, $port);
        }
        if (!empty($basic_key)) {
            curl_setopt($curl, CURLOPT_USERPWD, $basic_key);
        }
        // TIMEOUT
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeOut);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $resultData = curl_exec($curl);
        $httpInfo = curl_getinfo($curl);

        // END TIME
        $end_time = microtime(true);

        // LOG
        $log = [
            'urlAddress' => $url,
            'urlData' => $data,
            'httpCode' => $httpInfo['http_code'],
            'httpType' => $type,
            'resultData' => $resultData,
            'executionTime' => round($end_time - $start_time, 6),
        ];
        $file = isset($httpInfo['http_code']) ? 'request.' . $httpInfo['http_code'] : 'request';
        self::logMessage($log, $file);

        // RETURN
        if (curl_errno($curl)) {
            curl_close($curl);

            return false;
        } else {
            curl_close($curl);

            return $resultData;
        }
    }

}

?>