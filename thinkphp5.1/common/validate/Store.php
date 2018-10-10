<?php
namespace common\validate;

class Store extends \think\Validate
{
    protected $rule = [
        'foreign_id'  =>  'require|integer',
        'store_type' =>  'require|integer',
        'run_type' =>  'require|integer',
        'auth_status' =>  'require',
    ];
    protected $message  =   [
        'foreign_id.require' => '字段不能空',
        'store_type.require' => '字段不能空',
        'run_type.require'   => '字段不能空',
        'auth_status.require'   => '字段不能空',
    ];
    //验证场景
    protected $scene = [
        //验证编辑
        'edit'  =>  [
            'auth_status',
        ],
    ];
}