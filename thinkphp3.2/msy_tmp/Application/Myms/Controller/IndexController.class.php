<?php
namespace Myms\Controller;
use Common\Lib\AuthUser;
use Common\Controller\BaseController;
class IndexController extends BaseController {
    //首页
    public function index(){
        if(IS_POST){
        }else{
            //分类产品
            $this->gCategoryList = D('goods_category')->selectGoodsCategory();
            $this->pCategoryList = D('project_category')->selectProjectCategory();
            $this->cartList = D('cart') -> cartList();
            $this->cartInfo = D('cart') -> getAllCartInfo();
            C('HTTP_CACHE_CONTROL',"no-cache, no-store, must-revalidate");
            $this->display();
        }
    }


}