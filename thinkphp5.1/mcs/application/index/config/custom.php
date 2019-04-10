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
    'title' => '美创社',
    'module_type' => 1,//模块类型
    'default_page_size'=>10,

    /**
     * 底部菜单 :美创社，中心店，工作室，采购车，我
     * name,a,icon
     */
    'footer_menu' => [
/*        'Index/index' => [
            'name' => '美创社',
            'a'    => 'href='.url('Index/index'),
            'icon' => 'store',
        ],*/
        'CenterStore/index' => [
            'name' => '中心店',
            'a'    => 'href='.url('CenterStore/index'),
            'icon' => 'practitioners',
        ],
        'Studio/index' => [
            'name' => '工作室',
            'a'    => 'href='.url('Studio/index'),
            'icon' => 'business',
        ],
        'Cart/manage' => [
            'name' => '采购车',
            'a'    => "href=javascript:void(0) class='my_bottom_cart async_login' data-jump_url=.url('Cart/manage')",
            'icon' => 'cart',
        ],
        'Mine/index' => [
            'name' => '我',
            'a'    => 'href='.url('Mine/index'),
            'icon' => 'my',
        ],
    ],

    'pay_gateway'=>'https://msy.meishangyun.com/index/Payment/toPay/system_id/2/order_sn/'
];

