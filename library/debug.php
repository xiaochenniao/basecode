<?php

class debug {

    public static function vars() {
        if (func_num_args() === 0)
            return;
        $variables = func_get_args();
        $output = array();
        foreach ($variables as $var) {
            $output[] = self::_dump($var, 1024);
        }
        return '<pre class="debug">' . implode("\n", $output) . '</pre>';
    }

    public static function dump($value, $length = 128, $level_recursion = 10) {
        return self::_dump($value, $length, $level_recursion, 0);
    }

    protected static function _dump(& $var, $length = 128, $limit = 10, $level = 0) {
        if (PHP_SAPI == 'cli') {
            $smalls = '';
            $smalle = '';
            $spans = '';
            $spane = '';
            $codes = '';
            $codee = '';
            $cli = 1;
        } else {
            $smalls = '<small>';
            $smalle = '</small>';
            $spans = '<span>';
            $spane = '</span>';
            $codes = '<code>';
            $codee = '</code>';
            $cli = 0;
        }
        if ($var === NULL) {
            return $smalls . 'NULL' . $smalle;
        } elseif (is_bool($var)) {
            return $smalls . 'bool' . $smalle . ' ' . ($var ? 'TRUE' : 'FALSE');
        } elseif (is_float($var)) {
            return $smalls . 'float' . $smalle . ' ' . $var;
        } elseif (is_resource($var)) {
            if (($type = get_resource_type($var)) === 'stream' AND $meta = stream_get_meta_data($var)) {
                $meta = stream_get_meta_data($var);
                if (isset($meta['uri'])) {
                    $file = $meta['uri'];
                    if (function_exists('stream_is_local')) {
                        if (stream_is_local($file)) {
                            $file = self::path($file);
                        }
                    }
                    return $smalls . 'resource' . $smalle . $spans . '(' . $type . ') ' . $spane . ($cli ? $file : htmlspecialchars($file, ENT_NOQUOTES));
                }
            } else {
                return $smalls . 'resource' . $smalle . $spans . '(' . $type . ')' . $spane;
            }
        } elseif (is_string($var)) {
            if (strlen($var) > $length) {
                $str = ($cli ? substr($var, 0, $length) : htmlspecialchars(substr($var, 0, $length), ENT_NOQUOTES) . '&nbsp;&hellip;');
            } else {
                $str = ($cli ? $var : htmlspecialchars($var, ENT_NOQUOTES));
            }
            return $smalls . 'string' . $smalle . $spans . '(' . strlen($var) . ') "' . $spane . $str . '"';
        } elseif (is_array($var)) {
            $output = array();
            $space = str_repeat($s = '    ', $level);
            static $marker;
            if ($marker === NULL) {
                $marker = uniqid("\x00");
            }
            if (empty($var)) {
                
            } elseif (isset($var[$marker])) {
                $output[] = "(\n$space$s*RECURSION*\n$space)";
            } elseif ($level < $limit) {
                $output[] = $spans . "(";
                $var[$marker] = TRUE;
                foreach ($var as $key => & $val) {
                    if ($key === $marker)
                        continue;
                    if (!is_int($key)) {
                        $key = '"' . ($cli ? $key : htmlspecialchars($key, ENT_NOQUOTES)) . '"';
                    }
                    $output[] = "$space$s$key => " . self::_dump($val, $length, $limit, $level + 1);
                }
                unset($var[$marker]);
                $output[] = "$space)$spane";
            } else {
                $output[] = "(\n$space$s...\n$space)";
            }
            return $smalls . 'array' . $smalle . $spans . '(' . count($var) . ') ' . $spane . implode("\n", $output);
        } elseif (is_object($var)) {
            $array = (array) $var;
            $output = array();
            $space = str_repeat($s = '    ', $level);
            $hash = spl_object_hash($var);
            static $objects = array();

            if (empty($var)) {
                // Do nothing
            } elseif (isset($objects[$hash])) {
                $output[] = "{\n$space$s*RECURSION*\n$space}";
            } elseif ($level < $limit) {
                $output[] = "$codes{";
                $objects[$hash] = TRUE;
                foreach ($array as $key => & $val) {
                    if ($key[0] === "\x00") {
                        $access = $smalls . (($key[1] === '*') ? 'protected' : 'private') . '' . $smalle;
                        $key = substr($key, strrpos($key, "\x00") + 1);
                    } else {
                        $access = $smalls . 'public' . $smalle;
                    }
                    $output[] = "$space$s$access $key => " . self::_dump($val, $length, $limit, $level + 1);
                }
                unset($objects[$hash]);
                $output[] = "$space}$codee\n";
            } else {
                $output[] = "{\n$space$s...\n$space}\n";
            }
            return $smalls . 'object' . $smalle . ' ' . $spans . get_class($var) . '(' . count($array) . ')' . $spane . ' ' . implode("\n", $output);
        } else {
            return $smalls . gettype($var) . $smalle . ' ' . ($cli ? print_r($var, TRUE) : htmlspecialchars(print_r($var, TRUE), ENT_NOQUOTES));
        }
    }

