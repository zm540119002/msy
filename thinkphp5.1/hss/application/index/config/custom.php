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
        'Index/franchiseIndex' => [
            'name' => '加盟店家',
            'a'    => 'href='.url('Index/franchiseIndex'),
            'icon' => 'practitioners',
        ],
        'Index/cityPartnerIndex' => [
            'name' => '城市合伙人',
            'a'    => "href=javascript:void(0) class='async_login' data-jump_url=".url('Index/cityPartnerIndex'),
            'icon' => 'business',
        ],
        'Index/cartIndex' => [
            'name' => '采购车',
            'a'    => 'href='.url('Index/cartIndex',['type'=>1]),
            'icon' => 'cart',
        ],
        'Mine/index' => [
            'name' => '我',
            'a'    => 'href='.url('Mine/index'),
            'icon' => 'my',
        ],
    ],
    // 充值金额
    'recharge_amount'=>[1000,10000,20000,30000,50000,80000],
    //加盟费用
    'franchise_fee'=>9800,
    //城市合伙人加盟费用
    'cityPartner_fee'=>[
        //一类城市
        1=>[
            'earnest'=>5000,
            'amount'=>19800,
        ],
        //二类城市
        2=>[
            'earnest'=>5000,
            'amount'=>19800,
        ],
        //三类城市
        3=>[
            'earnest'=>5000,
            'amount'=>19800,
        ],
        //四类城市
        4=>[
            'earnest'=>5000,
            'amount'=>19800,
        ],

    ],

    // 支付链接 充值链接
    'pay_gateway' => 'https://msy.meishangyun.com/index/Payment/pay?system_id=3&sn=',
//    'pay_recharge'=> 'https://msy.meishangyun.com/index/Payment/pay?system_id=3&sn=',
//    'pay_franchise'=>'https://msy.meishangyun.com/index/Payment/pay?system_id=3&sn=',

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
        'cityPartnerSeatPay' => [
            'code' => 4,
            'name' => '城市合伙人席位订金',
        ],
        'cityPartnerBalancePay' => [
            'code' => 5,
            'name' => '城市合伙人资格尾款支付',
        ],
    ],

    // 首页场景行排列数 [1=>3,2=>2...] 第一行3个，第二行2个...
    'scene_arrangement' => [
        1 => 3,
        'default' => 2,
    ],

    // 订单状态：1:待付款 2:待发货 3:待收货 4:待评价 5:已完成 6:已取消  前端没有待发货
    'order_status' => [
        1 => [
            'code' => 1,
            'name' => '待付款',
        ],
        2 => [
            'code' => 2,
            'name' => '待收货',
        ],
        3 => [
            'code' => 3,
            'name' => '待收货',
        ],
        4 => [
            'code' => 4,
            'name' => '待评价',
        ],
        5 => [
            'code' => 5,
            'name' => '已完成',
        ],
        6 => [
            'code' => 6,
            'name' => '已取消',
        ],
    ],
];

