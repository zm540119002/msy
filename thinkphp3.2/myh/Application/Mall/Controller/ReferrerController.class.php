<?php
namespace Mall\Controller;
use web\all\Controller\AuthUserController;
class ReferrerController extends AuthUserController{
    //开通推客分享功能
    public function openReferrer(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        $mode = D('Member');
        $where['user_id'] = $this->user['id'];
        $_POST['referrer_status'] = 1;
        $rst = $mode->saveMember($where=array(),$rules=array());
        $this->ajaxReturn($rst);
    }
    //我的带产品推客二维码
    public function myQRCodesWithGoods(){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        $userId = $this->user['id'];
        $avatarPath = $this->user['avatar'];
        $url = $_POST['url'];
        $url = substr($url,0,strrpos($url,'/shareType'));
        $url = $url.'/userId/'.$userId;
        $newRelativePath = C('USER_LOGO');
        $shareQRCodes = createLogoQRcode($url,$avatarPath,$newRelativePath);
        $this->ajaxReturn(successMsg('成功',array('url'=>$shareQRCodes)));
    }

    //删除带产品我的推客二维码
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

    //我的平台推客二维码
    public function myQRCodes(){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        $userId = $this->user['id'];
        $scene_type = 'QR_LIMIT_SCENE';
        $res = $this -> getQRcode($scene_type, $userId);
        print_r($res);exit;
//        $where['user_id'] = $userId;
//        $mode = D('member');
//        $memberInfo = $mode->where($where)->find();
//        if(!empty($memberInfo) && $memberInfo['qr_code']){
//            $this->ajaxReturn(successMsg('成功',array('url'=>$memberInfo['qr_code'])));
//        }
//        $avatarPath = $this->user['avatar'];
//        $url =  $this->host.'index.php/Mall/Index/index/userId/'.$userId;
//        $newRelativePath = C('USER_LOGO');
//        $shareQRCodes = createLogoQRcode($url,$avatarPath,$newRelativePath);
//        $data['qr_code'] = $shareQRCodes;
//        $data['user_id'] = $this->user['id'];
//        if(empty($memberInfo)){
//            $res = $mode -> add($data);
//        }
//        if(!empty($memberInfo) && empty($memberInfo['qr_code'])){
//            $res = $mode -> where($where)->save($data);
//        }
//        if($res){
//            $this->ajaxReturn(successMsg('成功',array('url'=>$shareQRCodes)));
//        }else{
//            $this->ajaxReturn(errorMsg('失败'));
//        }
    }

    //我的推客收益
    public function referrerEarnings(){
        $userId = $this->user['id'];

    }

    
}
