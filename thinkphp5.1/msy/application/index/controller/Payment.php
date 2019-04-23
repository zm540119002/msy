<?php
namespace app\index\controller;
//class Payment extends \common\controller\Base{

use function GuzzleHttp\Promise\inspect;

class Payment extends \common\controller\Base {

    // 确定支付页

    public function toPay()
    {
        if(request()->isPost()){
            $modelOrder = new \app\index\model\Order();
            $systemId = input('post.system_id',0,'int');
            $modelOrder ->connection = config('custom.system_id')[$systemId]['db'];
            $orderSn = input('post.order_sn',null,'sting');
            $config = [
                'where' => [
                    ['o.status', '=', 0],
                    ['o.sn', '=', $orderSn],
                ],'field' => [
                    'o.id', 'o.sn', 'o.amount','o.actually_amount',
                    'o.user_id',
                ],
            ];
            $orderInfo = $modelOrder->getInfo($config);
            if(empty($orderInfo) OR !$orderInfo['actually_amount']){
                $this->error('订单不存在或金额不能为0 !');
            }
            switch($orderInfo['paymentCode']){
                case 1 : // 微信支付
                    $payInfo['notify_url'] = config('wx_config.notify_url');
                    $wxPay = new \common\component\payment\weixin\weixinpay;
                    $msg   = $wxPay->wxPay($payInfo);
                    break;
                case 2 : // 支付宝
                    $payInfo['notify_url'] = $this->host."/index.php/index/CallBack/aliBack/type/order";
                    $model = new \common\component\payment\alipay\alipay;
                    $model->aliPay($payInfo);
                    break;
                case 3 : // 银联
                    $payInfo['notify_url'] = $this->host."/index.php/index/CallBack/unionBack/type/order";
                    $model = new \common\component\payment\unionpay\unionpay;
                    $model->unionPay($payInfo);
                    break;
                case 4 : // 钱包
                    if ($orderInfo['order_status'] > 1) {
                        return errorMsg('订单已处理',['code'=>1]);
                    }
                    $modelWallet = new \app\index\model\Wallet();
                    $config = [
                        'where'=>[
                            ['status', '=', 0],
                            ['user_id', '=', $this->user['id']],
                        ]
                    ];
                    $walletInfo = $modelWallet->getInfo($config);
                    if($walletInfo['amount'] < $orderInfo['actually_amount']){
                        $modelOrder->rollback();
                        //返回状态给微信服务器
                        return errorMsg('余额不够',['code'=>2]);
                    }
                    $modelOrder ->startTrans();
                    $modelWalletDetail = new \app\index\model\WalletDetail();
                    $orderInfo['pay_sn'] = generateSN();
                    $orderInfo['payment_time'] = time();
                    $res = $modelWalletDetail->walletPaymentHandle($orderInfo);
                    if(!$res['status'] ){
                        $modelOrder->rollback();
                        //返回状态给微信服务器
                        return errorMsg('失败');
                    }
                    $data = [
                        'payment_code'=>4,
                        'pay_sn'=> $orderInfo['pay_sn'],
                        'payment_time'=> $orderInfo['payment_time'],
                        'order_sn'=> $orderInfo['sn'],
                    ];
                    $res = $modelOrder->orderHandle($data, $orderInfo);
                    if(!$res['status']){
                        $modelOrder->rollback();
                        //返回状态给微信服务器
                        return errorMsg('失败');
                    }
                    $modelOrder->commit();
                    return successMsg('成功');
                    break;
            }
            if(isset($msg)){
                $this -> error($msg);
            }
        }else{
//            $modelOrder = new \app\index\model\Order();
//            $systemId = input('system_id',0,'int');
//            $modelOrder ->connection = config('custom.system_id')[$systemId]['db'];
//            $orderSn = input('order_sn');
//            $config = [
//                'where' => [
//                    ['o.status', '=', 0],
//                    ['o.sn', '=', $orderSn],
//                ],'field' => [
//                    'o.id', 'o.sn', 'o.amount',
//                    'o.user_id',
//                ],
//            ];
//            $orderInfo = $modelOrder->getInfo($config);
//            if(empty($orderInfo) OR !$orderInfo['actually_amount']){
//                $this->error('订单不存在或金额不能为0 !');
//            }
//            $this->assign('orderInfo', $orderInfo);
            //判断为微信支付，并且为微信浏览器
            //if($orderInfo['paymentCode'] ==1 && isWxBrowser()){
            if(isWxBrowser()){
                $this->assign('isWxBrowser',1);
                //自定义参数，微信支付回调原样返回
                $systemId = 1;
                $attach = [
                    'system_id' =>$systemId,
                ];
                $attach = json_encode($attach);
                $jump_url =config('custom.system_id')[$systemId]['jump_url'];
                $return_url = config('wx_config.return_url');
                $tools = new \common\component\payment\weixin\Jssdk(config('wx_config.appid'), config('wx_config.appsecret'));
                $payOpenId  = $tools->getOpenid();
                $payInfo = [
                    'sn'=>generateSN(),
                    'product'=>5,
                    'actually_amount'=>0.01,
                    'success_url' => $return_url.'?pay_status=success&jump_url='.$jump_url,
                    'fail_url' => $return_url.'?pay_status=fail&jump_url='.$jump_url,
                    'notify_url'=>config('wx_config.notify_url'),
                    'attach'=>$attach,
                    'open_id'=>$payOpenId,
                ];
                $wxPay = new \common\component\payment\weixin\weixinpay;
                $jsApiParameters   = $wxPay->wxPay($payInfo);
                return $jsApiParameters;
                $this -> assign('jsApiParameters',$jsApiParameters);

            }
            return $this->fetch();
        }
    }


