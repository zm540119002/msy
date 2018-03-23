<?php
namespace app\index\controller;

use think\Controller;

class User extends Controller{
    public function __construct(){
        parent::__construct();
    }

    /**登录
     * @return array|mixed
     */
    public function login(){
        if (request()->isAjax()) {
            $modelUser = new \common\model\User();
            return $modelUser->login();
        } else {
            return $this->fetch();
        }
    }

    /**忘记密码
     * @return array|mixed
     */
    public function forgetPassword(){
        if (request()->isAjax()) {
            $modelUser = new \common\model\User();
            return $modelUser->setPassword();
        } else {
            return $this->fetch();
        }
    }

    //退出
    public function logout(){
        session('user', null);
        session('user_sign', null);
        header('Content-type: text/html; charset=utf-8');
        echo '退出成功！';exit;
        return redirect('login');
    }

    /*发送验证码
     */
    public function sendSms(){
        if (!(request()->isPost())) {
            return config('custom.not_post');
        }
        $mobilePhone = input('post.mobile_phone',0);
        $captcha = create_random_str();
        $config = array(
            'mobilePhone' => $mobilePhone,
            'smsSignName' => config('custom.sms_sign_name'),
            'smsTemplateCode' => config('custom.sms_template_code'),
            'captcha' => $captcha,
        );
        $response = \common\lib\Sms::sendSms($config);
        if('OK'!==$response->Code){
            if('BUSINESS_LIMIT_CONTROL'===$response->Code){
                return errorMsg('同一个手机号码发送短信验证码，支持1条/分钟，5条/小时 ，累计10条/天。');
            }
            return errorMsg($response->Message);
        }
        //设置session
        session('captcha_'.$mobilePhone,$captcha);
        return successMsg($response->Message);
    }
}