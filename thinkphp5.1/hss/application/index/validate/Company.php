<?php
namespace app\index\validate;

class Company extends \think\Validate
{
    //验证规则
    protected $rule = [
        'name'  => [
            'require|max:25',
        ],
        'mobile_phone'  => [
            'require', 'max' => 12,'mobile',
        ],
    ];
    protected $message  =   [
        'name.require' => '姓名必须填写',
        'name.max'     => '姓名最多不能超过25个字符',
        'mobile.mobile' => '请填写正确的手机号码！',
    ];
}