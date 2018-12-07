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
        ],
        'project'=>[
            'id'=>200,'name'=>'项目','type'=>2,
            'sub_menu' => [
                ['id'=>201,'name'=>'项目分类管理','display'=>1,'controller'=>'ProjectCategory','action'=>'manage',],
                ['id'=>202,'name'=>'项目管理','display'=>1,'controller'=>'Project','action'=>'manage',],
            ],
        ],


    ],
];