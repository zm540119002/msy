<?php
return array(
    //系统设置
//    'system'=>array(
//        'name'=>'系统设置','icon'=>'fa-book',
//        'sub_menu'=>array(
//            array('name' => '系统首页', 'act'=>'Home', 'op'=>'index'),
//            array('name' => '系统设置', 'act'=>'Set', 'op'=>'index'),
//        ),
//    ),

    //采购平台菜单
    'purchase'=>array(
        'name'=>'采购平台','icon'=>'fa-book',
        'sub_menu'=>array(
            array('name' => '分类管理', 'act'=>'PurchaseGoodsCategory', 'op'=>'goodsCategoryList'),
            array('name' => '商品管理', 'act'=>'PurchaseGoods', 'op'=>'goodsList'),
            array('name' => '促销管理', 'act'=>'PurchasePromotion', 'op'=>'bundlingList'),
            array('name' => '订单管理', 'act'=>'PurchaseOrder', 'op'=>'orderList'),
        ),
    ),

    //美妍美社菜单
    'myms'=>array(
        'name'=>'美妍美社','icon'=>'fa-book',
        'sub_menu'=>array(
            array('name' => '商品分类', 'act'=>'MymsGoodsCategory', 'op'=>'goodsCategoryManage'),
            array('name' => '商品管理', 'act'=>'MymsGoods', 'op'=>'goodsManage'),
            array('name' => '项目分类', 'act'=>'MymsProjectCategory', 'op'=>'projectCategoryManage'),
            array('name' => '项目管理', 'act'=>'MymsProject', 'op'=>'projectManage'),
        ),
    ),
);