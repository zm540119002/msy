<?php

namespace Store\Model;
use Think\Model;

class ProjectModel extends Model{

    //实现表单项目验证
    //通过重写父类属性_validate实现表单验证
    protected $_validate        =   array(

        //验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
        //验证用户名,require必须填写项目
        array('name',         'require', '请填写项目名称！'), //默认情况下用正则进行验证
        array('description', 'require', '请填写项目简要特点！'), //默认情况下用正则进行验证
        array('price',       'require', '请填写项目原价！'), //默认情况下用正则进行验证
        array('takes',       'require', '请填写项目服务耗时时间！'), //默认情况下用正则进行验证

    );



    
 



}

