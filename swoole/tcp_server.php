<?php

/**
 * Description of tcp_server
 * @author xiaochenniao <374270458@qq.com>
 * Created on : 2020-3-10
 */
class tcp_server {

    private $serv;
    private static $fd = null;

    public function __construct() {

        $this->serv = new Swoole\Server("127.0.0.1", 9501);
        $this->serv->set(array(
            'worker_num' => 1,
            'daemonize' => false,
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'debug_mode' => 1
        ));

        $this->serv->on('connect', array($this, 'onConnect'));
        $this->serv->on('receive', array($this, 'onReceive'));
        $this->serv->on('close', array($this, 'onClose'));

        $this->serv->start();
    }

    //监听连接进入事件
    function onOpen($server, $req) {
        $server->push($req->fd, "hello, welcome1\n");
    }

    //监听数据接收事件
    public function onReceive($server, $frame, $from_id, $data) {
        $server->send($frame->fd, "Server: " . $data);
        //print_r(db::getOne('sys_user'));
    }

    //监听连接关闭事件
    public function onClose($server, $fd) {
        echo "Client close: " . $fd;
    }

}
