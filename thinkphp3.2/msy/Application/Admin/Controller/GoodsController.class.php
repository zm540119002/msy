<?php
namespace Admin\Controller;

use web\all\Controller\BaseController;

class GoodsController extends BaseController {
    //商品管理
    public function goodsManage(){
        if(IS_POST){
        }else{
            //所有商品分类
            $this->allCategoryList = D('GoodsCategory')->selectGoodsCategory();
            $this->display();
        }
    }

    //商品列表
    public function goodsList(){
        if(!IS_GET){
            $this->ajaxReturn(errorMsg(C('NOT_GET')));
        }

        $model = D('Goods');
        $where = array(
            'g.status' => 0,
            'g.on_off_line' => 1,
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
            'g.id','g.name','g.category_id_1','g.category_id_2','g.category_id_3','g.inventory','g.sort',
            'g.price','g.special_price','g.vip_price','g.senior_vip_price','g.gold_vip_price','g.unit',
            'gc1.id category_id_1','gc1.name category_name_1','gc2.id category_id_2',
            'gc2.name category_name_2','gc3.id category_id_3','gc3.name category_name_3',
        );
        $join = array(
            ' left join goods_category gc1 on gc1.id = g.category_id_1 ',
            ' left join goods_category gc2 on gc2.id = g.category_id_2 ',
            ' left join goods_category gc3 on gc3.id = g.category_id_3 ',
        );

        $order = 'g.sort';
        $group = "";
        $pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ? I('get.pageSize',0,'int') : C('DEFAULT_PAGE_SIZE');

        $goodsList = page_query($model,$where,$field,$order,$join,$group,$pageSize,$alias='g');

        $this->goodsList = $goodsList['data'];
        $this->pageList = $goodsList['pageList'];
        $this->display();
    }

    //上下架
    public function goodsOnoffLine(){
        if(IS_POST){
        }else{
            //所有商品分类
            $this->allCategoryList = D('GoodsCategory')->selectGoodsCategory();
            $this->display();
        }
    }

