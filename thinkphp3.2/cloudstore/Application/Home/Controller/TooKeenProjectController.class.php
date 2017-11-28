<?php
namespace Home\Controller;

//店家档案
class TooKeenProjectController extends BaseAuthCompanyController {
    //管理云店拓客项目
    public function manageProject(){
        if(IS_POST){
            if( isset($_POST['list_view_img']) && $_POST['list_view_img'] ){
                $newRelativePath = C('PROJECT_LIST_VIEW');
                $_POST['list_view_img'] = $this->moveImgFromTemp($newRelativePath,basename($_POST['list_view_img']));
            }
            if( isset($_POST['home_focus_img']) && $_POST['home_focus_img'] ){
                $newRelativePath = C('PROJECT_HOME_FOCUS');
                $_POST['home_focus_img'] = $this->moveImgFromTemp($newRelativePath,basename($_POST['home_focus_img']));
            }
            if( isset($_POST['project_id']) && intval($_POST['project_id'])){
                $_POST['id'] = I('post.project_id',0,'int');
                $this->_saveProject();
            }else{
                $this->_addProject();
            }
            $this->assign('project',$_POST);

            $this->display('Public:projectInfo');
        }else{
            $where = array(
                's.user_id' => $this->user['id'],
            );
            $this->shopList = $this->selectShop($where);

            $this->display();
        }
    }

    //新增项目
    private function _addProject(){
        $model_project = M('project');
        $rules = array(
            array('name','require','项目名称必须！'),
            array('name','','项目名称已经存在！',0,'unique',1),
        );
        $_POST['user_id'] = $this->user['id'];
        $_POST['company_id'] = $this->company['id'];
        $_POST['create_time'] = time();

        $model_project->startTrans();//开启事务
        $res = $model_project->validate($rules)->create();
        if(!$res){
            $model_project->rollback();//回滚
            $this->ajaxReturn(errorMsg($model_project->getError()));
        }

        $projectId = $model_project->add();
        if(!$projectId){
            $model_project->rollback();//回滚
            $this->ajaxReturn(errorMsg($model_project->getError()));
        }

        if( isset($_POST['shopIdArr']) && count($_POST['shopIdArr']) ){
            $model_shop_project = M('shop_project');
            $res = $this->_addShopProjectByIds($model_shop_project,$_POST['shopIdArr'],$projectId);
            if(false === $res){
                $model_project->rollback();//回滚
                $this->ajaxReturn(errorMsg($model_shop_project->getError()));
            }
        }
        $model_project->commit();

        $_POST['id'] = $projectId;
    }

    //更新项目
    private function _saveProject(){
        $model_project = M('project');
        $rules = array(
            array('name','require','项目名称必须！'),
        );
        $_POST['update_time'] = time();

        $model_project->startTrans();//开启事务
        $res = $model_project->validate($rules)->create();
        if(!$res){
            $model_project->rollback();//回滚
            $this->ajaxReturn(errorMsg($model_project->getError()));
        }

        $res = $model_project->save();
        if(false === $res){
            $model_project->rollback();//回滚
            $this->ajaxReturn(errorMsg($model_project->getError()));
        }

        //更新提供项目的门店
        if( isset($_POST['shopIdArr']) && count($_POST['shopIdArr']) ){
            $newShopIdArr = $_POST['shopIdArr'];
            $model_shop_project = M('shop_project');
            $oldShopIdArr = $model_shop_project->where(array('project_id' => $_POST['id']))->getField('shop_id',true);
            $addShopProjectIdArr = array_diff ( $newShopIdArr ,  $oldShopIdArr );
            if(count($addShopProjectIdArr)){
                $res = $this->_addShopProjectByIds($model_shop_project,$addShopProjectIdArr,$_POST['id']);
                if(false === $res){
                    $model_project->rollback();//回滚
                    $this->ajaxReturn(errorMsg($model_shop_project->getError()));
                }
            }
            $delShopProjectIdArr = array_diff ( $oldShopIdArr ,  $newShopIdArr );
            if(count($delShopProjectIdArr)){
                $res = $this->_delShopProjectByIds($model_shop_project,$delShopProjectIdArr,$_POST['id']);
                if(false === $res){
                    $model_project->rollback();//回滚
                    $this->ajaxReturn(errorMsg($model_shop_project->getError()));
                }
            }
        }

        //提交
        $model_project->commit();
    }

    //新增shop_project记录
    private function _addShopProjectByIds($model_shop_project,$idsArr,$project_id){
        foreach ($idsArr as $item) {
            $_data[] = array(
                'shop_id' => $item,
                'project_id' => $project_id,
            );
        }
        return $model_shop_project->addAll($_data);
    }

    //删除shop_project记录
    private function _delShopProjectByIds($model_shop_project,$idsArr,$project_id){
        $where = array(
            'project_id' => $project_id,
            'shop_id' => array('in',$idsArr),
        );
        return $model_shop_project->where($where)->delete();
    }

    //加载云店项目
    public function getProjects(){
        if(!IS_GET){
            $this->ajaxReturn(errorMsg('请用GET方式访问！'));
        }
        $model = M('project p');
        $where = array(
            'p.status' => 0,
            'p.user_id' => $this->user['id'],
            'p.company_id' => $this->company['id'],
            'p.online' => 1,
        );
        $field = ' p.id,p.name,p.brief_trait,p.original_price,p.sale_price,p.time_consuming,
            p.list_view_img,p.home_focus_img,p.service_content,p.service_description,p.online ';

        $projectList = $model->where($where)->field($field)->select();
        if(false === $projectList){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        $model_shop_project = M('shop_project');
        foreach ($projectList as &$item){
            $where = array(
                'project_id' => $item['id'],
            );
            $field = ' shop_id ';
            $shopProjectList = $model_shop_project->field($field)->where($where)->select();
            $shopIdArr = array();
            foreach ($shopProjectList as $val){
                $shopIdArr[] = $val['shop_id'];
            }
            $item['shopIdArr'] = $shopIdArr;
        }
        $this->assign('projectList',$projectList);
        $this->display('Public:projectList');
    }

    //上下线
    public function onOffLine(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg('请用POST方式访问！'));
        }
        if(!isset($_POST['project_id']) || !$_POST['project_id']){
            $this->ajaxReturn(errorMsg('缺少参数project_id！'));
        }
        if(!isset($_POST['online'])){
            $this->ajaxReturn(errorMsg('缺少参数online！'));
        }
        $id = I('post.project_id',0,'int');
        $model = M('project');
        $where = array(
            'user_id' => $this->user['id'],
            'company_id' => $this->company['id'],
            'id' => $id,
        );
        $res = $model->where($where)->setField('online',$_POST['online']?1:0);
        if(false === $res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        $this->ajaxReturn(successMsg('成功'));
    }
}