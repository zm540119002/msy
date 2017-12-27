<?php
namespace Mall\Controller;

use web\all\Controller\AuthUserController;
use  web\all\Lib\Pay;

class WxPayController extends AuthUserController {
    //订单支付
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
                $result = $modelOrder->checkOrderStatus($orderInfo);
                if($result['status'] == 0){
                    $this->error($result['message']);
                }
                $payInfo = array(
                    'sn'=>$orderInfo['sn'],
                    'actually_amount'=>$orderInfo['actually_amount'],
                    'notify_url'=>C('WX_CONFIG')['CALL_BACK_URL_ORDER'].'/weixin.order',
                );
                Pay::wxPay($payInfo);
            }
        }
    }

    //充值支付
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
                    'notify_url'=>C('WX_CONFIG')['CALL_BACK_URL_RECHARGE'].'/weixin.recharge',
                );
                Pay::wxPay($payInfo);
            }
        }
    }
}