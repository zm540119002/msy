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
            'a'    => 'href='.url('StoreLeague/index'),
            'icon' => 'practitioners',
        ],
        'Business/index' => [
            'name' => '城市合伙人',
            'a'    => 'href='.url('Business/index'),
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
];

