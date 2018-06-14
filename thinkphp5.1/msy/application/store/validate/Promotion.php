<?php
namespace app\store\validate;

use think\Validate;

class Promotion extends Validate
{
    /**
     * @var array
     */
    protected $rule = [
        'name'  =>  'require|max:50',
        'img' =>  'require|max:30',
        'goods_id' =>  'require',
        'promotion_price' =>  'require|float',
        'start_time' =>  'require',
        'end_time' =>  'require',
    ];
    protected $message  =   [
        'name.require' => '促销活动名称必须填写',
        'name.max'     => '促销活动名称最多不能超过50字',
        'img.require'   => '请上传图片',
        'goods_id.require'   => '请链接商品',
        'promotion_price.require'   => '请填写促销价格',
        'promotion_price.float'   => '价格格式有误',
        'start_time.require'   => '请填写促销开始时间',
        'end_time.require'   => '请填写促销结束时间',
    ];
}