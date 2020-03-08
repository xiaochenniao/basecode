<?php

/**
 * 路由规则
 */
return array(
    'admin' => array(
        'rule' => array(
            array("^(login|logout|desktop|captcha)(.*)", "main/$1$2")
        )
    ),
    'www' => array(
        'rule' => array(
            array("^help\/([0-9\_]+)\.html", "help/index-id-$1"),
        ),
    ),
);
?>
