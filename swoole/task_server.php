<?php

/**
 * Description of task_server 异步任务服务
 * @author xiaochenniao <374270458@qq.com>
 * Created on : 2020-3-11
 */
set_time_limit(0);
@require_once 'core/init.php';

class task_server {

    private $serv;

    public function __construct() {
        $this->serv = new Swoole\Server("0.0.0.0", 9503);
        $this->serv->set(array(
            'worker_num' => 1, //一般设置为服务器CPU数的1-4倍
            //'daemonize' => 1, //以守护进程执行
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'task_worker_num' => 3, //task进程的数量
            "task_ipc_mode " => 3, //使用消息队列通信，并设置为争抢模式
                //"log_file" => "log/taskqueueu.log" ,//日志
        ));

        $this->serv->on('receive', array($this, 'onReceive'));
        $this->serv->on('task', array($this, 'onTask'));
        $this->serv->on('finish', array($this, 'onFinish'));

        $this->serv->start();
    }

    /**
     *  此回调函数在worker进程中执行
     * @param type $serv
     * @param type $task_id
     * @param type $from_id
     * @param type $data
     */
    public function onReceive($serv, $task_id, $from_id, $data) {
        //投递异步任务
        $serv->task($data);
    }

    /**
     * 处理异步任务(此回调函数在task进程中执行)
     * @param type $serv
     * @param type $task_id
     * @param type $from_id
     * @param type $data
     * @return type
     */
    public function onTask($serv, $task_id, $from_id, $data) {
        $array = json_decode($data, true);
        $class_name = $array['name'];
        $action = $class_name . 'Action';

        echo $class_name . ' \n';

        $task_controller = new $class_name();
        $res = $task_controller->$action();
        print_r($res);
        $serv->finish("$res -> OK");
    }

    /**
     * 处理异步任务的结果(此回调函数在worker进程中执行)
     * @param type $serv
     * @param type $task_id
     * @param type $data
     */
    public function onFinish($serv, $task_id, $data) {
        //echo "Task {$task_id} finish\n";
        //echo "Result: {$data}\n";
        //print_r($data);
        echo 'task_id:' . $task_id . 'from_id:' . $from_id;
    }

}

//调用 $serv->task() 后，程序立即返回，继续向下执行代码。onTask 回调函数 Task 进程池内被异步执行。执行完成后调用 $serv->finish() 返回结果。
//启动服务器
$server = new task_server();
?>
