<?php

return array(
    'alipay' => array(
        //'partner'        => "2088901967682691", //合作身份者ID
        //'security_code'  => "zjqclkw26h4nj6e4gtimy78a395vjkhe", //安全检验码
        //'seller_email'   => "2244247951@qq.com", //签约支付宝账号或卖家支付宝帐户
        'partner' => "2088021116192058", //合作身份者ID
        'security_code' => "utjmxxlrb8qi51y9fsmuwszzsmqgbupx", //安全检验码
        'seller_email' => "business@weichuanbo.com", //签约支付宝账号或卖家支付宝帐户
        'submit_type' => 'post', //可选get OR post
        'notify_url' => "/payment/alipaynotify.do",
        'return_url' => "/payment/alipayback.do",
        'show_url' => ""
    ),
    'toppay' => array(
        'partner' => "9486",
        'paykey' => "nqc8a39z6ti26h5jmy7jkhe4lkwve4gj"
    )
);
?>