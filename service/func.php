<?php

class func {

    public static function startProcess($script_name, $process = 0, $args = null, $r = array()) {
        if (is_array($script_name)) {
            $grep_t = '';
            $gn = 0;
            foreach ($script_name as $var) {
                if ($gn == 0) {
                    $grep_t .= '|grep "' . $var . '"';
                } else {
                    $grep_t .= '|grep "=' . $var . ' \|=' . $var . '$"';
                }
                $gn++;
            }
            $has_process = exec('ps aux' . $grep_t . '|grep -v "grep"|wc -l');
            $script_name = $script_name[0];
        } else {
            $has_process = exec('ps aux|grep "' . $script_name . '"|grep -v "grep"|wc -l');
        }
        if ($has_process > $process) {
            return 0;
        }
        $maxn = $process - $has_process + 1;
        if ($maxn < 1) {
            return 0;
        }
        if (is_array($args)) {
            $args_t = '';
            foreach ($args as $k => $v) {
                $args_t .= '--' . $k . '=' . $v . ' ';
            }
        } else {
            $args_t = $args;
        }
        for ($i = 0; $i < $maxn; $i++) {
            if ($r && $r[$i]) {
                $ids = '--ids=' . trim(implode($r[$i], ","), ',');
                exec('./crond.php ' . $script_name . ' ' . $args_t . ' ' . $ids . ' > /dev/null 2>&1 &');
            } else {
                exec('./crond.php ' . $script_name . ' ' . $args_t . ' > /dev/null 2>&1 &');
            }
        }
        return $maxn;
    }

    public static function rand($type = 1) {
        if ($type === 1) {
            list($u, $s) = explode(' ', microtime());
            return $s . substr($u, 2, 3);
        } elseif ($type === 0) {
            $str = '1234567890';
            $rand = '0.' . rand(1, 9);
            for ($i = 0; $i < 15; $i++) {
                $rand .= $str{rand(0, 9)};
            }
            return $rand;
        } elseif ($type == 3) {
            $str = 'abcdefghigklmnopqrstuvwxyz0987654321abcdefghigklmnopqrstuvwxyz';
            $rand = '';
            $rrrr = rand(2, 4);
            for ($j = 0; $j < $rrrr; $j++) {
                $rand .= $str{rand(0, 60)};
            }
            return $rand;
        } elseif ($type === 9) {
            $str = '1234567890';
            $rand = '';
            for ($i = 0; $i < 9; $i++) {
                $rand .= $str{rand(0, 9)};
            }
            return $rand;
        } elseif ($type === 11) {
            $str = 'abcdefghigklmnopqrstuvwxyz0987654321abcdefghigklmnopqrstuvwxyz';
            $rstr = '';
            for ($i = 0; $i < 11; $i++) {
                $rstr .= $str{rand(0, 60)};
            }
            return $rstr;
        } elseif ($type === 'pwd') {
            $str = 'abcdefghigklmnopqrstuvwxyzabcdefghigklmnopqrstuvwxyz';
            $rstr = '';
            $rand = rand(7, 13);
            for ($i = 0; $i < $rand; $i++) {
                $rstr .= $str{rand(0, 47)};
            }
            if (rand(1, 2) === 2) {
                $rstr .= rand(1, 99999);
            }
            return $rstr;
        } elseif ($type === 'un') {
            $str = 'abcdefghigklmnopqrstuvwxyzabcdefghigklmnopqrstuvwxyz';
            $rstr = $str{rand(0, 24)};
            $rand = rand(8, 15);
            for ($i = 0; $i < $rand; $i++) {
                $rstr .= $str{rand(0, 47)};
            }
            return $rstr;
        } elseif ($type === 'ip') {
            return rand(58, 222) . '.' . rand(1, 250) . '.' . rand(1, 250) . '.' . rand(1, 250);
        }
    }

    public static function formhash($formhash = false) {
        if ($formhash === false) {
            $formhash = md5(microtime(true));
            session::set('formhash', $formhash);
            return $formhash;
        } else {
            if (session::get('formhash') == $formhash) {
                session::set('formhash', null);
                return true;
            }
            return false;
        }
    }

    public static function json_decode($encodedValue, $objectDecodeType = true) {
        return Zend_Json_Decoder::decode($encodedValue, $objectDecodeType);
    }

    public static function tableHash($table, $max = 200, $len = 5) {
        $u = substr($table, 0, $len);
        $h = sprintf("%u", crc32($u));
        $h1 = intval($h / $max);
        $h2 = $h1 % $max + 1;
        return $h2;
    }

}

class Zend_Json_Decoder {

    const EOF = 0;
    const DATUM = 1;
    const LBRACE = 2;
    const LBRACKET = 3;
    const RBRACE = 4;
    const RBRACKET = 5;
    const COMMA = 6;
    const COLON = 7;

    protected $_source;
    protected $_sourceLength;
    protected $_offset;
    protected $_token;
    protected $_decodeType;

