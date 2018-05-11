<?php
/**type    0：所有|1：系统|2：普通
 * display    1：显示|0：隐藏
 */
return [
    'menu' => [
        'goods'=>[
            'id'=>40,'name'=>'商品管理','type'=>2,
            'sub_menu'=>[
                ['id'=>41,'name'=>'分类管理','display'=>1,'controller'=>'GoodsCategory','action'=>'manage',],
                ['id'=>42,'name'=>'单位管理','display'=>1,'controller'=>'Unit','action'=>'manage',],
                ['id'=>43,'name'=>'商品管理','display'=>1,'controller'=>'Goods','action'=>'manage',],
            ],
        ],
        'order'=>[
            'id'=>50,'name'=>'订单管理','type'=>2,
            'sub_menu'=>[
                ['id'=>51,'name'=>'订单列表','display'=>1,'controller'=>'Order','action'=>'index',],
                ['id'=>52,'name'=>'发货订单','display'=>1,'controller'=>'Order','action'=>'deliveryList',],
                ['id'=>53,'name'=>'退款订单','display'=>1,'controller'=>'Order','action'=>'refundOrderList',],
                ['id'=>54,'name'=>'退换货订单','display'=>1,'controller'=>'Order','action'=>'returnList',],
                ['id'=>55,'name'=>'导出数据','display'=>1,'controller'=>'Inout','action'=>'index',],
            ],
        ],
        'city'=>[
            'id'=>60,'name'=>'省市管理','type'=>2,
            'sub_menu' => [
                ['id'=>61,'name'=>'省份管理','display'=>1,'controller'=>'Province','action'=>'manage',],
                ['id'=>62,'name'=>'城市管理','display'=>1,'controller'=>'City','action'=>'manage',],
            ],
        ],
    ],
];