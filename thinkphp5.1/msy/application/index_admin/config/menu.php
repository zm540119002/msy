<?php
return [
    'menu' => [
        [
            'name' => '系统管理',
            'sub_menu' => [
                ['name' => '节点管理', 'controller'=>'Node', 'action'=>'manage'],
                ['name' => '角色管理', 'controller'=>'Role', 'action'=>'manage'],
                ['name' => '用户管理', 'controller'=>'User', 'action'=>'manage'],
            ],
        ],[
            'name' => '商品管理',
            'sub_menu' => [
                ['name'=>'分类管理','controller'=>'GoodsCategory','action'=>'manage'],
                ['name' => '单位管理', 'controller'=>'Unit', 'action'=>'manage'],
                ['name' => '商品管理', 'controller'=>'Goods', 'action'=>'manage'],
                ['name' => '商品公共图片管理', 'controller'=>'Goods', 'action'=>'commonImageEdit'],
            ],
        ],[
            'name' => '订单管理',
            'sub_menu' => [
                ['name'=>'订单列表','controller'=>'Order','action'=>'index'],
                ['name'=>'发货订单','controller'=>'Order','action'=>'deliveryList'],
                ['name'=>'退款订单','controller'=>'Order','action'=>'refundOrderList'],
                ['name'=>'退换货订单','controller'=>'Order','action'=>'returnList'],
                ['name'=>'导出数据','controller'=>'Inout','action'=>'index'],
            ],
        ],[
            'name' => '省市管理',
            'sub_menu' => [
                ['name' => '省份管理', 'controller'=>'Province', 'action'=>'manage'],
                ['name' => '城市管理', 'controller'=>'City', 'action'=>'manage'],
            ],
        ],
    ],
];
