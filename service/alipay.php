<?php

/*

 * 支付宝支付操作类
 * 启用支付 alipay::start();
 * 支付验签 alipay::notify();
 */

class alipay {

    protected static $_alipay = null;

    public static function init() {
        if (self::$_alipay === null) {
            // 加载可配置的参数
            $alipayconf = Load::conf('pay', 'alipay');
            /* 1. 静态参数 */
            $alipayconf['gateway'] = "https://mapi.alipay.com/gateway.do?";
            $alipayconf['service'] = "create_direct_pay_by_user";
            $alipayconf['payment_type'] = "1";
            $alipayconf['_input_charset'] = "utf-8";
            $alipayconf['transport'] = "http";
            $alipayconf['sign_type'] = "MD5";
            $alipayconf['antiphishing'] = "0"; //防钓鱼功能开关，'0'表示该功能关闭，'1'表示该功能开启。默认为关闭
            //请与网站订单系统中的唯一订单号匹配
            $alipayconf['out_trade_no'] = date("Ymdhms");
            //订单名称，显示在支付宝收银台里的“商品名称”里，显示在支付宝的交易管理的“商品名称”的列表里。
            $alipayconf['subject'] = '';
            //订单描述、订单详细、订单备注，显示在支付宝收银台里的“商品描述”里
            $alipayconf['body'] = '';
            //订单总金额，显示在支付宝收银台里的“应付总额”里
            $alipayconf['total_fee'] = 0;
            //默认支付方式，四个值可选：bankPay(网银); cartoon(卡通); directPay(余额); CASH(网点支付)
            $alipayconf['paymethod'] = 'directPay';
            //默认网银代号，代号列表见http://club.alipay.com/read.php?tid=8681379
            $alipayconf['defaultbank'] = "";

            /* 3. 扩展参数 */
            //防钓鱼时间戳，初始值 $this->antiphishing=1 时有效
            $alipayconf['encrypt_key'] = '';
            //客户端的IP地址，初始值 $this->antiphishing=1 时有效
            $alipayconf['exter_invoke_ip'] = '';
            //自定义参数，可存放任何内容（除=、&等特殊字符外），不会显示在页面上
            $alipayconf['extra_common_param'] = '';
            //默认买家支付宝账号
            $alipayconf['buyer_email'] = '';
            if (APP_NAME == 'sale') {
                $returnArea = SALE_URL . '/';
            } else {
                $returnArea = APP_NAME == 'tui' ? ADVER_URL . '/' : BASE_URL;
            }
            $alipayconf['notify_url'] = $returnArea . '/' . trim($alipayconf['notify_url'], '/');
            $alipayconf['return_url'] = $returnArea . '/' . trim($alipayconf['return_url'], '/');
            self::$_alipay = $alipayconf;
        }
    }

    /**
     * 开始进入支付程序
     */
    public static function start($out_trade_no = null, $subject = '', $body = '', $total_fee = '0', $buyer_email = null, $extra_common_param = null, $paymethod = null, $defaultbank = null) {
        self::init();
        self::$_alipay['subject'] = $subject;
        self::$_alipay['body'] = $body;
        self::$_alipay['total_fee'] = $total_fee;
        if ($out_trade_no) {
            self::$_alipay['out_trade_no'] = $out_trade_no;
        }
        if ($buyer_email) {
            self::$_alipay['buyer_email'] = $buyer_email;
        }
        if ($extra_common_param) {
            self::$_alipay['extra_common_param'] = $extra_common_param;
        }
        if ($paymethod) {
            self::$_alipay['paymethod'] = $paymethod;
        }
        if ($defaultbank) {
            self::$_alipay['defaultbank'] = $defaultbank;
        }
        if (self::$_alipay['antiphishing'] == 1) {
            self::$_alipay['encrypt_key'] = self::query_timestamp();
            self::$_alipay['exter_invoke_ip'] = request::get_ip();
        }
        $parameter = array(
            "service" => self::$_alipay['service'],
            "payment_type" => self::$_alipay['payment_type'],
            //获取配置文件(alipay_config.php)中的值
            "partner" => self::$_alipay['partner'],
            "seller_email" => self::$_alipay['seller_email'],
            "return_url" => self::$_alipay['return_url'],
            "notify_url" => self::$_alipay['notify_url'],
            "_input_charset" => self::$_alipay['_input_charset'],
            "show_url" => self::$_alipay['show_url'],
            //从订单数据中动态获取到的必填参数
            "out_trade_no" => self::$_alipay['out_trade_no'],
            "subject" => self::$_alipay['subject'],
            "body" => self::$_alipay['body'],
            "total_fee" => self::$_alipay['total_fee'],
            //扩展功能参数——网银提前
            "paymethod" => self::$_alipay['paymethod'],
            "defaultbank" => self::$_alipay['defaultbank'],
            //扩展功能参数——防钓鱼
            "anti_phishing_key" => self::$_alipay['encrypt_key'],
            "exter_invoke_ip" => self::$_alipay['exter_invoke_ip'],
            //扩展功能参数——分润(若要使用，请取消下面两行注释)
            //$royalty_type   => "10",      //提成类型，不需要修改
            //$royalty_parameters => "111@126.com^0.01^分润备注一",
            //扩展功能参数——自定义参数
            "buyer_email" => self::$_alipay['buyer_email'],
            "extra_common_param" => self::$_alipay['extra_common_param']
        );
        $parameter = self::para_filter($parameter);
        //获得签名结果
        $sort_array = self::arg_sort($parameter);    //得到从字母a到z排序后的加密参数数组
        $mysign = self::build_mysign($sort_array, self::$_alipay['security_code'], self::$_alipay['sign_type']);
        if (self::$_alipay['submit_type'] == 'get') {
            return self::create_url($sort_array, $mysign);
        } else {
            return self::build_postform($parameter, $mysign);
        }
    }

