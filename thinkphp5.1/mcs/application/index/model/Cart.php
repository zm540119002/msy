<?php
namespace app\index\model;

class Cart extends \common\model\Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'cart';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_1';
	//表的别名
	protected $alias = 'c';

    /**
     * 根据订单清除购物车的商品
     * @param $order_id string 订单ID
     * @return bool
     */
    public function clearCartGoodsByOrder($order_id){
        //根据订单号查询关联的购物车的商品
        $modelOrderDetail = new \app\index\model\OrderDetail();
        $config = [
            'where' => [
                ['od.status', '=', 0],
                ['od.father_order_id', '=', $order_id],
            ], 'field' => [
                'od.goods_id','od.buy_type','od.price', 'od.num', 'od.store_id','od.father_order_id','od.user_id',
            ]
        ];
        $orderDetailList = $modelOrderDetail->getList($config);

        foreach ($orderDetailList as &$orderDetailInfo){
            $condition = [
                ['user_id','=',$this->user['id']],
                ['foreign_id','=',$orderDetailInfo['goods_id']],
                ['buy_type','in',$orderDetailInfo['buy_type']],
            ];
            $result = $this -> del($condition,false);
            if(!$result['status']){
                return false;
            }
        }
        return true;
    }
}