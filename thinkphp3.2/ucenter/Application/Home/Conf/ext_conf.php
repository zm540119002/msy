<?php
return array(
    //'配置项'=>'配置值'
    //机构登记出差提醒
    'ERROR_COMPANY_REGISTER_REMIND' => '您还未进行机构登记，请先登记机构信息！',
    //机构认证出差提醒
    'ERROR_COMPANY_AUTHORISE_REMIND' => '您的机构信息还未实名认证，请进行机构实名认证，谢谢！',
    //短信验证码过期时间
    'SMS_EXPIRE' => 60 * 10,

    //省市区
    'PROVINCE_CITY_AREA' => @include_once(APP_PATH . 'Common/Conf/province_city_area_conf.php'),
);