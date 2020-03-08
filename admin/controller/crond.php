<?php

/*
 * 定时任务管理
 */

class crond_controller extends controller {

    function indexAction() {
        $this->_forward('list');
    }

    function listAction() {
        $page = r_int('page', 1);
        $pnum = r_int('pnum', 20);
        list($pager, $datas) = db::pageWhere('sys_crond', "", array(), 'stat asc,script desc', $pnum, $page);
        v_set('pager', $pager);
        v_set('datas', $datas);
        v_set('page', $page);
        v_set('pnum', $pnum);
    }

    function runAction() {
        $id = r_get('id');
        if (!$info = db::getByPk('sys_crond', $id)) {
            v_notice('您请求的数据不存在', 1);
        }
        exec(CROND_DIR . '/crond.php ' . $info['script'] . ' > /dev/null 2>&1 &');
        v_notice('成功发送执行请求', 'list');
    }

    function statAction() {
        $id = r_get('id');
        $stat = r_get('stat');
        if (!$info = db::getByPk('sys_crond', $id)) {
            v_notice('您请求的数据不存在', 1);
        }
        db::setByPk('sys_crond', array('stat' => $stat), $id);
        response::redirect('crond/list');
    }

    function addAction() {
        v_display('crond_info.tpl');
    }

    function editAction() {
        $id = r_get('id');
        if (!$info = db::getByPk('sys_crond', $id)) {
            v_notice('您请求的数据不存在', 1);
        }
        v_set('info', $info);
        v_display('crond_info.tpl');
    }

    function saveAction() {
        $info = r_get('info');
        db::set('sys_crond', $info);
        v_notice('操作成功', 'list');
    }

    function removeAction() {
        $id = r_get('id');
        if (!$rowCount = db::delByPk('sys_crond', $id)) {
            v_notice('您请求的数据不存在', 1);
        }
        v_notice('操作成功', 'list');
    }

}
