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
    protected $table = 'order';
    // 设置当前模型的数据库连接
    protected $connection = 'db_config_factory';
    // 设置主键
    protected $pk = 'order_id';

    /**
     * 相对的关联模型
     * @return \think\model\relation\BelongsTo
     */
    public function orderDetail()
    {
        return $this->belongsTo('OrderDetail');
    }

    //相对的关联模型
    public function addressStatic()
    {
        return $this->hasOne('AddressStatic', 'order_id');
    }

    /**
     * 订单拆分关联模型
     * @return \think\model\relation\HasMany
     */
    public function orderUnpack()
    {
        return $this->hasMany('OrderUnpack', 'order_id');
    }

    /**
     * 添加订单
     *
     * @param $user_id
     * @param $address_id
     * @return array
     */
    public function addOrder($user_id, $address_id, $remark = '备注')
    {
        config('database.datetime_format', false);  //解决时间戳自动转换问题
        $address = new \app\store\model\Address;
        $address_ret = $address->findAddress($user_id, $address_id);
        if($address_ret['status']!==1){
            return $address_ret;
        }
        unset($address_ret['data']['address_id']);
        $cart = new Cart;
        $ret = $cart->getCartList($user_id);
        if($ret['status']!==1){
            return  errorMsg($ret['info']);
        }
        $amount = 0.00;
        $data = [];
        $order_sn = $this->createOrderSn();
        foreach($ret['data'] as $v){
            $amount += $v['number']*$v['sale_price'];
            $data[] = [ 'order_sn' => $order_sn,
                    'goods_id' =>$v['goods_id'],
                    'number' => $v['number'],
                    'goods_price' => $v['sale_price'],
                    'store_id' => $v['store_id'],
                    'thumb_img' => $v['thumb_img'],
                    'name' =>   $v['name']
                ];
        }
        $this->startTrans();
        try{
            $order = $this->create([
                'order_sn' => $order_sn,
                'amount' => $amount,
                'user_id' => $user_id,
                'source' => 'PC',
                'remark' => $remark,
                'create_time' => time(),
            ], ['order_sn', 'amount', 'user_id', 'source', 'remark', 'create_time']);
            $this->orderDetail()->insertAll($data);
            $this->orderDetail()->where(['order_sn'=>$order_sn])->setField(['order_id'=>$order['order_id']]);
            //$cart->where(['user_id'=>$user_id])->delete();
            $address_ret['data']['order_id'] = $order['order_id'];
            $this->addressStatic()->insert($address_ret['data']);
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

    /**
     * 拆分订单
     * @param $order_id
     * @return array
     */
    public function setOrderUnpack($order_id)
    {
        $ret = $this->alias('a')->field('a.order_id, a.status, b.store_id, b.number, b.after_sale_price')
            ->join('order_detail b', 'b.order_id=a.order_id', 'INNER')
            ->where(['a.order_id'=>$order_id])
            ->select();
        if(count($ret)<=0){
           return errorMsg('订单信息不存在');
        }
        if($ret[0]['status']!=2){
           return errorMsg('未全额支付订单，不能拆分');
        }
        $data = [];
        foreach($ret as $v){
            if(array_key_exists($v['store_id'], $data)){
                $data[ $v['store_id'] ] ['pay_money']=
                    $data[$v['store_id']]['pay_money']+($v['after_sale_price']*$v['number']);
            }else{
                $data[ $v['store_id'] ] =[
                    'order_id' => $order_id,
                    'store_id' => $v['store_id'],
                    'pay_money' => $v['after_sale_price']*$v['number'],
                ];
            }
        }
        $this->startTrans();
        try{
            $this->orderUnpack()->insertAll($data);
            $this->where(['order_id'=>$order_id,'is_unpack'=>0])->setField(['is_unpack'=>1]);
            $this->commit();
            return successMsg('拆分订单成功');
        } catch (\Exception $e) {
            $this->rollback(); // 回滚事务
            return errorMsg('拆分订单失败');
        }
    }

}