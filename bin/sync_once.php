<?php

/**
 * mysql 表一次性同步到es
 * Description of sync_once
 * @author xiaochenniao <374270458@qq.com>
 * Created on : 2020-3-20
 */
$process = getopt::get('process', 1);
$has_process = exec('ps aux|grep "sync_once.php"|grep -v grep|wc -l');
if ($has_process > $process) {
    echo "process is full\n";
    exit;
}

$do = true;
$i = 1;
$id = 0;
while ($do) {
    $dataRows = db::getWhere('dml_order_profit', 'id>=?', array($id), 'id asc', 1000, 1);
    if (empty($dataRows)) {
        $do = false;
        break;
    }

    foreach ($dataRows as $row) {
        $params = ['body' => []];
        $params['body'][] = [
            'index' => [
                '_index' => 'my_index',
                '_type' => 'my_type',
                '_id' => $row['id']
            ]
        ];

        $params['body'][] = [
            'orderId' => $row['orderId'],
            'skuId' => $row['orderId'],
            'oid' => $row['orderId'],
            'uid' => $row['orderId'],
            'u_level' => $row['orderId'],
            'doact' => $row['orderId'],
            'type' => $row['orderId'],
            'estimateFee' => $row['orderId'],
            'actualFee' => $row['orderId'],
            'money' => $row['orderId'],
            'end_money' => $row['orderId'],
            'radio' => $row['orderId'],
            'order_date' => $row['orderId'],
            'end_date' => $row['orderId'],
            'create_time' => $row['orderId'],
            'update_time' => $row['orderId']
        ];

        $responses = $client->bulk($params);
        unset($responses);
        
        //记录游标
        $id = $row['id'];
    }
}
