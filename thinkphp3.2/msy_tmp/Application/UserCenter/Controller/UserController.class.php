<?php
namespace UserCenter\Controller;

use Think\Controller;
use Common\Lib\AuthUser;

class UserController extends Controller {
    //登录
    public function login(){
        if (IS_POST) {
            $this->_login();
        } else {
            $this->display();
        }
    }

    //注册
    public function register(){
        if (IS_POST) {
            $this->_register();
        } else {
            $this->display();
        }
    }

    //忘记密码
    public function forget_password(){
        if (IS_POST) {
            $this->_forget_password();
        } else {
            $this->display();
        }
    }

    //退出
    public function logout(){
        AuthUser::removeLogin();
        header("Content-type: text/html; charset=utf-8");
        echo '退出成功！';exit;
        $this->redirect('login');
    }

    //获取验证码
    public function send_sms(){
        $this->_send_sms();
    }

    private function _login(){
        $mobile = I('post.mobile',0,'number_int');
        $passwd = I('post.passwd','','string');
        if (!$mobile || !$passwd) {
            $this->error('请输入完整的登录信息');
        }

        $user = AuthUser::getUser(array(
            'mobile' => $mobile,
            'passwd' => $passwd,
        ));
        if (!$user) {
            $this->error('账号密码不正确，请重新输入');
        }

        //更新最后登录的时间
        AuthUser::updateLoginInfo($user['id']);

        //设置session
        AuthUser::setSession($user);
        $backUrl = session('backUrl');
        $this->success($backUrl ? $backUrl : U('Index/index'));
    }

    private function _register(){
        $mobile = I('post.mobile',0,'number_int');
        $captcha = I('post.captcha','','string');

//        if(AuthUser::mobileIsExist($mobile)){
//            $this->ajaxReturn(errorMsg("此号码已被注册，请更换手机号码"));
//        }
//        if( !$this->_check_captcha($mobile,$captcha) ){
//            $this->ajaxReturn(errorMsg('验证码错误，请重新获取验证码！'));
//        }

//        $notice = I('post.notice','','string');
//        if($notice != 'on'){
//            $this->ajaxReturn(errorMsg("阅读并同意美尚平台使用须知"));
//        }

        $user = M("User"); // 实例化user对象

        $_POST['valipasswd'] = $_POST['passwd'];//用于验证密码是否一致
        $rules = array(
            array('mobile','require','请填写手机号码！'),
            array('mobile','isMobile','手机号不正确',0,'function'),
            array('mobile','','此手机号已被注册，请更换手机号码！',0,'unique',1),
            array('passwd','require','请填写密码！'),
            array('valipasswd','repasswd','密码与确认密码不一致',0,'confirm'),
        );

        $_POST['salt'] = create_random_str(10,0);//盐值
        $_POST['passwd'] = md5($_POST['salt'] . $_POST['passwd']);//加密
        $_POST['create_time'] = time();

        if (!$user->validate($rules)->create()){
            $this->ajaxReturn(errorMsg($user->getError()));
        }

        $res = $user->add();
        if(!$res){
            $this->ajaxReturn(errorMsg("注册失败"));
        }

        $this->ajaxReturn(successMsg("注册成功"));
    }

    private function _check_captcha($mobile,$captcha,$type='register'){
        return session('captcha_'.$type.'_'.$mobile) == $captcha ;
    }

    private function _forget_password(){
        $mobile = I('post.mobile',0,'number_int');
        $captcha = I('post.captcha','','string');
        $type = I('post.type','','string');

//        if(!AuthUser::mobileIsExist($mobile)){
//            $this->ajaxReturn(errorMsg("此号码还未注册！"));
//        }
//        if( !$this->_check_captcha($mobile,$captcha,$type) ){
//            $this->ajaxReturn(errorMsg('验证码错误，请重新获取验证码！'));
//        }

        $user = M("User"); // 实例化user对象

        $rules = array(
            array('mobile','require','请填写手机号码！'),
            array('mobile','isMobile','手机号不正确',0,'function'), // 自定义函数验证密码格式
            array('passwd','require','请填写密码！'),
            array('passwd','repasswd','密码与确认密码不一致',0,'confirm'),
        );
        if (!$user->validate($rules)->create()){
            $this->ajaxReturn(errorMsg($user->getError()));
        }

        $where = array(
            'mobile' => $mobile,
        );

        $salt = create_random_str(10,0);//盐值
        $data = array(
            'salt' => $salt,
            'passwd' => md5($salt . I('post.passwd','','string')),//加密
            'update_time' => time(),
        );

        $res = $user->where($where)->save($data);
        if(false === $res){
            $this->ajaxReturn(errorMsg("重置失败"));
        }

        $this->ajaxReturn(successMsg("重置成功"));
    }

    private function _send_sms(){
        $mobilePhone = I('post.mobilePhone',0,'number_int');
        if(!$mobilePhone){
            $this->ajaxReturn(errorMsg('无效的手机号码'));
        }
        $url = "http://sms3.mobset.com:8080/Cloud?wsdl";
        $client = new \SoapClient($url);
        $client->soap_defencoding = 'utf-8';
        $client->decode_utf8 = false;
        $addNum="";
        $timer="";
        $lCorpID = "301289";
        $strLoginName = "Admin";
        $strPasswd = "sun19760924_++";
        $captcha = create_random_str();
        $smsContent = '验证码：'.$captcha.' 该验证码10分钟内有效';
        $longSms=0;
        $strTimeStamp=GetTimeString();
        $strInput=$lCorpID.$strPasswd.$strTimeStamp;
        $strMd5=md5($strInput);
        $group=$client-> ArrayOfMobileList[1];
        $group[0] =$client->MobileListGroup;
        $group[0]->Mobile = $mobilePhone;
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
            $type = I('post.type','','string') ;
            session('captcha_'.($type ? $type : 'register').'_'.$mobilePhone,$captcha,60*10);
            $this->ajaxReturn(successMsg('验证码已发送至手机:'.$mobilePhone . '，请注意查收。'));
        }catch (\SoapFault $fault){
            $this->ajaxReturn(errorMsg("验证码发送失败,请稍候再试。。。"));
        }
    }

}