<?php
namespace Home\Controller;

use Common\Cache\SellerCache;
class EditSellerController extends  BaseAuthUserController {
    //编辑卖手基本资料
    public function editSellerInfo(){
        $this->seller = SellerCache::get($this->user['id']);
        if(IS_POST){
            if( isset($_POST['clipAvatar']) && $_POST['clipAvatar'] ){
                $newRelativePath = C('SELLER_AVATAR');
                $_POST['avatar'] = $this->moveImgFromTemp($newRelativePath,basename($_POST['clipAvatar']));
            }
            if($this->seller){//修改
                //删除原头像
                if(isset($_POST['avatar']) && intval($_POST['avatar'])){
                    if($this->seller['avatar']){
                        if ( !unlink(C('UPLOAD_PATH') . $this->seller['avatar']) ) {
                            $this->ajaxReturn(errorMsg('删除原头像失败'));
                        }
                    }
                }
                $_POST['seller_id'] = $this->seller['id'];
                $this->saveSeller();
            }else{//新增
                $this->addSeller();
            }
        }else{
            $this->assign('seller',$this->seller);

            $this->display();
        }
    }

    //编辑卖手档案详情
    public function editSellerArchiveDetail(){
        $this->seller = SellerCache::get($this->user['id']);
        if(IS_POST){
            $figureUrlArr = C('FIGURE_URL');
            $newRelativePath = C('SELLER_FIGURE');
            $originalPath = array();
            foreach ($figureUrlArr as $val){
                if(isset($_POST[$val]) && $_POST[$val]){
                    $_POST[$val] = $this->moveImgFromTemp($newRelativePath,basename($_POST[$val]));
                    $originalPath[] = $val;
                }
            }
            if($this->seller){//修改
                $uploadPath = C('UPLOAD_PATH');
                foreach ($originalPath as $val){
                    if($this->seller[$val]){
                        if (!unlink($uploadPath . $this->seller[$val])) {
                            $this->ajaxReturn(errorMsg('删除图片失败'));
                        }
                    }
                }
                $_POST['seller_id'] = $this->seller['id'];
                $this->saveSeller();
            }else{//新增
                $this->addSeller();
            }
        }else{
            $this->assign('seller',$this->seller);
            $this->display();
        }
    }

    //我的卖手档案预览
    public function sellerArchivePreview(){
        $this->seller = SellerCache::get($this->user['id']);
        if(IS_POST){
        }else{
            $this->assign('seller',$this->seller);
            $sellerAvgScore = $this->getSellerAvgScoreById($this->seller['id']);
            $this->assign('sellerAvgScore',$sellerAvgScore);

            $this->display();
        }
    }
}