<?php
namespace Mall\Model;
use Think\Model;
class OrderModel extends Model {
    protected $tableName = 'order_details';
    protected $tablePrefix = '';
    protected $connection = 'DB_MYMS';
    
    //直接购买商品时，插入order_goods 表
    public function directlyAddOrderGoods($uid,$goodsInfo,$orderSn,$goodsNum){

        $data['order_sn']           = $orderSn; // 订单id
        $data['buyer_id']           = $uid; // 用户id
        $data['foreign_id']         = $goodsInfo['id']; // 商品id
        $data['foreign_name']       = $goodsInfo['name']; // 商品名称
        $data['num']                = $goodsNum; // 购买数量
        $data['price']              = $goodsInfo['real_price']; // 市场价
        $data['img']                = $goodsInfo['main_img']; //'',
        $data['type']               = $goodsInfo['type'];
        $data['buy_type']           = $goodsInfo['buy_type'];
        $orderGoodsId               =  D('OrderDetail')->add($data);
        return $orderGoodsId;
    }
    
    //根据$orderSn获取订单商品详情
    public function getOrderGoodsByOrderSn($orderSn){
        $where['order_sn'] = $orderSn;
        return D('OrderDetail') -> where($where)->select();
    }




}