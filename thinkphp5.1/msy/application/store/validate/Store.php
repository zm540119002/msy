<?php
namespace app\store\validate;

use think\Validate;

class Store extends Validate
{
    protected $rule = [
        'name'  =>  'require|max:25',
        'agent' =>  'require|max:50',
        'business_license' =>  'require',
    ];
    protected $message  =   [
        'name.require' => '产商全称必须填写',
        'name.max'     => '产商全称最多不能超过25个字符',
        'agent.require'   => '代理人名字必须填写',
        'agent.max'   => '代理人名字最多不能超过50个字符',
        'business_license.require'   => '营业执照必须上传',
    ];
}