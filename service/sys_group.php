<?php

/**
 * 后台管理组操作类
 */
class sys_group {
    /* 获取组权限 */

    public static function getPurview($id = 0) {
        $info = db::getByPk('sys_group', $id);
        if ($info) {
            $purview = unserialize($info['purview']);
            return self::formatPurview($purview);
        }
        return false;
    }

    public static function formatPurview($purview) {
        try {
            $ext = array();
            if (is_array($purview)) {
                foreach ($purview as $k => $v) {
                    foreach ($v as $vk => $val) {
                        $val = str_ireplace('，', ',', $val);
                        if (strpos($val, ',')) {
                            $ext = explode(',', $val);
                            unset($purview[$k][$vk]);
                            $purview[$k] = F::array_values_merge($ext, $purview[$k]);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            
        };
        return $purview;
    }

}

?>