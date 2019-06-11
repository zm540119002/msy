<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 测试服务器
if(strpos(PHP_OS,"WIN")!==false){
    return [
        // 数据库类型
        'type'            => 'mysql',
        // 服务器地址
        'hostname'        =>  '127.0.0.1',
        // 数据库名
        'database'        => 'weiya',
        // 用户名
        'username'        => 'root',
        // 密码
        'password'        => 'msy',
        // 端口
        'hostport'        => '3306',
        // 连接dsn
        'dsn'             => '',
        // 数据库连接参数
        'params'          => [],
        // 数据库编码默认采用utf8
        'charset'         => 'utf8',
        // 数据库表前缀
        'prefix'          => '',
        // 数据库调试模式
        'debug'           => true,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy'          => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate'     => false,
        // 读写分离后 主服务器数量
        'master_num'      => 1,
        // 指定从服务器序号
        'slave_no'        => '',
        // 是否严格检查字段是否存在
        'fields_strict'   => true,
        // 数据集返回类型
        'resultset_type'  => 'array',
        // 自动写入时间戳字段
        'auto_timestamp'  => false,
        // 时间字段取出后的默认时间格式
        'datetime_format' => 'Y-m-d H:i:s',
        // 是否需要进行SQL性能分析
        'sql_explain'     => false,
        // Query类
        'query'           => '\\think\\db\\Query',
        //数据库配置-公共
        'db_config_common' => [
            'type'        => 'mysql',// 数据库类型
            'hostname'    => '47.106.79.38',// 服务器地址
            'database'    => 'test_common',// 数据库名
            'username'    => 'root',// 数据库用户名
            'password'    => 'msy',// 数据库密码
            'charset'     => 'utf8',// 数据库编码默认采用utf8
        ],
        //数据库配置
        'db_config_1' => [
            'type'        => 'mysql',// 数据库类型
            'hostname'    => '47.106.79.38',// 服务器地址
            'database'    => 'test_hss',// 数据库名
            'username'    => 'root',// 数据库用户名
            'password'    => 'msy',// 数据库密码
            'charset'     => 'utf8',// 数据库编码默认采用utf8
        ],
    ];
}

return [
    // 数据库类型
    'type'            => 'mysql',
    // 服务器地址
    'hostname'        =>  '127.0.0.1',
    // 数据库名
    'database'        => 'weiya',
    // 用户名
    'username'        => 'root',
    // 密码
    'password'        => 'msy',
    // 端口
    'hostport'        => '3306',
    // 连接dsn
    'dsn'             => '',
    // 数据库连接参数
    'params'          => [],
    // 数据库编码默认采用utf8
    'charset'         => 'utf8',
    // 数据库表前缀
    'prefix'          => '',
    // 数据库调试模式
    'debug'           => true,
    // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'deploy'          => 0,
    // 数据库读写是否分离 主从式有效
    'rw_separate'     => false,
    // 读写分离后 主服务器数量
    'master_num'      => 1,
    // 指定从服务器序号
    'slave_no'        => '',
    // 是否严格检查字段是否存在
    'fields_strict'   => true,
    // 数据集返回类型
    'resultset_type'  => 'array',
    // 自动写入时间戳字段
    'auto_timestamp'  => false,
    // 时间字段取出后的默认时间格式
    'datetime_format' => 'Y-m-d H:i:s',
    // 是否需要进行SQL性能分析
    'sql_explain'     => false,
    // Query类
    'query'           => '\\think\\db\\Query',
    //数据库配置-公共
    'db_config_common' => [
        'type'        => 'mysql',// 数据库类型
        'hostname'    => '127.0.0.1',// 服务器地址
        'database'    => 'common',// 数据库名
        'username'    => 'root',// 数据库用户名
        'password'    => 'msy',// 数据库密码
        'charset'     => 'utf8',// 数据库编码默认采用utf8
    ],
    //数据库配置
    'db_config_1' => [
        'type'        => 'mysql',// 数据库类型
        'hostname'    => '127.0.0.1',// 服务器地址
        'database'    => 'hss',// 数据库名
        'username'    => 'root',// 数据库用户名
        'password'    => 'msy',// 数据库密码
        'charset'     => 'utf8',// 数据库编码默认采用utf8
    ],
];
