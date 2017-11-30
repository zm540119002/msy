<?php
namespace Purchase\Controller;
use Think\Controller;
use  Common\Lib\AuthUser;
class GoodsController extends BaseBaseController {
    //前台首页
    public function index(){


        $this->display();
    }
   //项目分类和所有分类的页面
    public function goodsList(){
        //一级商品分类
        if(isset($_GET['categoryId']) && intval($_GET['categoryId'])){
            $this -> categoryId = intval($_GET['categoryId']);
        }
        $catList = M('goods_category')->where("parent_id = 0")->select();
        $this -> catList = $catList;
        //购物车信息
        $user = AuthUser::getSession();
        if(isset($user)){
            $cartIds = D('cart')->getCartIds($user['id']);
            $this->assign('cartIds',$cartIds);
            $cartList =  D('cart') -> getCartList($user['id']);
        }else{
            $cartList = D('cart') -> getCartListBySession();
        }
        $cartInfo = D('cart') -> getCartInfo($cartList);
        if($cartInfo['count'] > 0){
            $this -> cartInfo = $cartInfo;
        }
        $this ->display();
    }
    //商品列表
    public function ajaxGoodsList(){
        $user = AuthUser::getSession();
        $this -> user = $user;
        if($_GET){
            $categoryId = intval($_GET['categoryId']);
            $goodsList  = D('goods')->getGoodsListByCategoryId($categoryId);
            $this->goodsList = $goodsList;
            $this ->display();
        }
    }

    //商品详情页
    public function goodsInfo(){
        if(IS_GET){
            $user = AuthUser::getSession();
            $this -> user = $user;
            $goodsId   = intval($_GET['id']);
            $goodsInfo = D('goods') -> getGoodsInfoByGoodsId($goodsId);
            $tag                      = explode(',',$goodsInfo['tag']);
            $detaiImgs                = explode(',',$goodsInfo['detail_imgs']);
            $goodsInfo['tag']         = $tag;
            $goodsInfo['detail_imgs'] = $detaiImgs;

            $goodsParameter=html_entity_decode($goodsInfo['parameter']);
            $this ->goodsParameter = $goodsParameter;
            $goodsIntro            = html_entity_decode($goodsInfo['info']);
           
            $this -> goodsIntro    = $goodsIntro;
            $this -> goodsInfo     = $goodsInfo;
            if(isset($user)){
                $cartList =  D('cart') -> getCartList($user['id']);
            }else{
                $cartList = D('cart') -> getCartListBySession();
            }
            $cartInfo = D('cart') -> getCartInfo($cartList);
            if($cartInfo['count'] > 0){
                $this -> goodsCount = $cartInfo['count'];
            }
            $this -> display();
        }
    }


    //套餐信息
    public function bundlingInfo(){
        
        $this ->display();
    }

    //商品库存量的检验
    
}