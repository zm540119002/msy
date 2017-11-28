<?php
namespace Admin\Controller;

use web\all\Controller\BaseController;

class ProjectController extends BaseController {
    //项目管理
    public function projectManage(){
        if(IS_POST){
        }else{
            //所有项目分类
            $this->allCategoryList = D('ProjectCategory')->selectProjectCategory();
            $this->display();
        }
    }

    //项目列表
    public function projectList(){
        $model = D('Project');
        if(IS_POST){
        }else{
            $where = array(
                'p.status' => 0,
                'p.on_off_line' => 1,
            );
            if(isset($_GET['category_id_1']) && intval($_GET['category_id_1'])){
                $where['p.category_id_1'] = I('get.category_id_1',0,'int');
            }
            if(isset($_GET['category_id_2']) && intval($_GET['category_id_2'])){
                $where['p.category_id_2'] = I('get.category_id_2',0,'int');
            }
            if(isset($_GET['category_id_3']) && intval($_GET['category_id_3'])){
                $where['p.category_id_3'] = I('get.category_id_3',0,'int');
            }
            $keyword = I('get.keyword','','string');
            if($keyword){
                $where['_complex'] = array(
                    'p.name' => array('like', '%' . trim($keyword) . '%'),
                );
            }
            $field = array(
                'p.id','p.name','p.category_id_1','p.category_id_2','p.category_id_3','p.price','p.group_price','p.sort',
                'pc1.id category_id_1','pc1.name category_name_1',
                'pc2.id category_id_2','pc2.name category_name_2',
                'pc3.id category_id_3','pc3.name category_name_3',
            );
            $join = array(
                ' left join project_category pc1 on pc1.id = p.category_id_1 ',
                ' left join project_category pc2 on pc2.id = p.category_id_2 ',
                ' left join project_category pc3 on pc3.id = p.category_id_3 ',
            );

            $order = 'p.sort';
            $group = "";
            $pageSize = (isset($_GET['pageSize']) && $_GET['pageSize']) ? I('get.pageSize',0,'int') : C('DEFAULT_PAGE_SIZE');

            $projectList = page_query($model,$where,$field,$order,$join,$group,$pageSize,$alias='p');

            $this->projectList = $projectList['data'];
            $this->pageList = $projectList['pageList'];
            $this->display();
        }
    }

    //上下架
    public function projectOnoffLine(){
        if(IS_POST){
        }else{
            //所有项目分类
            $this->allCategoryList = D('ProjectCategory')->selectProjectCategory();
            $this->display();
        }
    }

    //上下架-项目列表
    public function projectOnoffLineList(){
        $model = D('Project');
        if(IS_POST){
        }else{
            $where = array(
                'p.status' => 0,
            );
            if(isset($_GET['category_id_1']) && intval($_GET['category_id_1'])){
                $where['p.category_id_1'] = I('get.category_id_1',0,'int');
            }
            if(isset($_GET['category_id_2']) && intval($_GET['category_id_2'])){
                $where['p.category_id_2'] = I('get.category_id_2',0,'int');
            }
            if(isset($_GET['category_id_3']) && intval($_GET['category_id_3'])){
                $where['p.category_id_3'] = I('get.category_id_3',0,'int');
            }
            $keyword = I('get.keyword','','string');
            if($keyword){
                $where['_complex'] = array(
                    'p.name' => array('like', '%' . trim($keyword) . '%'),
                );
            }
            $field = array(
                'p.id','p.name','p.category_id_1','p.category_id_2','p.category_id_3','p.on_off_line','p.sort',
                'pc1.id category_id_1','pc1.name category_name_1',
                'pc2.id category_id_2','pc2.name category_name_2',
                'pc3.id category_id_3','pc3.name category_name_3',
            );
            $join = array(
                ' left join project_category pc1 on pc1.id = p.category_id_1 ',
                ' left join project_category pc2 on pc2.id = p.category_id_2 ',
                ' left join project_category pc3 on pc3.id = p.category_id_3 ',
            );

            $order = 'p.sort';
            $group = "";
            $pageSize = (isset($_GET['pageSize']) && $_GET['pageSize']) ? I('get.pageSize',0,'int') : C('DEFAULT_PAGE_SIZE');

            $projectList = page_query($model,$where,$field,$order,$join,$group,$pageSize,$alias='p');

            $this->projectList = $projectList['data'];
            $this->pageList = $projectList['pageList'];
            $this->display();
        }
    }

