<?php
namespace app\index\controller;

class Payment extends \common\controller\Base {
    // 确定支付页
    public function toPay()
    {
        if(request()->isPost()){
            $postData = input('post.');
            $systemId = $postData['system_id'];
            $paymentType = $postData['payment_type'];
            $sn = $postData['sn'];
            if(!in_array($paymentType,config('custom.payment_types'))){
                $this->error('提交的支付类型数据有误 !');
            }
            switch($paymentType){
                case 1 : // 订单
                    $info = $this->getOrderInfo($systemId,$sn);
                    break;
                case 2 : // 充值
                    $info = $this->getWalletDetailInfo($systemId,$sn);
                    break;
            }
            if(empty($info) OR !$info['actually_amount']){
                $this->error('订单不存在或金额不能为0 !');
            }
            $attach = [
                'system_id' =>$systemId,
                'payment_type'=>$paymentType
            ];
            $attach = json_encode($attach);
            $jump_url =config('custom.system_id')[$systemId]['jump_url'];
            $return_url = config('wx_config.return_url');
            $payInfo = [
                'sn'=>$info['sn'],
                'product'=>$info['id'],
                'actually_amount'=>$info['actually_amount'],
                'success_url' =>urlencode($return_url.'?pay_status=success&jump_url='.$jump_url),
                'fail_url' => $return_url.'?pay_status=fail&jump_url='.$jump_url,
                'notify_url'=>config('wx_config.notify_url'),
                'attach'=>$attach,
            ];

            switch($info['payment_code']){
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
//                    if ($orderInfo['order_status'] > 1) {
//                        return errorMsg('订单已处理',['code'=>1]);
//                    }
//                    $modelWallet = new \app\index\model\Wallet();
//                    $config = [
//                        'where'=>[
//                            ['status', '=', 0],
//                            ['user_id', '=', $orderInfo['user_id']],
//                        ]
//                    ];
//                    $walletInfo = $modelWallet->getInfo($config);
//                    if($walletInfo['amount'] < $orderInfo['actually_amount']){
//                        $modelOrder->rollback();
//                        //返回状态给微信服务器
//                        return errorMsg('余额不够',['code'=>2]);
//                    }
//                    $modelOrder ->startTrans();
//                    $modelWalletDetail = new \app\index\model\WalletDetail();
//                    $orderInfo['pay_sn'] = generateSN();
//                    $orderInfo['payment_time'] = time();
//                    $res = $modelWalletDetail->walletPaymentHandle($orderInfo);
//                    if(!$res['status'] ){
//                        $modelOrder->rollback();
//                        //返回状态给微信服务器
//                        return errorMsg('失败');
//                    }
//                    $data = [
//                        'payment_code'=>4,
//                        'pay_sn'=> $orderInfo['pay_sn'],
//                        'payment_time'=> $orderInfo['payment_time'],
//                        'order_sn'=> $orderInfo['sn'],
//                    ];
//                    $res = $modelOrder->orderHandle($data, $orderInfo);
//                    if(!$res['status']){
//                        $modelOrder->rollback();
//                        //返回状态给微信服务器
//                        return errorMsg('失败');
//                    }
//                    $modelOrder->commit();
//                    return successMsg('成功');
                    break;
            }
            if(isset($msg)){
                $this -> error($msg);
            }
        }else{
            $systemId = input('system_id',0,'int');
            $this->assign('system_id',$systemId);
            //1:订单支付 2：充值支付
            $paymentType = input('payment_type',0,'int');
            $this->assign('payment_type',$paymentType);
            if(!in_array($paymentType,config('custom.payment_types'))){
                $this->error('提交的支付类型数据有误 !');
            }
            $sn = input('sn','','string');
            switch($paymentType){
                case 1 : // 订单
                    $info = $this->getOrderInfo($systemId,$sn);
                    break;
                case 2 : // 充值
                    $info = $this->getWalletDetailInfo($systemId,$sn);
                    break;
            }
            if(empty($info) OR !$info['actually_amount']){
                $this->error('订单不存在或金额不能为0 !');
            }

            $this->assign('info', $info);
            //判断为微信支付，并且为微信浏览器
            if($info['payment_code'] ==1){
                if (!isPhoneSide()) {//pc端微信扫码支付
                    $this ->assign('browser_type',1);
                }elseif(strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') == false ){
                    return $this->fetch();
//                    //手机端非微信浏览器
//                    $this ->assign('browser_type',2);
                }else{//微信浏览器(手机端)
                    $this ->assign('browser_type',3);
                    $payOpenId = session('open_id');
                    if(!$payOpenId){
                        $tools = new \common\component\payment\weixin\Jssdk(config('wx_config.appid'), config('wx_config.appsecret'));
                        $payOpenId  = $tools->getOpenid();
                        session('open_id',$payOpenId);
                    }
                }
                $this->assign('isWxBrowser',1);
                //自定义参数，微信支付回调原样返回
                $attach = [
                    'system_id' =>$systemId,
                    'payment_type'=>$paymentType
                ];
                $attach = json_encode($attach);
                $jump_url =config('custom.system_id')[$systemId]['jump_url'];
                $return_url = config('wx_config.return_url');
                $payInfo = [
                    'sn'=>$info['sn'],
                    'product'=>$info['id'],
                    'actually_amount'=>$info['actually_amount'],
                    'success_url' => $return_url.'?pay_status=success&jump_url='.$jump_url,
                    'fail_url' => $return_url.'?pay_status=fail&jump_url='.$jump_url,
                    'notify_url'=>config('wx_config.notify_url'),
                    'attach'=>$attach,
                    'payOpenId'=>$payOpenId,
                ];$payInfo2 = [
                    'sn'=>$info['sn'],
                    'product'=>$info['id'],
                    'actually_amount'=>$info['actually_amount'],
                    'success_url' => $return_url.'?pay_status=success&jump_url='.$jump_url,
                    'fail_url' => $return_url.'?pay_status=fail&jump_url='.$jump_url,
                    'notify_url'=>config('wx_config.notify_url'),
                    'attach'=>$attach,
                ];

                $wxPay = new \common\component\payment\weixin\weixinpay;
                $jsApiParameters   = $wxPay::wxPay($payInfo);
                $this -> assign('jsApiParameters',$jsApiParameters);
                $unlockingFooterCart = unlockingFooterCartConfigTest([0,2,1]);
                array_push($unlockingFooterCart['menu'][0]['class'],'group_btn30');
                array_push($unlockingFooterCart['menu'][1]['class'],'group_btn30');
                array_push($unlockingFooterCart['menu'][2]['class'],'group_btn30');
                $this->assign('unlockingFooterCart',json_encode($unlockingFooterCart));
                $aa = array('info'=>array($payInfo2));
                print_r($aa);
                print_r($unlockingFooterCart);
                $this->assign('payInfo',json_encode($aa));
//                $this->assign('success_url',$payInfo['success_url']);
//                $this->assign('fail_url',$payInfo['fail_url']);
                //$this->assign('payInfo',json_encode($payInfo));
            }
            return $this->fetch();
        }
    }

    /**
     * @param $systemId
     * @return array|\PDOStatement|string|\think\Model|null
     * 获取订单信息
     */
    private function getOrderInfo($systemId,$sn){
        $model = new \app\index\model\Order();
        $model ->connection = config('custom.system_id')[$systemId]['db'];
        $config = [
            'where' => [
                ['status', '=', 0],
                ['sn', '=', $sn],
            ],'field' => [
                'id', 'sn', 'amount','actually_amount','payment_code',
                'user_id',
            ],
        ];
        return  $model->getInfo($config);
    }

    /**
     * @param $systemId
     * @return array|\PDOStatement|string|\think\Model|null
     * 获取订单信息
     */
    private function getWalletDetailInfo($systemId,$sn){
        $model = new \app\index\model\WalletDetail();
        $model ->connection = config('custom.system_id')[$systemId]['db'];
        $config = [
            'where' => [
                ['status', '=', 0],
                ['sn', '=', $sn],
            ],'field' => [
                'id', 'sn', 'amount','payment_code','type','actually_amount',
                'user_id',
            ],
        ];
        return  $model->getInfo($config);
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