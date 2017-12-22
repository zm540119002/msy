<?php
namespace Mall\Controller;

use  web\all\Lib\Date;
use  web\all\Controller\BaseController;

class GoodsController extends BaseController {
    //商品信息
    public function goodsInfo(){
        $modelGoods = D('Goods');
        $where = array(
            'g.status' => 0,
            'gb.on_off_line' => 1,
        );
        if(IS_POST){
            if(isset($_POST['goodsId']) && intval($_POST['goodsId'])){
                $where['g.id'] = I('post.goodsId',0,'int');
            }
        }else{
            if(isset($_GET['goodsId']) && intval($_GET['goodsId'])){
                $where['g.id'] = I('get.goodsId',0,'int');
            }
        }
        $field = array(
            'g.id','g.buy_type','g.sale_price',
            'gb.no','gb.name','gb.single_specification','gb.package_num','gb.package_unit','gb.purchase_unit','gb.price',
        );
        $join = array(
            ' left join goods_base gb on g.goods_base_id = gb.id ',
        );
        $goodsInfo = $modelGoods->selectGoods($where,$field,$join);
        $this->goodsInfo = $goodsInfo[0];
        $this->display('goodsInfoTpl');
    }

    //商品列表
    public function goodsList(){
        if(!IS_GET){
            $this->ajaxReturn(errorMsg(C('NOT_GET')));
        }
        $modelGoods = D('Goods');
        $where = array(
            'g.status' => 0,
            'gb.on_off_line' => 1,
        );
        if(isset($_GET['buyType']) && intval($_GET['buyType'])){
            $where['g.buy_type'] = I('get.buyType',0,'int');
        }
        $field = array(
            'g.id','g.buy_type','g.sale_price',
            'gb.name','gb.price','gb.main_img',
        );
        $join = array(
            ' left join goods_base gb on g.goods_base_id = gb.id ',
        );
        $group = "";
        $order = 'gb.sort';
        $pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ? I('get.pageSize',0,'int') : C('DEFAULT_PAGE_SIZE');
        $this->currentPage = (isset($_GET['p']) && intval($_GET['p'])) ? I('get.p',0,'int') : 1;
        $goodsList = page_query($modelGoods,$where,$field,$order,$join,$group,$pageSize,$alias='g');
        $this->goodsList = $goodsList['data'];
        $templateType = I('get.templateType','','string');
        if($templateType=='photo'){
            $this ->display('goodsPhotoListTpl');
        }else if($templateType=='list'){
            $this ->display('goodsListTpl');
        }
    }

    //商品详情页
    public function goodsDetail(){
        if(IS_POST){
        }else{
            $modelGoods = D('Goods');
            $where = array(
                'g.status' => 0,
                'gb.on_off_line' => 1,
            );
            if(isset($_GET['goodsId']) && intval($_GET['goodsId'])){
                $where['g.id'] = I('get.goodsId',0,'int');
            }
            $field = array(
                'g.id','g.buy_type','g.sale_price',
                'gb.no','gb.name','gb.price','gb.main_img','gb.single_specification','gb.param','gb.intro',
                'gb.usage','gb.notices','gb.detail_img',
            );
            $join = array(
                ' left join goods_base gb on g.goods_base_id = gb.id ',
            );
            $goodsInfo = $modelGoods->selectGoods($where,$field,$join);
            $this->goodsInfo = $goodsInfo[0];
            //公共图片
            $modelCommonImg = D('CommonImages');
            $commonImg = $modelCommonImg->selectCommonImages();
            $this->commonImg = $commonImg[0]['common_img'];
            $this ->display();
        }
    }
}