    //项目编辑
    public function projectEdit(){
        $model = D('Project');
        if(IS_POST){
            if( isset($_POST['main_img']) && $_POST['main_img'] ){
                $_POST['main_img'] = $this->moveImgFromTemp(C('MYMS_PROJECT_MAIN_IMG'),basename($_POST['main_img']));
            }
            if( isset($_POST['detail_img']) && $_POST['detail_img'] ){
                $detailArr = explode(',',I('post.detail_img','','string'));
                $tempArr = array();
                foreach ($detailArr as $item) {
                    if($item){
                        $tempArr[] = $this->moveImgFromTemp(C('MYMS_PROJECT_DETAIL_IMG'),basename($item));
                    }
                }
                $_POST['detail_img'] = implode(',',$tempArr);
            }
            if( isset($_POST['flow_img']) && $_POST['flow_img'] ){
                $detailArr = explode(',',I('post.flow_img','','string'));
                $tempArr = array();
                foreach ($detailArr as $item) {
                    if($item){
                        $tempArr[] = $this->moveImgFromTemp(C('MYMS_PROJECT_FLOW_IMG'),basename($item));
                    }
                }
                $_POST['flow_img'] = implode(',',$tempArr);
            }
            if( isset($_POST['explain_img']) && $_POST['explain_img'] ){
                $detailArr = explode(',',I('post.explain_img','','string'));
                $tempArr = array();
                foreach ($detailArr as $item) {
                    if($item){
                        $tempArr[] = $this->moveImgFromTemp(C('MYMS_PROJECT_EXPLAIN_IMG'),basename($item));
                    }
                }
                $_POST['explain_img'] = implode(',',$tempArr);
            }
            if(isset($_POST['projectId']) && intval($_POST['projectId'])){//修改
                $model->startTrans();
                try{
                    $projectId =I('post.projectId',0,'int');
                    $where = array(
                        'p.id' => $projectId ,
                    );
                    $projectInfo = $model->selectProject($where);
                    $projectInfo = $projectInfo[0];
                    //删除商品主图
                    if($projectInfo['main_img']){
                       $this->delImgFromPaths($projectInfo['main_img'],$_POST['main_img']);
                    }
                    if($projectInfo['detail_img']){
                        //删除商品详情图
                        $oldImgArr = explode(',',$projectInfo['detail_img']);
                        $newImgArr = explode(',',$_POST['detail_img']);
                        $this->delImgFromPaths($oldImgArr,$newImgArr);
                    }
                    if($projectInfo['explain_img']){
                        //删除商品详情图
                        $oldImgArr = explode(',',$projectInfo['explain_img']);
                        $newImgArr = explode(',',$_POST['explain_img']);
                        $this->delImgFromPaths($oldImgArr,$newImgArr);
                    }
                    if($projectInfo['flow_img']){
                        //删除商品详情图
                        $oldImgArr = explode(',',$projectInfo['flow_img']);
                        $newImgArr = explode(',',$_POST['flow_img']);
                        $this->delImgFromPaths($oldImgArr,$newImgArr);
                    }
                    $goodsWhere = array(
                        'project_id' => $projectId,
                    );
                    $proAllGoodsIds=M('project_goods','','DB_MYMS')->where($goodsWhere)->getField('goods_id',true);
                    $addGoods = $_POST['goods'];
                    $allGoodsIds = array();//添加产品存在的总goodsIds
                    $sameGoodsIds = array();//相同的ID
                    foreach ($addGoods as $k=>$v){
                        $allGoodsIds[]= $v['goodsId'];
                        foreach ($proAllGoodsIds as $kk => $vv){
                            if($v['goodsId'] == $vv) {//修改数据库数量
                                $sameGoodsIds[] = $vv;
                                $goodsNum = $v['goodsNum'];
                                $result= $model->updateProjectGoodsNum( $v['goodsId'], $goodsNum);
                                if($result === false){
                                    throw new \Exception( $this->ajaxReturn(errorMsg('修改商品信息失败')));
                                }
                            }
                        }
                    }
                    $addGoodsIds = array_diff($allGoodsIds,$sameGoodsIds);//添加goodsIds
                    if(!empty($addGoodsIds)){
                        foreach ($addGoodsIds as $k=>$v){
                            foreach ($addGoods as $kk=>$vv){
                                if(intval($v)==intval($vv['goodsId'])){
                                    $data = array();
                                    $data['project_id']   = $projectId;
                                    $data['goods_id']     = $vv['goodsId'];
                                    $data['goods_num']    = $vv['goodsNum'];
                                    $data_goods[] = $data;
                                }
                            }
                        }
                        $result =  M('project_goods','','DB_MYMS')->addAll($data_goods);
                        if($result === false){
                            throw new \Exception( $this->ajaxReturn(errorMsg('添加项目产品失败')));
                        }
                    }


                    $delGoodsIds = array_diff($proAllGoodsIds,$allGoodsIds);//删除goodsIds
                    if(!empty($delGoodsIds)){
                        $delWhere['goods_id'] = array('in',$delGoodsIds);
                        $result = M('project_goods','','DB_MYMS')->where($delWhere)->delete();
                        if(!$result){
                            throw new \Exception( $this->ajaxReturn(errorMsg('删除项目产品失败')));
                        }
                    }
                    $_POST['update_time'] = time();
                    $result = $model->saveProject();
                    if($result === false){
                        throw new \Exception( $this->ajaxReturn(errorMsg('删除项目产品失败')));
                    }
                    $model -> commit();
                    $this -> ajaxReturn( successMsg ('修改项目成功') );
                }catch (\Exception $e){
                    $model->rollback();
                    $this->ajaxReturn($e->getMessage());
                }

            }else{//新增
                $model->startTrans();
                try{
                    $_POST['create_time'] = time();
                    $project = $model->addProject();
                    if($project['status']==0){
                        throw new \Exception( $this->ajaxReturn(errorMsg('添加项目失败')));
                    }
                    if (!empty($_POST['goods']) && is_array($_POST['goods'])) {
                        foreach ($_POST['goods'] as $key => $val) {
                            $data = array();
                            $data['project_id']           = $project['id'];
                            $data['goods_id']            = $val['goodsId'];
                            $data['goods_num']           = $val['goodsNum'];
                            $data_goods[] = $data;
                        }
                        $return =  M('project_goods','','DB_MYMS')->addAll($data_goods);
                        if(!$return){
                            throw new \Exception( $this->ajaxReturn(errorMsg('添加项目产品失败')));
                        }
                    }else{
                        throw new \Exception( $this->ajaxReturn(errorMsg('请添加商品')));
                    }
                    $model -> commit();
                    $this -> ajaxReturn( successMsg ('添加项目成功') );
                }catch (\Exception $e){
                    $model->rollback();
                    $this->ajaxReturn($e->getMessage());
                }
            }

        }else{
            //所有项目分类
            $this->allCategoryList = D('ProjectCategory')->selectProjectCategory();
            //所有商品分类
            $this->allGoodsCategoryList = D('GoodCategory')->selectGoodsCategory();
            //要修改的项目
            if(isset($_GET['projectId']) && intval($_GET['projectId'])){
                $where = array(
                    'p.id' => I('get.projectId',0,'int'),
                );
                $projectInfo = $model->selectProject($where);
                $this->assign('projectInfo',$projectInfo[0]);
            }

            $this->display();
        }
    }

