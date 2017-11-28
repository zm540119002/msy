<?php
namespace Myms\Controller;
use Think\Controller;

class PaymentController extends Controller {
    //微信支付回调验证
    public function notify(){
        $xml = file_get_contents('php://input');
        $data = xmlToArray($xml);
        // 保存微信服务器返回的签名sign
        $data_sign = $data['sign'];
        // sign不参与签名算法
        unset($data['sign']);
        $sign = $this->makeSign($data);
        $dataOrderSn = $data['out_trade_no'];
        // 判断签名是否正确  判断支付状态
        if ( ($sign === $data_sign) && ($data['return_code'] == 'SUCCESS') && ($data['result_code'] == 'SUCCESS') ) {
            $orderInfo = D('order') -> getOrderInfoByOrderNo($dataOrderSn);
            if($orderInfo['order_state']!= 10){//判定订单状态，如已处理过，直接返回true
                \Think\Log::write('订单已完成(存在多次回调)','pay');
                $result = true;
            }else{
                if( $orderInfo['actually_amount']*100 != $data['total_fee'] ){//校验返回的订单金额是否与商户侧的订单金额一致
                    \Think\Log::write('回调的金额和订单的金额不符，终止购买，orderSn:'.$dataOrderSn,'pay');
                    $result = false;
                }else{
                    D('order') -> startTrans();
                    try{
                        //更新订单
                        $result = $this -> updateOrderAfterPay($data);
                        if(!$result){
                            \Think\Log::write('回调的金额和订单的金额不符，终止购买，orderSn:'.$dataOrderSn,'pay');
                            throw new \Exception( $this -> ajaxReturn(errorMsg('更新订单失败')));
                        }
                        //减库存
                        $oGoods    = D('order') -> getOrderGoodsByOrderSn($dataOrderSn);
                        $result    = $this -> decGoodsNum($oGoods);
                        D('order') -> commit();
                    }catch (Exception $e){
                        D('order')->rollback();
                        $this->ajaxReturn($e -> getMessage());
                    }
                }
            }
        }else{
            $result = false;
        }

        // 返回状态给微信服务器
        if ($result) {
            \Think\Log::write('支付成功，orderSn:'.$dataOrderSn,'pay');
            $str='<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        }else{
            \Think\Log::write('签名失败，orderSn:'.$dataOrderSn,'pay');
            $str='<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[签名失败]]></return_msg></xml>';
        }
        echo $str;
        return $result;
    }



    /**
     * 生成签名
     * @return 签名，本函数不覆盖sign成员变量
     */
    protected function makeSign($data){
        //获取微信支付秘钥
        require_once APP_PATH."Component/WxpayAPI/lib/WxPay.Api.php";
        $key = \WxPayConfig::KEY;
        // 去空
        $data=array_filter($data);
        //签名步骤一：按字典序排序参数
        ksort($data);
        $string_a=http_build_query($data);
        $string_a=urldecode($string_a);
        //签名步骤二：在string后加入KEY
        //$config=$this->config;
        $string_sign_temp=$string_a."&key=".$key;
        //签名步骤三：MD5加密
        $sign = md5($string_sign_temp);
        // 签名步骤四：所有字符转为大写
        $result=strtoupper($sign);
        return $result;
    }

    //支付成功更新订单
    public function updateOrderAfterPay($data){
        $where['order_sn'] = $data['out_trade_no'];
        $Data = array(
            'pay_sn' => $data['transaction_id'],
            'payment_code' =>$data['trade_type'],
            'payment_time' =>$data['time_end'],
            'order_state' =>20,
        );
         return M('order') -> where($where)->save($Data);

    }

    //检查订单商品库存
    public function checkOrderGoodsStock($oGoods){
        foreach ( $oGoods as $v ) {
            $where['id'] = $v['foreign_id'];
            if($v['type'] == 1){//商品
                $storage = M('goods') -> where($where) -> getField('inventory');
            }
            if($v['type'] == 2){
                $storage = M('project') -> where($where) -> getField('inventory');
            }
            if($storage < $v['num']){
                $this->error($v['foreign_name']."只有".$storage.'件，库存不足');
            }
        }
    }

    //减库存
    public function decGoodsNum($oGoods){
        foreach ( $oGoods as $v ) {
            $where['id'] = $v['foreign_id'];
            if($v['type'] == 1){//商品
                $result = M('goods')->lock(true) -> where($where) -> setDec('inventory',$v['num']);
            }
            if($v['type'] == 2){
                $result = M('project')->lock(true) -> where($where) -> setDec('inventory',$v['num']);
            }
            if(!$result){
                throw new \Exception( $this->ajaxReturn(errorMsg($v['goods_num'].'减库存失败')));
            }
        }
        return true;
    }

}