    protected function __construct($source, $decodeType) {
        $this->_source = self::decodeUnicodeString($source);
        $this->_sourceLength = strlen($this->_source);
        $this->_token = self::EOF;
        $this->_offset = 0;
        $this->_decodeType = $decodeType;
        $this->_getNextToken();
    }

    public static function decode($source = null, $objectDecodeType = true) {
        if (null === $source) {
            return null;
        } elseif (!is_string($source)) {
            return null;
        }
        try {
            $decoder = new self($source, $objectDecodeType);
            return $decoder->_decodeValue();
        } catch (except $e) {
            return null;
        }
    }

    /**
     * Recursive driving rountine for supported toplevel tops
     *
     * @return mixed
     */
    protected function _decodeValue() {
        switch ($this->_token) {
            case self::DATUM:
                $result = $this->_tokenValue;
                $this->_getNextToken();
                return($result);
                break;
            case self::LBRACE:
                return($this->_decodeObject());
                break;
            case self::LBRACKET:
                return($this->_decodeArray());
                break;
            default:
                return null;
                break;
        }
    }

    protected function _decodeObject() {
        $members = array();
        $tok = $this->_getNextToken();

        while ($tok && $tok != self::RBRACE) {
            if ($tok != self::DATUM || !is_string($this->_tokenValue)) {
                throw new except('Missing key in object encoding: ' . $this->_source);
            }

            $key = $this->_tokenValue;
            $tok = $this->_getNextToken();

            if ($tok != self::COLON) {
                throw new except('Missing ":" in object encoding: ' . $this->_source);
            }

            $tok = $this->_getNextToken();
            $members[$key] = $this->_decodeValue();
            $tok = $this->_token;

            if ($tok == self::RBRACE) {
                break;
            }

            if ($tok != self::COMMA) {
                throw new except('Missing "," in object encoding: ' . $this->_source);
            }

            $tok = $this->_getNextToken();
        }

        $result = $members;

        $this->_getNextToken();
        return $result;
    }

    /**
     * Decodes a JSON array format:
     *    [element, element2,...,elementN]
     *
     * @return array
     */
    protected function _decodeArray() {
        $result = array();
        $starttok = $tok = $this->_getNextToken(); // Move past the '['
        $index = 0;

        while ($tok && $tok != self::RBRACKET) {
            $result[$index++] = $this->_decodeValue();

            $tok = $this->_token;

            if ($tok == self::RBRACKET || !$tok) {
                break;
            }

            if ($tok != self::COMMA) {
                throw new except('Missing "," in array encoding: ' . $this->_source);
            }

            $tok = $this->_getNextToken();
        }

        $this->_getNextToken();
        return($result);
    }

    /**
     * Removes whitepsace characters from the source input
     */
    protected function _eatWhitespace() {
        if (preg_match(
                        '/([\t\b\f\n\r ])*/s', $this->_source, $matches, PREG_OFFSET_CAPTURE, $this->_offset) && $matches[0][1] == $this->_offset) {
            $this->_offset += strlen($matches[0][0]);
        }
    }

    /**
     * Retrieves the next token from the source stream
     *
     * @return int Token constant value specified in class definition
     */
    protected function _getNextToken() {
        $this->_token = self::EOF;
        $this->_tokenValue = null;
        $this->_eatWhitespace();

        if ($this->_offset >= $this->_sourceLength) {
            return(self::EOF);
        }

        $str = $this->_source;
        $str_length = $this->_sourceLength;
        $i = $this->_offset;
        $start = $i;

        switch ($str{$i}) {
            case '{':
                $this->_token = self::LBRACE;
                break;
            case '}':
                $this->_token = self::RBRACE;
                break;
            case '[':
                $this->_token = self::LBRACKET;
                break;
            case ']':
                $this->_token = self::RBRACKET;
                break;
            case ',':
                $this->_token = self::COMMA;
                break;
            case ':':
                $this->_token = self::COLON;
                break;
            case '"':
                $result = '';
                do {
                    $i++;
                    if ($i >= $str_length) {
                        break;
                    }

                    $chr = $str{$i};

                    if ($chr == '\\') {
                        $i++;
                        if ($i >= $str_length) {
                            break;
                        }
                        $chr = $str{$i};
                        switch ($chr) {
                            case '"' :
                                $result .= '"';
                                break;
                            case '\\':
                                $result .= '\\';
                                break;
                            case '/' :
                                $result .= '/';
                                break;
                            case 'b' :
                                $result .= "\x08";
                                break;
                            case 'f' :
                                $result .= "\x0c";
                                break;
                            case 'n' :
                                $result .= "\x0a";
                                break;
                            case 'r' :
                                $result .= "\x0d";
                                break;
                            case 't' :
                                $result .= "\x09";
                                break;
                            case '\'' :
                                $result .= '\'';
                                break;
                            default:
                                throw new except("Illegal escape "
                                . "sequence '" . $chr . "'");
                        }
                    } elseif ($chr == '"') {
                        break;
                    } else {
                        $result .= $chr;
                    }
                } while ($i < $str_length);

                $this->_token = self::DATUM;
                //$this->_tokenValue = substr($str, $start + 1, $i - $start - 1);
                $this->_tokenValue = $result;
                break;
            case 't':
                if (($i + 3) < $str_length && substr($str, $start, 4) == "true") {
                    $this->_token = self::DATUM;
                }
                $this->_tokenValue = true;
                $i += 3;
                break;
            case 'f':
                if (($i + 4) < $str_length && substr($str, $start, 5) == "false") {
                    $this->_token = self::DATUM;
                }
                $this->_tokenValue = false;
                $i += 4;
                break;
            case 'n':
                if (($i + 3) < $str_length && substr($str, $start, 4) == "null") {
                    $this->_token = self::DATUM;
                }
                $this->_tokenValue = NULL;
                $i += 3;
                break;
        }

        if ($this->_token != self::EOF) {
            $this->_offset = $i + 1; // Consume the last token character
            return($this->_token);
        }

        $chr = $str{$i};
        if ($chr == '-' || $chr == '.' || ($chr >= '0' && $chr <= '9')) {
            if (preg_match('/-?([0-9])*(\.[0-9]*)?((e|E)((-|\+)?)[0-9]+)?/s', $str, $matches, PREG_OFFSET_CAPTURE, $start) && $matches[0][1] == $start) {

                $datum = $matches[0][0];

                if (is_numeric($datum)) {
                    if (preg_match('/^0\d+$/', $datum)) {
                        throw new except("Octal notation not supported by JSON (value: $datum)");
                    } else {
                        $val = intval($datum);
                        $sVal = strval($datum);
                        $this->_tokenValue = ($val == $sVal ? $val : $sVal);
                    }
                } else {
                    throw new except("Illegal number format: $datum");
                }

                $this->_token = self::DATUM;
                $this->_offset = $start + strlen($datum);
            }
        } else {
            throw new except('Illegal Token');
        }

        return($this->_token);
    }

