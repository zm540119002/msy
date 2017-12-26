<?php
namespace Mall\Controller;

use  web\all\Controller\BaseController;

class ReferrerController extends BaseController{
    //推客分享首页
    public function index(){
        
       $this->display();
    }

    public function myQRCodes(){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        $userId = 14;
        $url = $_POST['url'];
        $url = substr($url,0,strrpos($url,'/footerType/share.html'));
        $url = $url.'/userId/'.$userId;
        $logPath =  realpath('Public/img/home') . '/';
        $logo = $logPath.'logo.png';
        $newRelativePath = C('USER_LOGO');
        $shareQRCodes = createLogoQRcode($url,$logo,$newRelativePath,$eclevel = "H", $pixelPerPoint = 8);
        $this->ajaxReturn($shareQRCodes);
    }
    public function aa(){
        $url = 'https://www.baidu.com';
        $newRelativePath = C('USER_LOGO');
        $logPath =  realpath('Public/img/home') . '/';
        $logo = $logPath.'logo.png';
        $shareQRCodes = createLogoQRcode($url,$logo,$newRelativePath,$eclevel = "H", $pixelPerPoint = 8);
        echo $shareQRCodes;
    }

}
