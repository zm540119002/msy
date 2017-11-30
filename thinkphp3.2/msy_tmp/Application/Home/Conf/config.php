<?php
return array(
	//'配置项'=>'配置值'
    'WEB_NAME' => '美尚云',
    'WEB_URL' => 'http://www.meishangyun.com',
    'URL_MODEL' => 1,


    //
    'DB_PURCHASE' => array(
        'DB_TYPE'   => 'mysql', // 数据库类型
        'DB_HOST'   => '127.0.0.1', // 服务器地址
        'DB_NAME'   => 'purchase', // 数据库名
        'DB_USER'   => 'root', // 用户名
        'DB_PWD'    => 'msy', // 密码
        'DB_PORT'   =>  3306, // 端口
        'DB_PREFIX' => '',  //数据库表前缀
    ),
    'DB_MYMS' => array(
        'DB_TYPE'   => 'mysql', // 数据库类型
        'DB_HOST'   => '127.0.0.1', // 服务器地址
        'DB_NAME'   => 'myms', // 数据库名
        'DB_USER'   => 'root', // 用户名
        'DB_PWD'    => 'msy', // 密码
        'DB_PORT'   =>  3306, // 端口
        'DB_PREFIX' => '',  //数据库表前缀
    ),
   //purchase支付回调地址
    'PURCHASE_NOTIFY_URL'  => "http://".$_SERVER['HTTP_HOST']."/index.php/Purchase/Payment/notify",
    //myms支付回调地址
    'MYMS_NOTIFY_URL'  => "http://".$_SERVER['HTTP_HOST']."/index.php/Myms/Payment/notify",
);