    public static function decodeUnicodeString($chrs) {
        $delim = substr($chrs, 0, 1);
        $utf8 = '';
        $strlen_chrs = strlen($chrs);

        for ($i = 0; $i < $strlen_chrs; $i++) {

            $substr_chrs_c_2 = substr($chrs, $i, 2);
            $ord_chrs_c = ord($chrs[$i]);

            switch (true) {
                case preg_match('/\\\u[0-9A-F]{4}/i', substr($chrs, $i, 6)):
                    // single, escaped unicode character
                    $utf16 = chr(hexdec(substr($chrs, ($i + 2), 2)))
                            . chr(hexdec(substr($chrs, ($i + 4), 2)));
                    $utf8 .= self::_utf162utf8($utf16);
                    $i += 5;
                    break;
                case ($ord_chrs_c >= 0x20) && ($ord_chrs_c <= 0x7F):
                    $utf8 .= $chrs{$i};
                    break;
                case ($ord_chrs_c & 0xE0) == 0xC0:
                    // characters U-00000080 - U-000007FF, mask 110XXXXX
                    //see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $utf8 .= substr($chrs, $i, 2);
                    ++$i;
                    break;
                case ($ord_chrs_c & 0xF0) == 0xE0:
                    // characters U-00000800 - U-0000FFFF, mask 1110XXXX
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $utf8 .= substr($chrs, $i, 3);
                    $i += 2;
                    break;
                case ($ord_chrs_c & 0xF8) == 0xF0:
                    // characters U-00010000 - U-001FFFFF, mask 11110XXX
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $utf8 .= substr($chrs, $i, 4);
                    $i += 3;
                    break;
                case ($ord_chrs_c & 0xFC) == 0xF8:
                    // characters U-00200000 - U-03FFFFFF, mask 111110XX
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $utf8 .= substr($chrs, $i, 5);
                    $i += 4;
                    break;
                case ($ord_chrs_c & 0xFE) == 0xFC:
                    // characters U-04000000 - U-7FFFFFFF, mask 1111110X
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $utf8 .= substr($chrs, $i, 6);
                    $i += 5;
                    break;
            }
        }

        return $utf8;
    }

    protected static function _utf162utf8($utf16) {
        // Check for mb extension otherwise do by hand.
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($utf16, 'UTF-8', 'UTF-16');
        }

        $bytes = (ord($utf16{0}) << 8) | ord($utf16{1});

        switch (true) {
            case ((0x7F & $bytes) == $bytes):
                // this case should never be reached, because we are in ASCII range
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return chr(0x7F & $bytes);

            case (0x07FF & $bytes) == $bytes:
                // return a 2-byte UTF-8 character
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return chr(0xC0 | (($bytes >> 6) & 0x1F))
                        . chr(0x80 | ($bytes & 0x3F));

            case (0xFFFF & $bytes) == $bytes:
                // return a 3-byte UTF-8 character
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return chr(0xE0 | (($bytes >> 12) & 0x0F))
                        . chr(0x80 | (($bytes >> 6) & 0x3F))
                        . chr(0x80 | ($bytes & 0x3F));
        }

        // ignoring UTF-32 for now, sorry
        return '';
    }

}
