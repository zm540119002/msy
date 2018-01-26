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
            //单位
//            $modelUnit = D('Unit');
//            $unitList = $modelUnit->selectUnit();
//            $this->assign('unitList',$unitList);

            $this->display();
        }
    }

    //商品列表
    public function goodsBaseList(){
        if(!IS_GET){
            $this->ajaxReturn(errorMsg(C('NOT_GET')));
        }

        $model = D('GoodsBase');
        $where = array(
            'gb.status' => 0,
            'gb.on_off_line' => 1,
        );
        if(isset($_GET['category_id_1']) && intval($_GET['category_id_1'])){
            $where['gb.category_id_1'] = I('get.category_id_1',0,'int');
        }
        if(isset($_GET['category_id_2']) && intval($_GET['category_id_2'])){
            $where['gb.category_id_2'] = I('get.category_id_2',0,'int');
        }
        if(isset($_GET['category_id_3']) && intval($_GET['category_id_3'])){
            $where['gb.category_id_3'] = I('get.category_id_3',0,'int');
        }
        $keyword = I('get.keyword','','string');
        if($keyword){
            $where['_complex'] = array(
                'gb.name' => array('like', '%' . trim($keyword) . '%'),
            );
        }
        $field = array(
            'gb.id','gb.name','gb.category_id_1','gb.category_id_2','gb.category_id_3','gb.inventory','gb.sort', 'gb.price',
            'gb.single_specification','gb.package_num','gb.package_unit','gb.purchase_unit',
            'gc1.id category_id_1','gc1.name category_name_1','gc2.id category_id_2',
            'gc2.name category_name_2','gc3.id category_id_3','gc3.name category_name_3',
        );
        $join = array(
            ' left join goods_category gc1 on gc1.id = gb.category_id_1 ',
            ' left join goods_category gc2 on gc2.id = gb.category_id_2 ',
            ' left join goods_category gc3 on gc3.id = gb.category_id_3 ',
        );
        $order = 'gb.sort, gb.id desc';
        $group = "";
        $pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ? I('get.pageSize',0,'int') : C('DEFAULT_PAGE_SIZE');
        $goodsList = page_query($model,$where,$field,$order,$join,$group,$pageSize,$alias='gb');
        $this->goodsList = $goodsList['data'];
        $this->pageList = $goodsList['pageList'];
        $this->display();
    }

    //设置购买类型
    public function setPurchaseType(){
        if(IS_POST){
            //增加
            if(isset($_POST['addData'])){
                $addData=$_POST['addData'];
                foreach ($addData as $item) {
                    $return =  D('Goods')->add($item);
                    if(!$return){
                        $this->ajaxReturn(errorMsg('增加失败'));
                    }
                }
            }
            //修改
            if(isset($_POST['editData'])){
                $editData=$_POST['editData'];
                foreach ($editData as $item) {
                    $where['id']=$item['goods_id'];
                    $return =  D('Goods')->where($where)->save($item);
                    if(false===$return){
                        $this->ajaxReturn(errorMsg('修改失败'));
                    }
                }
            }
            //删除
            if(isset($_POST['delData'])){
                $addData=$_POST['delData'];
                foreach ($addData as $item) {
                    $where['id']=$item;
                    $return =  D('Goods')->where($where)->delete();
                    if(!$return){
                        $this->ajaxReturn(errorMsg('删除失败'));
                    }
                }
            }
            $this->ajaxReturn(successMsg('成功'));

        }else{
            //所有商品分类
            if(isset($_GET['goodsBaseId']) && intval($_GET['goodsBaseId'])){
                $where['id'] = intval($_GET['goodsBaseId']);
                $goodsBaseInfo = D('GoodsBase')-> where($where) -> field('id,name,price') -> find();
                $this->goodsBaseInfo = $goodsBaseInfo;
                $where1['goods_base_id'] = intval($_GET['goodsBaseId']);
                $goodsList = D('Goods') -> where($where1)->select();
                $buyTypeArray=[];
                foreach ($goodsList as $item) {
                    $buyTypeArray[] = $item['buy_type'];
                }
                $buyTypeArrayAll= array_column(C('BUY_TYPE'),"buy_type");
                $noBuyTypeArray= array_diff($buyTypeArrayAll,$buyTypeArray);
                $this -> noBuyTypeArray = $noBuyTypeArray;
                $this -> goodsList = $goodsList;
            }
            $this->display();
        }
    }

    //商品编辑
    public function goodsBaseEdit(){
        $model = D('GoodsBase');
        if(IS_POST){
            if( isset($_POST['main_img']) && $_POST['main_img'] ){
                $_POST['main_img'] = $this->moveImgFromTemp(C('GOODS_MAIN_IMG'),basename($_POST['main_img']));
            }
            if( isset($_POST['thumb_img']) && $_POST['thumb_img'] ){
                $_POST['thumb_img'] = $this->moveImgFromTemp(C('GOODS_THUMB_IMG'),basename($_POST['thumb_img']));
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
            if(isset($_POST['goodsBaseId']) && intval($_POST['goodsBaseId'])){//修改
                $where = array(
                    'gb.id' => I('post.goodsId',0,'int'),
                );
                $goodsInfo = $model->selectGoodsBase($where);
                $goodsInfo = $goodsInfo[0];
                //删除商品主图
                if($goodsInfo['main_img']){
                    $this->delImgFromPaths($goodsInfo['main_img'],$_POST['main_img']);
                }
                if($goodsInfo['thumb_img']){
                    $this->delImgFromPaths($goodsInfo['thumb_img'],$_POST['thumb_img']);
                }

                if($goodsInfo['detail_img']){
                    //删除商品详情图
                    $oldImgArr = explode(',',$goodsInfo['detail_img']);
                    $newImgArr = explode(',',$_POST['detail_img']);
                    $this->delImgFromPaths($oldImgArr,$newImgArr);
                }
                $_POST['update_time'] = time();
                $res = $model->saveGoodsBase();
            }else{//新增
                $_POST['create_time'] = time();
                $res = $model->addGoodsBase();
            }
            $this->ajaxReturn($res);
        }else{
            //所有商品分类
            $this->allCategoryList = D('GoodsCategory')->selectGoodsCategory();
            //要修改的商品
            if(isset($_GET['goodsBaseId']) && intval($_GET['goodsBaseId'])){
                $where = array(
                    'gb.id' => I('get.goodsBaseId',0,'int'),
                );
                $goodsBaseInfo = $model->selectGoodsBase($where);
                $this->assign('goodsBaseInfo',$goodsBaseInfo[0]);
            }
            //单位
            $modelUnit = D('Unit');
            $unitList = $modelUnit->selectUnit();
            $this->assign('unitList',$unitList);

            $this->display();
        }
    }

    //商品删除
    public function goodsBaseDel(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        $model = D('GoodsBase');
        $res = $model->delGoodsBase();
        $this->ajaxReturn($res);
    }

    //公共图片编辑
    public function commonImageEdit(){
        $model = M('common_images','','DB_CONFIG_MALL');
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
