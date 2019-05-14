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
        'Franchise/index' => [
            'name' => '加盟店家',
            'a'    => 'href='.url('Index/franchiseIndex'),
//            'a'    => "href=javascript:void(0) class='async_login' data-jump_url=".url('Franchise/index'),
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
            'a'    => 'href='.url('Index/cartManage'),
//            'a'    => "href=javascript:void(0) class='my_bottom_cart async_login' data-jump_url=".url('Cart/manage'),
            'icon' => 'cart',
        ],
        'Mine/index' => [
            'name' => '我',
            'a'    => 'href='.url('Mine/index'),
            'icon' => 'my',
        ],
    ],
    // 充值金额
    'recharge_amount'=>[0.01,10000,20000,30000,50000,80000],
    //加盟费用
    'franchise_fee'=>0.01,

    // 支付链接 充值链接
    'pay_gateway' => 'https://msy.meishangyun.com/index/Payment/pay?system_id=3&sn=',
//    'pay_gateway' => 'https://msy.meishangyun.com/index/Payment/toPay?system_id=3&payment_type=1&sn=',
   // 'pay_recharge'=> 'https://msy.meishangyun.com/index/Payment/toPay?system_id=3&payment_type=2&sn=',
    'pay_recharge'=> 'https://msy.meishangyun.com/index/Payment/pay?system_id=3&sn=',
    'pay_franchise'=>'https://msy.meishangyun.com/index/Payment/pay?system_id=3&sn=',

    // 支付方式 1 微信 2：支付宝 3：网银 4:钱包
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
        'walletPay' => [
            'code' => 4,
            'name' => '钱包支付',
        ],
    ],

    // 充值方式 1 微信 2：支付宝 3：网银 4:线下支付
    'recharge_code' => [
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

    // 支付单的类型 1 订单 2：充值 3：加盟
    'pay_type' => [
        'orderPay' => [
            'code' => 1,
            'name' => '订单支付',
        ],
        'rechargePay' => [
            'code' => 2,
            'name' => '充值支付',
        ],
        'franchisePay' => [
            'code' => 3,
            'name' => '加盟支付',
        ],
    ],

    // 首页场景行排列数 [1=>3,2=>2...] 第一行3个，第二行2个...
    'scene_arrangement' => [
        1 => 3,
        'default' => 2,
    ]
];

