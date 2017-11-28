<?php
namespace Home\Model;
use Think\Model;
class PaymentModel extends Model {
    protected $tableName = 'order';
    protected $tablePrefix = '';
    protected $connection = 'DB_PURCHASE';
    protected $mymsConnection = 'DB_MYMS';
    protected $db;
    
    
    //获取订单详情
    public function getOrderInfo($uid,$orderId,$type){
        $where['user_id'] = $uid;
        $where['id'] = $orderId;
        if($type == 'purchase'){
            return  M('order','',$this->connection) -> where($where)->find();
        }
        if($type == 'myms'){
            return  M('order','',$this->mymsConnection) -> where($where)->find();
        }

    }
    
    
    //获取订单详情
    public function getOrderInfoByOrderNo($uid,$orderSn,$type){
        $where['user_id'] = $uid;
        $where['order_sn'] = $orderSn;
        if($type == 'purchase'){
            return  M('order','',$this->connection) -> where($where)->find();
        }
        if($type == 'myms'){
            return  M('order','',$this->mymsConnection) -> where($where)->find();
        }

    }

    //根据$orderSn获取订单商品详情
    public function getOrderGoodsByOrderSn($orderSn,$type){
        $where['order_sn'] = $orderSn;
        if($type == 'purchase'){
            return M('order_goods','',$this->connection) -> where($where)->select();
        }
        if($type == 'myms'){
            return  M('order_details','',$this->mymsConnection) -> where($where)->select();
        }

    }

    //检查订单状态
    public function checkOrderStatus($orderInfo){
        if(!$orderInfo){
            return $rst=array(
               'status' => 0,
                'message' => '订单信息有误'
            );
        }
        if($orderInfo['order_state'] != 10){
            return $rst=array(
                'status' => 0,
                'message' => '订单已支付或已取消'
            );
        }
        return $rst=array(
            'status' => 1,
            'message' => '待支付'
        );
    }

    //检查订单商品库存
    public function checkOrderGoodsStock($oGoods,$type){
        if($type == 'purchase'){
            foreach ( $oGoods as $v ) {
                $where['id'] = $v['goods_id'];
                $storage = M('goods','',$this->connection)-> where($where) -> getField('storage');
                if($storage < $v['goods_num']){
                    return $rst=array(
                        'status' => 0,
                        'message' => $v['goods_name']."只有".$storage.'件，库存不足',
                    );
                }
                return $rst=array(
                    'status' => 1,
                    'message' => '库存正常',
                );
            }
        }
        if($type == 'myms'){
            foreach ( $oGoods as $v ) {
                $where['id'] = $v['foreign_id'];
                if($v['type'] == '1'){
                    $storage = M('goods','',$this->mymsConnection)-> where($where) -> getField('inventory');
                }
                if($v['type'] == '2'){
                    $storage = M('project','',$this->mymsConnection)-> where($where) -> getField('inventory');
                }

                if($storage < $v['num']){
                    return $rst=array(
                        'status' => 0,
                        'message' => $v['foreign_name']."只有".$storage.'件，库存不足',
                    );
                }
                return $rst=array(
                    'status' => 1,
                    'message' => '库存正常',
                );
            }
        }

    }
    
}