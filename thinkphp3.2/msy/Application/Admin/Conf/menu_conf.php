<?php
return array(
    //系统设置
    'purchaser'=>array(
        'name'=>'采购商','icon'=>'fa-book',
        'sub_menu'=>array(
            array('name' => '级别管理', 'act'=>'Level', 'op'=>'levelManage'),
        ),
    ),

    //采购商城
    'mall'=>array(
        'name'=>'采购商城','icon'=>'fa-book',
        'sub_menu'=>array(
            array('name' => '商品分类管理', 'act'=>'GoodsCategory', 'op'=>'goodsCategoryManage'),
            array('name' => '商品管理', 'act'=>'Goods', 'op'=>'goodsManage'),
            array('name' => '单位管理', 'act'=>'Unit', 'op'=>'unitManage'),
            array('name' => '代金券管理', 'act'=>'Coupons', 'op'=>'couponsManage'),
        ),
    ),
);