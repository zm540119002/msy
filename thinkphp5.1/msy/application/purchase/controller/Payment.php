<?php
namespace app\purchase\controller;

class Payment extends \common\controller\UserBase{
    //订单-支付
    public function orderPayment(){
        if(IS_POST){
        }else{
            if(isset($_GET['orderId']) && !empty($_GET['orderId'])){
                $modelOrder = D('Order');
                $orderId = $_GET['orderId'];
                $where = array(
                    'o.user_id' => $this->user['id'],
                    'o.id' => $orderId,
                );
                $orderInfo = $modelOrder -> selectOrder($where);
                $orderInfo = $orderInfo[0];
                $this->orderInfo = $orderInfo;
                //检查订单状态
                $result = $modelOrder->checkOrderLogisticsStatus($orderInfo['logistics_status']);
                if($result['status'] == 0){
                    $this->error($result['message']);
                }
                $payInfo = array(
                    'sn'=>$orderInfo['sn'],
                    'actually_amount'=>$orderInfo['actually_amount'],
                    'cancel_back' => U('payCancel'),
                    'fail_back' => U('payFail'),
                    'success_back' => U('payComplete'),
                    'notify_url'=>C('WX_CONFIG')['CALL_BACK_URL'] .
                        ($orderInfo['type'] == 0?'/weixin.order':'/weixin.group_buy'),
                );
                if($orderInfo['type']==1){//团购订单
                    $where = array(
                        'gbd.user_id' => $this->user['id'],
                        'gbd.order_id' => $orderId,
                    );
                    $groupBuy = D('GroupBuyDetail')->selectGroupBuyDetail($where);
                    $groupBuy = $groupBuy[0];
                    if($groupBuy['goods_type'] == 1){
                        $successBackUrl =  U('Goods/goodsInfo',
                            array(
                                'goodsId'=>$groupBuy['foreign_id'],
                                'groupBuyId'=>$groupBuy['group_buy_id'],
                                'shareType'=>'groupBuy',
                            ));
                    }
                    if($groupBuy['goods_type'] == 2){
                        $successBackUrl =  U('Project/projectInfo',
                            array(
                                'projectId'=>$groupBuy['foreign_id'],
                                'groupBuyId'=>$groupBuy['group_buy_id'],
                                'shareType'=>'groupBuy',
                            ));
                    }
                    $payInfo['success_back'] = $successBackUrl;
                }
                Pay::wxPay($payInfo);
            }
        }

        //微信支付
//        $payInfo = array(
//            'sn'=>generateSN(12),
//            'actually_amount'=>0.01,
//            'cancel_back' => url('payCancel'),
//            'fail_back' => url('payFail'),
//            'success_back' => url('payComplete'),
//            'notify_url'=>config('wx_config.call_back_url')
//        );
//        \common\component\payment\weixin\weixinpay::wxPay($payInfo);

        //支付宝支付
        $order = [
            'sn'=>generateSN(10),
            'actually_amount'=>0.01,
        ];
        $model = new \common\component\payment\alipayMobile\alipayMobile;
        return $model->get_code($order);
        //银联支付
//        $order = [
//            'sn'=>generateSN(),
//            'actually_amount'=>0.01,
//        ];
//        $model = new \common\component\payment\unionpay\unionpay;
//        return $model->get_code($order);
    }

    //充值-支付
    public function rechargePayment(){
        $modelWalletDetail = D('WalletDetail');
        if(IS_POST){
        }else{
            if(isset($_GET['walletDetailId']) && intval($_GET['walletDetailId'])){
                $where = array(
                    'wd.id' => I('get.walletDetailId'),
                    'wd.user_id' => $this->user['id'],
                );
                $walletDetailInfo = $modelWalletDetail->selectWalletDetail($where);
                $walletDetailInfo = $walletDetailInfo[0];
                $this->amount = $walletDetailInfo['amount'];
                $payInfo = array(
                    'sn'=>$walletDetailInfo['sn'],
                    'actually_amount'=>$this->amount,
                    'cancel_back' => U('payCancel'),
                    'fail_back' => U('payFail'),
                    'success_back' => U('payComplete'),
                    'notify_url'=>C('WX_CONFIG')['CALL_BACK_URL'].'/weixin.recharge',
                );
                Pay::wxPay($payInfo);
            }
        }
    }
}