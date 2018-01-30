<?php
return array(
    //默认购物车到期时间
    'DEFAULT_DUE_TIME' => 7 * 24 * 60 * 60,
    //默认订单到期时间
    'DEFAULT_ORDER_TIME' => 3 * 24 * 60 * 60,
    //默认团购订单到期时间
    'DEFAULT_GROUPBUY_ORDER_TIME' => 7 * 24 * 60 * 60,

    //省市区
    'PROVINCE_CITY_AREA' => @include_once(APP_PATH . 'Common/Conf/province_city_area_conf.php'),
);