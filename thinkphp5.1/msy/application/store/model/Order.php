<?php
/**
 * Created by PhpStorm.
 * User: mr.wei
 * Date: 2018/5/21
 * Time: 14:17
 *  订单模块
 */
namespace  app\store\model;

use think\Model;
use app\store\model\Cart;

class Order extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'orders';
    // 设置当前模型的数据库连接
    protected $connection = 'db_config_factory';
    // 设置主键
    protected $pk = 'order_id';

    /**
     * 添加订单
     *
     * @param $user_id
     * @param $address_id
     * @return array
     */
    public function addOrder($user_id, $address_id)
    {
        $cart = new Cart;
        $detail = $cart->alias('a')
           ->join('goods b', 'a.goods_id=b.id', 'INNER')
           ->field('a.goods_id, a.number, a.store_id, b.sale_price, b.inventory, b.name, b.thumb_img')
           ->where(['a.user_id'=>$user_id])
            ->select()->toArray();
        if(!$detail||!is_array($detail)||count($detail)<=0){
            return errorMsg('请添加结算商品');
        }
        $amount = 0.00;
        $sql = '';
        $order_sn = $this->createOrderSn();
        foreach($detail as $v){
            if($v['sale_price']<=0.00){
                return errorMsg('【'.$v['name'].'】销售价格有误，不能购买！');
            }
            if($v['number']>$v['inventory']){
                return errorMsg('【'.$v['name'].'】库存不足，不能购买！');
            }
            $amount += $v['number']*$v['sale_price'];
            $sql .= ";insert into order_detail(order_sn, goods_id, number, goods_price, store_id, thumb_img)
           values('{$order_sn}',{$v['goods_id']},{$v['number']},{$v['sale_price']},{$v['store_id']},'{$v['thumb_img']}')";
        }
        $sql = trim($sql, ';');echo $sql; dump ($this->execute($sql) );exit;
        $this->startTrans();
        try{
            $order = $this->create([
                'order_sn' => $order_sn,
                'amount' => $amount,
                'user_id' => $user_id,
                'source' => 'PC',
                'address_id' => $address_id,
                'remark' => '星期六派送',
                'create_time' => time(),
            ], ['order_sn', 'amount', 'user_id', 'source', 'address_id', 'remark', 'create_time']);
            $this->query($sql);
            //$cart->where(['user_id'=>$user_id])->delete();
            $this->commit();
            return successMsg('添加订单成功');
        } catch (\Exception $e) {
            $this->rollback(); // 回滚事务
            return errorMsg('添加订单失败');
        }
    }

    /**
     * 创建订单号
     * @return string
     */
    private function createOrderSn()
    {
        $order_sn = date('YmdHis').mt_rand(1000, 9999);
        $ret = $this->where(['order_sn'=>$order_sn])->find();
        while(!$ret){
            return $order_sn;
        }
    }

    //关联模型
    public function orderDetail()
    {
        return $this->hasMany('OrderDetail', 'order_sn');
    }
}