<?php
namespace Business\Controller;

use  web\all\Controller\BaseController;

class IndexController extends BaseController{
    //商务-首页
    public function index(){
        //购物车配置开启的项
        $this->unlockingFooterCart = unlockingFooterCartConfig(array(13));
        $this->display();
    }
}
