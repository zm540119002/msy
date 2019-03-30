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
     * name,a,icon,controller
     */
    'bottom_menu' => [
        '1' => [
            'name' => '采购商场',
            'a'    => 'href='.url('Index/index'),
            'icon' => 'store',
            'class'=> 'Index',
        ],
        '2' => [
            'name' => '加盟店家',
            'a'    => 'href='.url('StoreLeague/index'),
            'icon' => 'practitioners',
            'class'=> 'StoreLeague',
        ],
        '3' => [
            'name' => '城市合伙人',
            'a'    => 'href='.url('Business/index'),
            'icon' => 'business',
            'class'=> 'Business',
        ],
        '4' => [
            'name' => '采购车',
            'a'    => 'href='.url('Cart/index'),
            'icon' => 'cart',
            'class'=> 'Cart',
        ],
        '5' => [
            'name' => '我',
            'a'    => 'href=javascript:void(0) class=my_bottom_cart data-jump_url='.url('Mine/index'),
            'icon' => 'my',
            'class'=> 'Mine',
        ],
    ],
];

