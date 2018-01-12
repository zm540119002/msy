<?php
namespace Mall\Controller;
use  web\all\Controller\BaseController;
use web\all\Component\WxpayAPI\Jssdk;
class WeiXinController extends BaseController {
    
    public function checkWxUser(){
        $url = $_GET['url'];
        $wxUser = $this -> getOAuthWeiXinUserInfo();
        $backUrl = substr($url,0,strrpos($url,'.html'));
        $url = $backUrl.'/code=';
        header("Location: $url");
    }


}

