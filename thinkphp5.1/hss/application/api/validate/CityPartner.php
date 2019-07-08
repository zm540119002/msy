<?php
namespace app\api\validate;

class CityPartner extends \think\Validate
{
    //验证规则
    protected $rule = [
        'province'  => [
            'require', 'max' => 6,'number'
        ],
        'city'  => [
            'require', 'max' => 6,'number'
        ],
        'company_name'  => [
            'require','max' => 200,
        ],
        'applicant'  => [
            'require', 'max' => 25,
        ],
        'mobile'  => [
            'require', 'max' => 12,'mobile',
        ],
        'pay_code'  => [
            'require', 'max' => 1,'integer'
        ],
    ];

    //验证消息
    protected $message  =   [
        'province.require' => '请选择省份！',
        'province.max' => '提交省份数据不符！',
        'city.require' => '请选择城市！',
        'city.max' => '提交城市数据不符！',
        'company_name.require' => '加盟店名称必须！',
        'company_name.max' => '加盟店名称过长！',
        'applicant.require' => '申请人名称必须！',
        'applicant.max' => '申请人名称过长！',
        'mobile.require' => '手机号码为空！',
        'mobile.mobile' => '请填写正确的手机号码！',
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
            'province',
            'city',
            'company_name',
            'applicant',
            'mobile',
        ],
        'step3'  =>  [
            'province',
            'city',
            'company_name',
            'applicant',
            'mobile',
            'pay_code',
        ],
    ];
}