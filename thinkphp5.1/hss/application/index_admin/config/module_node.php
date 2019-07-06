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
            'id'=>200,'name'=>'整体场景','type'=>2,
            'sub_menu' => [
                ['id'=>202,'name'=>'整体场景管理','display'=>1,'controller'=>'Scene','action'=>'manage',],
            ],
        ],
/*        'project'=>[
            'id'=>300,'name'=>'项目','type'=>2,
            'sub_menu' => [
                ['id'=>301,'name'=>'项目管理','display'=>1,'controller'=>'Project','action'=>'manage',],
            ],
        ],
        'scheme'=>[
            'id'=>400,'name'=>'分类','type'=>2,
            'sub_menu' => [
                ['id'=>401,'name'=>'分类管理','display'=>1,'controller'=>'Sort','action'=>'manage',],
            ],
        ],*/
        'promotion'=>[
            'id'=>500,'name'=>'套餐','type'=>2,
            'sub_menu' => [
                ['id'=>501,'name'=>'套餐管理','display'=>1,'controller'=>'Promotion','action'=>'manage',],
            ],
        ],'ad'=>[
            'id'=>600,'name'=>'广告','type'=>2,
            'sub_menu' => [
                ['id'=>601,'name'=>'广告管理','display'=>1,'controller'=>'AdPositions','action'=>'manage',],
            ],
        ],'information'=>[
            'id'=>700,'name'=>'订单','type'=>2,
            'sub_menu' => [
                ['id'=>701,'name'=>'订单管理','display'=>1,'controller'=>'Order','action'=>'manage',],
            ],
        ], 'city_partner'=>[
            'id'=>700,'name'=>'城市合伙人','type'=>2,
            'sub_menu' => [
                ['id'=>701,'name'=>'管理','display'=>1,'controller'=>'CityArea','action'=>'manage',],
            ],
        ],'weixin_share'=>[
            'id'=>800,'name'=>'微信分享','type'=>2,
            'sub_menu' => [
                ['id'=>801,'name'=>'管理','display'=>1,'controller'=>'WeixinShare','action'=>'manage',],
            ],
        ],'customer'=>[
            'id'=>900,'name'=>'客服','type'=>2,
            'sub_menu' => [
                ['id'=>901,'name'=>'售前','display'=>1,'controller'=>'custom_client','action'=>'beforeSale',],
                ['id'=>902,'name'=>'售后','display'=>1,'controller'=>'custom_client','action'=>'afterSale',],
            ],
        ],'sign_up'=>[
            'id'=>1000,'name'=>'第2期推介会名单','type'=>2,
            'sub_menu' => [
                ['id'=>1001,'name'=>'管理','display'=>1,'controller'=>'SignUp','action'=>'manage',],
            ],
        ],

    ],
];