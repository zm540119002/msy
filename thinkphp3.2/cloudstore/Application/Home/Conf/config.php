<?php
return array(
	//'配置项'=>'配置值'
    'WEB_NAME' => '美尚云',

    //数据库
    'DB_TYPE' => 'mysql',
    'DB_HOST' => '127.0.0.1',
    'DB_NAME' => 'msy',
    'DB_USER' => 'root',
    'DB_PWD' => 'msy',
    'DB_PORT' => '3306',
    'DB_PREFIX' => '',

    //cloudstore数据库连接配置
    'DB_CLOUD_STORE' => array(
        'DB_TYPE'   => 'mysql', // 数据库类型
        'DB_HOST'   => '127.0.0.1', // 服务器地址
        'DB_NAME'   => 'msy', // 数据库名
        'DB_USER'   => 'root', // 用户名
        'DB_PWD'    => 'msy', // 密码
        'DB_PORT'   =>  3306, // 端口
        'DB_PREFIX' => '',  //数据库表前缀
    ),

    //额外配置文件
    'LOAD_EXT_CONFIG' => 'ext_conf',
);