<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/21
 * Time: 14:17
 *  订单模块
 */
namespace  app\store\model;

use think\Model;
use think\Db;

class Order extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'orders';
    // 设置当前模型的数据库连接
    protected $connection = 'db_config_factory';
    // 设置主键
    protected $pk = 'order_id';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 添加订单
     *
     * @param $user_id
     * @param $address_id
     * @return array
     */
    public function addOrder($user_id, $address_id)
    {
       $detail = Db::table('msy_factory.cart')->alias('a')
           ->join('msy_factory.goods b', 'a.goods_id=b.goods_base_id', 'INNER')
           ->join('msy_factory.goods_base c', 'b.goods_base_id=c.id', 'INNER')
           ->field('a.goods_id, a.number, a.store_id, b.sale_price, c.name, c.thumb_img')
           ->where(['a.user_id'=>$user_id, 'a.status'=>1])
           ->select();
        if(!$detail||!is_array($detail)||count($detail)<=0){
            return errorMsg('请添加结算商品');
        }
        $amount = 0.00;
        $sql = '';
        $order_sn = '2018052400001';
        foreach($detail as $v){
            if($v['sale_price']<=0.00){
                return errorMsg($v['name'].'销售价格有误，不能购买！');
            }
            $amount +=$v['number']*$v['sale_price'];
            $sql = ";insert into msy_factory.order_detail(order_sn, goods_id, number, goods_price, store_id, thumb_img)
           values('{$order_sn}',{$v['goods_id']},{$v['number']},{$v['sale_price']},{$v['store_id']},'{$v['thumb_img']}');";
        }
        $sql = trim($sql, ';');
        Db::startTrans();
        try{
            Order::create([
                'order_sn' => $order_sn,
                'amount' => $amount,
                'user_id' => $user_id,
                'source' => 'PC',
                'address_id' => $address_id,
                'remark' => '星期六派送',
                'create_time' => time(),
            ], ['order_sn', 'amount', 'user_id', 'source', 'address_id', 'remark', 'create_time'], true);
            Db::execute($sql);
            Db::commit();
            return successMsg('添加订单成功');
        } catch (\Exception $e) {
            Db::rollback(); // 回滚事务
            return errorMsg('添加订单失败');
        }
    }

}