    public function orderPayment(){
//        if( empty(input('order_sn')) || empty(input('?pay_code'))){
//            $this -> error('参数错误');
//        }
        $orderSn = input('order_sn','','string');
        $systemId = input('system_id',0,'int');
        //自定义参数，微信支付回调原样返回
        $attach = [
            'system_id' =>$systemId,
        ];
        $modelOrder = new \app\index\model\Order();
        $modelOrder ->connection = config('custom.system_id')[$systemId]['db'];
        $config = [
            'where' => [
                ['o.status', '=', 0],
                ['o.sn', '=', $orderSn],
                //['o.user_id', '=', $this->user['id']],
            ],'field' => [
                'o.id', 'o.sn', 'o.amount','o.actually_amount',
                'o.user_id','o.type'
            ],
        ];
        $orderInfo = $modelOrder->getInfo($config);
        if($orderInfo['actually_amount']<=0){
            $this -> error('支付的金额不能为零');
        }

        $jump_url =config('custom.system_id')[$systemId]['jump_url'];
        $return_url = config('wx_config.return_url');
        $attach = json_encode($attach);
        $payInfo = [
            'sn'=>$orderInfo['sn'],
            'product'=>$orderInfo['id'],
            'actually_amount'=>$orderInfo['actually_amount'],
            'success_url' => $return_url.'?pay_status=success&jump_url='.$jump_url,
            'fail_url' => $return_url.'?pay_status=fail&jump_url='.$jump_url,
            'notify_url'=>config('wx_config.notify_url'),
            'attach'=>$attach
        ];
        $payCode = input('pay_code','0','int');

        switch($payCode){
            case 1 : // 微信支付
                $payInfo['notify_url'] = config('wx_config.notify_url');
                $wxPay = new \common\component\payment\weixin\weixinpay;
                $msg   = $wxPay->wxPay($payInfo);

                break;
            case 2 : // 支付宝
                $payInfo['notify_url'] = $this->host."/index.php/index/CallBack/aliBack/type/order";
                $model = new \common\component\payment\alipay\alipay;
                $model->aliPay($payInfo);
                break;
            case 3 : // 银联
                $payInfo['notify_url'] = $this->host."/index.php/index/CallBack/unionBack/type/order";
                $model = new \common\component\payment\unionpay\unionpay;
                $model->unionPay($payInfo);
                break;
            case 4 : // 钱包
                if ($orderInfo['order_status'] > 1) {
                    return errorMsg('订单已处理',['code'=>1]);
                }
                $modelWallet = new \app\index\model\Wallet();
                $config = [
                    'where'=>[
                        ['status', '=', 0],
                        ['user_id', '=', $this->user['id']],
                    ]
                ];
                $walletInfo = $modelWallet->getInfo($config);
                if($walletInfo['amount'] < $orderInfo['actually_amount']){
                    $modelOrder->rollback();
                    //返回状态给微信服务器
                    return errorMsg('余额不够',['code'=>2]);
                }
                $modelOrder ->startTrans();
                $modelWalletDetail = new \app\index\model\WalletDetail();
                $orderInfo['pay_sn'] = generateSN();
                $orderInfo['payment_time'] = time();
                $res = $modelWalletDetail->walletPaymentHandle($orderInfo);
                if(!$res['status'] ){
                    $modelOrder->rollback();
                    //返回状态给微信服务器
                    return errorMsg('失败');
                }
                $data = [
                    'payment_code'=>4,
                    'pay_sn'=> $orderInfo['pay_sn'],
                    'payment_time'=> $orderInfo['payment_time'],
                    'order_sn'=> $orderInfo['sn'],
                ];
                $res = $modelOrder->orderHandle($data, $orderInfo);
                if(!$res['status']){
                    $modelOrder->rollback();
                    //返回状态给微信服务器
                    return errorMsg('失败');
                }
                $modelOrder->commit();
                return successMsg('成功');
                break;
        }
        if(isset($msg)){
            $this -> error($msg);
        }
    }

