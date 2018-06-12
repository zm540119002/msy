<?php
namespace app\factory\validate;

use think\Validate;

class Brand extends Validate
{
    /**
     * @var array
     * 
     */
    protected $rule = [
        'name'  =>  'require|max:18',
        'brand_img' =>  'require|max:50',
        'category_id_1' =>  'require',
        'certificate' =>  'require',
    ];
    protected $message  =   [
        'name.require' => '商标全称必须填写',
        'name.max'     => '商标全称最多不能超过18字',
        'brand_img.require'   => '请上传商标图片',
        'category_id_1.require'   => '请选择商标所属分类',
        'certificate.require'   => '请上传证书',
    ];
    
    protected $scene = [
        'add'   =>  ['name','brand_img','cat_id_1','certificate'],
        'edit'  =>  ['name','brand_img','cat_id_1','certificate'],
    ];


}