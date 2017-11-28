<?php
namespace Purchase\Controller;
use Think\Controller;
use  Common\Lib\AuthUser;
class IndexController extends BaseBaseController {
    //前台首页
    public function index(){
       
        $user = AuthUser::getSession();
        if(isset($user)){
            $userMobile = substr_replace($user['mobile'],'****',3,4);
            $this->assign('userMobile',$userMobile);
            $cartList = D('cart') -> getCartList($user['id']);
            $this->assign('cartList',$cartList);
            $cartIds = D('cart')->getCartIds($user['id']);
            $this->assign('cartIds',$cartIds);
        }else{
            $cartList = D('cart') -> getCartListBySession();
        }
        
        $cartInfo = D('cart') -> getCartInfo($cartList);
        if($cartInfo['count']){
            $this->assign('cartInfo',$cartInfo);
        }
        //一级分类
        $catList = M('goods_category')->where("parent_id = 0")->select();
        $this -> assign('catList',$catList);
        $this -> display();
    }

    public function ajaxIndex(){
        $user = AuthUser::getSession();
        $this -> user = $user;
        if(IS_GET){
            $categoryId = intval($_GET['categoryId']);
            $condition['category_id']  = array($categoryId);
            $bundlingList = M('bundling')->where($condition)->Field('id,bundling_image')->order('id DESC')->select();
            $this->assign('bundlingList',$bundlingList);

            $goodsList = D('goods')->getCartInfo($categoryId);
            $this->assign('goodsList',$goodsList);
            $this->display();
        }
    }
}