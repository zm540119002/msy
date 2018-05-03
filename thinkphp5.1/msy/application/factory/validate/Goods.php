<?php
namespace app\factory\validate;

use think\Validate;

class Goods extends Validate
{
    /**
     * @var array
     */
    protected $rule = [
        'sale_price' =>  'require',
        'sale_type' =>  'require',
        'store_type' =>  'require',
    ];
    protected $message  =   [
        'sale_price.require'   => '请填写销售价格',
        'sale_type.require'   => '商品销售类型不能少',
        'store_type.require'   => '商品所属店铺类型不能少',
    ];



}