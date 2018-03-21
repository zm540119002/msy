<?php
namespace common\validate;

use think\Validate;

class User extends Validate
{
    //验证规则
    protected $rule = [
        'mobile_phone'  => [
            'require','mobile','unique' => 'user',
        ],
    ];
    //验证消息
    protected $message  =   [
        'mobile_phone.require' => '手机号码必须！',
        'mobile_phone.mobile' => '请填写正确的手机号码！',
        'mobile_phone.unique' => '此号码已被注册！',
    ];
    //验证场景
    protected $scene = [
        'login'  =>  ['mobile_phone'],
        'register'  =>  ['mobile_phone'],
    ];
    //login验证场景重定义
    public function sceneLogin()
    {
        return $this->only(['mobile_phone'])
            ->remove('mobile_phone', 'unique');
    }
}