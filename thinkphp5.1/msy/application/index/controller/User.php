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
    public function forget_password(){
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
    public function send_sms(){
        if (!request()->isAjax()) {
            return errorMsg('not_ajax');
        }
        $this->_send_sms();
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