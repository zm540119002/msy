<?php
namespace Purchase\Controller;
use Think\Controller;


class PaymentController extends Controller {

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
        $order = array(
            'sn' => generateSN(),
            'actually_amount' => 0.01
        );
        if (!isPhoneSide()) {//pc端微信扫码支付
            $code_str = $this->payment->pc_pay($order,$config_value='');
        }elseif(strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') == false && $this->pay_code == 'weixin'){//手机端非微信浏览器
            $code_str = $this->payment->h5_pay($order);
        }else{//微信浏览器
            $this->payment = new \web\all\Component\payment\weixin\weixin();
            $code_str = $this->payment->getJSAPI($order);
            exit($code_str);
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
            'create_time'=>time()
        );
        $code_str = $this->payment->get_code($order,$config_value='');
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
            'orderId'=>'20171214123212043135617076515427',
            'origQryId'=>'761712141232128600118',
            'txnTime'=> date('YmdHis'),
            'txnAmt'=>1
        );
        $this->payment->payment_refund($data);
    }
    //退款异步回调
    public function refundNotify(){
        $this->payment->refund_respose();
    }
}
