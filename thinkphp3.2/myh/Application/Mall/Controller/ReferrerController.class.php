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
        $shareQRCodes = createQRcode($url);
        $this->ajaxReturn($shareQRCodes);
    }
    public function aa(){
        
        $shareQRCodes = createLogoQRcode($url,$logo,$filename,$eclevel = "H", $pixelPerPoint = 8);

       
    }

}
