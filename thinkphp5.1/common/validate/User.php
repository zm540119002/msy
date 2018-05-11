<?php
namespace common\validate;

class User extends \think\Validate
{
    //验证规则
    protected $rule = [
        'mobile_phone'  => [
            'require',
            'mobile',
            'unique' => '\common\model\User',
        ],
        'captcha'  => [
            'regex' => '/^\d{6}$/',
        ],
        'password'  => [
            'regex' => '/^[A-Za-z0-9]{6,16}$/',
        ],
    ];
    
    //验证消息
    protected $message  =   [
        'mobile_phone.require' => '手机号码必须！',
        'mobile_phone.mobile' => '请填写正确的手机号码！',
        'mobile_phone.unique' => '此号码已被注册！',
        'captcha.regex' => '验证码格式错误！',
        'password.regex' => '密码格式错误！',
    ];

    //验证场景
    protected $scene = [
        //密码登录
        'login'  =>  [
            'mobile_phone',
            'password',
        ],
        //注册
        'register'  =>  [
            'mobile_phone',
            'captcha',
            'password',
        ],
        //修改密码
        'resetPassword'  =>  [
            'mobile_phone',
            'captcha',
            'password',
        ],
    ];

    // login 验证场景定义
    public function sceneLogin()
    {
        return $this->only(['mobile_phone','password'])
            ->remove('mobile_phone',['unique',]);
    }

    // resetPassword 验证场景定义
    public function sceneResetPassword()
    {
        return $this->only(['mobile_phone','captcha','password'])
            ->remove('mobile_phone',['unique',]);
    }
}