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
        v_display();
    }

}
?>