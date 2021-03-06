<?php
namespace app\purchase\controller;

class Payment extends \common\controller\UserBase{
    //订单-支付
    public function orderPayment(){
        if( !empty(input('order_sn')) && !empty(input('?pay_code'))){
            $modelOrder = new \app\purchase\model\Order();
            $orderSn = input('order_sn','','string');
            $config = [
                'where' => [
                    ['o.status', '=', 0],
                    ['o.sn', '=', $orderSn],
                    ['o.user_id', '=', $this->user['id']],
                ],'field' => [
                    'o.id', 'o.sn', 'o.amount','o.actually_amount',
                    'o.user_id','o.type'
                ],
            ];
            $orderInfo = $modelOrder->getInfo($config);
            $payInfo = [
                'sn'=>$orderInfo['sn'],
                'actually_amount'=>$orderInfo['actually_amount'],
                'return_url' => $this->host.url('payComplete'),
                'notify_url'=>$this->host."/purchase/".config('wx_config.call_back_url')
            ];
            $payCode = input('pay_code','0','int');
            //微信支付
            if($payCode == 1){
                $payInfo['notify_url'] = $payInfo['notify_url'].'/weixin.order';
                \common\component\payment\weixin\weixinpay::wxPay($payInfo);
            }
            //支付宝支付
            if($payCode == 2){
                $payInfo['notify_url'] = $payInfo['notify_url'].'/ali.order';
                $model = new \common\component\payment\alipay\alipay;
                $model->aliPay($payInfo);
            }
            //银联支付
            if($payCode == 3){
                $payInfo['notify_url'] = $payInfo['notify_url'].'/union.order';
                $model = new \common\component\payment\unionpay\unionpay;
                $model->unionPay($payInfo);
            }
        }
    }

   //支付完跳转的页面
    public function payComplete(){
        require_once dirname(__DIR__).'./../../../common/component/payment/alipay/wappay/service/AlipayTradeService.php';
        require_once dirname(__DIR__).'./../../../common/component/payment/alipay/config.php';
        $arr = $_GET;
        $alipaySevice = new \AlipayTradeService($config);
        $result = $alipaySevice->check($arr);
        print_r($result);exit;

        return $this->fetch();
    }

}