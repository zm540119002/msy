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

    //美妍美社菜单
    'myms'=>array(
        'name'=>'基本配置','icon'=>'fa-book',
        'sub_menu'=>array(
            array('name' => '商品分类', 'act'=>'GoodsCategory', 'op'=>'goodsCategoryManage'),
            array('name' => '商品管理', 'act'=>'Goods', 'op'=>'goodsManage'),
            array('name' => '项目分类', 'act'=>'ProjectCategory', 'op'=>'projectCategoryManage'),
            array('name' => '项目管理', 'act'=>'Project', 'op'=>'projectManage'),
            array('name' => '优惠券管理', 'act'=>'Coupons', 'op'=>'couponsManage'),
        ),
    ),
);