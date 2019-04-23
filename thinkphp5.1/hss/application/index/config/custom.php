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
    'title' => '黑森森',
    'module_type' => 1,//模块类型
    'default_page_size'=>10,

    /**
     * 底部菜单 :采购商场，加盟店家，城市合伙人，采购车，我
     * name,a,icon
     */
    'footer_menu' => [
        'Index/index' => [
            'name' => '采购商场',
            'a'    => 'href='.url('Index/index'),
            'icon' => 'store',
        ],
        'StoreLeague/index' => [
            'name' => '加盟店家',
            //'a'    => 'href='.url('StoreLeague/index'),
            'a'    => "href=javascript:void(0) class='async_login' data-jump_url=".url('StoreLeague/index'),
            'icon' => 'practitioners',
        ],
        'CityPartner/index' => [
            'name' => '城市合伙人',
            //'a'    => 'href='.url('Business/index'),
            'a'    => "href=javascript:void(0) class='async_login' data-jump_url=".url('CityPartner/index'),
            'icon' => 'business',
        ],
        'Cart/manage' => [
            'name' => '采购车',
            'a'    => "href=javascript:void(0) class='my_bottom_cart async_login' data-jump_url=".url('Cart/manage'),
            'icon' => 'cart',
        ],
        'Mine/index' => [
            'name' => '我',
            'a'    => 'href='.url('Mine/index'),
            'icon' => 'my',
        ],
    ],
    // 充值金额
    'recharge_amount'=>[5000,10000,20000,30000,50000,80000],

    // 支付链接
    'pay_gateway' => 'https://msy.meishangyun.com/index/Payment/toPay/system_id/3/order_sn/',
    'pay_recharge'=> 'https://msy.meishangyun.com/index/Payment/toPay/system_id/3/order_sn/',


    // 付款方式 1 微信 2：支付宝 3：网银 4:线下支付
    'pay_code' => [
        'WeChatPay' => [
          'code' => 1,
          'name' => '微信支付',
        ],
        'Alipay' => [
            'code' => 2,
            'name' => '支付宝',
        ],
        'UnionPay' => [
            'code' => 3,
            'name' => '银联支付',
        ],
        'OfflinePay' => [
            'code' => 4,
            'name' => '线下支付',
        ],
    ],
];

