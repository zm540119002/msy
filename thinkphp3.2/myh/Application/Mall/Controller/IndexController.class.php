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


    public function a(){
        $template = array(
            'touser'=>'oNalMuA6iE-T45TPb_ZeQYlJ3Jjk',
            'template_id'=>'u7WmSYx2RJkZb-5_wOqhOCYl5xUKOwM99iEz3ljliyY',
            "url"=>$this->host.U('Goods/goodsDetail',array(
                    'goodsId'=>72,
                    'groupBuyId'=> 37,
                    'shareType'=>'groupBuy' )),
            'data'=>array(
                'first'=>'你已团购',
                'Pingou_ProductName'=>'南岸ABB啊',
                'Weixin_ID'=>'巴阿罢罢罢',
                'Remark'=>'暖暖暖暖暖那你呢',
            ),

        );
        print_r($template);exit;
        $this->sendTemplateMessage($template);
    }
   
}
