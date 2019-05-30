<?php
namespace app\index\validate;

class CityPartner extends \think\Validate
{
    //验证规则
    protected $rule = [
        'company_name'  => [
            'require','max' => 200,
        ],
        'applicant'  => [
            'require', 'max' => 25,
        ],
        'mobile'  => [
            'require', 'max' => 12,'mobile',
        ],
        'province'  => [
            'require', 'max' => 5,'number'
        ],
        'city'  => [
            'require', 'max' => 5,'number'
        ],
        'pay_code'  => [
            'require', 'max' => 1,'integer'
        ],

    ];

    //验证消息
    protected $message  =   [
        'company_name.require' => '加盟店名称必须！',
        'company_name.max' => '加盟店名称过长！',
        'mobile.mobile' => '请填写正确的手机号码！',
        'province.require' => '省份必须！',
        'province.max' => '提交省份数据不符！',
        'city.require' => '城市必须！',
        'city.max' => '提交城市数据不符！',
        'pay_code.require' => '支付方式必须！',
        'pay_code.max' => '提交支付方式不符！',
    ];

    //验证场景
    protected $scene = [
        //申请
        'step1'  =>  [
            'province',
            'city',
        ],
        'step2'  =>  [
            'company_name',
            'mobile',
            'province',
            'city',
        ],
        'step3'  =>  [
            'company_name',
            'mobile',
            'province',
            'city',
            'pay_code',
        ],
    ];
}