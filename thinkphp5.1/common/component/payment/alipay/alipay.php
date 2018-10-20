<?php


namespace common\component\payment\alipay;

require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'wappay/service/AlipayTradeService.php';
require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'wappay/buildermodel/AlipayTradeWapPayContentBuilder.php';

/**
 * 支付 逻辑定义
 * Class AlipayPayment
 * @package Home\Payment
 */

class alipay
{
    public $alipay_config = array();// 支付宝支付配置参数

    /**
     * 析构流函数
     */
    public function  __construct() {
        //支付配置
        require dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'config.php';
        $this->alipay_config = $config;
    }
    /**
     * 生成支付代码
     * @param   array   $order      订单信息
     *
     */
    function get_code($payInfo)
    {
        if (!empty($payInfo['sn'])&& trim($payInfo['sn'])!=""){
            //商户订单号，商户网站订单系统中唯一订单号，必填
            $timeout_express="10m";//该笔订单允许的最晚付款时间，逾期将关闭交易。取值范围：1m～15d。m-分钟，h-小时，d-天，1c-当天（1c-当天的情况下，无论交易何时创建，都在0点关闭）。 该参数数值不接受小数点， 如 1.5h，可转换为 90m。
            $payRequestBuilder = new \AlipayTradeWapPayContentBuilder();
            $payRequestBuilder->setBody('美尚云');//对一笔交易的具体描述信息。如果是多种商品，请将商品描述字符串累加传给body。
            $payRequestBuilder->setSubject('美尚云');//	商品的标题/交易标题/订单标题/订单关键字等。
            $payRequestBuilder->setOutTradeNo($payInfo['sn']);//商户网站唯一订单号 最长64位
            $payRequestBuilder->setTotalAmount($payInfo['actually_amount']);//订单总金额，单位为元，精确到小数点后两位，取值范围[0.01,100000000]
            $payRequestBuilder->setTimeExpress($timeout_express);//
            $payResponse = new \AlipayTradeService($this->alipay_config);
            $result=$payResponse->wapPay($payRequestBuilder,$this->alipay_config['return_url'],$this->alipay_config['notify_url']);
            return ;
        }


    }

    /**
     * 交易订单查询
     */
    public function orderQuery($orderInfo){
        if (!empty($orderInfo['sn']) || !empty($orderInfo['pay_sn'])){

            //商户订单号和支付宝交易号不能同时为空。 trade_no、  out_trade_no如果同时存在优先取trade_no
            //商户订单号，和支付宝交易号二选一
            $out_trade_no = trim($orderInfo['sn']);

            //支付宝交易号，和商户订单号二选一
            $trade_no = trim($orderInfo['pay_sn']);

            $RequestBuilder = new AlipayTradeQueryContentBuilder();
            $RequestBuilder->setTradeNo($trade_no);
            $RequestBuilder->setOutTradeNo($out_trade_no);

            $Response = new \AlipayTradeService($config);
            $result=$Response->Query($RequestBuilder);
            return ;
        }
    }

