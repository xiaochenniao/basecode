<?php

class client {

    const GET = 'GET';
    const POST = 'POST';
    const CONTENT_TYPE = 'Content-Type';
    const CONTENT_LENGTH = 'Content-Length';
    const ENC_URLENCODED = 'application/x-www-form-urlencoded; charset=UTF-8';
    const ENC_URLENCODED_NONE = 'application/x-www-form-urlencoded';
    const ENC_FORMDATA = 'multipart/form-data';

    protected static $_file_infodb = null;
    protected $_proxy = null;
    protected $_socket = null;
    protected $_connected_to = array(null, null);
    protected $_use_curl = true;
    protected $_url_path = array();
    protected $_cl = null;
    protected $_timeout = 30;
    protected $_headers = array();
    protected $_files = array();
    protected $_status_code = 0;
    protected $_cookies = array();
    protected $_forwarded = null;
    protected $_charset = null;
    protected $_debug = 0;
    protected $_debug_info = null;
    protected $_agent = null;
    protected $_host_ips = array();
    protected $_response = null;
    protected $_response_header = null;
    protected $_response_body = null;
    protected $_response_headers = array();
    protected $_location = null;
    protected $_is_set_cookie = false;
    protected $_domain_cookie = false;

    public function __construct() {
        $agents = array(
            'Mozilla/5.0 (Windows; U; MSIE 9.0; Windows NT 9.0; en-US)',
            'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; Media Center PC 6.0; InfoPath.3; MS-RTC LM 8; Zune 4.7)',
            'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; Zune 4.0; InfoPath.3; MS-RTC LM 8; .NET4.0C; .NET4.0E)',
            'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET CLR 2.0.50727; Media Center PC 6.0)',
            'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Win64; x64; Trident/5.0; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET CLR 2.0.50727; Media Center PC 6.0)',
            'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Win64; x64; Trident/5.0; .NET CLR 2.0.50727; SLCC2; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; Zune 4.0; Tablet PC 2.0; InfoPath.3; .NET4.0C; .NET4.0E)',
            'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Win64; x64; Trident/5.0',
            'Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 5.1; Trident/5.0)',
            'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.2; Trident/4.0; Media Center PC 4.0; SLCC1; .NET CLR 3.0.04320)',
            'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; SLCC1; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET CLR 1.1.4322)',
            'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; InfoPath.2; SLCC1; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET CLR 2.0.50727)',
            'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 1.1.4322; .NET CLR 2.0.50727)',
            'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.0; Trident/4.0; InfoPath.1; SV1; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET CLR 3.0.04506.30)',
            'Mozilla/5.0 (compatible; MSIE 7.0; Windows NT 5.0; Trident/4.0; FBSMTWB; .NET CLR 2.0.34861; .NET CLR 3.0.3746.3218; .NET CLR 3.5.33652; msn OptimizedIE8;ENUS)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.2; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; Media Center PC 6.0; InfoPath.2; MS-RTC LM 8)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; Media Center PC 6.0; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET4.0C)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; InfoPath.3; .NET4.0C; .NET4.0E; .NET CLR 3.5.30729; .NET CLR 3.0.30729; MS-RTC LM 8)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; InfoPath.2)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; Zune 3.0)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; msn OptimizedIE8;ZHCN)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; MS-RTC LM 8; InfoPath.3; .NET4.0C; .NET4.0E) chromeframe/8.0.552.224',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; MS-RTC LM 8; .NET4.0C; .NET4.0E; Zune 4.7; InfoPath.3)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; MS-RTC LM 8; .NET4.0C; .NET4.0E; Zune 4.7)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; MS-RTC LM 8)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.3; Zune 4.0)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.3; .NET4.0C; .NET4.0E; MS-RTC LM 8; Zune 4.7)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.3)',
            'Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 6.0)',
            'Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.2; .NET CLR 1.1.4322; .NET CLR 2.0.50727; InfoPath.2; .NET CLR 3.0.04506.30)',
            'Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1; Media Center PC 3.0; .NET CLR 1.0.3705; .NET CLR 1.1.4322; .NET CLR 2.0.50727; InfoPath.1)',
            'Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1; FDM; .NET CLR 1.1.4322)',
            'Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1; .NET CLR 1.1.4322; InfoPath.1; .NET CLR 2.0.50727)',
            'Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1; .NET CLR 1.1.4322; InfoPath.1)',
            'Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1; .NET CLR 1.1.4322; Alexa Toolbar; .NET CLR 2.0.50727)',
            'Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1; .NET CLR 1.1.4322; Alexa Toolbar)',
            'Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)',
            'Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.40607)',
            'Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1; .NET CLR 1.1.4322)',
            'Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1; .NET CLR 1.0.3705; Media Center PC 3.1; Alexa Toolbar; .NET CLR 1.1.4322; .NET CLR 2.0.50727)',
            'Mozilla/5.0 (Windows; U; MSIE 7.0; Windows NT 6.0; en-US)',
            'Mozilla/5.0 (Windows; U; MSIE 7.0; Windows NT 6.0; el-GR)',
            'Mozilla/5.0 (MSIE 7.0; Macintosh; U; SunOS; X11; gu; SV1; InfoPath.2; .NET CLR 3.0.04506.30; .NET CLR 3.0.04506.648)',
            'Mozilla/5.0 (compatible; MSIE 7.0; Windows NT 6.0; WOW64; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; c .NET CLR 3.0.04506; .NET CLR 3.5.30707; InfoPath.1; el-GR)',
            'Mozilla/5.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; c .NET CLR 3.0.04506; .NET CLR 3.5.30707; InfoPath.1; el-GR)',
            'Mozilla/5.0 (compatible; MSIE 7.0; Windows NT 6.0; fr-FR)',
            'Mozilla/5.0 (compatible; MSIE 7.0; Windows NT 6.0; en-US)',
            'Mozilla/5.0 (compatible; MSIE 7.0; Windows NT 5.2; WOW64; .NET CLR 2.0.50727)',
            'Mozilla/4.79 [en] (compatible; MSIE 7.0; Windows NT 5.0; .NET CLR 2.0.50727; InfoPath.2; .NET CLR 1.1.4322; .NET CLR 3.0.04506.30; .NET CLR 3.0.04506.648)',
            'Mozilla/4.0 (Windows; MSIE 7.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727)',
            'Mozilla/4.0 (Mozilla/4.0; MSIE 7.0; Windows NT 5.1; FDM; SV1; .NET CLR 3.0.04506.30)',
            'Mozilla/4.0 (Mozilla/4.0; MSIE 7.0; Windows NT 5.1; FDM; SV1)',
            'Mozilla/4.0 (compatible;MSIE 7.0;Windows NT 6.0)',
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; WOW64; SLCC2; .NET CLR 2.0.50727; InfoPath.3; .NET4.0C; .NET4.0E; .NET CLR 3.5.30729; .NET CLR 3.0.30729; MS-RTC LM 8)',
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; WOW64; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; MS-RTC LM 8; .NET4.0C; .NET4.0E; InfoPath.3)',
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; .NET4.0E)',
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0)',
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0;)',
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; YPC 3.2.0; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; InfoPath.2; .NET CLR 3.5.30729; .NET CLR 3.0.30618)',
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; YPC 3.2.0; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506)',
            'Mozilla/4.0 (compatible; MSIE 6.1; Windows XP; .NET CLR 1.1.4322; .NET CLR 2.0.50727)',
            'Mozilla/4.0 (compatible; MSIE 6.1; Windows XP)',
            'Mozilla/4.0 (compatible; MSIE 6.01; Windows NT 6.0)',
            'Mozilla/4.0 (compatible; MSIE 6.0b; Windows NT 5.1; DigExt)',
            'Mozilla/4.0 (compatible; MSIE 6.0b; Windows NT 5.1)',
            'Mozilla/4.0 (compatible; MSIE 6.0b; Windows NT 5.0; YComp 5.0.2.6)',
            'Mozilla/4.0 (compatible; MSIE 6.0b; Windows NT 5.0; YComp 5.0.0.0)',
            'Mozilla/4.0 (compatible; MSIE 6.0b; Windows NT 5.0; .NET CLR 1.1.4322)',
            'Mozilla/4.0 (compatible; MSIE 6.0b; Windows NT 5.0)',
            'Mozilla/4.0 (compatible; MSIE 6.0b; Windows NT 4.0; .NET CLR 1.0.2914)',
            'Mozilla/4.0 (compatible; MSIE 6.0b; Windows NT 4.0)',
            'Mozilla/4.0 (compatible; MSIE 6.0b; Windows 98; YComp 5.0.0.0)',
            'Mozilla/4.0 (compatible; MSIE 6.0b; Windows 98; Win 9x 4.90)',
            'Mozilla/4.0 (compatible; MSIE 6.0b; Windows 98)',
            'Mozilla/4.0 (compatible; MSIE 6.0b; Windows NT 5.1)',
            'Mozilla/4.0 (compatible; MSIE 6.0b; Windows NT 5.0; .NET CLR 1.0.3705)',
            'Mozilla/4.0 (compatible; MSIE 6.0b; Windows NT 4.0)',
            'Mozilla/5.0 (Windows; U; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727)',
            'Mozilla/5.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727)',
            'Mozilla/5.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4325)',
            'Mozilla/5.0 (compatible; MSIE 6.0; Windows NT 5.1)',
            'Mozilla/45.0 (compatible; MSIE 6.0; Windows NT 5.1)',
            'Mozilla/4.08 (compatible; MSIE 6.0; Windows NT 5.1)',
            'Mozilla/4.01 (compatible; MSIE 6.0; Windows NT 5.1)',
            'Mozilla/4.0 (X11; MSIE 6.0; i686; .NET CLR 1.1.4322; .NET CLR 2.0.50727; FDM)',
            'Mozilla/4.0 (Windows; MSIE 6.0; Windows NT 6.0)',
            'Mozilla/4.0 (Windows; MSIE 6.0; Windows NT 5.2)',
            'Mozilla/4.0 (Windows; MSIE 6.0; Windows NT 5.0)',
            'Mozilla/4.0 (Windows; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727)',
            'Mozilla/4.0 (MSIE 6.0; Windows NT 5.1)',
            'Mozilla/4.0 (MSIE 6.0; Windows NT 5.0)',
            'Mozilla/4.0 (compatible;MSIE 6.0;Windows 98;Q312461)',
            'Mozilla/4.0 (Compatible; Windows NT 5.1; MSIE 6.0) (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)',
            'Mozilla/4.0 (compatible; U; MSIE 6.0; Windows NT 5.1)',
            'Mozilla/4.0 (Compatible; MSIE 8.0; Windows NT 6.1; WOW64; SLCC1; .NET CLR 3.0.04506; Windows NT 6.1; Trident/4.0; Mozilla/4.0; MSIE 6.0; Windows NT 5.1; SV1 ; SLCC2; .NET CLR 2.0.50727; Media Center PC 6.0; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NE',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0; Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1) ; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; InfoPath.3; Tablet PC 2.0)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0; GTB6.5; QQDownload 534; Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1) ; SLCC2; .NET CLR 2.0.50727; Media Center PC 6.0; .NET CLR 3.5.30729; .NET CLR 3.0.30729)',
            'Mozilla/4.0 (compatible; MSIE 6,0; Windows NT 5,1; SV1; Alexa Toolbar)',
        );
        $this->_agent = $agents[array_rand($agents)];
        if ($timeout = config::get('client.timeout')) {
            $this->setTimeout($timeout);
        }
    }

    public function setUseCurl($bool = true) {
        $this->_use_curl = $bool;
    }

    public function setAgent($agent) {
        $this->_agent = $agent;
    }

    public function setCharset($charset) {
        $this->_charset = $charset;
    }

    public function getCode() {
        return $this->_status_code;
    }

    public function setHostIp($host = null, $ip = null) {
        if ($host === null) {
            $this->_host_ips = array();
        } elseif ($ip === null) {
            unset($this->_host_ips[$host]);
        } else {
            $this->_host_ips[$host] = $ip;
        }
    }

    public function checkCookie($domain) {
        $this->_domain_cookie = $domain;
    }

    public function isSetCookie() {
        return $this->_is_set_cookie;
    }

    public function getResponse() {
        return $this->_response;
    }

    public function setForwarded($ip) {
        $this->_forwarded = $ip;
    }

    public function getForwarded() {
        return $this->_forwarded;
    }

    public function getBody() {
        return $this->_response_body;
    }

    public function getHeaders() {
        return $this->_response_headers;
    }

    public function setHeader($name, $value = null) {
        $this->_reset();
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                if (is_string($k)) {
                    $this->setHeader($k, $v);
                } else {
                    $this->setHeader($v, null);
                }
            }
        } else {
            if ($value === null && (strpos($name, ':') > 0)) {
                list($name, $value) = explode(':', $name, 2);
            }
            $normalized_name = strtolower($name);
            if ($value === null || $value === false) {
                unset($this->_headers[$normalized_name]);
            } else {
                if (is_string($value)) {
                    $value = trim($value);
                }
                $this->_headers[$normalized_name] = array($name, $value);
            }
        }
    }

    public function setHeaders($name, $value = null) {
        $this->setHeader($name, $value);
    }

    public function getHeader($key) {
        $key = strtolower($key);
        return isset($this->_response_headers[$key]) ? $this->_response_headers[$key] : null;
    }

    public function setProxy($proxy) {
        $this->_proxy = $proxy;
    }

    public function getProxy() {
        return $this->_proxy;
    }

    public function getTimeout() {
        return $this->_timeout;
    }

    public function setTimeout($second) {
        $this->_timeout = $second;
    }

    public function getLocation() {
        $location = $this->getHeader('location');
        if (!$location) {
            return null;
        }
        if (substr($location, 0, 1) == '/') {
            $location = $this->_url_path['scheme'] . '://' . $this->_url_path['host'] . (isset($this->_url_path['port']) ? ':' . $this->_url_path['port'] : '') . $location;
        } elseif (!preg_match("#^(http|https)://#i", $location)) {
            $location = $this->_url_path['scheme'] . '://' . $this->_url_path['host'] . (isset($this->_url_path['port']) ? ':' . $this->_url_path['port'] : '') . substr($this->_url_path['path'], 0, strrpos($this->_url_path['path'], '/') + 1) . $location;
        }
        return $location;
    }

    public function setCookie($name, $value = null, $domain = null, $expires = null, $encode = true) {
        if ($value !== null) {
            if ($encode) {
                $value = urlencode($value);
            }
            if ($expires !== null) {
                $expires = time() + $expires;
            }
            if ($domain === null) {
                $domain = '*';
            }
            $cookie = array($name, $value, $domain, $expires, '/');
            $this->_addCookie($cookie);
        } else {
            $this->_delCookie($name, $domain);
        }
    }

    function getCookie($key, $default = null, $host = null) {
        foreach ($this->_cookies as $domain => $val) {
            if ($host && strpos($host, $domain) === false) {
                continue;
            }
            if (isset($this->_cookies[$domain][$key])) {
                list($name, $value, $dn, $expires, $path) = $this->_cookies[$domain][$key];
                if ($expires > 0 && $expires < time()) {
                    return $default;
                }
                return $value;
            }
        }
        return $default;
    }

    function getCookies() {
        return $this->_cookies;
    }

    function setCookies($cookies) {
        if (is_array($cookies)) {
            $this->_cookies = $cookies;
        }
    }

    public function get($url) {
        return $this->_do(self::GET, $url);
    }

    public function post($url, $post = array()) {
        return $this->_do(self::POST, $url, $post);
    }

    protected function _do($method, $url, $post = array()) {
        $this->_reset();
        $response = null;
        $url = str_replace('&amp;', '&', $url);
        $pa = @parse_url($url);
        $this->_url_path = $pa;
        if (!isset($pa['host'])) {
            throw new except($url . ":Invalid request url, host required", 500);
        }
        if (!isset($pa['port'])) {
            if ($pa['scheme'] == 'https') {
                $pa['port'] = 443;
            } else {
                $pa['port'] = 80;
            }
        }
        if (!isset($pa['path'])) {
            $pa['path'] = '/';
            $url .= '/';
        }

        $host = strtolower($pa['host']);
        $port = intval($pa['port']);

        if ($this->_use_curl) {
            $this->_cl = curl_init();
            curl_setopt($this->_cl, CURLOPT_URL, $url);
            curl_setopt($this->_cl, CURL_HTTP_VERSION_1_1, true);
            curl_setopt($this->_cl, CURLOPT_FOLLOWLOCATION, false);
            //curl_setopt($this->_cl, CURLOPT_SSL_VERIFYPEER, true);
            //curl_setopt($this->_cl, CURLOPT_CAINFO, './https.pem'); /* fixed! */
            curl_setopt($this->_cl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($this->_cl, CURLOPT_SSL_VERIFYPEER, false);
            //curl_setopt($this->_cl, CURLOPT_FORBID_REUSE, true);
            //curl_setopt($this->_cl, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($this->_cl, CURLOPT_HEADER, true);
            //curl_setopt($this->_cl, CURLINFO_HEADER_OUT, true);
            curl_setopt($this->_cl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->_cl, CURLOPT_CONNECTTIMEOUT, $this->_timeout);
            curl_setopt($this->_cl, CURLOPT_TIMEOUT, $this->_timeout);
        }

        $timeout = $this->_timeout;

        $body = '';
        if (count($this->_files) > 0) {
            if ($this->_proxy && $this->_timeout < 120) {
                if ($this->_use_curl) {
                    curl_setopt($this->_cl, CURLOPT_CONNECTTIMEOUT, 120);
                    curl_setopt($this->_cl, CURLOPT_TIMEOUT, 120);
                } else {
                    $timeout = 120;
                }
            } else {
                if ($this->_use_curl) {
                    curl_setopt($this->_cl, CURLOPT_CONNECTTIMEOUT, 12);
                    curl_setopt($this->_cl, CURLOPT_TIMEOUT, 12);
                } else {
                    $timeout = 12;
                }
            }
            $boundary = '--------' . md5(microtime());
            $this->setHeader(self::CONTENT_TYPE, self::ENC_FORMDATA . "; boundary={$boundary}");
            $params = self::_flattenPost($post);
            foreach ($params as $pp) {
                $body .= self::_encodeFormdata($boundary, $pp[0], $pp[1]);
            }
            foreach ($this->_files as $file) {
                $fhead = array(self::CONTENT_TYPE => $file['ctype']);
                $body .= self::_encodeFormdata($boundary, $file['formname'], $file['data'], $file['filename'], $fhead);
            }
            $body .= "--{$boundary}--\r\n";
        } elseif (is_array($post) && count($post) > 0) {
            if ($this->_charset == 'none') {
                $this->setHeader(self::CONTENT_TYPE, self::ENC_URLENCODED_NONE);
            } else {
                $this->setHeader(self::CONTENT_TYPE, self::ENC_URLENCODED);
            }
            //$this->setHeader(self::CONTENT_TYPE, self::ENC_URLENCODED);
            $body = http_build_query($post, '', '&');
        } elseif (!empty($post)) {
            if ($this->_charset == 'none') {
                $this->setHeader(self::CONTENT_TYPE, self::ENC_URLENCODED_NONE);
            } else {
                $this->setHeader(self::CONTENT_TYPE, self::ENC_URLENCODED);
            }
            $body = $post;
        }
        if ($body || $method == self::POST) {
            if ($this->_use_curl) {
                curl_setopt($this->_cl, CURLOPT_POSTFIELDS, $body);
            } else {
                $this->setHeader(self::CONTENT_LENGTH, strlen($body));
            }
        }

        $headers = array();
        if ($this->_use_curl) {
            $headers[] = 'Expect: ';
        }
        if (!isset($this->_headers['keep-alive'])) {
            $headers[] = 'Keep-Alive: 115';
        }
        if (!isset($this->_headers['connection'])) {
            $headers[] = 'Connection: keep-alive';
        }
        if (!isset($this->_headers['pragma'])) {
            $headers[] = 'Pragma: no-cache';
        }

        if (!isset($this->_headers['accept-encoding'])) {
            if (function_exists('gzinflate')) {
                $headers[] = 'Accept-encoding: gzip, deflate';
            } else {
                $headers[] = 'Accept-encoding: identity';
            }
        }

        $now = time();
        $cookstr = '';
        $_cookarr = array();
        foreach ($this->_cookies as $domain => $ck_list) {
            if ($domain == '*') {
                
            } elseif ($this->_domain_cookie) {
                if (strpos($domain, $this->_domain_cookie) === false) {
                    continue;
                }
            } elseif (strpos($host, $domain) === false) {
                continue;
            }
            foreach ($ck_list as $name => $cookie) {
                if ($cookie[3] > 0 && $cookie[3] < $now) {
                    unset($this->_cookies[$domain][$name]);
                    continue;
                }
                if (substr($pa['path'], 0, strlen($cookie[4])) != $cookie[4]) {
                    continue;
                }
                if (isset($_cookarr[$name])) {
                    continue;
                }
                $_cookarr[$name] = $cookie[1];
                $cookstr .= '; ' . $name . '=' . $cookie[1];
            }
        }
        if ($cookstr) {
            $headers[] = 'Cookie:' . substr($cookstr, 1);
        }

        if (!isset($this->_headers['user-agent'])) {
            $headers[] = 'User-Agent: ' . $this->_agent;
        }

        foreach ($this->_headers as $header) {
            list($name, $value) = $header;
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $headers[] = ucfirst($name) . ': ' . $value;
        }

        if ($this->_forwarded) {
            $headers[] = 'X-Forwarded-For: ' . $this->_forwarded;
        }

        if ($this->_proxy) {
            if ($this->_use_curl) {
                $headers[] = 'Proxy-Connection: ';
                curl_setopt($this->_cl, CURLOPT_PROXY, $this->_proxy);
            } else {
                list($host, $port) = explode(':', $this->_proxy);
                $this->_connect($host, $port, $timeout);
            }
            $request_url = $url;
        } else {
            if (isset($pa['query']))
                $pa['path'] .= '?' . $pa['query'];
            $request_url = $pa['path'];
            $headers[] = 'Host: ' . $host;
            if (isset($this->_host_ips[$host])) {
                if ($this->_use_curl) {
                    $path = $this->_url_path['path'];
                    if (!empty($this->_url_path['query'])) {
                        $path .= "?" . $this->_url_path['query'];
                    }
                    curl_setopt($this->_cl, CURLOPT_URL, $this->_url_path['scheme'] . "://" . $this->_host_ips[$host] . $path);
                } else {
                    $this->_connect($this->_host_ips[$host], $port);
                }
            } else {
                if (!$this->_use_curl) {
                    $this->_connect($host, $port);
                }
            }
        }

        if ($this->_use_curl) {
            $debug_type = 'curl';
            curl_setopt($this->_cl, CURLOPT_HTTPHEADER, $headers);
            $this->_response = curl_exec($this->_cl);
            $request_header = curl_getinfo($this->_cl, CURLINFO_HEADER_OUT);
        } else {
            $debug_type = 'sock';
            $request_header = $this->_write($method, $request_url, $headers, $body);
            $this->_response = $this->_read();
        }


        $send_info = "[SEND] \r\n";
        $send_info .= $request_header;
        if ($this->_proxy) {
            $send_info .= "\r\n------use proxy---" . $this->_proxy . "---------";
        }
        $send_info .="\r\n$debug_type----------------------------------------------------\r\n";

        if ($this->_debug) {
            echo $send_info;
        }

        $this->_debug_info = $send_info;

        if (!$this->_response) {
            if ($this->_use_curl) {
                $cl_error = curl_error($this->_cl);
                curl_close($this->_cl);
                throw new except('curl error:' . $cl_error, -100);
            } else {
                $this->_close();
                throw new except('sock error', -100);
            }
        }
        if ($this->_use_curl) {
            curl_close($this->_cl);
        } else {
            $this->_close();
        }

        $response_header = '';
        $response_body = '';
        do {
            $parts = preg_split('|(?:\r?\n){2}|m', $this->_response, 2);
            $again = false;
            if (isset($parts[1]) && preg_match("|^HTTP/1\.[01](.*?)\r\n|mi", $parts[1])) {
                $this->_response = $parts[1];
                $again = true;
            }
        } while ($again);

        $response_header = $parts[0];
        $response_body = $parts[1];
        unset($parts);
        $this->_response_header = $response_header;

        $recv_info = "[RECV] \r\n";
        $recv_info .= $response_header . "\r\n";
        if ($this->_proxy) {
            $recv_info .= "\r\n------use proxy---" . $this->_proxy . "---------";
        }
        $recv_info .= "\r\n$debug_type----------------------------------------------------\r\n";

        if ($this->_debug) {
            echo $recv_info;
        }

        $this->_debug_info .= $recv_info;


        if (preg_match("|^HTTP/[\d\.x]+ (\d+)|", $response_header, $m)) {
            $this->_status_code = (int) $m[1];
        }
        $headers = array();
        if ($response_header) {
            $lines = explode("\n", $response_header);
            unset($response_header);
            $last_header = null;
            foreach ($lines as $line) {
                $line = trim($line, "\r\n");
                if ($line == "")
                    break;
                if (preg_match("|^([\w-]+):\s*(.+)|", $line, $m)) {
                    unset($last_header);
                    $h_name = strtolower($m[1]);
                    $h_value = $m[2];
                    if (isset($headers[$h_name])) {
                        if (!is_array($headers[$h_name])) {
                            $headers[$h_name] = array($headers[$h_name]);
                        }
                        $headers[$h_name][] = $h_value;
                    } else {
                        $headers[$h_name] = $h_value;
                    }
                    $last_header = $h_name;
                } elseif (preg_match("|^\s+(.+)$|", $line, $m) && $last_header !== null) {
                    if (is_array($headers[$last_header])) {
                        end($headers[$last_header]);
                        $last_header_key = key($headers[$last_header]);
                        $headers[$last_header][$last_header_key] .= $m[1];
                    } else {
                        $headers[$last_header] .= $m[1];
                    }
                }
            }
        }
        $this->_response_headers = $headers;
        $this->_addCookiesResponse();

        if ($this->_status_code == 304 || $this->_status_code == 204) {
            return $this->_response_body;
        }

        switch (strtolower($this->getHeader('content-encoding'))) {
            case 'gzip':
                $this->_response_body = self::_decodeGzip($response_body);
                break;
            case 'deflate':
                $this->_response_body = self::_decodeDeflate($response_body);
                break;
            default:
                $this->_response_body = $response_body;
                break;
        }

        if (strpos($this->_response_headers['content-type'], 'xml') !== false || strpos($this->_response_headers['content-type'], 'text') !== false || strpos($this->_response_headers['content-type'], 'json') !== false) {
            $this->_debug_info .= "\r\n" . $this->_response_body . "\n";
            if ($this->_debug > 2) {
                echo "\r\n" . $this->_response_body . "\n";
            }
        } else {
            $this->_debug_info .= "\r\n" . $this->_response_headers['content-type'] . ".............................\n";
            if ($this->_debug > 2) {
                echo "\r\n" . $this->_response_headers['content-type'] . ".............................\n";
            }
        }
        return $this->_response_body;
    }

    protected function _reset() {
        if ($this->_status_code != 0) {
            $this->_status_code = 0;
            $this->_files = array();
            $this->_headers = array();
            $this->_response = null;
            $this->_response_header = null;
            $this->_response_body = null;
            $this->_response_headers = array();
            $this->_location = null;
            $this->_is_set_cookie = false;
        }
    }

    public function setFiles($formname, $file_data, $data = null, $ctype = null) {
        $this->_reset();
        $mimes = array(
            'gif' => 'image/gif',
            'png' => 'image/png',
            'bmp' => 'image/bmp',
            'jpeg' => 'image/jpeg',
            'pjpg' => 'image/pjpg',
            'jpg' => 'image/jpeg',
            'tif' => 'image/tiff',
            'htm' => 'text/html',
            'css' => 'text/css',
            'html' => 'text/html',
            'txt' => 'text/plain',
            'gz' => 'application/x-gzip',
            'tgz' => 'application/x-gzip',
            'tar' => 'application/x-tar',
            'zip' => 'application/zip',
            'hqx' => 'application/mac-binhex40',
            'doc' => 'application/msword',
            'pdf' => 'application/pdf',
            'ps' => 'application/postcript',
            'rtf' => 'application/rtf',
            'dvi' => 'application/x-dvi',
            'latex' => 'application/x-latex',
            'swf' => 'application/x-shockwave-flash',
            'tex' => 'application/x-tex',
            'mid' => 'audio/midi',
            'au' => 'audio/basic',
            'mp3' => 'audio/mpeg',
            'ram' => 'audio/x-pn-realaudio',
            'ra' => 'audio/x-realaudio',
            'rm' => 'audio/x-pn-realaudio',
            'wav' => 'audio/x-wav',
            'wma' => 'audio/x-ms-media',
            'wmv' => 'video/x-ms-media',
            'mpg' => 'video/mpeg',
            'mpga' => 'video/mpeg',
            'wrl' => 'model/vrml',
            'mov' => 'video/quicktime',
            'avi' => 'video/x-msvideo'
        );
        if (is_array($file_data) && isset($file_data['name']) && isset($file_data['data'])) {
            $data = $file_data['data'];
            $filename = $file_data['name'];
        } else {
            $filename = $file_data;
        }
        if ($data === null) {
            if (strpos(basename($filename), '.') === false) {
                $filename .= '.jpg';
            }
            if (substr($filename, 0, 4) == 'http') {
                $cl = curl_init($filename);
                curl_setopt($cl, CURLOPT_CONNECTTIMEOUT, 30);
                curl_setopt($cl, CURLOPT_TIMEOUT, 60);
                curl_setopt($cl, CURLOPT_HEADER, false);
                curl_setopt($cl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($cl, CURL_HTTP_VERSION_1_1, true);
                curl_setopt($cl, CURLOPT_FOLLOWLOCATION, false);
                curl_setopt($cl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($cl, CURLOPT_SSL_VERIFYPEER, false);
                $data = curl_exec($cl);
                curl_close($cl);
            } else {
                $data = @file_get_contents($filename);
            }
            if (!$data) {
                //return false;
                throw new except("Unable to read file '{$filename}' for upload", 500);
            }
        }
        if (!$ctype) {
            $ext = strtolower(substr($filename, strrpos($filename, '.') + 1));
            if (isset($mimes[$ext]))
                $ctype = $mimes[$ext];
            if (!$ctype) {
                $ctype = 'application/octet-stream';
            }
        }
        $this->_enctype = true;
        $this->_files[] = array(
            'formname' => $formname,
            'filename' => basename($filename),
            'ctype' => $ctype,
            'data' => $data
        );
    }

    protected static function _encodeFormdata($boundary, $name, $value, $filename = null, $headers = array()) {
        $ret = "--{$boundary}\r\n" . 'Content-Disposition: form-data; name="' . $name . '"';

        if ($filename) {
            $ret .= '; filename="' . $filename . '"';
        }
        $ret .= "\r\n";

        foreach ($headers as $hname => $hvalue) {
            $ret .= "{$hname}: {$hvalue}\r\n";
        }
        $ret .= "\r\n";

        $ret .= "{$value}\r\n";
        return $ret;
    }

    protected static function _flattenPost($parray, $prefix = null) {
        if (!is_array($parray)) {
            return $parray;
        }
        $parameters = array();
        foreach ($parray as $name => $value) {
            if ($prefix) {
                if (is_int($name)) {
                    $key = $prefix . '[]';
                } else {
                    $key = $prefix . "[$name]";
                }
            } else {
                $key = $name;
            }
            if (is_array($value)) {
                $parameters = array_merge($parameters, self::_flattenPost($value, $key));
            } else {
                $parameters[] = array($key, $value);
            }
        }
        return $parameters;
    }

    protected static function _decodeGzip($body) {
        return gzinflate(substr($body, 10));
    }

    protected static function _decodeDeflate($body) {
        $zlibHeader = unpack('n', substr($body, 0, 2));
        if ($zlibHeader[1] % 31 == 0) {
            return gzuncompress($body);
        } else {
            return gzinflate($body);
        }
    }

    protected function _addCookiesResponse() {
        $cookie_hdrs = $this->getHeader('Set-Cookie');
        if (is_array($cookie_hdrs)) {
            foreach ($cookie_hdrs as $cookie) {
                $this->_addCookie($cookie);
            }
        } elseif (is_string($cookie_hdrs)) {
            $this->_addCookie($cookie_hdrs);
        }
    }

    protected function _addCookie($cookie) {
        if (is_string($cookie)) {
            $cookie = self::_cookieString($cookie);
        }
        if (!$cookie) {
            return false;
        }
        list($name, $value, $domain, $expires, $path) = $cookie;
        if ($value === 'deleted') {
            if (isset($this->_cookies[$domain][$name]))
                unset($this->_cookies[$domain][$name]);
        }else {
            if (!isset($this->_cookies[$domain]))
                $this->_cookies[$domain] = array();
            $this->_cookies[$domain][$name] = $cookie;
        }
        $this->_is_set_cookie = true;
    }

    protected function _delCookie($name, $host = null) {
        foreach ($this->_cookies as $domain => $val) {
            if ($host !== null) {
                if (strpos($host, $domain) === false) {
                    continue;
                }
            }
            if (isset($this->_cookies[$domain][$name]))
                unset($this->_cookies[$domain][$name]);
        }
    }

    protected static function _cookieString($cookieStr) {
        $name = '';
        $value = '';
        $domain = '*';
        $path = '/';
        $expires = null;
        $parts = explode(';', $cookieStr);
        if (strpos($parts[0], '=') === false)
            return false;
        list($name, $value) = explode('=', trim(array_shift($parts)), 2);
        $name = trim($name);
        $value = trim($value);
        foreach ($parts as $part) {
            $part = trim($part);
            if (strtolower($part) == 'secure') {
                continue;
            }
            $keyValue = explode('=', $part, 2);
            if (count($keyValue) == 2) {
                list($k, $v) = $keyValue;
                switch (strtolower($k)) {
                    case 'expires':
                        $expires = strtotime($v);
                        break;
                    case 'path':
                        $path = $v;
                        break;
                    case 'domain':
                        $domain = trim($v, '.');
                        break;
                    default:
                        break;
                }
            }
        }

        if ($name !== '') {
            return array($name, $value, $domain, $expires, $path);
        } else {
            return false;
        }
    }

    public function debug($level = 0) {
        $this->_debug = $level;
    }

    public function getDebugInfo() {
        return $this->_debug_info;
    }

    protected function _connect($host, $port = 80, $timeout = null) {
        if ($port == 443) {
            $host = 'ssl://' . $host;
        }
        if (($this->_connected_to[0] != $host || $this->_connected_to[1] != $port)) {
            if (is_resource($this->_socket))
                $this->_close();
        }
        if ($timeout === null) {
            $timeout = $this->_timeout;
        }
        $retry = 2;
        while (--$retry) {
            if ($this->_socket = fsockopen($host, $port, $errno, $errstr, 5)) {
                stream_set_timeout($this->_socket, (int) $timeout);
                break;
            }
            sleep(1);
        }
        if (!$this->_socket) {
            $this->_close();
            throw new except('Unable to Connect to ' . $host . ':' . $port . '. Error #' . $errno . ': ' . $errstr, -100);
        }
        $this->_connected_to = array($host, $port);
    }

    protected function _write($method, $url, $headers = array(), $body = '') {
        $request = "$method $url HTTP/1.1\r\n";
        foreach ($headers as $k => $v) {
            if (is_string($k))
                $v = "$k: $v";
            $request .= "$v\r\n";
        }
        $header = $request;
        $request .= "\r\n" . $body;
        if (!@fwrite($this->_socket, $request)) {
            throw new except('Error writing request to server', -100);
        }
        if (count($this->_files) > 0) {
            return $header;
        }
        return $request;
    }

    protected function _read() {
        $response = '';
        $got_status = false;

        while (($line = fgets($this->_socket)) !== false) {
            //echo $line;
            $got_status = $got_status || (strpos($line, 'HTTP') !== false);
            if ($got_status) {
                $response .= $line;
                if (rtrim($line) === '')
                    break;
            }
        }

        if (preg_match("|^HTTP/[\d\.x]+ (\d+)|", $response, $m)) {
            $this->_status_code = (int) $m[1];
        } else {
            throw new except('get status code faild', -100);
        }

        if ($this->_status_code == 100 || $this->_status_code == 101)
            return $this->_read();

        if ($this->_status_code == 304 || $this->_status_code == 204) {
            if (isset($headers['connection']) && $headers['connection'] == 'close') {
                $this->_close();
            }
            return $response;
        }

        $headers = array();
        $parts = preg_split('|(?:\r?\n){2}|m', $response, 2);
        if ($parts[0]) {
            $lines = explode("\n", $parts[0]);
            unset($parts);
            $last_header = null;
            foreach ($lines as $line) {
                $line = trim($line, "\r\n");
                if ($line == "")
                    break;
                if (preg_match("|^([\w-]+):\s*(.+)|", $line, $m)) {
                    unset($last_header);
                    $h_name = strtolower($m[1]);
                    $h_value = $m[2];
                    if (isset($headers[$h_name])) {
                        if (!is_array($headers[$h_name])) {
                            $headers[$h_name] = array($headers[$h_name]);
                        }
                        $headers[$h_name][] = $h_value;
                    } else {
                        $headers[$h_name] = $h_value;
                    }
                    $last_header = $h_name;
                } elseif (preg_match("|^\s+(.+)$|", $line, $m) && $last_header !== null) {
                    if (is_array($headers[$last_header])) {
                        end($headers[$last_header]);
                        $last_header_key = key($headers[$last_header]);
                        $headers[$last_header][$last_header_key] .= $m[1];
                    } else {
                        $headers[$last_header] .= $m[1];
                    }
                }
            }
        }

        if (isset($headers['transfer-encoding'])) {
            if (strtolower($headers['transfer-encoding']) == 'chunked') {
                do {
                    $line = @fgets($this->_socket);
                    $this->_checkSocketReadTimeout();
                    $chunk = $line;
                    // Figure out the next chunk size
                    $chunksize = trim($line);
                    if (!ctype_xdigit($chunksize)) {
                        $this->_close();
                        throw new except('Invalid chunk size "' . $chunksize . '" unable to read chunked body', -100);
                    }
                    $chunksize = hexdec($chunksize);
                    $read_to = ftell($this->_socket) + $chunksize;
                    do {
                        $current_pos = ftell($this->_socket);
                        if ($current_pos >= $read_to)
                            break;
                        $line = @fread($this->_socket, $read_to - $current_pos);
                        if ($line === false || strlen($line) === 0) {
                            $this->_checkSocketReadTimeout();
                            break;
                        }
                        $chunk .= $line;
                    } while (!feof($this->_socket));

                    $chunk .= @fgets($this->_socket);
                    $this->_checkSocketReadTimeout();
                    $response .= $chunk;
                } while ($chunksize > 0);
            } else {
                $this->_close();
                throw new except('Cannot handle "' . $headers['transfer-encoding'] . '" transfer encoding', -100);
            }
        } elseif (isset($headers['content-length'])) {
            if (is_array($headers['content-length'])) {
                $contentLength = $headers['content-length'][count($headers['content-length']) - 1];
            } else {
                $contentLength = $headers['content-length'];
            }
            $current_pos = ftell($this->_socket);
            $chunk = '';
            for ($read_to = $current_pos + $contentLength; $read_to > $current_pos; $current_pos = ftell($this->_socket)) {
                $chunk = @fread($this->_socket, $read_to - $current_pos);
                if ($chunk === false || strlen($chunk) === 0) {
                    $this->_checkSocketReadTimeout();
                    break;
                }
                $response .= $chunk;
                if (feof($this->_socket))
                    break;
            }
        } else {
            do {
                $buff = @fread($this->_socket, 8192);
                if ($buff === false || strlen($buff) === 0) {
                    $this->_checkSocketReadTimeout();
                    break;
                } else {
                    $response .= $buff;
                }
            } while (feof($this->_socket) === false);
            $this->_close();
        }
        if (isset($headers['connection']) && $headers['connection'] == 'close') {
            $this->_close();
        }
        return $response;
    }

    protected function _close() {
        if (is_resource($this->_socket))
            @fclose($this->_socket);
        $this->_socket = null;
        $this->_connected_to = array(null, null);
    }

    protected function _checkSocketReadTimeout() {
        if ($this->_socket) {
            $info = stream_get_meta_data($this->_socket);
            $timedout = $info['timed_out'];
            if ($timedout) {
                $this->_close();
                throw new except('Read timed out after ' . $this->_timeout . ' seconds', -100);
            }
        }
    }

}