    //项目删除
    public function projectDel(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        $model = D('Project');
        $res = $model->delProject();
        $this->ajaxReturn($res);
    }


   //商品列表
    public function goodsPhotoList(){
        $model = D('Goods');
        $where = array(
            'g.status' => 0,
        );
        if(isset($_GET['category_id_1']) && intval($_GET['category_id_1'])){
            $where['g.category_id_1'] = I('get.category_id_1',0,'int');
        }
        if(isset($_GET['category_id_2']) && intval($_GET['category_id_2'])){
            $where['g.category_id_2'] = I('get.category_id_2',0,'int');
        }
        if(isset($_GET['category_id_3']) && intval($_GET['category_id_3'])){
            $where['g.category_id_3'] = I('get.category_id_3',0,'int');
        }
        $keyword = I('get.keyword','','string');
        if($keyword){
            $where['_complex'] = array(
                'g.name' => array('like', '%' . trim($keyword) . '%'),
            );
        }
        $field = array(
            'g.id','g.name','g.category_id_1','g.category_id_2','g.category_id_3',
            'g.on_off_line','g.inventory','g.sort','g.price','g.main_img',
            'gc1.id category_id_1','gc1.name category_name_1',
            'gc2.id category_id_2','gc2.name category_name_2',
            'gc3.id category_id_3','gc3.name category_name_3',
        );
        $join = array(
            ' left join goods_category gc1 on gc1.id = g.category_id_1 ',
            ' left join goods_category gc2 on gc2.id = g.category_id_2 ',
            ' left join goods_category gc3 on gc3.id = g.category_id_3 ',
        );

        $order = 'g.sort';
        $group = "";
        $pageSize = (isset($_GET['pageSize']) && $_GET['pageSize']) ? I('get.pageSize',0,'int') : C('DEFAULT_PAGE_SIZE');
        $alias='g';

        $goodsList = page_query($model,$where,$field,$order,$join,$group,$pageSize,$alias);

        $this->goodsList = $goodsList['data'];
        $this->pageList = $goodsList['pageList'];

        $this->display('Public/goodsPhotoList');
    }
}
