<?php
namespace app\store\validate;

class Shop extends \think\Validate{
    //验证规则
    protected $rule = [
        'name'  => [
            'require',
            'max' => 64,
            'unique' => '\app\store\model\Shop',
        ],'operation_mobile_phone'  => [
            'require',
            'max' => 15,
        ],'operation_fix_phone'  => [
            'require',
            'max' => 15,
        ],'operation_address'  => [
            'require',
            'max' => 128,
        ],'consignee_mobile_phone'  => [
            'require',
            'max' => 15,
        ],'consignee_fix_phone'  => [
            'require',
            'max' => 15,
        ],'consignee_address'  => [
            'require',
            'max' => 128,
        ],
    ];
    //验证消息
    protected $message  =   [
        'name.require' => '名称必须！',
        'name.max' => '名称最多不能超过255个字符！',
        'name.unique' => '此名称已被注册！',
    ];
    //验证场景
    protected $scene = [
        //验证编辑
        'edit'  =>  [
            'name',
        ],'consignee_address'  =>  [
            'operation_mobile_phone',
            'operation_fix_phone',
            'operation_address',
        ],'consignee_address'  =>  [
            'consignee_mobile_phone',
            'consignee_fix_phone',
            'consignee_address',
        ],
    ];
}