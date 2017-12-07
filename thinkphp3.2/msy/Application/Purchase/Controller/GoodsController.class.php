<?php
namespace Purchase\Controller;

use  web\all\Lib\Date;
use  web\all\Controller\BaseController;

class GoodsController extends BaseController {
    //所有一级分类下商品（默认显示第一个分类下商品）
    public function allCategoryGoods(){
        $modelGoodsCategory = D('GoodsCategory');
        if(IS_POST){
        }else{
            //结算时间
            $this->settlementTime = Date::getSettlementTime();

            //单个一级分类信息
            if(isset($_GET['category_id_1']) && intval($_GET['category_id_1'])){
                $this->category_id_1 = I('get.category_id_1',0,'int');
            }
            $where = array(
                'gc.id' => $this->category_id_1,
                'gc.level' => 1,
            );
            $goodsCategoryList = $modelGoodsCategory->selectGoodsCategory($where);
            $this->goodsCategory1Info = $goodsCategoryList[0];
            //查询一级分类下二级分类
            $where = array();
            $where['gc.parent_id_1'] = $this->category_id_1;
            $where['gc.level'] = 2;
            $this->goodsCategory2List = $modelGoodsCategory->selectGoodsCategory($where);
            //购物车配置开启的项
            $this->unlockingFooterCart = unlockingFooterCartConfig(array(0,7));
            $this ->display();
        }
    }

    //单个一级分类下商品
    public function categoryGoods(){
        $modelGoodsCategory = D('GoodsCategory');
        if(IS_POST){
        }else{
            //结算时间
            $this->settlementTime = Date::getSettlementTime();

            //单个一级分类信息
            if(isset($_GET['category_id_1']) && intval($_GET['category_id_1'])){
                $this->category_id_1 = I('get.category_id_1',0,'int');
            }
            $where = array(
                'gc.id' => $this->category_id_1,
                'gc.level' => 1,
            );
            $goodsCategoryList = $modelGoodsCategory->selectGoodsCategory($where);
            $this->goodsCategory1Info = $goodsCategoryList[0];
            //购物车配置开启的项
            $this->unlockingFooterCart = unlockingFooterCartConfig(array(0,7));
            $this ->display();
        }
    }

    //分类商品-主图形式-页面
    public function goodsPage(){
        if(!IS_GET){
            $this->ajaxReturn(errorMsg(C('NOT_GET')));
        }
        if(isset($_GET['category_id_1']) && intval($_GET['category_id_1'])){
            $category_id_1 = I('get.category_id_1',0,'int');
        }
        //查询一级分类下二级分类
        $modelGoodsCategory = D('GoodsCategory');
        $where = array();
        $where['gc.level'] = 2;
        if($category_id_1){
            $where['gc.parent_id_1'] = $category_id_1;
        }
        $goodsCategory2List = $modelGoodsCategory->selectGoodsCategory($where);
        //二级分类下商品-分页查询
        $modelGoods = D('Goods');
        $field = array();
        $join = array();
        $group = "";
        $order = 'g.sort';
        $pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ? I('get.pageSize',0,'int') : C('DEFAULT_PAGE_SIZE');
        $this->currentPage = (isset($_GET['p']) && intval($_GET['p'])) ? I('get.p',0,'int') : 1;
        foreach ($goodsCategory2List as &$item){
            $where = array(
                'g.status' => 0,
                'g.on_off_line' => 1,
            );
            $where['g.category_id_2'] = $item['id'];
            $goodsList = page_query($modelGoods,$where,$field,$order,$join,$group,$pageSize,$alias='g');
            $item['goodsList'] = $goodsList['data'];
        }
        $this->goodsCategory2List = $goodsCategory2List;
        $this ->display('categoryGoodsPhotoListTpl');
    }

    //商品信息
    public function goodsInfo(){
        if(!IS_GET){
            $this->ajaxReturn(errorMsg(C('NOT_GET')));
        }
        $modelGoods = D('Goods');
        $where = array(
            'g.status' => 0,
            'g.on_off_line' => 1,
        );
        if(isset($_GET['goods_id']) && intval($_GET['goods_id'])){
            $where['g.id'] = I('get.goods_id',0,'int');
        }
        $goodsInfo = $modelGoods->selectGoods($where);
        $this->goodsInfo = $goodsInfo[0];
        //级别价格
        $this->levelPrice = getGoodsPirceByUserLevel($goodsInfo[0],$this->user['level']);
        $this->display('goodsInfoTpl');
    }

    //商品列表
    public function goodsList(){
        if(!IS_GET){
            $this->ajaxReturn(errorMsg(C('NOT_GET')));
        }
        //二级分类下商品-分页查询
        $modelGoods = D('Goods');
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
        $field = array();
        $join = array();
        $group = "";
        $order = 'g.sort';
        $pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ? I('get.pageSize',0,'int') : C('DEFAULT_PAGE_SIZE');
        $this->currentPage = (isset($_GET['p']) && intval($_GET['p'])) ? I('get.p',0,'int') : 1;
        $goodsList = page_query($modelGoods,$where,$field,$order,$join,$group,$pageSize,$alias='g');
        $this->goodsList = $goodsList['data'];
        $template_type = I('get.template_type','','string');
        if($template_type=='photo'){
            $this ->display('goodsPhotoListTpl');
        }else if($template_type=='list'){
            $this ->display('goodsListTpl');
        }else if($template_type=='jointPurchase'){
            $this ->display('Cart/jointPurchase');
        }
    }

    //商品详情页
    public function goodsDetail(){
        if(IS_POST){
        }else{
            //结算时间
            $this->settlementTime = Date::getSettlementTime();
            $modelGoods = D('Goods');
            $where = array(
                'g.status' => 0,
                'g.on_off_line' => 1,
            );
            if(isset($_GET['goodsId']) && intval($_GET['goodsId'])){
                $where['g.id'] = I('get.goodsId',0,'int');
            }
            $goodsInfo = $modelGoods->selectGoods($where);
            $this->goodsInfo = $goodsInfo[0];
            $this ->display();
        }
    }
}