<?php
namespace Mall\Controller;

use  web\all\Controller\BaseController;

class IndexController extends BaseController{
    //商城-首页
    public function index(){
        //购物车配置开启的项
        $this->unlockingFooterCartSingle = unlockingFooterCartConfig(array(2,3,4));
        $this->display();
    }

    //推客分享首页
    public function referrerGoodsIndex(){
        $this->display();
    }
}