    /**
     * 进行支付验签
     */
    public static function notify() {
        self::init();
        if (self::$_alipay['transport'] == "https") {
            self::$_alipay['gateway'] = "https://www.alipay.com/cooperate/gateway.do?";
            $veryfy_url = self::$_alipay['gateway'] . "service=notify_verify" . "&partner=" . self::$_alipay['partner'] . "&notify_id=" . $_POST["notify_id"];
        } else {
            self::$_alipay['gateway'] = "http://notify.alipay.com/trade/notify_query.do?";
            $veryfy_url = self::$_alipay['gateway'] . "partner=" . self::$_alipay['partner'] . "&notify_id=" . $_POST["notify_id"];
        }
        $mysign = "";
        $veryfy_result = self::get_verify($veryfy_url);
        //生成签名结果
        if (empty($_POST)) {//判断POST来的数组是否为空
            return false;
        } else {
            $post = self::para_filter($_POST);        //对所有POST返回的参数去空
            $sort_post = self::arg_sort($post);        //对所有POST反馈回来的数据排序
            $mysign = self::build_mysign($sort_post, self::$_alipay['security_code'], self::$_alipay['sign_type']);   //生成签名结果
            //写日志记录
            self::logstr("veryfy_result=" . $veryfy_result . "\n notify_url_log:sign=" . $_POST["sign"] . "&mysign=" . $mysign . "," . self::create_linkstring($sort_post));

            //判断veryfy_result是否为ture，生成的签名结果mysign与获得的签名结果sign是否一致
            //$veryfy_result的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
            //mysign与sign不等，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
            if (preg_match("/true$/i", $veryfy_result) && $mysign == $_POST["sign"]) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * GET方式进入支付
     */
    public static function create_url($sort_array, $mysign) {
        $arg = self::create_linkstring_urlencode($sort_array); //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $url = self::$_alipay['gateway'];
        //把网关地址、已经拼接好的参数数组字符串、签名结果、签名类型，拼接成最终完整请求url
        $url .= $arg . "&sign=" . $mysign . "&sign_type=" . self::$_alipay['sign_type'];
        return '<script type="text/javascript">window.location ="' . $url . '";</script>';
    }

    /**
     * POST方式进入支付
     */
    public static function build_postform($parameter, $mysign) {
        $sHtml = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>支付宝支付跳转中...</title></head><body onload="document.alipaysubmit.submit();">';
        $sHtml .= "<form name='alipaysubmit' action='" . self::$_alipay['gateway'] . "_input_charset=" . $parameter['_input_charset'] . "' method='post'>";
        while (list ($key, $val) = each($parameter)) {
            $sHtml.= "<input type='hidden' name='" . $key . "' value='" . $val . "'/>";
        }
        $sHtml .= "<input type='hidden' name='sign' value='" . $mysign . "'/>";
        $sHtml .= "<input type='hidden' name='sign_type' value='" . self::$_alipay['sign_type'] . "'/></form></body></html>";
        return $sHtml;
    }

    /**
     * 除去数组中的空值和签名参数
     * $parameter 加密参数组
     * return 去掉空值与签名参数后的新加密参数组
     */
    public static function para_filter($parameter) {
        $para = array();
        foreach ($parameter as $key => $val) {
            if ($key == "sign" || $key == "sign_type" || $val == "")
                continue;
            $para[$key] = $val;
        }
        return $para;
    }

    /**
     * 生成签名结果
     * $array要加密的数组
     * return 签名结果字符串
     */
    public static function build_mysign($sort_array, $security_code, $sign_type = "MD5") {
        $prestr = self::create_linkstring($sort_array); //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = $prestr . $security_code; //把拼接后的字符串再与安全校验码直接连接起来
        $mysgin = self::sign($prestr, $sign_type); //把最终的字符串加密，获得签名结果
        return $mysgin;
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * $array 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    public static function create_linkstring($array) {
        $arg = "";
        while (list ($key, $val) = each($array)) {
            $arg.=$key . "=" . $val . "&";
        }
        $arg = substr($arg, 0, count($arg) - 2); //去掉最后一个&字符
        return $arg;
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * 使用场景：GET方式请求时，对URL的中文进行编码
     * $array 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    public static function create_linkstring_urlencode($array) {
        $arg = "";
        while (list ($key, $val) = each($array)) {
            if ($key != "service" && $key != "_input_charset")
                $arg.=$key . "=" . urlencode($val) . "&";
            else
                $arg.=$key . "=" . $val . "&";
        }
        $arg = substr($arg, 0, count($arg) - 2); //去掉最后一个&字符
        return $arg;
    }

    /**
     * 对数组排序
     * $array 排序前的数组
     * return 排序后的数组
     */
    public static function arg_sort($array) {
        ksort($array);
        reset($array);
        return $array;
    }

    /**
     * 加密字符串
     * $prestr 需要加密的字符串
     * return 加密结果
     */
    public static function sign($prestr, $sign_type) {
        $sign = '';
        if ($sign_type == 'MD5') {
            $sign = md5($prestr);
        } elseif ($sign_type == 'DSA') {
            //DSA 签名方法待后续开发
            die("DSA 签名方法待后续开发，请先使用MD5签名方式");
        } else {
            die("支付宝暂不支持" . $sign_type . "类型的签名方式");
        }
        return $sign;
    }

    /**
     * 用于防钓鱼，调用接口query_timestamp来获取时间戳的处理函数
     * 注意：由于低版本的PHP配置环境不支持远程XML解析，因此必须服务器、本地电脑中装有高版本的PHP配置环境。建议本地调试时使用PHP开发软件
     * $partner 合作身份者ID
     * return 时间戳字符串
     */
    public static function query_timestamp() {
        $URL = "https://mapi.alipay.com/gateway.do?service=query_timestamp&partner=" . self::$_alipay['partner'];
        $encrypt_key = "";
        $doc = new DOMDocument();
        $doc->load($URL);
        $itemEncrypt_key = $doc->getElementsByTagName("encrypt_key");
        $encrypt_key = $itemEncrypt_key->item(0)->nodeValue;
        return $encrypt_key;
    }

    /**
     * 获取远程服务器ATN结果
     * $url 指定URL路径地址
     * return 服务器ATN结果集
     */
    public static function get_verify($url, $time_out = "60") {
        $urlarr = parse_url($url);
        $errno = "";
        $errstr = "";
        $transports = "";
        if ($urlarr["scheme"] == "https") {
            $transports = "ssl://";
            $urlarr["port"] = "443";
        } else {
            $transports = "tcp://";
            $urlarr["port"] = "80";
        }
        $fp = @fsockopen($transports . $urlarr['host'], $urlarr['port'], $errno, $errstr, $time_out);
        if (!$fp) {
            die("ERROR: $errno - $errstr<br />\n");
        } else {
            fputs($fp, "POST " . $urlarr["path"] . " HTTP/1.1\r\n");
            fputs($fp, "Host: " . $urlarr["host"] . "\r\n");
            fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
            fputs($fp, "Content-length: " . strlen($urlarr["query"]) . "\r\n");
            fputs($fp, "Connection: close\r\n\r\n");
            fputs($fp, $urlarr["query"] . "\r\n\r\n");
            while (!feof($fp)) {
                $info[] = @fgets($fp, 1024);
            }
            fclose($fp);
            $info = implode(",", $info);
            return $info;
        }
    }

    public static function logstr($hmac) {
        logger::log("alipay_log", date("Y-m-d H:i:s") . "|[" . $hmac . "]");
    }

}

?>