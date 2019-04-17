<?php
namespace app\index\controller;

// 订单统一处理类
//class Order extends \common\controller\Base
class Order
{




    /**
     * system_id,order_sn
     */
    public function refundOrder(){

        $systemId= input('system_id/d');
        $orderSn = input('order_sn');

        if(!$orderInfo = $this->orderInfo($systemId,$orderSn)){
            return json_encode(errorMsg('订单不存在或金额不能为0 !'));
        };
        // 各方式退款
        switch($orderInfo['payment_code']){
            case 1 : // 微信支付
                return $this->wxRefundOrder($orderInfo);
                break;
        }
        return false;
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

    // 申请退款
    /**
     * @param $data array 订单信息
     * @return bool
     */
    private function wxRefundOrder($data){

        try {
            $pay_open_id = $this->getWxOpenid();

            $input = new \WxPayRefund();
            $input->SetTransaction_id($data['pay_sn']);
            $input->SetOut_refund_no($data['sn']);
            $input->SetTotal_fee($data['actually_amount'] * 100);
            $input->SetRefund_fee($data['actually_amount'] * 100);
            $input->SetOp_user_id($pay_open_id);
            list($res,$list) =  \WxPayApi::refund( $input);

            \think\facade\Log::init(['path' => './logs/pay/']);
            \think\facade\Log::error(array('微信申请退款成功: ',json_encode($res),$list));
            \think\facade\Log::save();

        } catch (\WxPayException $e){

            //$msg = $e->errorMessage();
            // 记录日志
            //\think\facade\Log::init(['path' => '../logs/wx/']);
            \think\facade\Log::init(['path' => './logs/pay/']);
            \think\facade\Log::error(array('微信申请退款失败: '.$e->errorMessage(),json_encode($data)));
            \think\facade\Log::save();

            return false;
        }
        return true;

    }


    private function getWxOpenid(){
        $payOpenId =  session('pay_open_id');
        if(empty(session('pay_open_id'))){
            $tools = new \common\component\payment\weixin\Jssdk(config('wx_config.appid'), config('wx_config.appsecret'));
            $payOpenId  = $tools->getOpenid();
            session('pay_open_id',$payOpenId);
        }
        return $payOpenId;
    }


}