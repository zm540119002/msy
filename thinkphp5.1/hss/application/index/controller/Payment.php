<?php
namespace app\index\controller;
class Payment extends \common\controller\UserBase{



    // 订单-支付页
    public function orderPayment(){
        if( empty(input('order_sn'))){
            $this -> error('参数错误');
        }
        $modelOrder = new \app\index\model\Order();
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

        if($orderInfo['actually_amount']<=0){
            $this -> error('订单不存在或已支付');
        }


        return $this->fetch();
    }





}