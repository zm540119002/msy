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
    'error' => [
        'default' => [
            'code' => '1000',
            'msg' => '失败！',
        ],'login' => [
            'code' => '1001',
            'msg' => '您还未登录平台，请先登录！',
        ],
    ],'success' => [
        'default' => [
            'code' => '1000',
            'msg' => '成功！',
        ],'login' => [
            'code' => '1001',
            'msg' => '已登录！',
        ],
    ],
];