<?php

/**
 * 日期类
 */
class mdate {

    // 根据theday 20140625 的格式筛选条件
    public static function getDateTime($datetype, $startdate = 0, $enddate = 0) {
        $time = time();
        $tweek = intval(date("w"));
        $tweek = $tweek == 0 ? 7 : $tweek;
        $today = intval(date("Ymd"));
        $start = $end = $today;
        $endday = $today;
        if ($datetype == 'jt') {
            return "theday='" . $today . "'";
        } elseif ($datetype == 'zt') {
            return "theday='" . date("Ymd", $time - 86400) . "'";
            $endday = date("Ymd", $time - 86400);
        } elseif ($datetype == 'bz') {
            $start = date("Ymd", $time - $tweek * 86400);
            return "theday>='" . $start . "'";
        } elseif ($datetype == 'sz') {
            $start = date("Ymd", $time - ($tweek + 8) * 86400);
            $end = date("Ymd", $time - ($tweek + 1) * 86400);
            return "theday>='" . $start . "' AND theday<='" . $end . "'";
            $endday = $end;
        } elseif ($datetype == 'by') {
            $start = date("Ymd", $time - (date("j") - 1) * 86400);
            return "theday>='" . $start . "'";
        } elseif ($datetype == 'sy') {
            $start = date("Ymd", strtotime("-1 month") - (time() - $today) - (date("j") - 1) * 86400);
            $end = date("Ymd", $today - (date("j") - 1) * 86400);
            return "theday >= '" . $start . "'" . " and theday < '" . $end . "'";
        } elseif ($datetype == '7') {
            $start = date("Ymd", $time - 6 * 86400);
            return "theday>='" . $start . "'";
        } elseif ($datetype == '30') {
            $start = date("Ymd", $time - 29 * 86400);
            return "theday>='" . $start . "'";
        } elseif ($datetype == '60') {
            $start = date("Ymd", $time - 59 * 86400);
            return "theday>='" . $start . "'";
        } elseif ($datetype == 'all') {
            return false;
        } elseif ($datetype == 'diy') {
            $start = $startdate ? date("Ymd", strtotime($startdate)) : 0;
            $end = $enddate ? date("Ymd", strtotime($enddate)) : 0;
            if ($start == 0 && $end == 0) {
                $datetype = 'all';
                return false;
            } elseif ($start == $end) {
                return "theday='" . $start . "'";
                $endday = $end;
                $start = $end = 0;
            } else {
                $datearr = array();
                if ($start) {
                    $datearr[] = "theday>='" . $start . "'";
                }
                if ($end) {
                    $datearr[] = "theday<='" . $end . "'";
                }
                if ($datearr) {
                    return implode(" AND ", $datearr);
                }
                $endday = $end;
            }
        }
        return false;
    }

    // 根据时间戳筛选条件  $field 为 查询字段 
    public static function getDateTimestamp($datetype, $field = 'tasktime', $startdate = 0, $enddate = 0) {
        $tweek = intval(date("w"));
        $tweek = $tweek == 0 ? 7 : $tweek;
        $today = strtotime('today');
        $start = $end = $today;
        $endday = $today;
        if ($datetype == 'jt') {
            $end = $today + 86400;
            return $field . ">=" . $today . " and " . $field . " <= " . $end;
        } elseif ($datetype == 'zt') {
            $start = ($today - 86400);
            return $field . ">=" . $start . " and " . $field . " <= " . $today;
        } elseif ($datetype == 'bz') {
            $start = $today - $tweek * 86400;
            return $field . ">='" . $start . "'";
        } elseif ($datetype == 'sz') {
            $start = $today - ($tweek + 8) * 86400;
            $end = $today - ($tweek + 1) * 86400;
            return $field . ">='" . $start . "' AND " . $field . "<='" . $end . "'";
        } elseif ($datetype == 'by') {
            $start = $today - (date("j") - 1) * 86400;
            return $field . ">='" . $start . "'";
        } elseif ($datetype == 'sy') {
            $start = strtotime("-1 month") - (time() - $today) - (date("j") - 1) * 86400;
            $end = $today - (date("j") - 1) * 86400;
            return $field . ">='" . $start . "'" . " and " . $field . "<'" . $end . "'";
        } elseif ($datetype == '7') {
            $start = $today - 6 * 86400;
            return $field . ">='" . $start . "'";
        } elseif ($datetype == '30') {
            $start = $today - 29 * 86400;
            return $field . ">='" . $start . "'";
        } elseif ($datetype == '60') {
            $start = $today - 59 * 86400;
            return $field . ">='" . $start . "'";
        } elseif ($datetype == 'all') {
            return false;
        } elseif ($datetype == 'diy') {
            $start = $startdate ? strtotime($startdate) : 0;
            $end = $enddate ? strtotime($enddate) : 0;
            if ($start == 0 && $end == 0) {
                $datetype = 'all';
                return false;
            } elseif ($start == $end) {
                return $field . ">='" . $start . "'" . " and " . $field . " <= " . ($start + 86400);
            } else {
                $datearr = array();
                if ($start) {
                    $datearr[] = $field . ">='" . $start . "'";
                }
                if ($end) {
                    $datearr[] = $field . "<='" . $end . "'";
                }
                if ($datearr) {
                    return implode(" AND ", $datearr);
                }
            }
        }
        return false;
    }

