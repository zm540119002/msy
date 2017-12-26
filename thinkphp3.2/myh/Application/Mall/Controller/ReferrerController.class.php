<?php
namespace Mall\Controller;

use web\all\Controller\AuthUserController;


class ReferrerController extends AuthUserController{
    //我的推客二维码
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
        $shareQRCodes = createLogoQRcode($url,$logo,$newRelativePath);
        $this->ajaxReturn(successMsg('成功',array('url'=>$shareQRCodes)));
    }

    //删除我的推客二维码
    public function delMyQRCodes(){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        $shareQRCodes = WEB_PATH.$_POST['imgUrl'];
        if(!unlink($shareQRCodes)){
            $this->ajaxReturn(errorMsg('删除二维码图片失败！'));
        }else{
            $this->ajaxReturn(successMsg('删除二维码图片成功！'));
        }
    }
}
