<?php
namespace app\factory\validate;

use think\Validate;

class Goods extends Validate
{
    /**
     * @var array
     */
    protected $rule = [
        'name'  =>  'require|max:18',
        'trait' =>  'require|max:28',
        'retail_price' =>  'require',
        'sale_price' =>  'require',
        'cat_id_1' =>  'require',
        'thumb_img' =>  'require',
        'tag' =>  'require',
        'main_img' =>  'require',
        'parameters' =>  'require|max:1000',
        'details_img' =>  'require',

    ];
    protected $message  =   [
        'name.require' => '产商全称必须填写',
        'name.max'     => '产商全称最多不能超过18字',
        'trait.require'   => '请填写商品特点',
        'trait.max'   => '商品特点字数不能超过28字',
        'retail_price.require'   => '请填写零售价格',
        'sale_price.require'   => '请填写销售价格',
        'thumb_img.require'   => '请上传缩略图',
        'tag.require'   => '请选择标签',
        'main_img.require'   => '请上传首焦图',
        'parameters.require'   => '请填写商品参数',
        'parameters.max'   => '商品参数不能超过1000字',
        'details_img.require'   => '请上传商品详情图',

    ];
    
    protected $scene = [
        'add'   =>  ['name','trait','retail_price','settle_price','thumb_img','tab','main_img','parameters','details_img'],
        'edit'  =>  ['name','trait','retail_price','settle_price','thumb_img','tab','main_img','parameters','details_img'],

    ];


}