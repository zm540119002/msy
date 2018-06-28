<?php
/**
 * Created by PhpStorm.
 * User: Mr.wei
 * Date: 2018/5/29
 * Time: 9:39
 * 订单管理控制器
 */
namespace app\factory\model;

use think\Model;

class Order extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'order_unpack';
    // 设置当前模型的数据库连接
    protected $connection = 'db_config_factory';
    // 设置主键
    protected $pk = 'order_id';

    public function getOrderList($store_id)
    {
        return $this->alias('a')->field('a.pay_money, a.order_id, a.status_unpack, b.order_sn, b.status, 
            b.create_time, b.source, b.pay_method, b.remark, c.consignee, c.phone, c.detail')
            ->join('order b', 'a.order_id=b.order_id', 'INNER')
            ->join('address_static c', 'b.order_id=c.order_id', 'INNER')
            ->where(['a.store_id'=>$store_id])
            ->paginate(1);
    }

    /**
     * 属性获取器
     * @param $value
     * @return mixed
     */
    public function getStatusAttr($value)
    {
        $status = [0=>'已取消', 1=>'待支付', 2=>'已支付', 3=>'部分支付', 4=>'已完成'];
        return $status[$value];
    }

    public function getStatusUnpackAttr($v)
    {
        $su = [1=>'待仓库拣货', 2=>'仓库拣货', 3=>'已出库', 4=>'发货中', 5=>'已发货', 6=>'部分发货'];
        return $su[$v];
    }

    public function getPayMethodAttr($value)
    {
        $pay = [0=>'未知', 1=>'银联支付', 2=>'支付宝支付', 3=>'微信支付'];
        return $pay[$value];
    }

    /**
     * 根据店铺与订单ID获取订单明细
     * @param $store_id
     * @param $order_id
     * @return array
     */
    public function getOrderDetail($store_id, $order_id)
    {
        $ret = $this->alias('a')->field('a.pay_money, a.order_id, a.status_unpack, b.order_sn, b.status, 
            b.create_time, b.source, b.pay_method, b.remark, c.consignee, c.phone, c.detail, d.*')
            ->join('order b', 'a.order_id=b.order_id', 'INNER')
            ->join('address_static c', 'b.order_id=c.order_id', 'INNER')
            ->join('order_detail d', 'c.order_id=d.order_id', 'INNER')
            ->where(['a.order_id'=>$order_id, 'a.store_id'=>$store_id, 'd.store_id'=>$store_id])
            ->select();
        if( count($ret)<=0 ){
            errorMsg('订单数据有误');
        }
        $data = []; //'' => $v[''],''=>$v['']{order_id=order-id}
        foreach($ret as $v){
            if( !array_key_exists($data['order'], $data) ){
                $data['order'] =[
                    'pay_money' => $v['pay_money'],
                    'status' => $v['status'],
                    'status_unpack' => $v['status_unpack'],
                    'order_id' => $v['order_id'],
                    'order_sn' => $v['order_sn'],
                    'consignee' => $v['consignee'],
                    'phone' => $v['phone'],
                    'detail' => $v['detail'],
                    'source' => $v['source'],
                    'create_time' => $v['create_time'],
                    'pay_method' => $v['pay_method'],
                    'remark' => $v['remark'],
                ];
            }
            $data['order_detail'][] = [
                'name' => $v['name'],
                'number' => $v['number'],
                'send_number' => $v['send_number'],
                'goods_price' => $v['goods_price'],
                'after_sale_price' => $v['after_sale_price'],
                'thumb_img' => $v['thumb_img'],
                'goods_id' => $v['goods_id'],
            ];
        }
        unset($ret);
        return successMsg('订单明细数据', $data);
    }

    public function isOwnOrder($store_id, $order_sn)
    {
        $ret = $this->alias('a')->field('a.order_id, a.status_unpack, b.status')
            ->join('order b', 'a.order_id=b.order_id', 'INNER')
            ->where(['a.store_id'=>$store_id, 'b.order_sn'=>$order_sn])
            ->find();
        if(!$ret){
            return errorMsg('订单不存在');
        }
        $data = [
            'order_id'=>$ret->getAttr('order_id'),
            'status_unpack'=>$ret->getData('status_unpack'),
            'pay_status'=>$ret->getAttr('status'),
        ];
        return successMsg('订单存在', $data);

    }

    public function setStatusUnpack($store_id, $order_id, $status)
    {
        //设置订单状态$su = [1=>'待仓库拣货', 2=>'仓库拣货', 3=>'已出库', 4=>'发货中', 5=>'已发货', 6=>'已完成'];
        $where = ['order_id'=>$order_id, 'store_id'=>$store_id];
        $where_status = '';
        if($status<=1&&$status>5){
            return errorMsg('不允许修改订单状态');
        }
        if($status==2){
            $where_status = '(status_unpack=1 or status_unpack=3)';
        }
        if($status==3){
            $where_status = '(status_unpack=2 or status_unpack=4)';
        }
        if($status==4){
            $where_status = 'status_unpack=3';
        }
        if($status==5){
            $where_status = 'status_unpack=4';
        }
        $ret = $this->where($where)->where($where_status)->setField('status_unpack', $status);
        if($ret){
            return successMsg('设置订单状态成功',['status_unpack'=>$status]);
        }
        return errorMsg('设置订单状态失败');
    }

    public function getOrderExpress($store_id, $order_sn)
    {
        $ret = $this->alias('a')
            ->field('a.status_unpack, b.*, c.express_name, c.express_code, c.id as express_id')
            ->join('order b', 'a.order_id=b.order_id', 'INNER')
            ->join('express c', 'c.order_id=b.order_id', 'LEFT')
            ->where(['b.order_sn'=>$order_sn, 'a.store_id'=>$store_id])
            ->select();
        if( count($ret)<=0 ){
            return errorMsg('订单不存在');
        }
        return successMsg('查询物流信息成功',['data'=>$ret]);
    }

    /**
     * 设置出仓发货数量
     * @param $store_id int : 1
     * @param $order_id int : 1
     * @param $goods array : ['goods_id':1, 'send_number':1]
     * @return array
     */
    public function setDelivery($store_id, $order_id, $goods)
    {
        $data = $this->alias('a')->field('a.status_unpack, b.number, b.send_number, b.goods_id')
            ->join('order_detail b', 'a.order_id=b.order_id and a.store_id=b.store_id', 'INNER')
            ->where(['a.order_id'=>$order_id ,'a.store_id'=>$store_id])
            ->select();
        if( count($data)<=0 ){
            return errorMsg('订单不存在');
        }
        $send_number = 0;
        try{
            foreach($data as $k=>$v){
                if($k==0){
                    if( !in_array($v->getData('status_unpack'), [1, 2, 3, 4, 5, 6]) ){
                        return errorMsg('已不可发货');
                    }
                    if( in_array($v->getData('status_unpack'), [1, 2]) ){
                        $this->where(['order_id'=>$order_id,'store_id'=>$store_id])->setField(['status_unpack'=>3]);
                    }
                }
                if($v['number']==$v['send_number']){
                    continue;
                }
                foreach($goods as $val){
                    if($v['goods_id']==$val['goods_id']){
                        if(
                            $val['send_number']>0&&($val['send_number']+$v['send_number']<=$v['number'])
                        ){
                            $this->alias('a')
                                ->join('order_detail b', 'a.order_id=b.order_id and a.store_id=b.store_id', 'INNER')
                                ->where(['a.order_id'=>$order_id ,'a.store_id'=>$store_id, 'b.goods_id'=>$val['goods_id']])
                                ->inc('b.send_number', $val['send_number'])
                                ->update();
                            $send_number += $val['send_number'];
                        }
                    }
                }
            }
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return errorMsg('出仓失败');
        }
        if($send_number<=0){
            $this->where(['order_id'=>$order_id,'store_id'=>$store_id])->setField(['status_unpack'=>2]);
            return errorMsg('出仓数量有误');
        }
        return successMsg('货物出仓');
    }

    /**
     * 设置订单为仓库拣货状态
     * @param $store_id
     * @param $order_id
     * @return array
     */
    public function setPick($store_id, $order_id)
    {
        $ret = $this->where(['order_id'=>$order_id, 'store_id'=>$store_id, 'status_unpack'=>1])
            ->setField(['status_unpack'=>2]);
        if($ret){
            return successMsg('仓库拣货');
        }
        return errorMsg('仓库拣货');
    }

}