    //支付宝批量付款到支付宝账户有密接口接口
    function transfer($data){
        require_once("lib/alipay_submit.class.php");
        //付款详细数据格式为：流水号1^收款方账号1^收款账号姓名1^付款金额1^备注说明1|流水号2^收款方账号2^收款账号姓名2^付款金额2^备注说明2。
        $parameter = array(
            "service" => "batch_trans_notify",//批量转账接口名称
            "partner" => $this->alipay_config['partner'],//合作身份者ID
            "notify_url" => SITE_URL.U('Home/Payment/notifyBack'),//回调通知地址
            "email"	=> $this->alipay_config['seller_email'],//付款账号
            "account_name"	=> $this->alipay_config['alipay_account_name'],//付款账号名
            "pay_date"	=> date('Y-m-d H:i:s'),//支付日期
            "batch_no"	=> $data['batch_no'],//批量付款批次号
            "batch_fee"	=> $data['batch_fee'],//付款总金额
            "batch_num"	=> $data['batch_num'],//付款总笔数
            "detail_data" => trim($data['detail_data']),//付款详细数据
            "_input_charset" => $this->alipay_config['input_charset']
        );
        //建立请求
        $alipaySubmit = new \AlipaySubmit($this->alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
        echo $html_text;
    }

    function transfer_response(){
        require_once("lib/alipay_notify.class.php");  // 请求返回
        //计算得出通知验证结果
        $alipayNotify = new \AlipayNotify($this->alipay_config); // 使用支付宝原生自带的类和方法 这里只是引用了一下 而已
        $verify_result = $alipayNotify->verifyNotify();
        if($verify_result){
            //返回数据格式：0315001^gonglei1@163.com^龚本林^20.00^S^null^200810248427067^20081024143652|
            $success_details = $_POST['success_details'];
            if($success_details){
                $sdata = explode('|', $success_details);
                foreach ($sdata as $val){
                    $pay_arr = explode('^', $val);
                    $pay_id[] = $pay_arr[0];
                }
                $withdrawals = M('withdrawals')->where(array('id'=>array('in',$pay_id)))->select();
                foreach ($withdrawals as $wd){
                    accountLog($wd['user_id'], ($wd['money'] * -1), 0,"平台处理用户提现申请");
                }
                M('withdrawals')->where(array('id'=>array('in',$pay_id)))->save(array('pay_time'=>strtotime($pay_arr[7]),'status'=>2,'pay_code'=>$pay_arr[6]));
            }else{
                //失败数据格式：0315006^xinjie_xj@163.com^星辰公司1^20.00^F^TXN_RESULT_TRANSFER_OUT_CAN_NOT_EQUAL_IN^200810248427065^20081024143651|
                //格式为：流水号^收款方账号^收款账号姓名^付款金额^失败标识(F)^失败原因^支付宝内部流水号^完成时间。
                //批量付款数据中转账失败的详细信息
                $fail_details = $_POST['fail_details'];
                $fdata = explode('|', $fail_details);
                foreach ($fdata as $val){
                    $pay_arr = explode('^', $val);
                    $update = array('error_code'=>$pay_arr[5],'pay_time'=>strtotime($pay_arr[7]),'status'=>3,'pay_code'=>$pay_arr[6]);
                    M('withdrawals')->where(array('id'=>$pay_arr[0]))->save($update);
                }
            }
            echo "success"; //告诉支付宝处理成功
        }else{
            $verify_result = print_r($verify_result);
            error_log($verify_result,3,'pay.log');
        }
    }

    //支付宝即时到账批量退款有密接口接口
    public function payment_refund($data){
        require_once("lib/alipay_submit.class.php");
        /**************************请求参数**************************/
        //批次号，必填，格式：当天日期[8位]+序列号[3至24位]，如：201603081000001
        //退款数据集的格式为：原付款支付宝交易号^退款总金额^退款理由#原付款支付宝交易号^退款总金额^退款理由;
        /**
         * // $detail_data = $order['transaction_id'].'^'.$return_money.'^'.'用户申请订单退款';
        //$data = array('batch_no'=>date('YmdHi').$rec_goods['rec_id'],'batch_num'=>1,'detail_data'=>$detail_data);
         */
        // 服务器异步通知页面路径，需http://格式的完整路径，不能加?id=123这类自定义参数,必须外网可以正常访问
        /************************************************************/
        //构造要请求的参数数组，无需改动
        $this->alipay_config['seller_user_id'] = $this->alipay_config['partner'];
        $parameter = array(
            "service" => 'refund_fastpay_by_platform_pwd',
            "partner" => $this->alipay_config['partner'],
            //"notify_url" => SITE_URL.U('Payment/notifyUrl',array('pay_code'=>'refund_respose')),
            "notify_url" => SITE_URL.U('Payment/refundNotify'),
            "seller_user_id" => $this->alipay_config['partner'],
            "refund_date"	=> date("Y-m-d H:i:s",time()),
            "batch_no"	=> $data['batch_no'],
            "batch_num"	=> $data['batch_num'],
            "detail_data"	=> $data['detail_data'],
            "_input_charset" => $this->alipay_config['input_charset']
        );

        //建立请求
        $alipaySubmit = new \AlipaySubmit($this->alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
        echo $html_text;
    }

    //支付宝即时到账批量退款有密接口接口 异步回调
    public function  refund_respose(){

        require_once("lib/alipay_notify.class.php");  // 请求返回
        //计算得出通知验证结果
        $alipayNotify = new \AlipayNotify($this->alipay_config); // 使用支付宝原生自带的类和方法 这里只是引用了一下 而已
        $verify_result = $alipayNotify->verifyNotify();

        if(!$verify_result){
            $verify_result = print_r($verify_result);
            error_log($verify_result,3,'pay.log');
        }
        $batch_no = $_POST['batch_no'];
       //批量退款数据中转账成功的笔数
        $success_num = $_POST['success_num'];
        if(intval($success_num)>0){
            $result_details = $_POST['result_details'];
            $res = explode('^', $result_details);
            $batch_no =  $_POST['batch_no'];
            //$rec_id = substr($batch_no,12);
            if($res[2] == 'SUCCESS'){
                $data = array(
                    'code'=>'alipay_refund',
                    'name'=>'alipay_refund'
                );
                D('Plugin')->add($data);
                //退款成功，做自己的业务逻辑
                $xml = file_get_contents('php://input');
                file_put_contents('tui2.text',$xml);
            }
            echo "success"; //告诉支付宝处理成功
        }
    }
}