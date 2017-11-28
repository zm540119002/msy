<?php
namespace UserCenter\Controller;

use Common\Cache\CompanyCache;

class CompanyAuthoriseController extends BaseAuthCompanyRegisterController {
    //机构认证-首页
    public function index(){
        if(IS_POST){
        }else{
            if($this->company['auth_status'] == 1){
                $this->display();
            }else if($this->company['auth_status'] ==2){
                $this->display('authorizeComplete');
            }
        }
    }

    //机构认证状态查询
    public function authStatusSearch(){
        $res = M('company')->where(array('id'=>$this->company['id']))->field('auth_status')->find();

        if(!$res){
            $this->ajaxReturn(errorMsg(M('company')->getError()));
        }
        $this->ajaxReturn(successMsg($res));
    }

    //机构认证-基本资料
    public function authorizeInfo(){
        if(IS_POST){
            $rules = array(
                array('name','require','美容机构完整名称必须！'),
                array('shorten_name','require','美容机构简称必须！'),
                array('registrant','require','申请人姓名必须！'),
                array('registrant_mobile','isMobile','请输入正确的手机号码',0,'function'),
            );
            $_POST['companyId'] = $this->company['id'];
            $this->saveCompany($rules);
        }else{
            $this->assign('company',$this->company);
            $this->display();
        }
    }

    //机构认证-认证资料
    public function authorizeData(){
        if(IS_POST){
        }else{
            $this->assign('company',$this->company);
            $this->display();
        }
    }

    //机构认证-提交申请
    public function authorizeSubmit(){
        if(IS_POST){
            //暂时在这里认证成功，以后在后台确认
            $this->companyAuthSuccess($this->company['id']);
        }else{
            $this->display();
        }
    }

    //机构认证-认证完成
    public function authorizeComplete(){
        if(IS_POST){
        }else{
            $this->display();
        }
    }

    //上传机构认证图片
    public function uploadCompanyImg(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg('请用POST方式访问！'));
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
        $companyPath = C('COMPANY_VERIFY_PATH');

        $storePath = $uploadPath.$companyPath;
        if(!mk_dir($storePath)){
            $this->ajaxReturn(errorMsg('创建目录失败'));
        }
        $storeName = time().$ext;

        $photo = $storePath . $storeName;

        $uploadType = isset($_POST['uploadType'])?$_POST['uploadType']:'';

        $model = M('company');
        $where = array(
            'id' => $this->company['id'],
        );
        $res = $model->where($where)->find();
        if(!$res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }
        if($res[$uploadType]){
            if (!unlink($uploadPath.$res[$uploadType])) {
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
            $uploadType => $companyPath . $storeName,
        );
        $res = $model->where($where)->setField($data);
        if(false === $res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        CompanyCache::remove($this->user['id']);
        $this->ajaxReturn(successMsg($companyPath . $storeName));
    }
}