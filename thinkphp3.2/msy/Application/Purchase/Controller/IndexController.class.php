<?php
namespace Purchase\Controller;

use  web\all\Lib\AuthUser;
use  web\all\Controller\BaseController;
use Think\Controller;

class IndexController extends BaseController
{
    //采购商城-首页
    public function index(){
        $modelGoodsCategory = D('GoodsCategory');
        //一级分类
        $where = array(
            'gc.level' => 1,
        );
        $goodsCategoryList = $modelGoodsCategory->selectGoodsCategory($where);
        $this->goodsCategoryList = $goodsCategoryList;

        //购物车配置开启的项
        $unlockingArr = array('amount','add_cart','buy_now');
        $unlockingFooterCart = unlockingFooterCartConfig($unlockingArr);
        $this->assign('unlockingFooterCart',$unlockingFooterCart);

        $this->display();
    }
}
