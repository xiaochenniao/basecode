<?php

/**
 * 地区管理类
 */
class b_area {

    static function find($id) {
        $id = $id * 1;
        return db::getByPk('b_area', $id);
    }

    /* 根据ID获取UPID */

    function getUpidById($id = 0) {
        $info = self::find($id);
        return $info['upid'];
    }

    static function getPname($pid) {
        $info = self::find($pid);
        return $info['joinname'];
    }

    public static function getTrees($id = 0, $all = array()) {
        if ($id <= 0) {
            return $all;
        }
        $info = self::find($id);
        if ($info['level'] < 3) {
            $all[] = $info;
        }
        if ($info['upid'] > 0) {
            $all = self::getTrees($info['upid'], $all);
        }
        return $all;
    }

    /* 根据id获取一个地区的名字 */

    function getAreaName($areaid) {
        if (!$areaid) {
            return false;
        } else {
            $area = self::find($areaid);
            return $area['name'];
        }
    }

}

?>