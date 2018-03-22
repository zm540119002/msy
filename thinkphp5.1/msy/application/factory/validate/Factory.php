<?php
namespace app\factory\validate;

use think\Validate;

class Factory extends Validate
{

    protected $rule = [
        'name'  =>  'require|max:25',
        'agent' =>  'require|max:50',
        'business_license' =>  'require|max:50',
        'auth_letter' =>  'require|max:150',
    ];
    protected $message  =   [
        'name.require' => '产商全称必须填写',
        'name.max'     => '产商全称最多不能超过25个字符',
        'agent.require'   => '代理人名字必须填写',
        'agent.max'   => '代理人名字最多不能超过25个字符',
        'business_license.require'   => '营业执照必须上传',
        'business_license.max'   => '营业执照存在地址过长',
        'auth_letter.require'   => '授权信必须上传',
        'auth_letter.max'   => '授权信存在地址最多不能超过25个字符',
    ];
    
    protected $scene = [
        'add'   =>  ['name','agent','business_license','auth_letter'],
        'edit'  =>  ['name','agent','business_license','auth_letter'],
    ];


}