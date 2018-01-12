<?php
namespace Mall\Controller;
use  web\all\Controller\BaseController;
use web\all\Component\WxpayAPI\Jssdk;
class WeiXinController extends BaseController {
    
    public function checkWxUser(){
        $url = $_GET['url'];
        $wechat= new Jssdk(C('WX_CONFIG')['APPID'], C('WX_CONFIG')['APPSECRET']);
        $wechat -> getOauthRedirect($url,"wxbase");
//        $wxUser = $this -> getOAuthWeiXinUserInfo();
//        $backUrl = substr($url,0,strrpos($url,'.html'));
//        $url = $backUrl.'/code=';
//        header("Location: $url");
    }


}

