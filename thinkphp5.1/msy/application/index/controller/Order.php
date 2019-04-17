<?php
namespace app\index\controller;

// 订单统一处理类
class Order extends \common\controller\Base
{

    /**
     * system_id,order_sn
     */
    public function refundOrder(){

        $systemId= input('system_id/d');
        $orderSn = input('order_sn');

        if(!$orderInfo = $this->orderInfo($systemId,$orderSn)){
            return errorJson('订单不存在或金额不能为0 !');
        };

        // 各方式退款
        switch($orderInfo['payment_code']){
            case 1 : // 微信支付
                $wxPay = new \common\component\payment\weixin\weixinpay;
                if(!$result = $wxPay->refundOrder($orderInfo)){
                    return errorJson($wxPay->msg);

                }else{
                    return successJson($result);
                }

                break;
        }
        return errorJson('失败');
    }

    private function orderInfo($systemId,$orderSn){

        $modelOrder = new \app\index\model\Order();
        $modelOrder ->connection = config('custom.system_id')[$systemId]['db'];
        $config = [
            'where' => [
                ['o.status', '=', 0],
                ['o.sn', '=', $orderSn],
            ],'field' => [
                'o.id', 'o.pay_sn','o.sn', 'o.amount','o.actually_amount','payment_code',
                'o.user_id',
            ],
        ];
        return $modelOrder->getInfo($config);
    }


}