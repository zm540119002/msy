<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

return [
    'title' => '美尚云',
    'error_login' => '您还未登录平台，请先登录！',
    'default_page_size' => 5,//默认每页显示记录数
    'not_ajax' => '请用AJAX方式访问！',//不是POST
    'not_post' => '请用POST方式访问！',//不是POST
    'not_get' => '请用GET方式访问！',//不是GET
    'sms_expire' => 60 * 10,//短信验证码过期时间
    'sms_sign_name' => '美尚云',//短信签名名称（阿里云）
    'sms_template_code' => 'SMS_127169884',//短信模板CODE（阿里云）
    'factory_cache_time' => 60 * 60 * 24,//供应商缓存时间
    'store_cache_time' => 60 * 60 * 24,//厂商缓存时间
    'system_type' => [
        '1000' => 'factory',
        '1001' => 'store',
    ],'store_type' => [
        '1' => '企业官方旗舰店',
        '2' => '品牌旗舰店',
    ],'run_type' => [
        '1' => '采购店铺',
        '2' => '零售店铺',
        '3' => '分成店铺',
        '4' => '美尚会云店',
        '5' => '机构公众号内嵌式门店',
    ],'operational_model'=>[
        '1'=>'联营',
        '2'=>'自营',
    ],
];
