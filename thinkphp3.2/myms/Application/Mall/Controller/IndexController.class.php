<?php
namespace Mall\Controller;
use  web\all\Controller\BaseController;

class IndexController extends BaseController {
    //首页
    public function index(){
        if(IS_POST){
        }else{
            //分类产品
            $this->gCategoryList = D('GoodCategory')->selectGoodsCategory();
            $this->pCategoryList = D('ProjectCategory')->selectProjectCategory();
            $this->display();
        }
    }
}