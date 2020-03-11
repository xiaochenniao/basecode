<?php

/**
 * Description of ws_test
 * @author xiaochenniao <374270458@qq.com>
 * Created on : 2020-3-10
 */
class ws_test_Controller extends base_Controller {

    function indexAction() {
        $this->_forward('test');
    }

    function testAction() {
        $task_client = new task_client();
        $task_client->connect();
        $task_client->send(['name' => 'task_test']);
        $task_client->close();
        v_display();
    }

}

?>