<?php
namespace Admin\Controller;

use web\all\Controller\BaseController;
class UserController extends BaseController {
    public function _initialize() {
    }
    //登录
    public function login(){
        if (IS_POST) {
            var_dump(I());exit;
            $this->_login();
        } else {
            $this->display();
        }
    }


    //退出
    public function logout(){
        AuthUser::removeLogin();
        header('Content-type: text/html; charset=utf-8');
        echo '退出成功！';exit;
        $this->redirect('login');
    }


    private function _login(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        $name = I('post.name','','string');
        if (!$name) {
            $this->ajaxReturn(errorMsg('请输入账号！'));
        }
        $password = I('post.password','','string');
        if (!$password) {
            $this->ajaxReturn(errorMsg('请输入密码！'));
        }
        $mobile_phone = I('post.mobile_phone',0,'number_int');
        if (!$mobile_phone) {
            $this->ajaxReturn(errorMsg('请输入手机号码！'));
        }
        $captcha = I('post.captcha',0,'number_int');
        if (!$captcha) {
            $this->ajaxReturn(errorMsg('请输入验证码！'));
        }
        $captcha_type = 'login';

        if ($name && $password) {//账号密码登录
            $user = AuthUser::getUser(array(
                'name' => $name,
                'password' => $password,
            ));
            if (!$user) {
                $this->ajaxReturn(errorMsg('账号密码不正确，请重新输入'));
            }
        }elseif($mobile_phone && $captcha){//短信验证码登录
            if( !$this->_check_captcha($mobile_phone,$captcha,$captcha_type) ){
                $this->ajaxReturn(errorMsg('验证码错误，请重新获取验证码！'));
            }
            $user = AuthUser::getUser(array(
                'mobile_phone' => $mobile_phone,
                'password' => $password,
            ));
            if (!$user) {
                $this->ajaxReturn(errorMsg('不是预留手机号码！'));
            }
        }else{
            $this->ajaxReturn(errorMsg('请输入完整的登录信息'));
        }

        //更新最后登录的时间
        AuthUser::saveLastLoginTimeById($user['id']);

        //设置session
        AuthUser::setSession($user);
        $this->ajaxReturn(successMsg(session('backUrl')?(is_ssl()?'https://':'http://').session('backUrl'):U('login')));
    }

    private function _register(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
//        $notice = I('post.notice','','string');
//        if($notice != 'on'){
//            $this->ajaxReturn(errorMsg('阅读并同意美尚平台使用须知'));
//        }

        $mobile_phone = I('post.mobile_phone',0,'number_int');
        $name = I('post.name','','string');
        $captcha = I('post.captcha','','string');
        $captcha_type = 'register';
        if( !$this->_check_captcha($mobile_phone,$captcha,$captcha_type) ){
            $this->ajaxReturn(errorMsg('验证码错误，请重新获取验证码！'));
        }

        $modelUser = D('User');
        $_POST['salt'] = create_random_str(10,0);//盐值
        $_POST['password'] = md5($_POST['salt'] . $_POST['password']);//加密
        $_POST['name'] = $name;
        $_POST['mobile_phone'] = $mobile_phone;
        $_POST['create_time'] = time();

        $res = $modelUser->addUser();
        if($res['status'] == 1){
            $this->ajaxReturn(successMsg('注册成功'));
        }else{
            $this->ajaxReturn(errorMsg('注册失败'));
        }
    }

    private function _forget_password(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        $mobile_phone = I('post.mobile_phone',0,'number_int');
        $name = I('post.name','','string');
        if(!isReservedMobilePhone($mobile_phone,$name)){
            $this->ajaxReturn(errorMsg('不是预留手机号码'));
        }

        $captcha = I('post.captcha','','string');
        $captcha_type = 'reset';
        if( !$this->_check_captcha($mobile_phone,$captcha,$captcha_type) ){
            $this->ajaxReturn(errorMsg('验证码错误，请重新获取验证码！'));
        }

        $modelUser = D('User');
        $_POST['salt'] = create_random_str(10,0);//盐值
        $_POST['password'] = md5($_POST['salt'] . $_POST['password']);//加密
        $_POST['update_time'] = time();

        $res = $modelUser->saveUser();

        if($res['status'] == 1){
            $this->ajaxReturn(successMsg('重置密码成功'));
        }else{
            $this->ajaxReturn(errorMsg('重置密码失败'));
        }
    }

    private function _check_captcha($mobile_phone,$captcha,$captcha_type='login'){
        return session('captcha_' . $captcha_type . '_' . $mobile_phone) == $captcha ;
    }


    public function verify_c(){
        $Verify = new \Think\Verify();
        $Verify->fontSize = 18;
        $Verify->length   = 4;
        $Verify->useNoise = false;
        $Verify->imageW = 130;
        $Verify->imageH = 50;
        $Verify->entry();
    }

}