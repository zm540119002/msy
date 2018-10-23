<?php
namespace app\purchase\controller;
require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'./../../../../common/component/payment/alipay/lib/alipay_notify.class.php';
class Payment extends \common\controller\UserBase{
    //订单-支付
    public function orderPayment(){

        if( !empty(input('sn')) && !empty(input('?pay_code'))){
            $modelOrder = new \app\purchase\model\Order();
            $orderSn = input('sn','','string');
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
                'return_url' => "http://".$_SERVER['HTTP_HOST'].url('payComplete'),
                'notify_url'=>"http://".$_SERVER['HTTP_HOST']."/purchase/".config('wx_config.call_back_url')

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

   //支付完的页面
    public function payComplete(){

        require_once("lib/alipay_notify.class.php");  // 请求返回
        //计算得出通知验证结果
        $alipayNotify = new \common\component\payment\alipay\lib\AlipayNotify($this->alipay_config);
        $verify_result = $alipayNotify->verifyReturn();

        if($verify_result) //验证成功
        {
            $order_sn = $out_trade_no = $_GET['out_trade_no']; //商户订单号
            $trade_no = $_GET['trade_no']; //支付宝交易号
            $trade_status = $_GET['trade_status']; //交易状态

            if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS')
            {
                return  array('status'=>1,'order_sn'=>$order_sn);//跳转至成功页面
            }
            else {
                return  array('status'=>0,'order_sn'=>$order_sn); //跳转至失败页面
            }
        }
        else
        {
            return  array('status'=>0,'order_sn'=>$_GET['out_trade_no']);//跳转至失败页面
        }
        return $this->fetch();
    }

}