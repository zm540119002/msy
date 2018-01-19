<?php
namespace Home\Controller;
use Think\Controller;
use  web\all\Lib\AuthUser;
use  web\all\Lib\Pay;


class PaymentController extends Controller {

    /**
     * 微信支付
     * 微信访问:
     */
    public function pay(){
        //采购平台调用
        if(isset($_GET['payInfo']) && !empty($_GET['payInfo'])){
            $payInfo=$_GET['payInfo'];
            $payInfo = explode(',',$payInfo);
            $type = $payInfo[1];
            $this->type = $type;
            $wxShareUrl = session('baseShareUrl').'/groupBuySn/'.session('groupBuySn').'/shareType/'.'1'.'.html';
            $this->assign('wxShareUrl' ,$wxShareUrl);
            if(!empty($payInfo[0])){
                $orderSn   = $payInfo[0];
                $user      = AuthUser::check();
                $model     = D('Payment');
                $orderInfo = $model -> getOrderInfoByOrderNo($user['id'],$orderSn,$type);
                $oGoods    = $model -> getOrderGoodsByOrderSn($orderSn,$type);
                //检查订单状态
                $result = $model -> checkOrderStatus($orderInfo,$type);

                if($result['status'] == 0){
                    $this ->error($result['message']);
                }
                //检查商品库存
                $result = $model -> checkOrderGoodsStock($oGoods,$type);
                if($result['status'] == 0){
                    $this ->error($result['message']);
                }
                $this -> assign('orderInfo',$orderInfo);
                $orderSn = $orderInfo['order_sn'];
                $totalFee = $orderInfo['actually_amount'];
                if($type == 'purchase'){
                    $notifyUrl = C('PURCHASE_NOTIFY_URL');
                }
                if($type == 'myms'){
                    $notifyUrl = C('MYMS_NOTIFY_URL');
                }
                 $jsApiParameters =Pay::wxPay($orderSn,$notifyUrl,$totalFee,'');
                $this->assign(array(
                    'data' => $jsApiParameters,
                ));
            }
        }
        $this->display();
    }




}