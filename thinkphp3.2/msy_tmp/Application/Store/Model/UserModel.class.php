<?php

//用户模型model

namespace Store\Model;
use Think\Model;


//父类Model  ThinkPHP/Library/Think/Model.class.php

class UserModel extends Model{

    //实现表单项目验证
    //通过重写父类属性_validate实现表单验证
    protected $_validate        =   array(

        //验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
        //验证用户名,require必须填写项目
        array('phone', 'require', '手机号码不能为空！'), //默认情况下用正则进行验证
        array('vfyCode', 'require', '注册码不能为空！'), //默认情况下用正则进行验证
        array('passwd', 'require', '密码不能为空！'), //默认情况下用正则进行验证
        array('resetPsw', 'require', '确认密码不能为空！'), //默认情况下用正则进行验证
        array('notice', 'noticeCheck', '阅读并同意美尚平台使用须知', 0, 'function'), // 判断验证码是否正确
//
        array('phone', '', '该用户名已被注册！', 0, 'unique', 1), // 在新增的时候验证name字段是否唯一
        //array('passwd', '^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,10}$', '密码格式不正确,请重新输入！', 0),
        //array('resetPsw', '^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,10}$', '确认密码格式不正确,请重新输入！', 0),
        array('resetPsw', 'passwd', '确认密码不正确', 0, 'confirm'), // 验证确认密码是否和密码一致
        array('phone', '/^1[34578]\d{9}$/', '手机号码格式不正确', 0), // 正则表达式验证手机号码
        array('vfyCode', '/^\d{6}$/', '验证码必须为6位数字', 0), // 正则表达式验证手机号码
        array('vfyCode', 'verifyCheck', '验证码错误', 0, 'function'), // 判断验证码是否正确

    );
//    /**
//     * 自动完成
//     */
//    protected $_auto = array (
//        array('passwd', 'md5', 3, 'function') , // 对password字段在新增和编辑的时候使md5函数处理
//        //array('create_time','time',1,'function'), // 对regdate字段在新增的时候写入当前时间戳
//    );

    public function noticeCheck(){
        $notice = I('post.notice','','string');
       if($notice != 'on'){
          return false;
       }else{
           return true;
       }
    }

 



}

