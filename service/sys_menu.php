<?php

/**
 * 后台管理菜单操作类
 */
class sys_menu {

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
        $rows = db::getWhere("sys_menu", "parent_id='" . $parentid . "'");
        foreach ($rows as $row) {
            $row['prename'] = $pre_name;
            if ($type == 1) {
                $all[] = $row;
                $all = self::all($row['id'], $maxdepth, $type, $depth, $all);
            } else {
                $row['children'] = self::all($row['id'], $maxdepth, $type, $depth);
                $all[] = $row;
            }
        }
        return $all;
    }

    public static function menuset() {
        $main_menu = $sub_menu = array();
        $rows = db::getWhereForFields("sys_menu", 'id, menu_name as name, menu_link as url,icon_style', "parent_id=0 AND isshow=1", array(), 'order_id asc,id asc');
        foreach ($rows as $row) {
            $main_menu[] = array('id' => $row['icon_style'], 'name' => $row['name'], 'icon_style' => $row['icon_style']);
            $sub_menu[$row['icon_style']]['id'] = '';
            $subrows = db::getWhereForFields("sys_menu", 'id, menu_name as name, menu_link as url,icon_style', "parent_id=" . $row['id'] . " AND isshow=1", array(), 'order_id asc,id asc');
            foreach ($subrows as $row1) {
                $submenu = array('id' => $row1['url'], 'name' => $row1['name'], 'url' => $row1['url'] . '.do', 'icon_style' => $row1['icon_style']);
                $subrows2 = db::getWhereForFields("sys_menu", 'id, menu_name as name, menu_link as url,icon_style', "parent_id=" . $row1['id'] . " AND isshow=1", array(), 'order_id asc,id asc');
                if ($subrows2) {
                    unset($submenu['url']);
                    foreach ($subrows2 as $row2) {
                        $submenu['items'][] = array('id' => $row2['url'], 'name' => $row2['name'], 'url' => $row2['url'] . '.do', 'icon_style' => $row2['icon_style']);
                    }
                }
                $sub_menu[$row['icon_style']]['items'][] = $submenu;
            }
        }
        return array('main' => $main_menu, 'items' => $sub_menu);
    }

    public static function menuAll() {
        $menus = array();
        $selects = 'id, menu_name, menu_link,has_im,parent_id,order_id,icon_style';
        $rows = db::getWhereForFields("sys_menu", $selects, "parent_id=0 AND isshow=1", array(), 'order_id asc,id asc');
        foreach ($rows as $row) {
            $menus[$row['id']] = $row;
            $subrows = db::getWhereForFields("sys_menu", $selects, "parent_id=" . $row['id'] . " AND isshow=1", array(), 'order_id asc,id asc');
            foreach ($subrows as $row1) {
                $subrows2 = db::getWhereForFields("sys_menu", $selects, "parent_id=" . $row1['id'] . " AND isshow=1", array(), 'order_id asc,id asc');
                if ($subrows2) {
                    foreach ($subrows2 as $row2) {
                        $row2['menu_name'] = $row1['menu_name'] . '-' . $row2['menu_name'];
                        $menus[$row['id']]['items'][$row2['id']] = $row2;
                    }
                } else {
                    $menus[$row['id']]['items'][$row1['id']] = $row1;
                }
            }
        }
        return $menus;
    }

    public static function getcache() {
        return self::flushcache('menus_' . F::logininfo('id', APP_NAME), true);
    }

    public static function flushcache($memkey = '', $return = false) {
        if (!$memkey) {
            $memkey = 'menus_' . F::logininfo('id', APP_NAME);
        }
        $rows = self::menuset();
        //echo '<pre>';
        //print_r($rows);exit;
        $auth = F::logininfo('auth', APP_NAME);
        if ($auth) {
            $auth_ids = array_keys($auth);

            $newrows = array();
            if (F::logininfo('usergroup', APP_NAME) != 1) {
                foreach ($rows['items'] as $k1 => $v1) {
                    if (!empty($v1['items'])) {
                        $j = 0;
                        foreach ($v1['items'] as $key => $val) {
                            if (isset($val['items']) && !empty($val['items'])) {
                                $i = 0;
                                foreach ($val['items'] as $k2 => $v2) {
                                    if (!in_array($v2['id'], $auth_ids)) {
                                        unset($rows['items'][$k1]['items'][$key]['items'][$k2]);
                                    } else {
                                        $i++;
                                    }
                                }
                                if ($i < 1) {
                                    unset($rows['items'][$k1]['items'][$key]);
                                } else {
                                    $j++;
                                    $rows['items'][$k1]['items'][$key]['items'] = array_values($rows['items'][$k1]['items'][$key]['items']);
                                }
                            } else {
                                if (!in_array($val['id'], $auth_ids)) {
                                    unset($rows['items'][$k1]['items'][$key]);
                                } else {
                                    $j++;
                                }
                            }
                        }
                        if ($j < 1) {
                            unset($rows['items'][$k1]);
                        } else {
                            $rows['items'][$k1]['items'] = array_values($rows['items'][$k1]['items']);
                        }
                    }
                }
                $mainkeys = array_keys($rows['items']);
                foreach ($rows['main'] as $k1 => $v1) {
                    if (!in_array($v1['id'], $mainkeys)) {
                        unset($rows['main'][$k1]);
                    }
                }
                //$rows['items'] = array_values($rows['items']);
                $rows['main'] = array_values($rows['main']);
            }
        }
        //var_dump('x',$rows);exit;
        cache::set($memkey, $rows);
        if ($return)
            return $rows;
    }

    //根据权限加载左侧操作菜单
    public static function adminMenus() {
        $auth = F::logininfo('auth', APP_NAME);
        if (is_array($auth['purview'])) {
            foreach ($auth['purview'] as $key => $val) {
                if (!empty($val))
                    $pck[] = '/' . str_replace("admin_", "admin/", $key) . ".do";
            }
        }
        else {
            return false;
        }
        return $pck;
    }

    public static function getperviewid($id) {
        $info = db::getByPk("sys_menu", $id);
        return $info['has_im'];
    }

}

?>