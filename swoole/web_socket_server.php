<?php

/**
 * Description of ws_server
 * @author xiaochenniao <374270458@qq.com>
 * Created on : 2020-3-10
 */
set_time_limit(0);
@require_once 'core/init.php';

class web_socket_server {

    private $serv;
    private static $fd = null;

    public function __construct() {

        $this->serv = new Swoole\WebSocket\Server("0.0.0.0", 9502);
        $this->serv->set(array(
            'worker_num' => 1,
            'daemonize' => false,
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'debug_mode' => 1
        ));

        $this->serv->on('open', array($this, 'onOpen'));
        $this->serv->on('message', array($this, 'onMessage'));
        $this->serv->on('close', array($this, 'onClose'));

        $this->serv->start();
    }

    //连接触发
    function onOpen($server, $req) {
        $server->push($req->fd, "hello, welcome1\n");
    }

    //发送消息
    public function onMessage($server, $frame) {
        //$server->push($frame->fd, json_encode(["hello", "world"]));
        $server->push($frame->fd, "server: {$frame->data}"); //推送到发送者
        //print_r(db::getOne('sys_user'));
    }

    //关闭事件
    public function onClose($server, $fd) {
        echo "connection close: " . $fd;
    }

}

// 启动服务器
$server = new web_socket_server();
