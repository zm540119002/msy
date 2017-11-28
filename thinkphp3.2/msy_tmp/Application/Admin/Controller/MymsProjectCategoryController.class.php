<?php
namespace Admin\Controller;

use Common\Controller\BaseController;

class MymsProjectCategoryController extends BaseController {
    /**商品分类-管理
     */
    public function projectCategoryManage(){
        $model = D('MymsProjectCategory');
        if(IS_POST){
            $level = I('post.level',0,'int');
            $where = array();
            if($level == 1){
                $where['pc.parent_id_1'] = I('post.projectCategoryId',0,'int');
                $where['pc.level'] = 2;
            }elseif($level == 2){
                $where['pc.parent_id_1'] = I('post.parent_id_1',0,'int');
                $where['pc.parent_id_2'] = I('post.projectCategoryId',0,'int');
                $where['pc.level'] = 3;
            }
            $this->projectCategoryList = $model->selectProjectCategory($where);

            $this->display('projectCategoryList');
        }else{
            $where = array(
                'pc.level' => 1,
            );
            $this->projectCategoryList = $model->selectProjectCategory($where);

            $this->display();
        }
    }

    /**商品分类-编辑
     */
    public function projectCategoryEdit(){
        $model = D('MymsProjectCategory');
        if(IS_POST){
            if( isset($_POST['img']) && $_POST['img'] ){
                $_POST['img'] = $this->moveImgFromTemp(C('MYMS_PROJECT_CATEGORY_IMG'),basename($_POST['img']));
            }
            if(isset($_POST['projectCategoryId']) && intval($_POST['projectCategoryId'])){
                $projectCategoryId =I('post.projectCategoryId',0,'int');
                $where = array(
                    'pc.id' => $projectCategoryId ,
                );
                $projectCategoryInfo = $model->selectProjectCategory($where);
                $projectCategoryInfo = $projectCategoryInfo[0];
                //删除项目分类图
                if($projectCategoryInfo['img']){
                    $this->delImgFromPaths($projectCategoryInfo['img'],$_POST['img']);
                }
                $res = $model->saveProjectCategory();
            }else{
                $res = $model->addProjectCategory();
            }
            $this->ajaxReturn($res);
        }else{
            $this->operate = I('get.operate','','string');

            if (isset($_GET['projectCategoryId']) && intval($_GET['projectCategoryId'])){
                $projectCategoryId = I('get.projectCategoryId', 0, 'int');
                $where = array(
                    'pc.id' => $projectCategoryId,
                );
                $editProjectCategory = $model->selectProjectCategory($where);
                $this->editProjectCategory = $editProjectCategory[0];
            }
            $this->allCategoryList = $model->selectProjectCategory();

            $this->display();
        }
    }

    /**商品分类-删除
     */
    public function delProjectCategory(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        $model = D('MymsProjectCategory');
        $res = $model->delProjectCategory();
        $this->ajaxReturn($res);
    }
}