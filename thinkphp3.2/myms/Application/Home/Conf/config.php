<?php
return array(
    //'配置项'=>'配置值'
    'WEB_NAME' => '美创会',
    //数据库
    'DB_TYPE' => 'mysql',
    'DB_HOST' => '127.0.0.1',
    'DB_NAME' => 'myms',
    'DB_USER' => 'root',
    'DB_PWD' => 'msy',
    'DB_PORT' => '3306',
    'DB_PREFIX' => '',


    'DB_CONFIG2' => array(
        'DB_TYPE'   => 'mysql', // 数据库类型
        'DB_HOST'   => '127.0.0.1', // 服务器地址
        'DB_NAME'   => 'ucenter', // 数据库名
        'DB_USER'   => 'root', // 用户名
        'DB_PWD'    => 'msy', // 密码
        'DB_PORT'   =>  3306, // 端口
        'DB_PREFIX' => '',  //数据库表前缀
    ),
    
    'DEFAULT_MODULE'        =>  'Home',  // 默认模块
    'DEFAULT_CONTROLLER'    =>  'Index', // 默认控制器名称
    'DEFAULT_ACTION'        =>  'studio', // 默认操作名称

    //额外配置文件
    'LOAD_EXT_CONFIG' => 'ext_conf',

    //purchase支付回调地址
    'PURCHASE_NOTIFY_URL'  => "http://".$_SERVER['HTTP_HOST']."/index.php/Purchase/Payment/notify",
    //myms支付回调地址
    'MYMS_NOTIFY_URL'  => "http://".$_SERVER['HTTP_HOST']."/index.php/Myms/Payment/notify",
);