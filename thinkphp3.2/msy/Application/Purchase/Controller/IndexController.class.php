<?php
namespace Purchase\Controller;

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
        $this->unlockingFooterCart = unlockingFooterCartConfig(array(0,2,4));

        $this->display();
    }


}
