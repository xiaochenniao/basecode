<?php

/**
 * 后台管理员权限操作类
 */
class sys_purview {
    /*
      $type=1 一维数组， $type=2 多维数组
     */

    public static function all($parentid = 0, $maxdepth = 0, $type = 1, $depth = 1, $all = array()) {
        if ($maxdepth > 0 && $depth > $maxdepth) {
            return $all;
        }
        $pre_name = '';
        for ($i = 1; $i < $depth; $i++) {
            if ($i == 1) {
                $pre_name = '<i class="lower"></i>';
                continue;
            }
            $pre_name = '<i class="lower"></i>' . $pre_name;
        }
        $depth++;
        $rows = db::getWhere('sys_purview', 'parent_id=' . $parentid);
        foreach ($rows as $row) {
            $row['prename'] = $pre_name;
            if ($type == 1) {
                $all[$row['id']] = $row;
                $all = self::all($row['id'], $maxdepth, $type, $depth, $all);
            } else {
                $row['children'] = self::all($row['id'], $maxdepth, $type, $depth);
                $all[$row['id']] = $row;
            }
        }
        return $all;
    }

}

?>