<?php
namespace Purchase\Controller;
use web\all\Controller\AuthCompanyAuthoriseController;
use  web\all\Lib\Pay;
class WxPayController extends AuthCompanyAuthoriseController {
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
                $this -> orderInfo = $orderInfo;
                $totalFee = $orderInfo['actually_amount'];
                //检查订单状态
                $result = $modelOrder->checkOrderStatus($orderInfo);
                if($result['status'] == 0){
                    $this ->error($result['message']);
                }
                //检查商品库存
                $notifyUrl = C('WX_CONFIG')['CALL_BACK_URL_ORDER'];
                $jsApiParameters = Pay::wxPay($totalFee,$notifyUrl,$orderInfo['sn']);
                $this->assign(array(
                    'data' => $jsApiParameters,
                ));
                $this->display();
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

                $jsApiParameters =Pay::wxPay($this->amount,C('WX_CONFIG')['CALL_BACK_URL_RECHARGE'],$walletDetailInfo['sn']);
                $this->assign(array(
                    'data' => $jsApiParameters,
                ));
                $this->display();
            }
        }
    }
}