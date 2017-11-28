<?php
return array(
    //上传路径
    'SHOP_LOGO' =>'shop/logo/',
    'PROJECT_LIST_VIEW' =>'project/listview/',
    'PROJECT_HOME_FOCUS' =>'project/homefocus/',
    //机构规模配置
    'SCALE' => @include_once(APP_PATH . 'Common/Conf/scale_conf.php'),
    //职位配置
    'POSITION' => @include_once(APP_PATH . 'Common/Conf/position_conf.php'),
    //省市区
    'PROVINCE_CITY_AREA' => @include_once(APP_PATH . 'Common/Conf/province_city_area_conf.php'),
);