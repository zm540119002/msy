<?php
return array(
    //抢单限额次数
    'REQUEST_QUOTA' => 10000,
    //卖手认证出错提醒
    'ERROR_SELLER_AUTHORISE_REMIND' => '您还不是卖手，请进行卖手免费实名认证！',

    //省市区
    'PROVINCE_CITY_AREA' => @include_once(APP_PATH . 'Common/Conf/province_city_area_conf.php'),
);