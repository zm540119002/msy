<?php
return array(
    //'配置项'=>'配置值'
    'WEB_NAME' => '管理后台',
    'WEB_URL' => 'http://www.meishangyun.com',

    //admin数据库连接配置
    'DB_CONFIG1' => array(
        'DB_TYPE'   => 'mysql', // 数据库类型
        'DB_HOST'   => '127.0.0.1', // 服务器地址
        'DB_NAME'   => 'admin', // 数据库名
        'DB_USER'   => 'root', // 用户名
        'DB_PWD'    => 'msy', // 密码
        'DB_PORT'   =>  3306, // 端口
        'DB_PREFIX' => '',  //数据库表前缀
    ),

    //purchase数据库连接配置
    'DB_CONFIG2' => array(
        'DB_TYPE'   => 'mysql', // 数据库类型
        'DB_HOST'   => '127.0.0.1', // 服务器地址
        'DB_NAME'   => 'purchase', // 数据库名
        'DB_USER'   => 'root', // 用户名
        'DB_PWD'    => 'msy', // 密码
        'DB_PORT'   =>  3306, // 端口
        'DB_PREFIX' => '',  //数据库表前缀
    ),

    //myms数据库连接配置
    'DB_MYMS' => array(
        'DB_TYPE'   => 'mysql', // 数据库类型
        'DB_HOST'   => '127.0.0.1', // 服务器地址
        'DB_NAME'   => 'myms', // 数据库名
        'DB_USER'   => 'root', // 用户名
        'DB_PWD'    => 'msy', // 密码
        'DB_PORT'   =>  3306, // 端口
        'DB_PREFIX' => '',  //数据库表前缀
    ),

    //Cookie
    'SESSION_PREFIX' => 'admin_',
    'COOKIE_PREFIX' => 'admin_',
    'COOKIE_DOMAIN' => '',
    'COOKIE_PATH' => '/',

    //额外配置文件
    'LOAD_EXT_CONFIG' => 'ext_conf',
);