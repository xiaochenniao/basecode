#!/usr/bin/php
<?php
set_time_limit(0);
@require_once 'core/init.php';
define('PHP_CLI', 1);
define('IN_CROND', 1);
if (file_exists(DATA_DIR . '/crond.break')) {
    echo "crond.break\n";
    exit;
}
chdir(BIN_DIR);
ob_end_clean();
$self = $argv[0];
$time = time();
if (isset($argv[1])) {
    if (!getopt::get('root')) {
        posix_setuid(99);
    }
    require BIN_DIR . '/' . trim($argv[1]);
} elseif ($rows = db::getWhere('sys_crond', 'stat=? and runtime<?', array('0', $time))) {
    $h = intval(date('H'));
    foreach ($rows as $row) {
        if ($row['start_time']) {
            if ($h > 4 && $h < $row['start_time']) {
                continue;
            }
        }
        if ($row['end_time']) {
            if ($h <= 4 && $h >= $row['end_time']) {
                continue;
            }
        }
        $rr = rand(1, 3);
        if ($rr == 3) {
            $runtime = $time + $row['seconds'] + 60;
        } elseif ($rr == 2) {
            $runtime = $time + $row['seconds'] - 60;
        } else {
            $runtime = $time + $row['seconds'];
        }
        db::setByPk('sys_crond', array('runtime' => $runtime), $row['id']);
        exec(BIN_DIR . '/crond.php ' . $row['script'] . ' > /dev/null 2>&1 &');
    }
}