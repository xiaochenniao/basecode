<?php

/**
 * Description of task_test
 * @author xiaochenniao <374270458@qq.com>
 * Created on : 2020-3-11
 */
class task_test {

    public function task_testAction() {
        $user_info = db::getOne('sys_user');
        return $user_info;
    }

}
