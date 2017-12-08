<?php
/**
 * tpshop
 * ============================================================================
 * * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 采用TP5助手函数可实现单字母函数M D U等,也可db::name方式,可双向兼容
 * ============================================================================
 * $Author: IT宇宙人 2015-08-10 $
 */ 
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
        if(IS_POST)
        {
            $this->pay_code = $_POST['pay_code']; // 支付 code
        }
        else // 第三方 支付商返回
        {
            //file_put_contents('./a.html',$_GET,FILE_APPEND);
            $this->pay_code = I('get.pay_code');
            unset($_GET['pay_code']); // 用完之后删除, 以免进入签名判断里面去 导致错误
        }
        // 导入具体的支付类文件
        if( $this->pay_code  == 'weixin'){
            $this->payment = new \Component\payment\weixin\weixin();
        }
        if( $this->pay_code  == 'alipayMobile'){
            $this->payment = new \Component\payment\alipayMobile\alipayMobile();
        }
        if( $this->pay_code  == 'unionpay'){
            $this->payment = new \Component\payment\unionpay\unionpay();
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
            $this->assign('code_str', $code_str);
            $this->display('wx_h5');
        }else{//微信浏览器
            $this->payment = new \Component\payment\weixin\weixin();
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
            'actually_amount' => 0.01
        );
        $code_str = $this->payment->get_code($order,$config_value='');
    }

    // 服务器点对点 // http://www.a.cn/index.php/Home/Payment/notifyUrl
    public function notifyUrl(){
        $this->payment->response();
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
        $this->payment->payment_refund($data);
    }
    //退款异步回调
    public function refundNotify(){
        $this->payment->refund_respose();
    }
}
