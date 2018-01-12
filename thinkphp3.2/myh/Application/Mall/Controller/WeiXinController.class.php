<?php
namespace Mall\Controller;
use  web\all\Controller\BaseController;
use web\all\Component\WxpayAPI\Jssdk;
class WeiXinController extends BaseController {
    //微信登录
    public function wxLogin(){
        $url = $_GET['url'];
        if(isWxBrowser()) {//判断是否为微信浏览器
            $wechat= new Jssdk(C('WX_CONFIG')['APPID'], C('WX_CONFIG')['APPSECRET']);
            $code = isset($_GET['code'])?$_GET['code']:'';
            if($code){
                $wxUser = $this -> getOAuthWeiXinUserInfo();
                if(!$wxUser){
                    return false;
                }else{
                    return $wxUser;
                }
            }else{
                $wechat -> getOauthRedirect($url,"wxbase");
            }
        }

    }
    public function checkWxUser(){
        $wxUser = $this -> getOAuthWeiXinUserInfo();
        print_r($wxUser);
    }


}

