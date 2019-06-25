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

    //支付完成后，跳转页面
    'return_url' => 'https://msy.meishangyun.com/index/Payment/payComplete/',

    /***
     * 根据系统号连接不同数据库
     */
    'system_id'=>[
        '1'=>[
            'db'=>'db_config_weiya',
            'jump_url'=>[
                1 => 'https://www.worldview.com.cn/index/Order/manage/order_status/2',
                2 => 'https://mcs.meishangyun.com/index/Wallet/index',
            ],
        ],
        '2'=>[
            'db'=>'db_config_mcs',
            'jump_url'=>[
                1 => 'https://mcs.meishangyun.com/index/Order/manage/order_status/2',
                2 => 'https://mcs.meishangyun.com/index/Wallet/index',
            ],
        ],
        '3'=>[
            'db'=>'db_config_hss',
            'jump_url'=>[
                1 => 'https://hss.meishangyun.com/index/Order/manage/order_status/2', //订单支付成功跳转
                2 => 'https://hss.meishangyun.com/index/Wallet/index',  //钱包支付成功跳转
                3 => 'https://hss.meishangyun.com/index/Franchise/applyFranchise', //申请加盟店家支付成功跳转
                4 => 'https://hss.meishangyun.com/index/CityPartner/applicationList', //申请城市合伙人定金支付成功跳转
            ],
        ],
    ],
    /**
     * 支付方式 1： 订单支付 2：充值支付
     */
    'payment_types'=>[
        'order'=>1,
        'recharge'=>2
    ],

];


