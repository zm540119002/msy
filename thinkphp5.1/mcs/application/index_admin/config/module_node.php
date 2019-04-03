<?php
/**type    0：所有|1：系统|2：普通
 * display    1：显示|0：隐藏
 */
return [
    'menu' => [
        'goods'=>[
            'id'=>100,'name'=>'商品','type'=>2,
            'sub_menu' => [
                ['id'=>101,'name'=>'商品分类管理','display'=>1,'controller'=>'GoodsCategory','action'=>'manage',],
                ['id'=>102,'name'=>'商品管理','display'=>1,'controller'=>'Goods','action'=>'manage',],
            ],
        ],'scene'=>[
            'id'=>200,'name'=>'场景','type'=>2,
            'sub_menu' => [
                ['id'=>202,'name'=>'场景管理','display'=>1,'controller'=>'Scene','action'=>'manage',],
            ],
        ],'project'=>[
            'id'=>300,'name'=>'项目','type'=>2,
            'sub_menu' => [
                ['id'=>301,'name'=>'项目管理','display'=>1,'controller'=>'Project','action'=>'manage',],
            ],
        ],'scheme'=>[
            'id'=>400,'name'=>'方案','type'=>2,
            'sub_menu' => [
                ['id'=>401,'name'=>'方案管理','display'=>1,'controller'=>'scheme','action'=>'manage',],
            ],
        ],'promotion'=>[
            'id'=>500,'name'=>'促销','type'=>2,
            'sub_menu' => [
                ['id'=>501,'name'=>'促销管理','display'=>1,'controller'=>'promotion','action'=>'manage',],
            ],
        ],'information'=>[
            'id'=>600,'name'=>'资讯','type'=>2,
            'sub_menu' => [
                ['id'=>601,'name'=>'资讯管理','display'=>1,'controller'=>'Information','action'=>'manage',],
            ],
        ],'customer'=>[
            'id'=>700,'name'=>'客服','type'=>2,
            'sub_menu' => [
                ['id'=>701,'name'=>'售前','display'=>1,'controller'=>'custom_client','action'=>'beforeSale',],
                ['id'=>702,'name'=>'售后','display'=>1,'controller'=>'custom_client','action'=>'afterSale',],
            ],
        ],
    ],
];