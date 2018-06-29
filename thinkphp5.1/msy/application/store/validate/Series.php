<?php
namespace app\store\validate;

use think\Validate;

class Series extends Validate
{
    /**
     * @var array
     * 
     */
    protected $rule = [
        'name'  =>  'require|max:18',
    ];
    protected $message  =   [
        'name.require' => '请填写系列分类',
    ];
    protected $scene = [
        'add'   =>  ['name'],
        'edit'  =>  ['name'],
    ];


}