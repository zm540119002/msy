<?php
namespace app\store\validate;

class Shop extends \think\Validate{
    //验证规则
    protected $rule = [
        'name'  => [
            'require',
            'max' => 64,
        ],
    ];
    //验证消息
    protected $message  =   [
        'name.require' => '名称必须！',
        'name.max' => '名称最多不能超过255个字符！',
    ];
    //验证场景
    protected $scene = [
        //验证编辑
        'edit'  =>  [
            'name',
        ],
    ];
}