    //上下架-商品列表
    public function goodsOnoffLineList(){
        $model = D('Goods');
        if(IS_POST){
        }else{
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
                'g.on_off_line','g.inventory','g.sort','g.unit',
                'gc1.id category_id_1','gc1.name category_name_1','gc2.id category_id_2',
                'gc2.name category_name_2','gc3.id category_id_3','gc3.name category_name_3',
            );
            $join = array(
                ' left join goods_category gc1 on gc1.id = g.category_id_1 ',
                ' left join goods_category gc2 on gc2.id = g.category_id_2 ',
                ' left join goods_category gc3 on gc3.id = g.category_id_3 ',
            );

            $order = 'g.sort';
            $group = "";
            $pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ? I('get.pageSize',0,'int') : C('DEFAULT_PAGE_SIZE');
            $alias='g';

            $goodsList = page_query($model,$where,$field,$order,$join,$group,$pageSize,$alias);

            $this->goodsList = $goodsList['data'];
            $this->pageList = $goodsList['pageList'];

            $this->display();
        }
    }

    //商品编辑
    public function goodsEdit(){
        $model = D('Goods');
        if(IS_POST){
            if( isset($_POST['main_img']) && $_POST['main_img'] ){
                $_POST['main_img'] = $this->moveImgFromTemp(C('GOODS_MAIN_IMG'),basename($_POST['main_img']));
            }
            if( isset($_POST['detail_img']) && $_POST['detail_img'] ){
                $detailArr = explode(',',I('post.detail_img','','string'));
                $tempArr = array();
                foreach ($detailArr as $item) {
                    if($item){
                        $tempArr[] = $this->moveImgFromTemp(C('GOODS_DETAIL_IMG'),basename($item));
                    }
                }
                $_POST['detail_img'] = implode(',',$tempArr);
            }
            if(isset($_POST['goodsId']) && intval($_POST['goodsId'])){//修改
                $where = array(
                    'g.id' => I('post.goodsId',0,'int'),
                );
                $goodsInfo = $model->selectGoods($where);
                $goodsInfo = $goodsInfo[0];
                //删除商品主图
                if($goodsInfo['main_img']){
                    $this->delImgFromPaths($goodsInfo['main_img'],$_POST['main_img']);
                }
                if($goodsInfo['detail_img']){
                    //删除商品详情图
                    $oldImgArr = explode(',',$goodsInfo['detail_img']);
                    $newImgArr = explode(',',$_POST['detail_img']);
                    $this->delImgFromPaths($oldImgArr,$newImgArr);
                }
                $_POST['update_time'] = time();
                $res = $model->saveGoods();
            }else{//新增
                $_POST['create_time'] = time();
                $res = $model->addGoods();
            }
            $this->ajaxReturn($res);
        }else{
            //所有商品分类
            $this->allCategoryList = D('GoodsCategory')->selectGoodsCategory();
            //要修改的商品
            if(isset($_GET['goodsId']) && intval($_GET['goodsId'])){
                $where = array(
                    'g.id' => I('get.goodsId',0,'int'),
                );
                $goodsInfo = $model->selectGoods($where);
                $this->assign('goodsInfo',$goodsInfo[0]);
            }
            //单位
            $modelUnit = D('Unit');
            $unitList = $modelUnit->selectUnit();
            $this->assign('unitList',$unitList);

            $this->display();
        }
    }

    //商品删除
    public function goodsDel(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        $model = D('Goods');
        $res = $model->delGoods();
        $this->ajaxReturn($res);
    }

   //项目产品
    public function goodsProjectList(){
        if(IS_POST){
            if(isset($_POST['goodsId']) && $_POST['goodsId']){
                $goodsId = I('post.goodsId',0,'int');
                $where['id']  = $goodsId;
                $addGoodsList = D('Goods')->selectGoods($where);
                $this->addGoodsList = $addGoodsList;
            }
            if(isset($_POST['projectId']) && $_POST['projectId']){
                $projectId = I('post.projectId',0,'int');
                $where['project_id']  = $projectId;
                $field = array(
                    'g.id','g.no','g.name','g.status','g.category_id_1','g.category_id_2','g.category_id_3','g.param','g.on_off_line',
                    'g.sort','g.specification','g.price','g.special_price','g.vip_price','g.senior_vip_price','g.gold_vip_price',
                    'g.inventory','g.main_img','g.detail_img','g.create_time','g.intro','g.unit',
                );
                $join = array(
                    ' left join goods g on g.id = pg.goods_id ',
                );
                $addGoodsList = D('Project')->selectProjectGoods($where,$field,$join);
                $this->addGoodsList = $addGoodsList;
            }
            $this->display();
        }
    }

    //公共图片编辑
    public function commonImageEdit(){
        $model = M('common_images','','DB_CONFIG1');
        $commonImg = $model -> find();
        if(IS_POST) {
            if (isset($_POST['common_img']) && $_POST['common_img']) {
                $detailArr = explode(',', I('post.common_img', '', 'string'));
                $tempArr = array();
                foreach ($detailArr as $item) {
                    if ($item) {
                        $tempArr[] = $this->moveImgFromTemp(C('GOODS_COMMON_IMG'), basename($item));
                    }
                }
                $_POST['common_img'] = implode(',', $tempArr);
            }
            if(empty($commonImg)){
                $rst = $model->add($_POST);
                if($rst){
                    $this->ajaxReturn(successMsg('增加成功'));
                }else{
                    $this->ajaxReturn(errorMsg('增加失败'));
                }
            }else{
                $oldImgArr = explode(',',$commonImg);
                $newImgArr = explode(',',$_POST['common_img']);
                $this->delImgFromPaths($oldImgArr,$newImgArr);
                $rst = $model->where($commonImg['id'])->save($_POST);
                if($rst===false){
                    $this->ajaxReturn(errorMsg('修改失败'));
                }else{
                    $this->ajaxReturn(successMsg('修改成功'));
                }
            }
        }else{
           $this->commonImg = $commonImg['common_img'];
        }
        $this->display();
    }
}
