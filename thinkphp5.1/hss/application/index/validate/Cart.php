<?php
namespace app\index\validate;

class Cart extends \think\Validate
{
    //验证规则
    protected $rule = [
        'goods_id'  => [
            'require','number','gt'=>0
        ],
        'num'  => [
            'require','number','gt'=>0
        ],
    ];
}