<?php
return array(
    //上传路径
    'COMPANY_FIGURE_PATH' =>'company/figure/',
    'COMPANY_VERIFY_PATH' =>'company/verify/',
    'SELLER_AVATAR' =>'seller/avatar/',
    'SELLER_FIGURE' =>'seller/figure/',
    'SELLER_AUTHENTICATE' =>'seller/authenticate/',
    'COMPLAIN_INFORM' =>'seller/complainInform/',
    //默认每页显示记录数
    'DEFAULT_PAGE_SIZE' => 5,
    //默认机构认证到期时间
    'DEFAULT_DUE_TIME' => 180 * 24 * 60 * 60,
    //登录出错提醒
    'ERROR_LOGIN_REMIND' => '您还未登录，请先登录！',
    //机构登记出错提醒
    'ERROR_COMPANY_REGISTER_REMIND' => '您还未进行机构登记，请先登记机构信息！',
    //机构认证出错提醒
    'ERROR_COMPANY_AUTHORISE_REMIND' => '您的机构信息还未实名认证，请进行机构实名认证！',
    //不是POST
    'NOT_POST' => '请用POST方式访问！',
    //不是GET
    'NOT_GET' => '请用GET方式访问！',
    //性别
    'SEX' => array(
        '保密','男','女',
    ),
    //卖手类型
    'SELLER_TYPE' => array(
        '保留', '店销','技术','培训',
    ),
    //形象URL
    'FIGURE_URL' => array(
        'figure_url_0','figure_url_1','figure_url_2','figure_url_3',
        'figure_url_4','figure_url_5','figure_url_6','figure_url_7',
    ),
    //任务单类型
    'TASK_ORDER_TYPE' => array(
        '保留','店销分成','技术分成','培训分成',
    ),
    //任务单签约状态
    'TASK_ORDER_SIGN_STATU' => array(
        '未签约','已签约','已完成','已取消',
    ),

    //省市区
    'PROVINCE_CITY_AREA' => @include_once(APP_PATH . 'Common/Conf/province_city_area_conf.php'),
);