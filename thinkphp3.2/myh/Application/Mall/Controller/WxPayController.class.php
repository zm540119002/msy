<?php
namespace Mall\Controller;

use web\all\Controller\AuthUserController;
use  web\all\Lib\Pay;

class WxPayController extends AuthUserController {
    /**
     * 微信支付
     */
    public function wxPay($payInfo){
        Pay::wxPay($payInfo);
    }
    

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
//                $totalFee = $orderInfo['actually_amount'];
                //检查订单状态
                $result = $modelOrder->checkOrderStatus($orderInfo);
                if($result['status'] == 0){
                    $this ->error($result['message']);
                }
                //检查商品库存
//                $notifyUrl = C('WX_CONFIG')['CALL_BACK_URL_ORDER'];
                $payInfo = array(
                    'sn'=>$orderInfo['sn'],
                    'actually_amount'=>$orderInfo['actually_amount'],
                    'notify_url'=>C('WX_CONFIG')['CALL_BACK_URL_ORDER']
                );

                $jsApiParameters = Pay::getJSAPI($payInfo);
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

                $payInfo = array(
                    'sn'=>$walletDetailInfo['sn'],
                    'actually_amount'=>$this->amount,
                    'notify_url'=>C('WX_CONFIG')['CALL_BACK_URL_RECHARGE']
                );
                if (!isPhoneSide()) {//pc端微信扫码支付
                    $code_str = $this->payment->pc_pay($payInfo);
                }elseif(strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') == false ){//手机端非微信浏览器
                    $code_str =Pay::h5_pay($payInfo);
                }else{//微信浏览器
                    $code_str =Pay::getJSAPI($payInfo);
//            $this->payment = new \web\all\Component\payment\weixin\weixin();
//            $code_str = $this->payment->getJSAPI($order1);
                }

//                $this->assign(array(
//                    'data' => $jsApiParameters,
//                ));
//                $this->display();
            }
        }
    }
}