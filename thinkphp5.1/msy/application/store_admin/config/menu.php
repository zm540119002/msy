<?php
return [
    'menu' => [
        ['name' => '商品管理',
            'sub_menu' => [
                ['name'=>'分类管理','act'=>'GoodsCategory','op'=>'goodsCategoryManage'],
                ['name' => '单位管理', 'act'=>'Unit', 'op'=>'unitManage'],
                ['name' => '商品管理', 'act'=>'Goods', 'op'=>'goodsManage'],
                ['name' => '商品公共图片管理', 'act'=>'Goods', 'op'=>'commonImageEdit'],
            ],
        ],
        ['name' => '订单管理',
            'sub_menu' => [
                ['name'=>'订单列表','act'=>'Order','op'=>'index'],
                ['name'=>'发货订单','act'=>'Order','op'=>'deliveryList'],
                ['name'=>'退款订单','act'=>'Order','op'=>'refundOrderList'],
                ['name'=>'退换货订单','act'=>'Order','op'=>'returnList'],
                ['name'=>'导出数据','act'=>'Inout','op'=>'index'],
            ],
        ],
        ['name' => '省市管理',
            'sub_menu' => [
                ['name' => '省份管理', 'act'=>'Province', 'op'=>'provinceManage'],
                ['name' => '城市管理', 'act'=>'City', 'op'=>'cityManage'],
            ],
        ],
    ],

];
