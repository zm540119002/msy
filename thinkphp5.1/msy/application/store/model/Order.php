<?php
/**
 * Created by PhpStorm.
 * User: Mr.wei
 * Date: 2018/5/29
 * Time: 9:39
 * 订单管理控制器
 */
namespace app\store\model;

use think\Model;

class Order extends \common\model\Base
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'order_unpack';
    // 设置当前模型的数据库连接
    protected $connection = 'db_config_factory';
    // 别名
    protected $alias = 'o';
    // 设置主键
    protected $pk = 'order_id';

    /**
     * 获取订单分页列表
     * @param $store_id
     * @return \think\Paginator
     */
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
     * 订单支付状态获取器
     * @param $value
     * @return mixed
     */
    public function getStatusAttr($value)
    {
        $status = [0=>'已取消', 1=>'待支付', 2=>'已支付', 3=>'部分支付', 4=>'已完成'];
        return $status[$value];
    }

    /**
     * 订单发货状态获取器
     * @param $v
     * @return mixed
     */
    public function getStatusUnpackAttr($v)
    {
        $su = [1=>'待仓库拣货', 2=>'部分拣货中', 3=>'全部拣货中', 4=>'部分发货中', 5=>'全部发货中', 6=>'部分发货',
            7=>'已发货', 8=>'部分发货', 9=>'全部发货'];
        return $su[$v];
    }

    /**
     * 获取支付方式获取器
     * @param $value
     * @return mixed
     */
    public function getPayMethodAttr($value)
    {
        $pay = [ ''=>'未支付', 0=>'未支付', 1=>'银联支付', 2=>'支付宝支付', 3=>'微信支付'];
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

    /**
     * 是否拥有订单
     * @param $store_id
     * @param $order_sn
     * @return array
     */
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

    /**
     * 查询订单物流信息
     * @param $store_id
     * @param $order_sn
     * @return array
     */
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
    public function setDeliveryGoods($store_id, $order_id, $goods)
    {
        if(!is_array($goods)||count($goods)<=0){  //var_dump($goods);
            return errorMsg('拣货数据有误');
        }
        $data = $this->alias('a')->field('a.status_unpack, b.number, b.send_number, b.goods_id')
            ->join('order_detail b', 'a.order_id=b.order_id and a.store_id=b.store_id', 'INNER')
            ->where(['a.order_id'=>$order_id ,'a.store_id'=>$store_id])
            ->select();
        if( count($data)<=0 ){
            return errorMsg('订单不存在');
        }
        $number = $send_number = $new_send_number = $status_unpack  = $status = 0;
        try{
            foreach($data as $k=>$v){
                if($k==0){
                    $status = $v->getData('status_unpack');
                    if( !in_array($status, [1, 2, 6, 8]) ){
                        return errorMsg('已不可发货');
                    }
                }
                $number += $v['number'];
                $send_number += $v['send_number'];
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
                            $new_send_number += $val['send_number'];
                        }else{
                            return errorMsg('拣货数量有误');
                        }
                    }
                }
            }
            if($new_send_number<=0){
                return errorMsg('拣货数量有误');
            }
            if($status==1){
                if($number==$send_number){
                    $status_unpack = 3;
                }else{
                    $status_unpack = 2;
                }
            }
            if($status==2){
                if($number==$send_number){
                    $status_unpack = 3;
                }
            }
            if($status==6){
                if($number==$send_number){
                    $status_unpack = 9;
                }else{
                    $status_unpack = 8;
                }
            }
            if($status==8){
                if($number==$send_number){
                    $status_unpack = 9;
                }
            }
            if($status>0&&$status_unpack>0){
                $this->where(['order_id'=>$order_id,'store_id'=>$store_id])->setField(['status_unpack'=>$status_unpack]);
            }
            $this->commit();
            return successMsg('出仓成功');
        } catch (\Exception $e) {
            $this->rollback();
            return errorMsg('出仓失败');
        }
    }

    /**
     * 设置出仓状态
     * @param $store_id
     * @param $order_id
     * @return array
     */
    public function setDelivery($store_id, $order_id)
    {
        $where = ['order_id'=>$order_id, 'store_id'=>$store_id];
        $isExpress = $this->express()->where($where)->count();
        if($isExpress<=0){
            return errorMsg('请填写物流信息在发货');
        }
        $data = $this->where($where)->field('status_unpack')->find();
        if(is_null($data)){
            return errorMsg('订单不存在');
        } 
        $status_unpack = $data->getData('status_unpack');
        if( !in_array($status_unpack, [2, 3, 6, 8, 9]) ){
            return errorMsg('此订单状态下不可发货');
        }
        switch( $status_unpack ){
            case 2: $status_unpack = 6; break;
            case 3: $status_unpack = 7; break;
            case 6: $status_unpack = 8; break;
            case 8: $status_unpack = 6; break;
            case 9: $status_unpack = 7; break;
        }
        $ret = $this->where(['order_id'=>$order_id, 'store_id'=>$store_id])
            ->setField(['status_unpack'=>$status_unpack]);
        if($ret){
            return successMsg( $this->getStatusUnpackAttr($status_unpack) );
        }
        return errorMsg('发货失败');
    }

    /**
     * -对多关联
     * @return \think\model\relation\HasMany
     */
    public function express()
    {
        return $this->hasMany('Express', 'id', 'order_id');
    }
}