    //充值-支付
    public function rechargePayment(){
        //微信支付
        if( empty(input('amount')) ||  empty(input('?pay_code'))){
            $this -> error('参数错误');
        }
        $model = new \app\index\model\WalletDetail();
        $amount = input('amount/f');
        //生成充值明细
        $WalletDetailSn = generateSN();
        $data = [
            'sn'=>$WalletDetailSn,
            'user_id'=>$this->user['id'],
            'amount'=>$amount,
            'create_time'=>time()
        ];
        $res = $model->isUpdate(false)->save($data);
        if(!$res){
            $this -> error('生成充值明细失败');
        }
        //支付信息
        $payInfo = [
            'sn'=>$WalletDetailSn,
            'actually_amount'=>$amount,
            'return_url' => $this->host.url('payComplete'),
            'cancel_url' => $this->host.url('payCancel'),
            'fail_url' => $this->host.url('payFail'),
            'notify_url'=>$this->host."/index/".config('wx_config.call_back_url'),
        ];
        $payCode = input('pay_code','0','int');
        //微信支付
        if($payCode == 1){
            $payInfo['notify_url'] = $this->host."/index.php/index/CallBack/weixinBack/type/recharge";
            \common\component\payment\weixin\weixinPay::wxPay($payInfo);
        }
        //支付宝支付
        if($payCode == 2){
            $payInfo['notify_url'] = $this->host."/index.php/index/CallBack/aliBack/type/recharge";
            $model = new \common\component\payment\alipay\alipay;
            $model->aliPay($payInfo);
        }
        //银联支付
        if($payCode == 3){
            $payInfo['notify_url'] = $this->host."/index.php/index/CallBack/unionBack/type/recharge";
            $model = new \common\component\payment\unionpay\unionpay;
            $model->unionPay($payInfo);
        }
    }

   //支付完跳转的页面
    public function payComplete(){
        return $this->fetch();
    }
    //取消支付完跳转的页面
    public function payCancel(){
        return $this->fetch();
    }
    //支付失败完跳转的页面
    public function payFail(){
        return $this->fetch();
    }


    // 微信支付回调处理
    /*
     * 回调处理，修改信息，通知，记录日志
     * */
    public function wxPayNotifyCallBack(){

        $wxPay = new \common\component\payment\weixin\weixinpay;
        $data  = $wxPay->wxNotify();

        if($data){
            $attach = json_decode($data['attach'],true);
            $order['system_id'] = $attach['system_id'];
            $order['sn'] = $data['out_trade_no'];
            $order['actually_amount'] = $data['total_fee']/100;
            $order['payment_code'] = 0;
            $order['pay_sn'] = $data['transaction_id'];

            $this->setOrderPayStatus($order);
        }
    }



    /**
     * 更新订单支付成功后的信息
     * @param $systemId int 平台代码
     * @param $orderSn string 订单号
     */
    private function setOrderPayStatus($info){
        $modelOrder = new \app\index\model\Order();
        $modelOrder ->connection = config('custom.system_id')[$info['system_id']]['db'];
        $condition = [
            'where' => [
                ['status', '=', 0],
                ['sn', '=', $info['sn']],
                ['order_status', '=', 1],
            ]
        ];

        $orderInfo = $modelOrder->getInfo($condition);
        if($orderInfo){

            // 金额判断
            if($orderInfo['amount']!=$info['actually_amount']){
                $msg = '回调的金额和订单的金额不符';

            }else{
                $data = [
                    'order_status'=>2,                              // 订单状态
                    'actually_amount'=>$info['actually_amount'],    // 实际支付金额
                    'payment_time'=>time(),
                    'payment_code'=>$info['payment_code'],          // 支付方式
                    'pay_sn'=>$info['pay_sn'],                      // 支付单号 退款用
                ];
                $result = $modelOrder -> allowField(true) -> save($data,$condition);
                if(!$result){
                    $msg = '订单支付更新失败';

                }else{
                    echo 'SUCCESS';
                }
            }

            // 记录日志
            if(isset($msg)){
                \think\facade\Log::init(['path' => './logs/pay/']);
                \think\facade\Log::error($msg,$info);
                \think\facade\Log::save();
            }
        }

    }


}