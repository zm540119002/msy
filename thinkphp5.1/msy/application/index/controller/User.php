<?php
namespace app\index\controller;

use think\Controller;
use common\lib\UserAuth;

class User extends Controller{
    public function __construct(){
        parent::__construct();
    }

    //登录
    public function login(){
        if (request()->isAjax()) {
            $modelUser = new \common\model\User();
            return $modelUser->login();
        } else {
            return $this->fetch();
        }
    }

    //注册
    public function register(){
        if (request()->isAjax()) {
            return $this->_register();
        } else {
            return $this->fetch();
        }
    }

    //忘记密码
    public function forget_password(){
        if (request()->isAjax()) {
            return $this->_forget_password();
        } else {
            return $this->fetch();
        }
    }

    //退出
    public function logout(){
        UserAuth::removeLogin();
        header('Content-type: text/html; charset=utf-8');
        echo '退出成功！';exit;
        return redirect('login');
    }

    //发送验证码
    public function send_sms(){
        $this->_send_sms();
    }

    private function _login(){
        if(!request()->isAjax()){
            return errorMsg(config('not_post'));
        }
        $name = input('post.name','','string');
        $password = input('post.password','','string');
        $mobile_phone = input('post.mobile_phone',0,'number_int');
        $captcha = input('post.captcha',0,'number_int');
        if ($name && $password) {//账号密码登录
            if (!$name) {
                return errorMsg('请输入账号！');
            }
            if (!$password) {
                return errorMsg('请输入密码！');
            }
            $user = UserAuth::get(array(
                'name' => $name,
                'password' => $password,
            ));
            if (!$user) {
                return errorMsg('账号密码不正确，请重新输入');
            }
        }elseif($mobile_phone && $captcha){//短信验证码登录
            if (!$mobile_phone) {
                return errorMsg('请输入手机号码！');
            }
            if (!$captcha) {
                return errorMsg('请输入验证码！');
            }

            $user = UserAuth::get(array(
                'mobile_phone' => $mobile_phone,
                'password' => $password,
            ));
            if (!$user) {
                return errorMsg('不是预留手机号码！');
            }
        }else{
            return errorMsg('请输入完整的登录信息');
        }
        $backUrl = session('backUrl');
        $pattern  =  '/index.php\/([A-Z][a-z]*)\//' ;
        preg_match ($pattern,$backUrl,$matches);
        //cookie购物车入库
        if($user['id']){
            $res = UserAuth::saveCookieCartToMysql($user['id'],$matches[1]);
            if(!$res){
                return errorMsg('购物车入库失败');
            }
        }
        //更新最后登录的时间
        UserAuth::saveLastLoginTimeById($user['id']);
        //设置session
        UserAuth::setSession($user);
        return successMsg($backUrl?(is_ssl()?'https://':'http://').$backUrl:U('login'));
    }

    private function _register(){
        if(!request()->isAjax()){
            return errorMsg(C('NOT_POST'));
        }
        $mobile_phone = input('post.mobile_phone',0,'number_int');
        $name = input('post.name','','string');
        $captcha = input('post.captcha','','string');
        $captcha_type = 'register';
        if( !$this->_check_captcha($mobile_phone,$captcha,$captcha_type) ){
            return errorMsg('验证码错误，请重新获取验证码！');
        }
        $modelUser = D('User');
        $_POST['salt'] = create_random_str(10,0);//盐值
        $_POST['password'] = md5($_POST['salt'] . $_POST['pass_word']);//加密
        $_POST['name'] = $name;
        $_POST['mobile_phone'] = $mobile_phone;
        $_POST['create_time'] = time();

        $res = $modelUser->addUser();
        if($res['status'] == 1){
            return successMsg('注册成功');
        }else{
            return errorMsg($res);
        }
    }

    private function _forget_password(){
        if(!request()->isAjax()){
            return errorMsg(C('NOT_POST'));
        }
        $mobile_phone = input('post.mobile_phone',0,'number_int');
        $name = input('post.name','','string');
        if(!isReservedMobilePhone($mobile_phone,$name)){
            return errorMsg('不是预留手机号码');
        }

        $captcha = input('post.captcha','','string');
        $captcha_type = 'reset';
        if( !$this->_check_captcha($mobile_phone,$captcha,$captcha_type) ){
            return errorMsg('验证码错误，请重新获取验证码！');
        }

        $modelUser = D('User');
        $_POST['salt'] = create_random_str(10,0);//盐值
        $_POST['password'] = md5($_POST['salt'] . $_POST['password']);//加密
        $_POST['update_time'] = time();

        $res = $modelUser->saveUser();

        if($res['status'] == 1){
            return successMsg('重置密码成功');
        }else{
            return errorMsg('重置密码失败');
        }
    }

    private function _send_sms(){
        $mobile_phone = input('post.mobile_phone',0,'number_int');
        if(!isMobile($mobile_phone)){
            return errorMsg('无效的手机号码');
        }
        $url = 'http://sms3.mobset.com:8080/Cloud?wsdl';
        $client = new \SoapClient($url);
        $client->soap_defencoding = 'utf-8';
        $client->decode_utf8 = false;
        $addNum='';
        $timer='';
        $lCorpID = '301289';
        $strLoginName = 'Admin';
        $strPasswd = 'sun19760924_++';
        $captcha = create_random_str();
        $smsContent = '验证码：'.$captcha.' 该验证码10分钟内有效';
        $longSms=0;
        $strTimeStamp=GetTimeString();
        $strInput=$lCorpID.$strPasswd.$strTimeStamp;
        $strMd5=md5($strInput);
        $group=$client-> ArrayOfMobileList[1];
        $group[0] =$client->MobileListGroup;
        $group[0]->Mobile = $mobile_phone;
        $param = array(
            'CorpID'=>$lCorpID,
            'LoginName'=>$strLoginName,
            'Password'=>$strMd5,
            'TimeStamp'=>$strTimeStamp,
            'AddNum'=>$addNum,
            'Timer'=>$timer,
            'LongSms'=>$longSms,
            'MobileList'=>$group,
            'Content'=>$smsContent
        );
        try {
            $client->Sms_Send($param);
            $captcha_type = input('post.captcha_type','','string') ;
            $captcha_type = ($captcha_type ? $captcha_type : 'login');
            $smsExpire = C('SMS_EXPIRE');
            session('captcha_'. $captcha_type . '_' . $mobile_phone,$captcha,$smsExpire);
            return successMsg('验证码已发送至手机:'.$mobile_phone . '，请注意查收。');
        }catch (\SoapFault $fault){
            return errorMsg('验证码发送失败,请稍候再试。。。');
        }
    }
}