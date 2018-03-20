<?php
namespace common\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        'mobile_phone' => 'require|mobile',
        'mobile_phone' => 'unique:user',
    ];
    protected $message  =   [
        'mobile_phone.require' => '请填写正确的手机号码！',
        'mobile_phone.unique' => '此号码已被注册！',
    ];
    protected $scene = [
        'add' => ['mobile_phone.require',],
        'edit' => ['mobile_phone.require','mobile_phone.unique',],
    ];
}