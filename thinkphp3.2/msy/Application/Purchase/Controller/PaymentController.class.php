<?php
namespace Purchase\Controller;
use Think\Controller;


class PaymentController extends Controller {

//    public $payment; //  具体的支付类
//    public $pay_code; //  具体的支付code

    /**
     * 析构流函数
     */
//    public function  __construct() {
//        parent::__construct();
//        // 订单支付提交
//        $this->pay_code=$_POST['pay_code'];
//        if(!empty($this->pay_code))
//        {
//            $this->pay_code = $_POST['pay_code']; // 支付 code
//            $this->pay_code= get_url_param('pay_code');
//            $a='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING'];
//            $data = array(
//                'code'=> $this->pay_code,
//                'config'=>$a,
//                'name'=>'支付'
//            );
//            D('Plugin')->add($data);
//        }
////        else // 第三方 支付商返回
////        {
////            $this->pay_code= get_url_param('pay_code');
////            $a='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING'];
////
////            $data = array(
////                'code'=> $this->pay_code,
////                'config'=>$a,
////                'name'=>'回调'
////            );
////            D('Plugin')->add($data);
////
////        }
//        // 导入具体的支付类文件
//
//        if( $this->pay_code  == 'weixin'){
//            $payment = new \Component\payment\weixin\weixin();
//        }
//        if( $this->pay_code  == 'alipayMobile'){
//            $payment = new \Component\payment\alipayMobile\alipayMobile();
//        }
//        if( $this->pay_code  == 'unionpay'){
//            $payment = new \Component\payment\unionpay\unionpay();
//        }
//    }
    /**
     *  微信支付提交支付方式
     */
    public function getCode(){
        $payment = new \Component\payment\weixin\weixin();
        //  订单支付提交
        header("Content-type:text/html;charset=utf-8");
        $order = array(
            'sn' => generateSN(),
            'actually_amount' => 0.01
        );
        if (!isPhoneSide()) {//pc端微信扫码支付
            $code_str = $payment->pc_pay($order,$config_value='');
        }elseif(strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') == false && $this->pay_code == 'weixin'){//手机端非微信浏览器
            $code_str = $payment->h5_pay($order);
        }else{//微信浏览器
//            $payment = new \Component\payment\weixin\weixin();
            $code_str = $payment->getJSAPI($order);
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
        $this->pay_code=$_POST['pay_code'];
        if(!empty($this->pay_code)) {
            $this->pay_code = $_POST['pay_code']; // 支付 code
            $this->pay_code = get_url_param('pay_code');
            $a = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . $_SERVER['QUERY_STRING'];
            $data = array(
                'code' => $this->pay_code,
                'config' => $a,
                'name' => '支付'
            );
            D('Plugin')->add($data);
        }

        if( $this->pay_code  == 'alipayMobile'){
            $payment = new \Component\payment\alipayMobile\alipayMobile();
        }
        if( $this->pay_code  == 'unionpay'){
            $payment = new \Component\payment\unionpay\unionpay();
        }

        $code_str = $payment->get_code($order,$config_value='');
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

        if(strpos($_SERVER['HTTP_USER_AGENT'],'weixin') == true){
            $payment = new \Component\payment\weixin\weixin();
        }
        if(strpos($_SERVER['HTTP_USER_AGENT'],'alipayMobile') == true){
            $payment = new \Component\payment\alipayMobile\alipayMobile();
        }
        if(strpos($_SERVER['HTTP_USER_AGENT'],'unionpay') == true){
            $payment = new \Component\payment\unionpay\unionpay();
        }

        unset($_GET['pay_code']); // 用完之后删除, 以免进入签名判断里面去 导致错误
        $payment->response();
        exit();
    }

    // 页面跳转 // http://www.a.cn/index.php/Home/Payment/returnUrl
    public function returnUrl(){
        $this->redirect('Index/payComplete');
        }

    //退款
    public function refund_back(){
        $detail_data = '2017120521001004170524388308'.'^'.'0.01'.'^'.'用户申请订单退款';
        $data = array('batch_no'=>date('YmdHi').'145','batch_num'=>1,'detail_data'=>$detail_data);
        $payment->payment_refund($data);
    }
    //退款异步回调
    public function refundNotify(){
        $payment->refund_respose();
    }
}
