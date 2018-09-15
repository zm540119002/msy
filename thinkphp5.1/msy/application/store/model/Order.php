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

    public function getOrderList($factory_id)
    {
        return $this->alias('a')->field('a.pay_money, a.order_id, a.status_unpack, b.order_sn, b.status, 
            b.create_time, b.source, b.pay_method, b.remark, c.consignee, c.phone, c.detail')
            ->join('order b', 'a.order_id=b.order_id', 'INNER')
            ->join('address_static c', 'b.order_id=c.order_id', 'INNER')
            ->where(['a.factory_id'=>$factory_id])
            ->paginate(2);
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
     * @param $factory_id
     * @param $order_id
     * @return array
     */
    public function getOrderDetail($factory_id, $order_id)
    {
        $ret = $this->alias('a')->field('a.pay_money, a.order_id, a.status_unpack, b.order_sn, b.status, 
            b.create_time, b.source, b.pay_method, b.remark, c.consignee, c.phone, c.detail, d.*')
            ->join('order b', 'a.order_id=b.order_id', 'INNER')
            ->join('address_static c', 'b.order_id=c.order_id', 'INNER')
            ->join('order_detail d', 'c.order_id=d.order_id', 'INNER')
            ->where(['a.order_id'=>$order_id, 'a.factory_id'=>$factory_id, 'd.factory_id'=>$factory_id])
            ->select();
        if(count($ret)<=0){
            errorMsg('订单数据有误');
        }
        $data = []; //'' => $v[''],
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

    public function isOwnOrder($factory_id, $order_sn)
    {
        $ret = $this->alias('a')->field('a.order_id, a.status_unpack, b.status')
            ->join('order b', 'a.order_id=b.order_id', 'INNER')
            ->where(['a.factory_id'=>$factory_id, 'b.order_sn'=>$order_sn])
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

    public function setStatusUnpack($factory_id, $order_id, $status)
    {
        //设置订单状态$su = [1=>'待仓库拣货', 2=>'仓库拣货', 3=>'已出库', 4=>'发货中', 5=>'已发货', 6=>'已完成'];
        $where = ['order_id'=>$order_id, 'factory_id'=>$factory_id];
        $where_status = 's';
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

    public function getOrderExpress($factory_id, $order_sn)
    {
        $ret = $this->alias('a')->field('a.status_unpack, b.*, c.express_name, c.express_code')
            ->join('order b', 'a.order_id=b.order_id', 'INNER')
            ->join('express c', 'c.order_id=b.order_id', 'LEFT')
            ->where(['b.order_sn'=>$order_sn, 'a.factory_id'=>$factory_id])
            ->select();
        if( count($ret)<=0 ){
            return errorMsg('订单不存在');
        }
        return successMsg('查询物流信息成功',['data'=>$ret]);
    }

}
