<?php

class ajax_Controller extends Controller {

    function init() {
        
    }

    function timeAction() {
        v_json(time());
    }

    function getuserinfobAction() {
        $uid = rtrim(trim(r_get('uid')), '/');
        if (!$uid) {
            return '';
        }
        $data = sinaapi::getuserinfo($uid, 'array');
        $data = $data[$uid];
        $num = db::countWhere('m_account', "uid = ? and status not in(7,11)", array($data['uid']));
        if ($num > 0) {
            v_json("2");
        } else {
            v_json($data);
        }
    }

}

?>