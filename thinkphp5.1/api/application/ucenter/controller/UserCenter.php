<?php
namespace app\ucenter\controller;
class UserCenter extends \think\Controller{
    /**登录
     */
    public function login(){
        if (request()->isAjax()) {
            $modelUser = new \common\model\UserCenter();
            $postData = input('post.');
            $res = $modelUser->login($postData);
            if($res['status']==0){
                $this->errorMsg($res['info']);
            }else{
                $this->successMsg('登录成功！',config('code.success.login'));
            }
        }
    }
    /**后台登录
     */
    public function login_admin(){
        if (request()->isAjax()) {
            $modelUser = new \common\model\UserCenter();
            $postData = input('post.');
            $modelUser->login($postData);
            $this->successMsg('登录成功！',config('code.success.login'));
        }
    }
    /**注册
     */
    public function register(){
        if (request()->isAjax()) {
            $modelUser = new \common\model\UserCenter();
            $postData = input('post.');
            $res = $modelUser->register($postData);
            if($res['status']==0){
                $this->errorMsg($res['info']);
            }else{
                $this->successMsg('注册成功！',config('code.success.login'));
            }
        }
    }
    //退出
    public function logout(){
        session(null);
        header('Content-type: text/html; charset=utf-8');
        $this->successMsg('成功') ;
    }
    /*发送验证码
     */
    public function sendSms(){
        if (!(request()->isPost())) {
            return config('custom.not_post');
        }
        $mobilePhone = input('post.mobile_phone',0);
        $captcha = create_random_str(4);
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
            $this->errorMsg($response->Message);
        }
        //设置session
        session('captcha_'.$mobilePhone,$captcha);
        $this->successMsg($response->Message);
    }
}