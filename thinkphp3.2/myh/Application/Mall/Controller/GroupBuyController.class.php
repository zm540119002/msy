<?php
namespace Mall\Controller;

use  web\all\Controller\AuthUserController;

class GroupBuyController extends AuthUserController{
    public function index(){
        $this->display();
    }

    //发起微团购
    public function send(){
        if(IS_POST){
            $goods = $_POST['goodsList'][0];
            $controllerOrder = A('Order');
            //订单类型为团购
            $data['type'] = 1;
            //物流状态为待付款
            $data['logistics_status'] = 1;
            $res = $controllerOrder->generate(true,$data);
            $orderId = $res['id'];
            if(!$orderId){
                $this->ajaxReturn(errorMsg('订单生成失败'));
            }
            $modelGroupBuy = D('GroupBuy');
            $modelGoods = D('Goods');
            $where =array(
                'g.id' => $goods['foreign_id'],
            );
            $res = $modelGoods->selectGoods($where);
            if(!$res[0]['id']){
                $this->ajaxReturn(errorMsg('团购的商品不存在'));
            }
            $goods['price'] = $res['sale_price'];
            //插入团购记录
            $_POST = [];
            $_POST['goods_id'] = $goods['foreign_id'];
            $_POST['user_id'] = $this->user['id'];
            $_POST['create_time'] = time();
            $_POST['overdue_time'] = strtotime('+3 day');
            $_POST['sn'] = generateSN();
            $modelGroupBuy->startTrans();//开启事务
            $res = $modelGroupBuy->addGroupBuy();
            $groupBuyId = $res['id'];
            if(!$groupBuyId){
                $modelGroupBuy->rollback();//回滚事务
                $this->ajaxReturn(errorMsg('发起团购失败'));
            }
            $modelGroupBuyDetail = D('GroupBuyDetail');
            $_POST = [];
            $_POST['goods_id'] = $goods['foreign_id'];
            $_POST['num'] = $goods['num'];
            $_POST['price'] = $goods['price'];
            $_POST['user_id'] = $this->user['id'];
            $_POST['order_id'] = $orderId;
            $_POST['group_buy_id'] = $groupBuyId;
            $res = $modelGroupBuyDetail->addGroupBuyDetail();
            $groupBuyDetailId = $res['id'];
            if(!$groupBuyDetailId){
                $modelGroupBuy->rollback();//回滚事务
                $this->ajaxReturn(errorMsg('发起团购失败'));
            }
            $modelGroupBuy->commit();//提交事务
            $this->ajaxReturn(successMsg('成功',array('orderId'=>$orderId,'groupBuyId'=>$groupBuyId)));
        }else{
        }
    }
}
