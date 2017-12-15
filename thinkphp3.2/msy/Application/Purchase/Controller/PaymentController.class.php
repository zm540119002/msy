<?php
namespace Purchase\Controller;
use Think\Controller;
use web\all\Controller\AuthCompanyAuthoriseController;

class PaymentController extends AuthCompanyAuthoriseController {

    public $payment; //  具体的支付类
    public $pay_code; //  具体的支付code

    /**
     * 析构流函数
     */
    public function  __construct() {
        parent::__construct();
        // 订单支付提交
        $this->pay_code=$_POST['pay_code'];
        if(!empty($this->pay_code))
        {
            $this->pay_code = $_POST['pay_code']; // 支付 code
            $this->pay_code= get_url_param('pay_code');
            $a='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING'];
            $data = array(
                'code'=> $this->pay_code,
                'config'=>$a,
                'name'=>'支付'
            );
            D('Plugin')->add($data);
        }
        else // 第三方 支付商返回
        {
            $this->pay_code= get_url_param('pay_code');
            $a='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING'];
            $data = array(
                'code'=> $this->pay_code,
                'config'=>$a,
                'name'=>'回调'
            );
            D('Plugin')->add($data);
            unset($_GET['pay_code']); // 用完之后删除, 以免进入签名判断里面去 导致错误
        }
        // 导入具体的支付类文件
        if( $this->pay_code  == 'weixin'){
            $this->payment = new \web\all\Component\payment\weixin\weixin();
        }
        if( $this->pay_code  == 'alipayMobile'){
            $this->payment = new \web\all\Component\payment\alipayMobile\alipayMobile();
        }
        if( $this->pay_code  == 'unionpay'){
            $this->payment = new  \web\all\Component\payment\unionpay\unionpay();
        }

    }
    /**
     *  微信支付提交支付方式
     */
    public function getCode(){
        //  订单支付提交
        header("Content-type:text/html;charset=utf-8");
        $order = $this->getOrderInfoByOrderType();
        $order = array(
            'sn' => generateSN(),
            'actually_amount' => 0.01,
            'create_time'=>time()
        );
        if (!isPhoneSide()) {//pc端微信扫码支付
            $code_str = $this->payment->pc_pay($order);
        }elseif(strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') == false && $this->pay_code == 'weixin'){//手机端非微信浏览器
            $code_str = $this->payment->h5_pay($order);
        }else{//微信浏览器
            $this->payment = new \web\all\Component\payment\weixin\weixin();

            $code_str = $this->payment->getJSAPI($order);
        }

    }

    /**
     *  其他支付提交支付方式
     */
    public function getCode2(){
        //  订单支付提交
        header("Content-type:text/html;charset=utf-8");
        $order = array(
            'sn' => generateSN(),
            'actually_amount' => 0.01,
            'create_time'=>time(),
            'notify_url'=>SITE_URL.U('CallBack/notifyUrl',array('pay_code'=>'weixin.recharge'))
        );
        $code_str = $this->payment->get_code($order);
    }

    // 服务器点对点 // http://www.a.cn/index.php/Home/Payment/notifyUrl
    public function notifyUrl(){
        $a=$_SERVER['QUERY_STRING'];
        $data = array(
            'code'=> $this->pay_code,
            'config'=>$a,
            'name'=>'回调2'
        );
        D('Plugin')->add($data);
        $this->payment->response();
        exit();
    }

    // 页面跳转 // http://www.a.cn/index.php/Home/Payment/returnUrl
    public function returnUrl(){
        $this->redirect('Index/payComplete');
        }

    //退款
    public function refund_back(){


        $this->payment = new  \web\all\Component\payment\unionpay\unionpay();
        $data = array(
            'sn'=>'20171212135434572991016642830329 ',
            'origQryId'=>'631712121354340543158',
            'actually_amount'=> date('YmdHis'),
            'txnAmt'=>0.01
        );
       
        $this->payment->payment_refund($data);
    }
    //退款异步回调
    public function refundNotify(){
        $this->payment->refund_respose();
    }

    /**
     * @return array
     * 按照订单类型获取支付订单信息
     */
    public function getOrderInfoByOrderType(){

        //订单支付信息
        if(isset($_POST['orderId']) && !empty($_POST['orderId'])){
            $modelOrder = D('Order');
            $orderId = $_GET['orderId'];
            $where = array(
                'o.user_id' => $this->user['id'],
                'o.id' => $orderId,
            );
            $orderInfo = $modelOrder -> selectOrder($where);
            $orderInfo = $orderInfo[0];
            $this -> orderInfo = $orderInfo;
            $totalFee = $orderInfo['actually_amount'];
            //检查订单状态
            $result = $modelOrder->checkOrderStatus($orderInfo);
            if($result['status'] == 0){
                $this ->error($result['message']);
            }
            $order = array(
                'sn' => $orderInfo['sn'],
                'actually_amount' => $totalFee,
                'create_time'=>time(),
                'notify_url'=>SITE_URL.U('CallBack/notifyUrl',array('pay_code'=>'weixin.order'))
            );

        }


        //充值信息
        $this->amount = floatval($_POST['amount']);
        if(isset($_POST['amount']) && $this->amount>0) {
            $_POST['user_id'] = $this->user['id'];
            $_POST['create_time'] = time();
            $_POST['amount'] = $this->amount;
            $_POST['sn'] = generateSN();
            $res = D('WalletDetail')->addWalletDetail();
            if(!$res){
                $this->error('充值订单无法生成');
            }
            $order = array(
                'sn' => $_POST['sn'],
                'actually_amount' => $this->amount,
                'create_time'=>time(),
                'notify_url'=>SITE_URL.U('CallBack/notifyUrl',array('pay_code'=>'weixin.recharge'))
            );
        }
        return $order;

    }
}
