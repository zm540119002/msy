<?php
return array(
    //'配置项'=>'配置值'
    'DEFAULT_PAGE_SIZE' => 5,//默认每页显示记录数
    'NOT_POST' => '请用POST方式访问！',//不是POST
    'NOT_GET' => '请用GET方式访问！',//不是GET
    'ERROR_LOGIN_REMIND' => '您还未登录系统，请先登录！',//不是GET

    'COOKIE_PREFIX' => 'msy_',//cookie默认前缀
    'COOKIE_EXPIRE' => 60 * 60 * 24,//cookie默认有效期

    //省市区
    'PROVINCE_CITY_AREA' => @include_once(APP_PATH . 'Common/Conf/province_city_area_conf.php'),
);