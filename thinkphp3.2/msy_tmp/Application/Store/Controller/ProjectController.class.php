<?php
namespace Store\Controller;
use Think\Controller;
use Common\Controller\BaseStoreController;
use Think\Model;

class ProjectController extends BaseStoreController {
 
    public function projectManagement(){
        if(IS_POST){
           //把临时的存放的图片移动上传目录
            $newRelativePath = C('PROJECT_PATH');
            if( isset($_POST['list_view_img']) && $_POST['list_view_img'] ){
                $_POST['list_view_img'] = moveImgFromTemp($newRelativePath,basename($_POST['list_view_img']));
            }
            if( isset($_POST['home_focus_img']) && $_POST['home_focus_img'] ){
                $_POST['home_focus_img'] = moveImgFromTemp($newRelativePath,basename($_POST['home_focus_img']));
            }
            
            if(isset($_POST['project_id']) && $_POST['project_id'] ){//修改
                //过滤ID
                $_POST['project_id'] = intval($_POST['project_id']);
                $where = array('project_id' => $_POST['project_id']);
                $projectInfo = M('Project') -> where($where)->find();
                $this->projectInfo=$projectInfo;

                //var_dump($projectInfo);exit;
                $this->saveProject();
                $this -> display();
            }else if(isset($_POST['img']) && $_POST['img']){
                $this -> uploadImg();
            }else{//新增

                $this->addProject();
            }
        }else{
            $where = array(
                'uid' => session('uid'),
            );
            //获取机构类型的状态
            $scaleType = M('company') -> where($where) ->getField('shop_type');
            $this -> scaleType = intval($scaleType);
            //查找门店信息
            $shopList = D('shop') -> getShopList();
            foreach ($shopList as $key => $value){
                $shop_ids[]  = $value['id'];
                $shop_names[]  = $value['storename'];
                $company_ids[]  = $value['company_id'];
            }

            $shop_ids = implode(',',$shop_ids);
            $shop_names = implode(',',$shop_names);
            $company_ids = implode(',',$company_ids);
            $this -> shop_ids = $shop_ids;
            $this -> shop_names = $shop_names;
            $this -> company_ids = $company_ids;
            $this -> shopList = $shopList;

            //查找项目列表
            $where = array('uid' => session('uid'));
            $projectList = M('Project') -> where($where)->select();
            krsort($projectList);
            //var_dump($projectList);exit;
            $this -> projectList = $projectList;
            $this -> display();
        }

    }


    /**
     * 上传图片
     */
    public function uploadImg(){
        if(IS_POST){
            $base64_image     = $_POST['img'];
            $UPLOAD_PATH      = C('UPLOAD_PATH');
            $CACHE_IMAGE_PATH = C('CACHE_IMAGE_PATH');
            $path             = $UPLOAD_PATH.$CACHE_IMAGE_PATH;//图片上传路径
            $imgUrl           = upload($base64_image,$path);

            $image = new \Think\Image();
            //var_dump($imgUrl);exit;
            //open();打开图像资源，通过路径名找到图像
            $image -> open($imgUrl);
            if($_POST['name'] == 'uploadListViewImg'){
                $image -> thumb(950,500,\Think\Image::IMAGE_THUMB_FIXED) ->save($imgUrl);  //按照比例缩小
                $imgUrl = '/'.$imgUrl;
                show(1,'',$imgUrl);
            }else if($_POST['name'] == 'uploadHomeFocusImg'){
                $image -> thumb(1000,1000,\Think\Image::IMAGE_THUMB_FIXED) ->save($imgUrl);  //按照比例缩小
                $imgUrl = '/'.$imgUrl;
                show(1,'',$imgUrl);
            }else{
                show(0,'没有此类图片上传');
            }
        }

    }


    /**
     *  增加美容预约项目
     */
    public function addProject(){
       if(IS_POST){

//           if(!D('Project')->create()){
//               $error_info = D('Project')->getError();
//               show(0, $error_info);
//           }

           if( isset($_POST['shop_ids']) && !$_POST['shop_ids'] ){
               $shopList = D('shop') -> getShopList();
               $_POST['shop_ids']    = $shopList[0]['id'];
               $_POST['shop_names']  = $shopList[0]['storename'];
               $_POST['company_ids'] = D('Company') -> getCompanyID();
           }
           if(isset($_POST['shop_ids']) && is_array($_POST['shop_ids'])){
               $_POST['shop_ids']    = implode(',',$_POST['shop_ids']);
               $_POST['shop_names']  = implode(',',$_POST['shop_names']);
               $_POST['company_ids'] = implode(',',$_POST['company_ids']);
           }
           $_POST['create_time'] = time();
           $_POST['uid'] = session('uid');
           $project_id   = M('Project') -> add($_POST);

           if($project_id){
               show(1,'增加项目成功',$project_id);
           }else{
               show(0,'增加项目失败');
           }

       }
    }

    public function saveProject(){
        if(IS_POST){
            if(!D('Project')->create()){
               $error_info = D('Project')->getError();
               show(0, $error_info);
           }

           //判断是否更改图片，如果更改就把之前的图片删除
            $where = array('id' => $_POST['project_id']);
            $imgInfo =M('Project') -> where($where) -> field('list_view_img,home_focus_img') -> find();
            if( ($imgInfo['list_view_img'] !== $_POST['list_view_img']) && (!empty($imgInfo['list_view_img'])) ){
                if(file_exists($imgInfo['list_view_img'])){
                    if(!unlink($imgInfo['list_view_img'])){
                        show(0,'删除原图失败');
                    }
                }
            }
            if( ($imgInfo['home_focus_img'] !== $_POST['home_focus_img']) && (!empty($imgInfo['home_focus_img'])) ){
                if(file_exists($imgInfo['home_focus_img'])){
                    if(!unlink($imgInfo['home_focus_img'])){
                        show(0,'删除原图失败');
                    }
                }
            }


            if( isset($_POST['shop_ids']) && !$_POST['shop_ids'] ){
                $shopList = D('shop') -> getShopList();
                $_POST['shop_ids']    = $shopList[0]['id'];
                $_POST['shop_names']  = $shopList[0]['storename'];
                $_POST['company_ids'] = D('Company') -> getCompanyID();
            }

            if(isset($_POST['shop_ids']) && is_array($_POST['shop_ids'])){
                $_POST['shop_ids']    = implode(',',$_POST['shop_ids']);
                $_POST['shop_names']  = implode(',',$_POST['shop_names']);
                $_POST['company_ids'] = implode(',',$_POST['company_ids']);
            }

            $_POST['update_time'] = time();
            $project_id   = M('Project') -> where($where) -> save($_POST);
            if($project_id){
                show(1,'修改项目成功',$project_id);
            }else{
                show(0,'修改项目失败');
            }
        }


    }

    //上下线
    public function onOffLine(){
        if(!IS_POST){
            show(0,'请用POST方式访问！');
        }
        if(!isset($_POST['project_id']) || !$_POST['project_id']){
            show(0,'缺少参数project_id！');
        }
        if(!isset($_POST['status'])){
            show(0,'缺少参数status！');
        }
        $id = I('post.project_id',0,'int');
        $where = array('id' => $id );
        $data  = array('status' => I('post.status',0,'int'));
        $rst   = M('project') -> where($where) -> save($data);
        if(false == $rst){
            show(0,'更改项目状态失败');
        }else{
            show( 1,'更改项目状态成功',$_POST['status'] );
        }
    }

  
}