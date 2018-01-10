<?php
namespace Home\Controller;
use Think\Controller;
use Home\Lib\WeiXin;
class IndexController extends Controller {
    public function index(){
        $weixin = new WeiXin();
        if (!isset($_GET["code"])){
            $redirect_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $jumpurl = $weixin->oauth2_authorize($redirect_url, "snsapi_userinfo", "123");
            Header("Location: $jumpurl");
        }else{
            $access_token_oauth2 = $weixin->oauth2_access_token($_GET["code"]);
            $userinfo = $weixin->oauth2_get_user_info($access_token_oauth2['access_token'], $access_token_oauth2['openid']);
            print_r($userinfo);
        }
    }
    
    public function user(){

    }
}