<?php
namespace Myms\Model;
use Think\Model;
class OrderModel extends Model {
    protected $tableName = 'order';
    protected $tablePrefix = '';
    protected $db;

    public function __construct()
    {
        $this->db = M($this->tableName);
    }

    /**
     * 添加一个订单
     * @param $user_id|用户id
     * @param $address_id|地址id
     * @param $shipping_code|物流编号
     * @param $invoice_title|发票
     * @param int $coupon_id|优惠券id
     * @param $car_price|各种价格
     * @param string $user_note|用户备注
     * @return array
     */
    public function addOrder($orderSn,$uid,$address_id,$invoice_title,$car_price,$user_note='')
    {
        // 0插入订单 order
        $data = array(
            'order_sn'  => $orderSn, // 订单编号
            'user_id'   => $uid, // 用户id
            'address_id'=> $address_id,

            'invoice_title'   => $invoice_title, //'发票抬头',
            'goods_amount'    => $car_price['goods_amount'],//'商品价格',
            'shipping_fee'    => $car_price['shipping_fee'],//'运费',
            'pd_amount'       => $car_price['pd_amount'],//'预存款支付金额,
            'coupon_price'    => $car_price['coupon_price'],//'使用优惠券',
            'coupon_id'       => $car_price['coupon_id'],//'使用优惠券',
            'order_amount'    => $car_price['order_amount'],//'订单总价格；
            'actually_amount' => $car_price['actually_amount'],//'应付款金额',
            'create_time'     => time(), // 下单时间
            'user_note'       => $user_note, // 用户下单备注
        );
         $orderId = $this->db->add($data);
         return $orderId;
    }

    // 从结算时插入order_goods 表
    public function addOrderGoods($uid,$cartList,$orderSn){
        $dataGoods = array();
        foreach($cartList as $key => $val)
        {
            $data['order_sn']           = $orderSn; // 订单id
            $data['buyer_id']           = $uid; // 用户id
            $data['foreign_id']         = $val['foreign_id']; // 商品id
            $data['foreign_name']       = $val['foreign_name']; // 商品名称
            $data['num']                = $val['num']; // 购买数量
            $data['price']              = $val['price']; // 商品价
            $data['img']                = $val['img']; //,
            $data['type']               = $val['type'];
            $data['buy_type']           = $val['buy_type'];
            $dataGoods[] = $data;
        }
        $orderGoodsId  = M('order_details')->addAll($dataGoods);
        return $orderGoodsId;
    }

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
        $orderGoodsId               =  M('order_details')->add($data);
        return $orderGoodsId;
    }


    //获取订单详情
    public function getOrderInfo($uid,$orderId){
        $where['user_id'] = $uid;
        $where['id'] = $orderId;
        return $this->db -> where($where)->find();
    }

    //获取订单详情
    public function getOrderInfoByOrderNo($orderSn){
//        $where['user_id'] = $uid;
        $where['order_sn'] = $orderSn;
        return $this->db -> where($where)->find();
    }

    //根据$orderSn获取订单商品详情
    public function getOrderGoodsByOrderSn($orderSn){
        $where['order_sn'] = $orderSn;
        return M('order_details') -> where($where)->select();
    }


}