<?php
namespace Home\Controller;

use  web\all\Lib\AuthUser;
use  web\all\Controller\BaseController;

class IndexController extends BaseController {
    //前台首页
    public function index(){
        $this -> display();
        exit;
        $user = AuthUser::check();
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
    }

    public function ajaxIndex(){
        $user = AuthUser::check();
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