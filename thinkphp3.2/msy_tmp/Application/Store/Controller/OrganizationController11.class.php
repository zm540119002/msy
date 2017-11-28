<?php
namespace Store\Controller;

use Common\Model\Verifyaction;
use Think\Controller;
use Common\Controller\AuthUserController;
class OrganizationController extends AuthUserController {
    public function index(){

        $this -> display();
    }


    public function basicInfoRegister(){
        $this -> display();
    }

    public function addressRegister(){

        $model = D('organization');
        if(!empty(I('post.'))) {
            if (!$model->create()) {
                $info = $model->getError();
                $this -> error($info);
            }else{
                $basicInfo = I('post.');
                $this -> assign('basicInfo',$basicInfo);
            }
        }
        $this -> display();
    }

    /**
     * 机构登记全部，
     */
    public function registerAll(){
           if(IS_POST){
            if (!D('Organization')->create()) {
                $info = D('Organization')->getError();
                return show(0,$info);
            }
               $data = (I('post.'));
               $data['uid'] = session('uid');
               $data['create_time'] =time();
               $data['auth_status'] = 1;
               $result = M('organization')->add($data);
           if($result) {
                show(1, "登记成功",$result);
            }else{
                show(0, "登记失败");
            }

        }
    }

    public function registerFinish(){
        $this -> display();
    }

    /**
     * 机构认证须知
     */
    public function authNotice(){

        $this -> display();
    }

    /**
     * 认证基本资料页面
     */
    public function authInfo(){
        if(IS_POST){
        }else{
            $where = array(
                'uid' => session('uid'),
            );
            $info = M('organization') -> where($where) -> field('name,organizajc')->find();
            $this -> assign('info',$info);
            $this -> display();
        }

    }

    /**
     *  认证图片页面
     */
    public function authImg(){
        if(IS_POST){
            if (!D('Organization') -> create()) {
                $info = D('Organization') ->getError();
                $this -> error($info);
            }else{
                $authInfo = I('post.');
                $this -> assign('authInfo',$authInfo);
            }

        }

        $this ->display();
    }

    /**
     * 上传认证图片
     */
    public function uploadImg(){

        $base64_image = $_POST['img'];
        $field = $_POST['field'];
        $where = array(
            'uid' => session('uid'),
        );
        //如果有图片先删除再上传
        $field = M('organization') ->where($where) ->getField($field);

        if(!($field == '')){
            if (!unlink($field)) {
               show(0,"替换图片失败");
            }
        }
        $UPLOAD_PATH = C('UPLOAD_PATH');
        $ORGANIZATION_VERIFY_PATH = C('ORGANIZATION_VERIFY_PATH');
        $path = $UPLOAD_PATH.$ORGANIZATION_VERIFY_PATH;
        $imgUrl = upload($base64_image,$path);
        if($imgUrl == ''){
            show(0,"上传图片失败",$imgUrl);
        }else{
            show(1,"上传图片成功",$imgUrl);
        }

    
    }

    /**
     * 认证资料提交
     */
    public function authCommit(){
        if(IS_POST) {
            if (!D('Organization')->create()) {
                $info = D('Organization')->getError();
                return show(0, $info);
            }
            $where = array(
                'uid' => session('uid'),
            );
            $data = I('post.');
            $result = D('Organization') -> where($where) ->save($data);
            if($result){
                show(1,"提交资料完成");
            }else{
                show(0,"无法提交资料");
            }

        }
    }

    /**
     * 认证资料提交完页面
     */
    public function authComplete(){
        
        $this -> display();
    }











}


