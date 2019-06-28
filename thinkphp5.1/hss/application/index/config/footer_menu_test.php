<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c] 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 ]
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

return [
    //底部购物车配置
    'menu' => [
        [//0
            'name'   => '',
            'class'=>[
                'bottom_item',
                'amount',
            ],
        ],[//1
            'name'   => '加入购物车',
            'class'=>[
                'bottom_item',
                'add_cart',
            ],
            'action'=>url('Cart/addCart'),
        ],[//2
            'name'   => '购物车',
            'class'=>[
                'bottom_item',
                'add_cart_icon',
            ],
            'action'=>url('Index/cartIndex'),
        ],[//3
            'name'   => '立即购买',
            'class'=>[
                'bottom_item',
                'buy_now',
            ],
            'action'=>url('Order/generate'),
        ],[//4
            'name'   => '提交订单',
            'class'=>[
                'bottom_item',
                'confirm_order',
            ],
        ],[//5
            'name'   => '支付',
            'class'=>[
                'bottom_item',
                'pay',
            ],
        ],[//6
            'name'   => '增加地址',
            'class'=>[
                'bottom_item',
                'address_save',
            ],
        ],[//7
            'name'   => '修改地址',
            'class'=>[
                'bottom_item',
                'address_save',
            ],
        ],[//8
            'name'   => '新建地址',
            'class'=>[
                'bottom_item',
                'address_create',
            ],
        ],[//9
            'name'   => '结算',
            'class'=>[
                'bottom_item',
                'settlement',
            ],
            'action'=>url('Order/generate'),
        ],[//10
            'name'   => '',
            'class'=>[
                'bottom_item',
                'checked_all',
            ],
        ],[//11
            'name'   => '提交订单',
            'class'=>[
                'bottom_item',
                'confirm_order',
            ],
            'action'=>url('Order/confirmOrder'),
        ],[//12
            'name'   => '确认收货',
            'class'=>[
                'bottom_item',
                'confirm_receive',
            ],
            'action'=>url('Order/setOrderStatus'),
        ], [//13
            'name'   => '立即评价',
            'class'=>[
                'bottom_item',
                'to_evaluate',
            ],
            'action'=>url('Order/setOrderStatus'),
        ],[//14
            'name'   => '再次购买',
            'class'=>[
                'bottom_item',
                'purchase_again',
            ],
        ],[//15
            'name'   => '增加商标',
            'class'=>[
                'bottom_item',
                'add_brand',
            ],
        ],[//16
            'name'   => '立即提交',
            'class'=>[
                'bottom_item',
                'submit',
            ],
        ],[//17
            'name'   => '删除',
            'class'=>[
                'bottom_item',
                'delete',
            ],
        ],[//18
            'name'   => '查看物流',
            'class'=>[
                'bottom_item',
                'confirm_receive',
            ],
        ],[//19
            'name'   => '查看物流2',
            'class'=>[
                'bottom_item',
                'delete',
            ],
        ],[// 20
            'name'   => '去结算',
            'class'=>[
                'bottom_item',
                'confirm_order',
            ],
            'action'=>url('Order/confirmOrder'),
        ],[// 21
            'name'   => '申请加盟店资格',
            'class'=>[
                'bottom_item',
                'apply_franchisee_qualification'
            ],
            'action'=>url('/index/Franchise/applyFranchise'),
        ],[// 22
            'name'   => '申请城市合伙人资格',
            'class'=>[
                'bottom_item',
                'apply_city_partner_qualification'
            ],
            'action'=>url('/index/CityPartner/registered'),
        ],[// 23
            'name'   => '取消订单',
            'class'=>[
                'bottom_item',
                'cancel_order',
            ],
            'action'=>url('Order/setOrderStatus'),
        ],
    ],
];
