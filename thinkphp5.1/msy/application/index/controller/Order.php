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
            return errorMsg('订单不存在或金额不能为0 !');
        };

        $modelOrder = new \app\index\model\Order();
        $walletDetail = new \app\index\model\WalletDetail();
        $modelOrder -> setConnection(config('custom.system_id')[3]['db']);


        if($systemId==1){
            $config = [
                'where' => [
                    ['status', '=', 0],
                    ['order_status', '=', 2],
                    ['pay_sn', '<>', ''],
                    ['pay_code', '=', 1],
                ],'field' => [
                    'id', 'pay_sn','sn', 'amount','actually_amount','pay_code',
                    'user_id',
                ],
            ];
            $data =  $modelOrder->getList($config);
        }else{
            $config = [
                'where' => [
                    ['status', '=', 0],
                    ['pay_sn', '<>', ''],
                    ['pay_code', '=', 1],
                ],'field' => [
                    'id', 'pay_sn','sn', 'amount','actually_amount','pay_code',
                    'user_id',
                ],
            ];
            $data =  $walletDetail->getList($config);

        }


        foreach($data as $k => $orderInfo){
            // 各方式退款
            switch($orderInfo['pay_code']){
                case 1 : // 微信支付
                    $wxPay = new \common\component\payment\weixin\weixinpay;
                    if(!$result = $wxPay->refundOrder($orderInfo)){
                        //return errorMsg($wxPay->msg);

                    }else{
                        //return successMsg($result);
                    }

                    break;
            }
        }


        return errorMsg('失败');
    }

    private function orderInfo($systemId,$orderSn){

        $modelOrder = new \app\index\model\Order();
        $modelOrder -> setConnection(config('custom.system_id')[$systemId]['db']);
        $config = [
            'where' => [
                ['o.status', '=', 0],
                ['o.sn', '=', $orderSn],
            ],'field' => [
                'o.id', 'o.pay_sn','o.sn', 'o.amount','o.actually_amount','pay_code',
                'o.user_id',
            ],
        ];
        return $modelOrder->getInfo($config);
    }


}