<?php
namespace app\api\validate;

class Cart extends \think\Validate
{
    //验证规则
    protected $rule = [
        'goods_id'  => [
            'require','number','gt'=>0,
        ],
        'num'  => [
            'require','number',
        ],
    ];
    protected $message  =   [
        'goods_id.require' => '商品ID必须',
        'goods_id.number'     => '商品ID错误',
        'num.require'     => '商品数量必须',
        'num.number'   => '商品数量错误',
    ];
}