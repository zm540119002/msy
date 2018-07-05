<?php
namespace app\factory\validate;

class Store extends \think\Validate
{
    protected $rule = [
        'foreign_id'  =>  'require|integer',
        'store_type' =>  'require|integer',
        'run_type' =>  'require|integer',
    ];
    protected $message  =   [
        'foreign_id.require' => '字段不能空',
        'store_type.require' => '字段不能空',
        'run_type.require'   => '字段不能空',
    ];
}