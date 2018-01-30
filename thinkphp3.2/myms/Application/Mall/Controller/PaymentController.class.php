<?php
namespace Mall\Controller;
use Think\Controller;
use web\all\Component\WxpayAPI;

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
        if ( $sign === $data_sign && ($data['return_code'] == 'SUCCESS') && ($data['result_code'] == 'SUCCESS') ) {
            $orderInfo = D('order') -> getOrderInfoByOrderNo($dataOrderSn);
            $uid = $orderInfo['user_id'];
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
                        if(false === $result){
                            \Think\Log::write('更新订单失败','pay');
                            throw new \Exception( $this -> ajaxReturn(errorMsg('更新订单失败')));
                        }
                        //减库存
                        $oGoods    = D('order') -> getOrderGoodsByOrderSn($dataOrderSn);
                        $result    = $this -> decGoodsNum($oGoods);
                        //更新goupBuy表为支付成功
                       $this ->updateGroupBuy($dataOrderSn);
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
            if($v['type'] == 2){//项目
                $result = M('project')->lock(true) -> where($where) -> setDec('inventory',$v['num']);
            }
            if(!$result){
                throw new \Exception( $this->ajaxReturn(errorMsg($v['foreign_name'].'减库存失败')));
            }
        }
        return true;
    }

   //更新团购表
    public function updateGroupBuy($dataOrderSn){
        $oGoods    = D('order') -> getOrderGoodsByOrderSn($dataOrderSn);
        if($oGoods[0]['buy_type'] == 3){
            $where['order_sn'] = $dataOrderSn;
            $groupBuyData['status'] = 1;
            $result = M('groupbuy') -> where($where)->save($groupBuyData);
            if(false === $result){
                \Think\Log::write('更新团购表支付成功标识位失败','pay');
                throw new \Exception( $this -> ajaxReturn(errorMsg('更新团购表支付成功标识位失败')));
            }
            //判断是否成团，更新groupbuy表成团标识位为 1，代表已成团
            $findWhere = array(
                'order_sn' => $dataOrderSn,
                'status' => 1,
            );
            //查出团购ID
            $groupBuyInfo = M('groupbuy')->where($findWhere)->find();
            $groupBuySn = $groupBuyInfo['groupbuy_sn'];
            $updateWhere = array(
                'groupbuy_sn' => $groupBuySn,
                'status' => 1,
            );
            //查找团购ID数量是否已满足成团要求
            $groupBuyNum = M('groupbuy') -> where($updateWhere)->count();
            if($oGoods[0]['type'] == 1){//商品类型
                $goodsInfo = D('goods')->getGoodsInfoByGoodsId($oGoods[0]['foreign_id']);
            }
            if($oGoods[0]['type'] == 2){//项目类型
                $goodsInfo = D('project')->getProjectInfoByProjectId($oGoods[0]['foreign_id']);
            }
            if($groupBuyNum == $goodsInfo['groupbuy_num']){
                $updateData['shipments'] = 1;
                $result = M('groupbuy')->where($updateWhere)->save($updateData);
                if(false === $result){
                    \Think\Log::write('更新团购表成团标识位失败','pay');
                    throw new \Exception( $this -> ajaxReturn(errorMsg('更新团购表成团标识位失败')));
                }
            }
        }

    }

}