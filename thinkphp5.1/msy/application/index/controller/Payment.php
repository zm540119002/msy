<?php
namespace app\index\controller;

class Payment extends \common\controller\Base {

    /**
     * @return mixed // 确定支付页
     */
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
            }
            if(isset($msg)){
                $this -> error($msg);
            }
        }else{

            $systemId = input('system_id',0,'int');
            $this->assign('system_id',$systemId);
            //$paymentType 1:订单支付 2：充值支付
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
                    //手机端非微信浏览器
                    return $this->fetch();
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
                $jump_url =config('custom.system_id')[$systemId]['jump_url'][$paymentType];
                $return_url = config('wx_config.return_url');
                $payInfo = [
                    'sn'=>$info['sn'],
                    'product'=>$info['id'],
                    'actually_amount'=>$info['actually_amount'],
                    'success_url' => $return_url.'?pay_status=success&jump_url='.$jump_url,
                    'fail_url' => $return_url.'?pay_status=fail&jump_url='.$jump_url,
                    'notify_url'=>config('wx_config.notify_url'),
                    'attach'=>$attach,
                    'open_id'=>$payOpenId,
                ];
                $wxPay = new \common\component\payment\weixin\weixinpay;
                $jsApiParameters   = $wxPay::wxPay($payInfo);
                $this -> assign('jsApiParameters',$jsApiParameters);
                $response = [
                    'success_url' => $jump_url,
                    'fail_url' => $return_url.'?pay_status=fail&jump_url='.$jump_url,
                ];
/*                $response = [
                    'success_url' => $jump_url,
                    'fail_url' => $return_url.'?pay_status=fail&jump_url='.$jump_url,
                ];*/
                $this->assign('payInfo',json_encode($response));
            }
            return $this->fetch();
        }
    }

    /**
     * @return mixed // 确定支付页
     */
    public function pay()
    {
        if(request()->isPost()){
            $postData = input('post.');
            $systemId = $postData['system_id'];
            $sn = $postData['sn'];
            $info = $this->getPayInfo($systemId,$sn);
            if(empty($info) OR !$info['actually_amount']){
                $this->error('订单不存在或金额不能为0 !');
            }
            $attach = [
                'system_id' =>$systemId,
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
            }
            if(isset($msg)){
                $this -> error($msg);
            }
        }else{
            $systemId = input('system_id',0,'int');
            $this->assign('system_id',$systemId);
            $sn = input('sn','','string');
            $info = $this->getPayInfo($systemId,$sn);
            if(empty($info) OR !$info['actually_amount']){
                $this->error('订单不存在或金额不能为0 !');
            }

            $this->assign('info', $info);
            //判断为微信支付，并且为微信浏览器
            if($info['payment_code'] ==1){
                if (!isPhoneSide()) {//pc端微信扫码支付
                    $this ->assign('browser_type',1);
                }elseif(strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') == false ){
                    //手机端非微信浏览器
                    return $this->fetch();
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
                    'open_id'=>$payOpenId,
                ];
                $wxPay = new \common\component\payment\weixin\weixinpay;
                $jsApiParameters   = $wxPay::wxPay($payInfo);
                $this -> assign('jsApiParameters',$jsApiParameters);
                $response = [
                    'success_url' => $jump_url,
                    'fail_url' => $return_url.'?pay_status=fail&jump_url='.$jump_url,
                ];
                $this->assign('payInfo',json_encode($response));
            }
            return $this->fetch();
        }
    }

    /**
     * @param $systemId
     * @return array|\PDOStatement|string|\think\Model|null
     * 获取支付订单信息
     */
    public function getPayInfo($systemId,$sn){
        $model = new \app\index\model\Pay();
        $model ->setConnection(config('custom.system_id')[$systemId]['db']);
        $config = [
            'where' => [
                ['status', '=', 0],
                ['sn', '=', $sn],
            ],'field' => [
                'id','user_id', 'sn', 'actually_amount','payment_code','pay_status','type'
            ],
        ];
        return  $model->getInfo($config);
    }
    /**
     * @param $systemId
     * @return array|\PDOStatement|string|\think\Model|null
     * 获取订单信息
     */
    public function getOrderInfo($systemId,$sn){
        $model = new \app\index\model\Order();
        $model ->setConnection(config('custom.system_id')[$systemId]['db']);
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
    /**获取订单信息
     * @param $systemId 平台系统id 根据此参数连接数据库
     * @param $sn 订单sn
     * @return array|\PDOStatement|string|\think\Model|null
     */
    public function getWalletDetailInfo($systemId,$sn){
        $model = new \app\index\model\WalletDetail();
        $model ->setConnection(config('custom.system_id')[$systemId]['db']);
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
        $jump_url = input('jump_url');
        $this->assign('jump_url',$jump_url);
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
     * wxPayNotifyCallBack
     * */
//    public function wxPayNotifyCallBack(){
//        $wxPay = new \common\component\payment\weixin\weixinpay;
//        $data  = $wxPay->wxNotify();
//        if($data){
//            $attach = json_decode($data['attach'],true);
//            $order['system_id'] = $attach['system_id'];
//            $order['payment_type'] = $attach['payment_type'];
//            $order['sn'] = $data['out_trade_no'];
//            $order['actually_amount'] = $data['total_fee']/100;
//            $order['payment_code'] = 0;
//            $order['pay_sn'] = $data['transaction_id'];
//
//            if($attach['payment_type'] == 1){
//                $this->setOrderPayStatus($order);
//            }elseif($attach['payment_type'] == 2){
//                $this->setRechargePayStatus($order);
//            }
//        }
//    }
    // 微信支付回调处理
    /*
     * 回调处理，修改信息，通知，记录日志
     * wxPayNotifyCallBack
     * */
    public function wxPayNotifyCallBack(){
//        $wxPay = new \common\component\payment\weixin\weixinpay;
//        $data  = $wxPay->wxNotify();
        /**
         * <xml>
        <appid><![CDATA[wx2421b1c4370ec43b]]></appid>
        <attach><![CDATA[支付测试]]></attach>
        <bank_type><![CDATA[CFT]]></bank_type>
        <fee_type><![CDATA[CNY]]></fee_type>
        <is_subscribe><![CDATA[Y]]></is_subscribe>
        <mch_id><![CDATA[10000100]]></mch_id>
        <nonce_str><![CDATA[5d2b6c2a8db53831f7eda20af46e531c]]></nonce_str>
        <openid><![CDATA[oUpF8uMEb4qRXf22hE3X68TekukE]]></openid>
        <out_trade_no><![CDATA[1409811653]]></out_trade_no>
        <result_code><![CDATA[SUCCESS]]></result_code>
        <return_code><![CDATA[SUCCESS]]></return_code>
        <sign><![CDATA[B552ED6B279343CB493C5DD0D78AB241]]></sign>
        <sub_mch_id><![CDATA[10000100]]></sub_mch_id>
        <time_end><![CDATA[20140903131540]]></time_end>
        <total_fee>1</total_fee>
        <coupon_fee_0><![CDATA[10]]></coupon_fee_0>
        <coupon_count><![CDATA[1]]></coupon_count>
        <coupon_type><![CDATA[CASH]]></coupon_type>
        <coupon_id><![CDATA[10000]]></coupon_id>
        <trade_type><![CDATA[JSAPI]]></trade_type>
        <transaction_id><![CDATA[1004400740201409030005092168]]></transaction_id>
        </xml>
         */
        $data = [
            'attach'=>"{'system_id':3 }",
            'out_trade_no'=>"20190508133552360792209865014610",
            'total_fee'=>1,
            'transaction_id'=>'1004400740201409030005092168',
        ];
        if($data){
            $attach = json_decode($data['attach'],true);
            $systemId = $attach['system_id'];
            $systemId = 3;
            $payInfo = $this->getPayInfo($systemId,$data['out_trade_no']);
            if(empty($payInfo)){
                return $this->writeLog("数据库没有此订单",$payInfo);
            }
            //此订单回调已处理过
            if($payInfo['pay_status']>=2){
                echo 'SUCCESS';
                die;
            }
            if($data['total_fee']/100!=$payInfo['actually_amount']){
                return $this->writeLog("订单支付回调的金额和订单的金额不符",$payInfo);
            }
            $payModel = new \app\index\model\Pay();
            $payModel ->setConnection(config('custom.system_id')[$systemId]['db']);
            $data1 = [
                'pay_status'=>2,                              // 订单状态
                'payment_time'=>time(),
                'pay_sn'=>$data['transaction_id'],                      // 支付单号 退款用
            ];
            $condition = [
                'where' => [
                    ['status', '=', 0],
                    ['sn', '=', $data['out_trade_no']],
                    ['pay_status', '=', 1],
                ],
            ];

            $payModel ->startTrans();
            $result = $payModel->isUpdate(true)->save($data1,$condition);
            if($result === false){
                $payModel ->rollback();
                $info['mysql_error'] = $payModel->getError();
                return $this->writeLog("支付订单更新失败",$info);
            }
            if($payInfo['type'] == 1){
                $this->setOrderPayStatusNew($payInfo,$systemId);
            }elseif($payInfo['type'] == 2){
                $this->setRechargePayStatusNew($payInfo,$systemId);
            }elseif($payInfo['type'] == 3){
                $this->setFranchisePayStatus($payInfo,$systemId);
            }
        }
    }
    /**
     * 订单支付单回调处理
     * @param $info 回调信息
     */
    public function setOrderPayStatus($info){
        $modelOrder = new \app\index\model\Order();
        $modelOrder ->setConnection(config('custom.system_id')[$info['system_id']]['db']);
        $condition = [
            'where' => [
                ['status', '=', 0],
                ['sn', '=', $info['sn']],
                ['order_status', '=', 1],
            ],'field' => [
                'id', 'sn', 'amount','payment_code','actually_amount',
                'user_id','order_status'
            ],
        ];
        $orderInfo = $modelOrder->getInfo($condition);
        if(empty($orderInfo)){
            return $this->writeLog("数据库没有此订单",$info);
        }

        //此订单回调已处理过
        if($orderInfo['order_status']>=2){
            echo 'SUCCESS';
            die;
        }
        if($orderInfo['amount']!=$info['actually_amount']){
            return $this->writeLog("订单支付回调的金额和订单的金额不符",$info);
        }
        $data = [
            'order_status'=>2,                              // 订单状态
            'payment_time'=>time(),
            'pay_sn'=>$info['pay_sn'],                      // 支付单号 退款用
        ];
        $condition = [
            'where' => [
                ['status', '=', 0],
                ['sn', '=', $info['sn']],
                ['order_status', '=', 1],
            ],
        ];
        $result = $modelOrder -> allowField(true) -> save($data,$condition);
        if(!$result){
            $modelOrder->rollback();
            $info['mysql_error'] = $modelOrder->getError();
            return $this->writeLog("订单支付更新失败",$info);
        }

        echo 'SUCCESS';
    }

    /**
     * 充值支付单回调处理
     * @param $info 回调信息
     */
    public function setRechargePayStatus($info){
        $modelWalletDetail= new \app\index\model\WalletDetail();
        $modelWalletDetail ->setConnection(config('custom.system_id')[$info['system_id']]['db']);
        $modelWallet = new \app\index\model\Wallet();
        $modelWallet ->setConnection(config('custom.system_id')[$info['system_id']]['db']);
        $condition = [
            'where' => [
                ['status', '=', 0],
                ['sn', '=', $info['sn']],
                //['recharge_status', '=', 1],
                ['recharge_status', '=', 0],
            ],'field' => [
                'id', 'sn', 'amount','payment_code','type','actually_amount','recharge_status',
                'user_id',
            ],
        ];
        $walletDetailInfo = $modelWalletDetail->getInfo($condition);
        if(empty($walletDetailInfo)){
            return $this->writeLog("数据库没有此订单",$info);
        }
        //此订单回调已处理过
        if($walletDetailInfo['recharge_status']>=2){
            echo 'SUCCESS';
            die;
        }
        if($walletDetailInfo['amount']!=$info['actually_amount']){
            return $this->writeLog("订单支付回调的金额和订单的金额不符",$info);
        }
        $modelWalletDetail ->startTrans();
        $data = [
            'recharge_status'=>2,                           // 订单状态
            'payment_time'=>time(),
            'pay_sn'=>$info['pay_sn'],                      // 支付单号 退款用
        ];
        $condition = [
            'where' => [
                ['status', '=', 0],
                ['sn', '=', $info['sn']],
                ['recharge_status', '=', 0],
                //['recharge_status', '=', 1],
            ]
        ];
        $result = $modelWalletDetail -> allowField(true) -> save($data,$condition);
        if(!$result){
            $modelWalletDetail ->rollback();
            $info['mysql_error'] = $modelWalletDetail->getError();
            return $this->writeLog('充值订单支付更新失败',$info);
        }
        //充值到钱包表
        $where = [
            ['user_id', '=', $walletDetailInfo['user_id']],
        ];
        $res = $modelWallet->where($where)->setInc('amount', $walletDetailInfo['amount']);
        if($res === false){
            $modelWallet->rollback();
            $info['mysql_error'] = $modelWallet->getError();
            //返回状态给微信服务器
            return $this->writeLog('充值订单支付更新失败',$info);
        }
        $modelWalletDetail->commit();//提交事务

        echo 'SUCCESS';

    }

    /**
     * @param $info 回调信息
     * @param $systemId 平台id
     */
    public function setFranchisePayStatus($info,$systemId){
        $modelFranchise = new \app\index\model\Franchise();
        $modelFranchise ->setConnection(config('custom.system_id')[$systemId]['db']);
        $data = [
            'apply_status'=>2,                              // 状态
        ];
        $condition = [
            'where' => [
                ['status', '=', 0],
                ['sn', '=', $info['sn']],
                ['user_id', '=', $info['user_id']],
                ['apply_status', '=', 1],
            ],
        ];
        $result = $modelFranchise -> allowField(true) -> save($data,$condition);
        if(!$result){
            $modelFranchise ->rollback();
            $info['mysql_error'] = $modelFranchise->getError();
            return $this->writeLog("订单支付更新失败",$info);
        }
        $modelFranchise ->commit();
        echo 'SUCCESS';
    }
    /**
     * 支付订单 错误日志记录
     * @param $msg 错误信息
     * @param $info 订单信息
     */
    private function writeLog($msg='',$info=[]){
        \think\facade\Log::init(['path' => './logs/pay/']);
        \think\facade\Log::error($msg,json_encode($info));
        \think\facade\Log::save();
    }
}