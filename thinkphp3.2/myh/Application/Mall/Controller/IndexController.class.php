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
        $modelGroupBuyDetail = D('GroupBuyDetail');
        $where = array(
            'user_id' => 4,
            'order_id' => 315,
        );
        $groupBuyDetail = $modelGroupBuyDetail->selectGroupBuyDetail($where);
        //团购成功通知
        unset($where);
        $where = array(
            'gbd.type' => 1,
            'gbd.group_buy_id' => $groupBuyDetail[0]['group_buy_id'],
        );
        $field=[ 'g.cash_back','g.goods_base_id','g.commission',
            'gb.name','wxu.headimgurl','wxu.nickname'
        ];
        $join=[ ' left join goods g on g.id = gbd.goods_id',
            ' left join goods_base gb on g.goods_base_id = gb.id ',
            ' left join wx_user wxu on wxu.user_id = gbd.user_id'
        ];
        $templateMessageInfo = $modelGroupBuyDetail->selectGroupBuyDetail($where,$field,$join);
        echo $modelGroupBuyDetail->getLastSql();exit;
        print_r($templateMessageInfo);exit;
        $template = array(
            'touser'=>$groupBuyDetail[0]['openid'],
            'template_id'=>'u7WmSYx2RJkZb-5_wOqhOCYl5xUKOwM99iEz3ljliyY',
            "url"=>$this->host.U('Goods/goodsDetail',array(
                    'goodsId'=>$groupBuyDetail[0]['goods_id'],
                    'groupBuyId'=> $groupBuyDetail[0]['group_buy_id'],
                    'shareType'=>'groupBuy' )),
            'data'=>array(
                'first'=>array(
                    'value'=>'亲，您已成功参加团购！','color'=>'#173177',
                ),
                'Pingou_ProductName'=>array(
                    'value'=>$templateMessageInfo[0]['name'],'color'=>'#173177',
                ),
                'Weixin_ID'=>array(
                    'value'=>$templateMessageInfo[0]['nickname'],'color'=>'#173177',
                ),
                'Remark'=>array(
                    'value'=>'您的已付款项将在3-5天内退至您的支付账户，请留意相关信息。','color'=>'#173177',
                ),

            ),

        );
        print_r($this->sendTemplateMessage($template));
    }
   
}
