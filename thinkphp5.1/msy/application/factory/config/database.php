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

return [
    // 服务器地址
    'hostname'        => '127.0.0.1',
    // 数据库名
    'database'        => 'msy_factory',
    // 用户名
    'username'        => 'root',
    // 密码
    'password'        => 'msy',

    'db_factory' => [
        // 数据库类型
        'type'        => 'mysql',
        // 服务器地址
        'hostname'    => '127.0.0.1',
        // 数据库名
        'database'    => 'msy_factory',
        // 数据库用户名
        'username'    => 'root',
        // 数据库密码
        'password'    => 'msy',
        // 数据库编码默认采用utf8
        'charset'     => 'utf8',
        // 数据库表前缀
        'prefix'      => '',
    ],
    //数据库配置2
    'db_config2' => 'mysql://root:1234@192.168.1.10:3306/thinkphp#utf8',
];
