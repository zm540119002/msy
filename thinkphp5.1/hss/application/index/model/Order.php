<?php
namespace app\index\model;

class Order extends \common\model\Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'order';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_1';
	//表的别名
	protected $alias = 'o';
	    /**
     * @param $data
     * 普通订单支付回调
     */
	public function orderHandle($data,$orderInfo){
		//更新订单状态
		$data2 = [];
		$data2['order_status'] = 2;
		$data2['pay_code'] = $data['pay_code'];
		$data2['pay_sn'] = $data['pay_sn'];
		$data2['payment_time'] = $data['payment_time'];
		$condition = [
			['user_id', '=', $orderInfo['user_id']],
			['sn', '=', $data['order_sn']],
		];
		$res = $this->allowField(true)->save($data2,$condition);
		if($res === false){
			//返回状态给微信服务器
			return errorMsg('失败');
		}
//        //根据订单号查询关联的商品
//        $modelOrderDetail = new \app\index\model\OrderDetail();
//        $config = [
//            'where' => [
//                ['od.status', '=', 0],
//                ['od.father_order_id', '=', $orderInfo['id']],
//            ], 'field' => [
//                'od.goods_id', 'od.price', 'od.num', 'od.store_id','od.father_order_id','od.user_id'
//            ]
//        ];
//
//        $orderDetailList = $modelOrderDetail->getList($config);
//        $modelOrderChild = new \app\index\model\OrderChild();
//
//        //生成子订单
//        $rse = $modelOrderChild -> createOrderChild($orderDetailList);
//        if(!$rse['status']){
//            $this->rollback();
//            return errorMsg($this->getError());
//        }
		//返回状态给微信服务器
		return successMsg('成功');
	}

	/**
     * 获取各类的订单总数量
     */
    public function statusSum($user_id){
        if(!$user_id){
            return [];

        }else{
            $condition = [
                'field' => [
                    'order_status','count(*) sum'
                ],'where' => [
                    ['order_status','<>',0],
                    ['status','=',0],
                    ['user_id','=',$user_id],
                ],'group' => 'order_status'
            ];
            $data = $this->getList($condition);
            $list = [];
            foreach($data as $k => $v){
                if($v['sum']){
                    $list[$v['order_status']] = $v['sum'];
                }
            }
            if($list[2]+$list[3]){
                $list[2] = $list[2]+$list[3];
            }
            unset($list[3]);

            return $list;
        }
    }
	
}
