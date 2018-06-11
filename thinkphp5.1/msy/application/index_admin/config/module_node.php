<?php
/**type    0：所有|1：系统|2：普通
 * display    1：显示|0：隐藏
 */
return [
    'menu' => [
        'goods_category'=>[
            'id'=>1000,'name'=>'商品分类','type'=>2,
            'sub_menu' => [
                ['id'=>1001,'name'=>'商品分类管理','display'=>1,'controller'=>'GoodsCategory','action'=>'manage',],
            ],
        ],
    ],
];