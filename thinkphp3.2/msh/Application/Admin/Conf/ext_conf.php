<?php
return array(
    //上传路径
    'GOODS_FIRST_IMG_PATH' =>'purchase/goods/first/',//商品首焦图
    'GOODS_DETAIL_IMG_PATH' =>'purchase/goods/detail/',//商品详情页
    'BUNDLING_IMG_PATH' =>'purchase/bundling/',//套餐图片
    'THEME_IMG_PATH' =>'purchase/theme/',//主题采购图片

    //myms
    'MYMS_GOODS_MAIN_IMG' => 'myms/goods-main-img/',//商品主图
    'MYMS_GOODS_DETAIL_IMG' => 'myms/goods-detail-img/',//商品主图
    'MYMS_PROJECT_MAIN_IMG' => 'myms/project-main-img/',//商品主图
    'MYMS_PROJECT_DETAIL_IMG' => 'myms/project-detail-img/',//项目详情图
    'MYMS_PROJECT_EXPLAIN_IMG' => 'myms/project-explain-img/',//项目说明图
    'MYMS_PROJECT_FLOW_IMG' => 'myms/project-flow-img/',//项目流程图
    'MYMS_GOODS_CATEGORY_IMG' => 'myms/goods-category-img/',//项目流程图
    'MYMS_PROJECT_CATEGORY_IMG' => 'myms/project-category-img/',//项目流程图
    'MYMS_GOODS_COMMON_IMG' => 'myms/goods-common-img/',//商品公共图
    'ON_OFF_LINE' => array('保留','已上架','已下架'),

    'MENU' => @include_once(APP_PATH . 'Admin/Conf/menu_conf.php'),
);