<?php
return array(
    //上传路径
    'GOODS_MAIN_IMG' => 'business/goods-main/',//商品主图
    'GOODS_THUMB_IMG' => 'business/goods-thumb/',//商品主图
    'GOODS_DETAIL_IMG' => 'business/goods-detail/',//商品主图
    'GOODS_CATEGORY_IMG' => 'business/goods-category/',//项目流程图
    'GOODS_COMMON_IMG' => 'business/goods-common/',//商品公共图
    'PURCHASER_LEVEL_IMG' => 'purchase/purchaser-level/',//采购商级别图
    'ON_OFF_LINE' => array('保留','已上架','已下架'),
    'ARR' => @include_once(APP_PATH . 'Admin/Conf/array_conf.php'),
    'MENU' => @include_once(APP_PATH . 'Admin/Conf/menu_conf.php'),
);