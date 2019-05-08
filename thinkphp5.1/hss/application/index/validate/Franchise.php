<?php
namespace app\index\validate;

class Franchise extends \think\Validate
{
    //验证规则
    protected $rule = [
        'name'  => [
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
        'area'  => [
            'require', 'max' => 5,'number'
        ],
        'detail_address'  => [
            'require', 'max' => 200,
        ],
        'payment_code'  => [
            'require', 'max' => 1,'integer'
        ],

    ];

    //验证消息
    protected $message  =   [
        'name.require' => '加盟店名称必须！',
        'name.max' => '加盟店名称过长！',
        'mobile.mobile' => '请填写正确的手机号码！',
        'province.require' => '省份必须！',
        'province.max' => '提交省份数据不符！',
        'city.require' => '城市必须！',
        'city.max' => '提交城市数据不符！',
        'area.require' => '地区必须！',
        'area.max' => '提交地区数据不符！',
        'detail_address.require' => '详情地址必须！',
        'detail_address.max' => '详情地址过长！',
        'payment_code.require' => '支付方式必须！',
        'payment_code.max' => '提交支付方式不符！',
    ];

    //验证场景
    protected $scene = [
        //申请
        'add'  =>  [
            'name',
            'mobile',
            'province',
            'city',
            'area',
            'detail_address',
            'payment_code',
        ],

    ];



}