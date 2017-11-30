<?php
return array(
    //'配置项'=>'配置值'
    'WEB_NAME' => '用户中心',//网站名称

    'DEFAULT_MODULE'        =>  'Home',  // 默认模块
    'DEFAULT_CONTROLLER'    =>  'User', // 默认控制器名称
    'DEFAULT_ACTION'        =>  'login', // 默认操作名称

    //数据库连接配置1
    'DB_CONFIG1' => array(
        'DB_TYPE'   => 'mysql', // 数据库类型
        'DB_HOST'   => '127.0.0.1', // 服务器地址
        'DB_NAME'   => 'ucenter', // 数据库名
        'DB_USER'   => 'root', // 用户名
        'DB_PWD'    => 'msy', // 密码
        'DB_PORT'   =>  3306, // 端口
        'DB_PREFIX' => '',  //数据库表前缀
    ),

    'DATA_CACHE_PREFIX' => 'Redis_',//缓存前缀
    'DATA_CACHE_TYPE'=>'Redis',//默认动态缓存为Redis
    'REDIS_RW_SEPARATE' => true, //Redis读写分离 true 开启
    'REDIS_HOST'=>'127.0.0.1', //redis服务器ip，多台用逗号隔开；读写分离开启时，第一台负责写，其它[随机]负责读；
    'REDIS_PORT'=>'6379',//端口号
    'REDIS_TIMEOUT'=>'300',//超时时间
    'REDIS_PERSISTENT'=>false,//是否长连接 false=短连接

    //额外配置文件
    'LOAD_EXT_CONFIG' => 'ext_conf',
);