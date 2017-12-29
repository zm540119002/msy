<?php
namespace Mall\Controller;
use web\all\Controller\AuthUserController;
class ReferrerController extends AuthUserController{
    //推客分享首页
    public function index(){
        $this->display();
    }
    //我的带产品推客二维码
    public function myQRCodesWithGoods(){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        $userId = $this->user['id'];
        $avatarPath = $this->user['avatar'];
        $url = $_POST['url'];
        $url = substr($url,0,strrpos($url,'/footerType/share.html'));
        $url = $url.'/userId/'.$userId;
        $newRelativePath = C('USER_LOGO');
        $shareQRCodes = createLogoQRcode($url,$avatarPath,$newRelativePath);
        $this->ajaxReturn(successMsg('成功',array('url'=>$shareQRCodes)));
    }

    //删除我带产品的推客二维码
    public function delMyQRCodesWithGoods(){
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
