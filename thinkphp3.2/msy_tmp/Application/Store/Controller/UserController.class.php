<?php
namespace Store\Controller;
use Think\Controller;

class UserController extends Controller {
    public function index(){
        $this->display();
    }

    public function register(){
        $this->display();
    }
    public function resetpsw(){
        $this->display();
    }
    public function login(){
        if(IS_POST){
        }else{
            $this->display();
        }

    }

    /**
     * 发送手机验证码
     */
    public function register_verify(){
        if(!empty($_POST['phone'])){
            sent_verify($_POST['phone']);
        }
    }

    /**
     * 用户注册
     */
    public function ajaxregister(){
        $user = D('User');
        //判断表单是否提交
        if(!empty(I('post.'))){
            $salt =  create_random_str(5);
            $data = array(
                'phone' => I('post.phone'),
                'salt' => $salt,
                'passwd' => md5($salt . I('post.passwd','','string')),
                'create_time' => time(),
            );

            if(!$user -> create()){
                //验证失败,输出错误信息
                //getError()方法返回验证失败的信息
                 $info = $user->getError();
                //$info = implode(",",$info);
                 return show(0,$info);
            } else {
                $rst = $user -> add($data);
                if(!$rst){
                    return show(0,"注册失败，请重新注册");
                }else{
                    return show(1,"注册成功！是否现在登录",$rst);
                }
         
            }
        }
    }

    /**
     * 用户重置密码
     */
    public function ajaxResetPassword(){

        $user = D('User');
        if(!empty(I('post.'))){
            if(!$user -> field('vfyCode,passwd,resetPsw')->create()){
                //验证失败,输出错误信息
                //getError()方法返回验证失败的信息
                $info = $user->getError();
                return show(0,$info);
            } else {
                $where = array(
                    'phone' => I('post.phone'),
                );
                $info = $user->field('salt,passwd')->where($where)->find();
                if(empty($info)){
                    return show(0,"此手机号码还没有注册，请先注册!");
                }
                $passwd = md5($info['salt'] . I('post.passwd','','string'));
                if($info['passwd']==$passwd){
                    return show(0,"和现在密码一样，不需更改");
                }
                /***
                 * 把新的密码入库
                 */
                $salt =  create_random_str(5);
                $data = array(
                    'salt' => $salt,
                    'passwd' => md5($salt . I('post.passwd','','string')),
                );
                $rst = $user->where($where)->save($data);
                if(!$rst){
                    return show(0,"修改密码失败！");
                }else{
                    return show(1,"修改密码成功！现在登录？",$rst);
                }
            }
        }
    }


    /**
     * 登录
     */
    public function ajaxlogin(){

        if(session('uid')) {
            $this -> redirect('Index/Index');
        }
        if(IS_POST){
            $user = D("User"); //实例化类
            $where = array(
                'phone'=> I('post.phone'),
            );
            $info = $user ->  where($where) -> find();
            if(!$info || $info['status'] != 1) {
                return show(0,'该用户不存在');
            }
            $passwd = md5($info['salt'] . I('post.passwd','','string'));
            if($info['passwd'] != $passwd) {
                return show(0,'密码错误');
            }

            session('username',$info['phone']);
            session('uid',$info['id']);
            return show(1,'登录成功');
        }


    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}


    

         






