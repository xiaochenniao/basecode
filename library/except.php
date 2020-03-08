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

    public static function handler(Exception $e) {
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

    public static function text(Exception $e) {
        return sprintf('%s [ %s ]: %s ~ %s [ %d ]', get_class($e), $e->getCode(), strip_tags($e->getMessage()), debug::path($e->getFile()), $e->getLine());
    }

}

?>
