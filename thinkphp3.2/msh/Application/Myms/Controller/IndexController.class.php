<?php
namespace Myms\Controller;
use  web\all\Controller\BaseController;
use Component\WxpayAPI\Jssdk;

class IndexController extends BaseController {
    //首页
    public function index(){
        if(IS_POST){
        }else{

            $this -> shareInfo = $this ->weiXinShareInfo('微团购','147','http://myms.meishangyun.com/Uploads/myms/goods-main-img/1503293931.jpeg','好友邀请你参加美尚云团购','http://myms.meishangyun.com');
            $this -> signPackage = $this -> weiXinShareInit();
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