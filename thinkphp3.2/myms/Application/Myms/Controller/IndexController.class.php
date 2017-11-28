<?php
namespace Myms\Controller;
use  web\all\Controller\BaseController;
//use web\all\Component\WxpayAPI\Jssdk;
//use web\all\Lib\Pay;
//use web\all\Lib\AuthUser;

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