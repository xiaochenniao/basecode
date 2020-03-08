<?php

class except extends exception {

    public static $php_errors = array(
        E_ERROR => 'Fatal Error',
        E_USER_ERROR => 'User Error',
        E_PARSE => 'Parse Error',
        E_WARNING => 'Warning',
        E_USER_WARNING => 'User Warning',
        E_STRICT => 'Strict',
        E_NOTICE => 'Notice',
        E_RECOVERABLE_ERROR => 'Recoverable Error',
        E_DEPRECATED => 'Deprecated',
    );
    public static $error_html = 'error/html.php';
    public static $error_cli = 'error/cli.php';

    public function __construct($message, $code = 500) {
        parent::__construct($message, (int) $code);
        $this->code = $code;
    }

    public function __toString() {
        return self::text($this);
    }

    /**
     * 参数说明
     * 自 PHP 7 以来，大多数错误抛出 Error 异常，如果在用户回调里将 $ex 参数的类型明确约束为Exception， PHP 7 中由于异常类型的变化，将会产生问题，所以最好的兼容方案就是：移除 $ex 参数前的类型约束。
     * //register exceptoin handler
      set_exception_handler('handler');

      // PHP 5 work only
      function handler(Exception $e) { ... }

      // PHP 7 work only
      function handler(Throwable $e) { ... }

      // PHP 5 and 7 compatible.
      function handler($e) { ... }
     * @param Exception $e
     * @throws exception
     */
    public static function handler($e) {
        try {
            $type = get_class($e);
            $code = $e->getCode();
            $message = $e->getMessage();
            $file = $e->getFile();
            $line = $e->getLine();
            $trace = $e->getTrace();
            if (isset(self::$php_errors[$code])) {
                $code = self::$php_errors[$code];
            }
            $error = self::text($e);
            //logger::log(); //to log

            if (defined('PHP_CLI')) {
                if (!config::get('base.is_debug')) {
                    echo "error";
                    exit(1);
                } else {
                    $file_name = CORE_DIR . '/' . self::$error_cli;
                }
            } else {
                if (!headers_sent()) {
                    $http_header_status = $code;
                    header('Content-Type: text/html; charset=utf-8', TRUE, $http_header_status);
                }
                if (!config::get('base.is_debug')) {
                    echo "<h1>error</h1>";
                    exit(1);
                } else {
                    $file_name = CORE_DIR . '/' . self::$error_html;
                }
            }

            ob_start();
            if (file_exists($file_name)) {
                include $file_name;
            } else {
                throw new exception('Error debug file does not exist');
            }
            echo ob_get_clean();
            exit(1);
        } catch (Exception $e) {
            ob_get_level() and ob_clean();
            echo self::text($e), "\n";
            exit(1);
        }
    }

    public static function text($e) {
        return sprintf('%s [ %s ]: %s ~ %s [ %d ]', get_class($e), $e->getCode(), strip_tags($e->getMessage()), debug::path($e->getFile()), $e->getLine());
    }

}

?>
