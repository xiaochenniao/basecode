<?php

/**
 * Description of task_client 异步任务 客户端
 * @author xiaochenniao <374270458@qq.com>
 * Created on : 2020-3-11
 */
class task_client {

    private $client;

    public function __construct() {
        $this->client = new Swoole\Client(SWOOLE_SOCK_TCP);
    }

    /**
     * 连接客户端
     */
    public function connect() {
        if (!$this->client->connect("0.0.0.0", 9503, 1)) {
            throw new Exception(sprintf('Swoole Error: %s', $this->client->errCode));
        }
    }

    /**
     * 任务总入口
     * @param array $data
     * @return type
     * @throws Exception
     */
    public function send($data) {
        if ($this->client->isConnected()) {
            if (!is_string($data)) {
                $data = json_encode($data);
            } else {
                throw new Exception('Swoole Task Parameter no.');
            }
            //任务名称最好用task_开头
            if (isset($data['name'])) {
                throw new Exception('Swoole Task Name no.');
            }
            return $this->client->send($data);
        } else {
            throw new Exception('Swoole Server does not connected.');
        }
    }

    /**
     * 关闭客户端
     */
    public function close() {
        $this->client->close();
    }

}
