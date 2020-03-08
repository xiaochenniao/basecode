<?php

/**
 */
class Scache {

    public static function sys_user($flash = false) {
        $key = 'sys_user';
        if ($flash || !($data = cache::get($key))) {
            $rows = db::getAllForFields('sys_user', 'id,username,usergroup,fullname', "usergroup ASC");
            $data = array();
            foreach ($rows as $k => $v) {
                $data[$v['id']] = $v;
            }
            cache::set($key, $data);
            $data = $data;
        }
        return $data;
    }

    public static function sys_group($flash = false) {
        $key = 'sys_group';
        if ($flash || !($data = cache::get($key))) {
            $rows = db::getAllForFields('sys_group', 'id,group_name', "order_id ASC");
            $data = array();
            foreach ($rows as $k => $v) {
                $data[$v['id']] = $v['group_name'];
            }
            cache::set($key, $data);
        }
        return $data;
    }

    public static function sys_config($flash = false) {
        $key = 'sys_config';
        if ($flash || !($data = cache::get($key))) {
            $data = db::getByPk('sys_config', 1);
            $data['ifopen'] = unserialize($data['ifopen']);
            cache::set($key, $data);
        }
        return $data;
    }

    //����
    public static function b_area($key = 'area', $flash = false) {
        if ($flash || !($data = cache::get($key))) {
            $rows = db::getAll('b_area', 'level=1 and isshow=1', "displayorder ASC,id ASC");
            $newdata = array();
            foreach ($rows as $key => $val) {
                $newdata['area_province'][$val['id']] = array('id' => $val['id'], 'name' => $val['name'], 'joinname' => $val['joinname']);
                $newdata['area'][$val['id']] = array('id' => $val['id'], 'name' => $val['name'], 'joinname' => $val['joinname']);
                if ($val['parent_id'] <= 0) {
                    $newdata['area_province'][$val['id']] = array('id' => $val['id'], 'name' => $val['name'], 'joinname' => $val['joinname']);
                } else {
                    $newdata['area_citys'][$val['parent_id']][$val['id']] = array('id' => $val['id'], 'name' => $val['name'], 'joinname' => $val['joinname']);
                }
            }
            $rows = db::getWhere('b_area', 'level=2 and isshow=1', array(), 'displayorder asc,id asc');
            foreach ($rows as $key => $val) {
                $newdata['area_citys'][$val['upid']][$val['id']] = array('id' => $val['id'], 'name' => $val['name'], 'joinname' => $val['joinname']);
                $newdata['area'][$val['id']] = array('id' => $val['id'], 'name' => $val['name'], 'joinname' => $val['joinname']);
            }
            cache::set('area', $newdata['area']);
            cache::set('area_province', $newdata['area_province']);
            cache::set('area_citys', $newdata['area_citys']);
            $data = $newdata[$key];
        }
        return $data;
    }

    // ΢������
    public static function category_account($flash = false) {
        $key = 'account_category';
        if ($flash || !($data = cache::get($key))) {
            $rows = db::getAllForFields('m_account_category', 'id,name', "id DESC");
            $data = array();
            foreach ($rows as $k => $v) {
                $data[$v['id']] = $v;
            }
            cache::set($key, $data);
        }
        return $data;
    }

    // ΢�ŷ���
    public static function category_wechat($flash = false) {
        $key = 'wechat_category';
        if ($flash || !($data = cache::get($key))) {
            $rows = db::getAllForFields('m_wechat_category', 'id,name', "id ASC");
            $data = array();
            foreach ($rows as $k => $v) {
                $data[$v['id']] = $v;
            }
            cache::set($key, $data);
        }
        return $data;
    }

    //Υ��ؼ���
    public static function violation_keywords($type = 1, $flash = false) {
        $key = 'violation_keywords_' . $type;
        if ($flash || !($data = cache::get($key))) {
            $row = db::getOneForFields('violation', 'type,content', 'type = ?', array($type));
            $data = $row['content'];
            cache::set($key, $data);
        }
        return $data;
    }

}

?>
