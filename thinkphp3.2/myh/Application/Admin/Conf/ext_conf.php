<?php
return array(
    //上传路径
    'PURCHASE_MAIN_IMG' => 'purchase/goods-main/',//商品主图
    'PURCHASE_GOODS_DETAIL_IMG' => 'purchase/goods-detail/',//商品主图
    'PURCHASE_GOODS_CATEGORY_IMG' => 'purchase/goods-category/',//项目流程图
    'PURCHASE_GOODS_COMMON_IMG' => 'purchase/goods-common/',//商品公共图
    'PURCHASER_LEVEL_IMG' => 'purchase/purchaser-level/',//采购商级别图
    'ON_OFF_LINE' => array('保留','已上架','已下架'),

    'ARR' => @include_once(APP_PATH . 'Admin/Conf/array_conf.php'),
    'MENU' => @include_once(APP_PATH . 'Admin/Conf/menu_conf.php'),
    'BUY_TYPE' => @include_once(APP_PATH . 'Admin/Conf/buy_type_conf.php'),
);