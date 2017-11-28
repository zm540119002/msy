<?php
return array(
    //上传路径
    'COMPANY_FIGURE_PATH' =>'company/figure/',
    'COMPANY_VERIFY_PATH' =>'company/verify/',
    'SELLER_AVATAR' =>'seller/avatar/',
    'SELLER_FIGURE' =>'seller/figure/',
    'SELLER_AUTHENTICATE' =>'seller/authenticate/',
    'COMPLAIN_INFORM' =>'seller/complainInform/',

    //省市区
    'PROVINCE_CITY_AREA' => @include_once(APP_PATH . 'Common/Conf/province_city_area_conf.php'),
);