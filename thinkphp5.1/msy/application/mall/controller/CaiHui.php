<?php
namespace app\mall\controller;

class CaiHui extends \think\Controller{
    //商城首页
    public function index(){
        return $this->fetch();
    }

    //产品展示
    public function goods(){
        return $this->fetch();
    }

    //关于我们
    public function about(){
        return $this->fetch();
    }

    //联系方式
    public function contact(){
        return $this->fetch();
    }

    //商品详情页
    public function detail(){
        $goods_id = input('goods_id');
        $this->assign('goods_id',$goods_id);
        return $this->fetch();
    }
    //商品详情页
    public function detail2(){
        return $this->fetch();
    }
    //商品详情页
    public function detail3(){
        return $this->fetch();
    }

    //商品详情页
    public function detail4(){
        return $this->fetch();
    }
    //商品详情页
    public function detail5(){
        return $this->fetch();
    }

    //商品详情页
    public function detail6(){
        return $this->fetch();
    }
    //商品详情页
    public function detail7(){
        return $this->fetch();
    }
    //商品详情页
    public function detail8(){
        return $this->fetch();
    }
    //结算
    public function pay(){
        return $this->fetch();
    }


    //订单-支付
    public function orderPayment(){
        print_r(input());exit;
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
}