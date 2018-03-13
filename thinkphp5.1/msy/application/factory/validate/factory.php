<?php
namespace app\factory\validate;

use think\facade\Validate;

class Factory extends Validate
{
    protected $rule = [
        'name'  =>  'require|max:25',
        'agent' =>  'require|max:50',
        'business_license' =>  'require|max:50',
        'auth_letter' =>  'require|max:150',
    ];
    protected $message  =   [
        'name.require' => '名称必须',
        'name.max'     => '名称最多不能超过25个字符',
        'agent.require'   => '年龄必须是数字',
        'agent.max'   => '名称最多不能超过25个字符',
        'business_license.require'   => '年龄必须是数字',
        'business_license.max'   => '名称最多不能超过25个字符',
        'auth_letter.require'   => '年龄必须是数字',
        'auth_letter.max'   => '名称最多不能超过25个字符',
    ];

}