<?php
namespace Purchase\Controller;

use  web\all\Lib\AuthUser;
use  web\all\Controller\BaseController;
use Think\Controller;

class IndexController extends BaseController
{
    //采购商城-首页
    public function index(){
        echo $_SERVER['HTTP_USER_AGENT'];exit;
        $modelGoodsCategory = D('GoodsCategory');
        //一级分类
        $where = array(
            'gc.level' => 1,
        );
        $goodsCategoryList = $modelGoodsCategory->selectGoodsCategory($where);
        $this->goodsCategoryList = $goodsCategoryList;

        $this->display();
    }


}
