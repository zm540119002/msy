<?php
namespace app\index\controller;
//class Payment extends \common\controller\Base{

use function GuzzleHttp\Promise\inspect;

class Payment extends \common\controller\Base {

    // 确定支付页
    public function toPay()
    {
        $modelOrder = new \app\index\model\Order();
        $systemId = input('system_id',0,'int');
        $this->assign('system_id', $systemId);
        $modelOrder ->connection = config('custom.system_id')[$systemId]['db'];
        $orderSn = input('order_sn');
        $config = [
            'where' => [
                ['o.status', '=', 0],
                ['o.sn', '=', $orderSn],
//                ['o.user_id', '=', $this->user['id']],
            ],'field' => [
                'o.id', 'o.sn', 'o.amount',
                'o.user_id',
            ],
        ];
        $orderInfo = $modelOrder->getInfo($config);

        if(empty($orderInfo) OR !$orderInfo['amount']){
            $this->error('订单不存在或金额不能为0 !');
        }

        $this->assign('orderInfo', $orderInfo);

        if(isWxBrowser() && !request()->isAjax()) {//判断是否为微信浏览器
            $payOpenId =  session('pay_open_id');
            if(empty($payOpenId)){
                $tools = new \common\component\payment\weixin\Jssdk(config('wx_config.appid'), config('wx_config.appsecret'));
                $payOpenId  = $tools->getOpenid();
                session('pay_open_id',$payOpenId);
            }
        }

        //钱包
        $modelWallet = new \app\index\model\Wallet();
        $modelWallet ->connection = config('custom.system_id')[$systemId]['db'];
        $config = [
            'where' => [
                ['status', '=', 0],
                ['user_id', '=', $orderInfo['user_id']],
            ],'field' => [
                'id','amount',
            ],
        ];
        $walletInfo = $modelWallet->getInfo($config);
        $this->assign('walletInfo', $walletInfo);
        return $this->fetch();
    }

    // 支付处理
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

    //public function wxPayNotifyCallBack(){
    public function notifyUrl(){

        $xml = file_get_contents('php://input');
        //$xml = 222222;
        file_put_contents('./xml1.json',$xml);

        $data = xmlToArray($xml);
        //$data = 4444;
        file_put_contents('./array1.json',$data);
        echo 2222;
        exit;
    }

}