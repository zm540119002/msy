<?php
namespace Mall\Controller;

use  web\all\Controller\BaseController;
use  web\all\Lib\Pay;

class IndexController extends BaseController{
    //商城-首页
    public function index(){
        //购物车配置开启的项
        $this->unlockingFooterCartSingle = unlockingFooterCartConfig(array(2,3,4));
        $this->display();
    }

    //微团购-首页
    public function groupBuyIndex(){
        $modelComment = D('Comment');
        $this->aveScore = round($modelComment->avg('score'),1);//平均分数
        $this->userCommentNum =$modelComment->count();//多少用户评价
        $this->display('GroupBuy/index');
    }

    //推客分享首页
    public function referrerIndex(){
        $this->display('Referrer/index');
    }
   
}