    public static function path($file) {
        if (strpos($file, APP_DIR) === 0) {
            $file = 'APP_DIR' . substr($file, strlen(APP_DIR));
        } elseif (strpos($file, CORE_DIR) === 0) {
            $file = 'CORE_DIR' . substr($file, strlen(CORE_DIR));
        } elseif (strpos($file, CONTROLLER_DIR) === 0) {
            $file = 'CONTROLLER_DIR' . substr($file, strlen(CONTROLLER_DIR));
        } elseif (strpos($file, SERVICE_DIR) === 0) {
            $file = 'SERVICE_DIR' . substr($file, strlen(SERVICE_DIR));
        } elseif (strpos($file, TEMPLATE_DIR) === 0) {
            $file = 'TEMPLATE_DIR' . substr($file, strlen(TEMPLATE_DIR));
        } elseif (strpos($file, DATA_DIR) === 0) {
            $file = 'DATA_DIR' . substr($file, strlen(DATA_DIR));
        }
        return $file;
    }

    public static function source($file, $line_number, $padding = 5) {
        if (!$file OR ! is_readable($file)) {
            return FALSE;
        }

        $file = fopen($file, 'r');
        $line = 0;

        $range = array('start' => $line_number - $padding, 'end' => $line_number + $padding);

        $format = '% ' . strlen($range['end']) . 'd';

        $source = '';
        while (($row = fgets($file)) !== FALSE) {
            if (++$line > $range['end'])
                break;

            if ($line >= $range['start']) {
                $row = htmlspecialchars($row, ENT_NOQUOTES);
                $row = '<span class="number">' . sprintf($format, $line) . '</span> ' . $row;

                if ($line === $line_number) {
                    $row = '<span class="line highlight">' . $row . '</span>';
                } else {
                    $row = '<span class="line">' . $row . '</span>';
                }
                $source .= $row;
            }
        }
        fclose($file);
        return '<pre class="source"><code>' . $source . '</code></pre>';
    }

    public static function trace(array $trace = NULL) {
        if ($trace === NULL) {
            $trace = debug_backtrace();
        }
        $statements = array('include', 'include_once', 'require', 'require_once');

        $output = array();
        foreach ($trace as $step) {
            if (!isset($step['function'])) {
                continue;
            }

            if (isset($step['file']) AND isset($step['line'])) {
                $source = self::source($step['file'], $step['line']);
            }

            if (isset($step['file'])) {
                $file = $step['file'];
                if (isset($step['line'])) {
                    $line = $step['line'];
                }
            }
            $function = $step['function'];
            if (in_array($step['function'], $statements)) {
                if (empty($step['args'])) {
                    $args = array();
                } else {
                    $args = array($step['args'][0]);
                }
            } elseif (isset($step['args'])) {
                if (!function_exists($step['function']) OR strpos($step['function'], '{closure}') !== FALSE) {
                    $params = NULL;
                } else {
                    if (isset($step['class'])) {
                        if (method_exists($step['class'], $step['function'])) {
                            $reflection = new ReflectionMethod($step['class'], $step['function']);
                        } else {
                            $reflection = new ReflectionMethod($step['class'], '__call');
                        }
                    } else {
                        $reflection = new ReflectionFunction($step['function']);
                    }
                    $params = $reflection->getParameters();
                }

                $args = array();

                foreach ($step['args'] as $i => $arg) {
                    if (isset($params[$i])) {
                        $args[$params[$i]->name] = $arg;
                    } else {
                        $args[$i] = $arg;
                    }
                }
            }

            if (isset($step['class'])) {
                $function = $step['class'] . $step['type'] . $step['function'];
            }

            $output[] = array(
                'function' => $function,
                'args' => isset($args) ? $args : NULL,
                'file' => isset($file) ? $file : NULL,
                'line' => isset($line) ? $line : NULL,
                'source' => isset($source) ? $source : NULL,
            );
            unset($function, $args, $file, $line, $source);
        }
        return $output;
    }

    public static function __($string, array $values = NULL) {
        return empty($values) ? $string : strtr($string, $values);
    }

}