    // 根据格式化后的时间筛选条件 格式:2014-06-25 12:12:12  $field 为 查询字段
    public static function gettimeDate($datetype, $field = 'createdate', $startdate = 0, $enddate = 0) {
        $tweek = intval(date("w"));
        $tweek = $tweek == 0 ? 7 : $tweek;
        $today = strtotime('today');
        $start = $end = $today;
        $endday = $today;
        if ($datetype == 'jt') {
            $end = $today + 86400;
            return $field . ">='" . date("Y-m-d H:i:s", $today) . "' and " . $field . " <= '" . date("Y-m-d H:i:s", $end) . "'";
        } elseif ($datetype == 'zt') {
            $start = ($today - 86400);
            return $field . ">='" . date("Y-m-d H:i:s", $start) . "' and '" . $field . "' <= '" . date("Y-m-d H:i:s", $today) . "'";
        } elseif ($datetype == 'bz') {
            $start = $today - $tweek * 86400;
            return $field . ">='" . date("Y-m-d H:i:s", $start) . "'";
        } elseif ($datetype == 'sz') {
            $start = $today - ($tweek + 8) * 86400;
            $end = $today - ($tweek + 1) * 86400;
            return $field . ">='" . date("Y-m-d H:i:s", $start) . "' AND " . $field . "<='" . date("Y-m-d H:i:s", $end) . "'";
        } elseif ($datetype == 'by') {
            $start = $today - (date("j") - 1) * 86400;
            return $field . ">='" . date("Y-m-d H:i:s", $start) . "'";
        } elseif ($datetype == 'sy') {
            $start = date("Y-m-d H:i:s", strtotime("-1 month") - (time() - $today) - (date("j") - 1) * 86400);
            $end = date("Y-m-d H:i:s", $today - (date("j") - 1) * 86400);
            return $field . ">='" . $start . "'" . " and " . $field . "<'" . $end . "'";
        } elseif ($datetype == '7') {
            $start = $today - 6 * 86400;
            return $field . ">='" . date("Y-m-d H:i:s", $start) . "'";
        } elseif ($datetype == '30') {
            $start = $today - 29 * 86400;
            return $field . ">='" . date("Y-m-d H:i:s", $start) . "'";
        } elseif ($datetype == '60') {
            $start = $today - 59 * 86400;
            return $field . ">='" . date("Y-m-d H:i:s", $start) . "'";
        } elseif ($datetype == 'all') {
            return false;
        } elseif ($datetype == 'diy') {
            $start = $startdate ? strtotime($startdate) : 0;
            $end = $enddate ? strtotime($enddate) : 0;
            if ($start == 0 && $end == 0) {
                $datetype = 'all';
                return false;
            } elseif ($start == $end) {
                return $field . ">='" . date("Y-m-d H:i:s", $start) . "'" . " and " . $field . " <= " . date("Y-m-d H:i:s", ($start + 86400));
            } else {
                $datearr = array();
                if ($start) {
                    $datearr[] = $field . ">='" . date("Y-m-d H:i:s", $start) . "'";
                }
                if ($end) {
                    $datearr[] = $field . "<='" . date("Y-m-d H:i:s", $end) . "'";
                }
                if ($datearr) {
                    return implode(" AND ", $datearr);
                }
            }
        }
        return false;
    }

}
