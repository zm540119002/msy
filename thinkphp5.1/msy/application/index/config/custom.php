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

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

return [
    'title' => '美尚云',

    /***
     * 根据系统号连接不同数据库
     */
    'system_id'=>[
        '1'=>[
            'db'=>'db_config_weiya',
            'success_url'=>'https://http://www.worldview.com.cn/index/Order/manage/order_status/2',
            'cancel_url'=>'db_config_weiya',
            'fail_url'=>'db_config_weiya',
            ],
        '2'=>[
            'db'=>'db_config_mcs',
            'success_url'=>'db_config_weiya',
            'cancel_url'=>'db_config_weiya',
            'fail_url'=>'db_config_weiya',
        ],
    ]

];


