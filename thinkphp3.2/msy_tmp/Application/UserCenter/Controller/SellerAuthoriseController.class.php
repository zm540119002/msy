<?php
namespace UserCenter\Controller;

use Common\Cache\SellerCache;
class SellerAuthoriseController extends  BaseAuthUserController {
    //卖手登记首页
    public function index(){
        if(IS_POST){
        }else{
            $this->seller = SellerCache::get($this->user['id']);
            if($this->seller['auth_status'] == 2){
                $this->display('complete');
            }else{
                $this->display();
            }
        }
    }

    //卖手实名认证-基本资料
    public function basicInfo(){
        if(IS_POST){
            unset($_POST['id']);
            if( isset($_POST['seller_id']) && intval($_POST['seller_id']) ){//修改
                //过滤ID
                $_POST['seller_id'] = I('post.seller_id',0,'int');
                $this->saveSeller();
            }else{//新增
                $this->addSeller();
            }
        }else{
            $this->seller = SellerCache::get($this->user['id']);

            $this->display();
        }
    }

    //卖手实名认证-认证资料
    public function authenticateInfo(){
        $this->seller = SellerCache::get($this->user['id']);
        if(IS_POST){
            $this->sellerAuthSuccess($this->seller['id']);
        }else{
            $this->display();
        }
    }

    //卖手实名认证-递交申请
    public function submitApply(){
        if(IS_POST){
        }else{
            $this->display();
        }
    }

    //上传卖手认证图片
    public function uploadSellerAuthoriseImg(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg('请用POST方式访问！'));
        }
        $this->seller = SellerCache::get($this->user['id']);
        if(!$this->seller){
            $this->ajaxReturn(errorMsg(C('ERROR_SELLER_AUTHORISE_REMIND')));
        }

        $img = isset($_POST['img'])? $_POST['img'] : '';
        // 获取图片
        list($type, $data) = explode(',', $img);
        // 判断类型
        if(strstr($type,'image/jpeg')!=''){
            $ext = '.jpg';
        }elseif(strstr($type,'image/gif')!=''){
            $ext = '.gif';
        }elseif(strstr($type,'image/png')!=''){
            $ext = '.png';
        }

        // 生成的文件名
        $uploadPath = C('UPLOAD_PATH');
        $relativePath = C('SELLER_AUTHENTICATE');

        $storePath = $uploadPath . $relativePath;
        if(!mk_dir($storePath)){
            $this->ajaxReturn(errorMsg('创建目录失败'));
        }
        $storeName = time().$ext;

        $photo = $storePath . $storeName;

        $updateField = isset($_POST['updateField'])?$_POST['updateField']:'id_front_img';

        $model = M('seller');
        $where = array(
            'id' => $this->seller['id'],
            'user_id' => $this->user['id'],
        );
        $res = $model->where($where)->find();
        if(!$res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }
        if($res[$updateField]){
            if (!unlink($uploadPath.$res[$updateField])) {
                $this->ajaxReturn(errorMsg('删除图片失败'));
            }
        }

        // 生成文件
        $returnData = file_put_contents($photo, base64_decode($data), true);
        if(false === $returnData){
            $this->ajaxReturn(errorMsg('保存文件失败'));
        }

        $image = new \Think\Image();
        $image->open($photo);
        $image->thumb(370, 660,\Think\Image::IMAGE_THUMB_SCALE)->save($photo);

        $data = array(
            $updateField => $relativePath . $storeName,
        );
        $res = $model->where($where)->setField($data);
        if(false === $res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        SellerCache::remove($this->user['id']);
        $this->ajaxReturn(successMsg($relativePath . $storeName));
    }
}