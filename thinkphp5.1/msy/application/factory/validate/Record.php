<?php
namespace app\factory\validate;


class Record extends \think\Validate
{
    protected $rule = [
        'company_img'  =>  'require',
        'provinces' =>  'require',
        'detail_address' =>  'require',
        'logo_img' =>  'require',
        'introduction' =>  'require',
        'factory_video' =>  'require',
        'rb_img' =>  'require',
        'license' =>  'require',
        'glory_img' =>  'require',
    ];
    protected $message  =   [
        'company_img.require' => '请上传企业形象图片',
        'provinces.require'   => '请选择所在的省市区',
        'detail_address.require'   => '请填写详细地址',
        'logo_img.require'   => '请上传企业标志图',
        'introduction.require'   => '请编辑企业简介',
        'factory_video.require'   => '请上传企业视频',
        'rb_img.require'   => '请上传企业研发生产图片',
        'license.require'   => '请上传执照资质图片',
        'glory_img.require'   => '请上传专利荣誉图片',
    ];
}