<?php
namespace app\purchase\controller;
class Order extends \common\controller\UserBase
{
    //生成订单
    public function generate()
    {
        if (!request()->isPost()) {
            return errorMsg('请求方式错误');
        }
        $goodsList = $_POST['goodsList'];
        if (empty($goodsList)) {
            return errorMsg('请求数据不能为空');
        }
        //计算订单总价
        $modelGoods = new \app\purchase\model\Goods();
        $amount = 0;
        foreach ($goodsList as $k => &$v) {
            $config = [
                'where' => [
                    ['g.status', '=', 0],
                    ['g.id', '=', $v['goods_id']],
                ], 'field' => [
                    'g.id', 'g.name', 'g.sale_price', 'g.retail_price', 'g.store_id'
                ],
            ];
            $goodsInfo = $info = $modelGoods->getInfo($config);;
            $goodsNum = intval($v['num']);
            $goodsList[$k]['price'] = $goodsInfo['sale_price'];
            $totalPrice = $goodsInfo['sale_price'] * $goodsNum;
            $amount += number_format($totalPrice, 2, '.', '');
        }
        $modelOrder = new \app\purchase\model\Order();
        $modelOrderDetail = new \app\purchase\model\OrderDetail();
        $modelLogistics = new \app\purchase\model\Logistics();
        //开启事务
        $modelOrder->startTrans();
        //订单编号
        $orderSN = generateSN();
        //生成订单
        $data = [];
        $data['sn'] = $orderSN;
        $data['user_id'] = $this->user['id'];
//        $_POST['logistics_id'] = $logisticsId;
        $data['amount'] = $amount;
        $data['create_time'] = time();
        $res = $modelOrder->edit($data);
        $orderId = $res['id'];
        if (!$orderId) {
            $modelOrder->rollback();
            return errorMsg($res);
        }
        //生成订单明细
        foreach ($goodsList as $item) {
            $data = [];
            $data['order_sn'] = $orderSN;
            $data['order_id'] = $orderId;
            $data['price'] = $item['price'];
            $data['num'] = $item['num'];
            $data['goods_id'] = $item['goods_id'];
            $data['user_id'] = $this->user['id'];
            $res = $modelOrderDetail->edit($data);
            if (!$res['id']) {
                $modelLogistics->rollback();
                return errorMsg($res);
            }
        }
        $modelLogistics->commit();
        return successMsg('生成订单成功', array('sn' => $orderSN));
    }

   //订单-结算页
    public function settlement()
    {
        $modelOrder = new \app\purchase\model\Order();
        $orderSn = input('sn');
        $config = [
            'where' => [
                ['o.status', '=', 0],
                ['o.sn', '=', $orderSn],
                ['o.user_id', '=', $this->user['id']],
            ],'join' => [
                ['order_detail od','od.order_sn = o.sn','left'],
                ['goods g','g.id = od.goods_id','left']
            ],'field' => [
                'o.id', 'o.sn', 'o.amount',
                'o.user_id', 'od.goods_id','od.num','od.price',
                'g.name','g.thumb_img',
            ],
        ];
        $orderInfo = $modelOrder->getList($config);
        $this ->assign('info',$orderInfo);
        $unlockingFooterCart = unlockingFooterCartConfig([3]);
        $this->assign('unlockingFooterCart', $unlockingFooterCart);
        return $this->fetch();
    }
    //确定订单
    public function confirmOrder()
    {
        if (!request()->isPost()) {
            return errorMsg('请求方式错误');
        }
        $id = input('post.id',0,'int');
        $modelOrder = new \app\purchase\model\Order();
        $data = [
            'logistics_status' => 1,
        ];
        $condition = [
            ['user_id','=',$this->user['id']],
            ['id','=',$id],
        ];
        $res = $modelOrder->edit($data,$condition);
        $sn = input('post.sn','','string');
        if(false === $res){
           return errorMsg('失败');
        }
        return successMsg('成功',array('sn'=>$sn));
    }

    //确定订单
    public function pay()
    {
        $modelOrder = new \app\purchase\model\Order();
        $orderSn = input('sn');
        $config = [
            'where' => [
                ['o.status', '=', 0],
                ['o.sn', '=', $orderSn],
                ['o.user_id', '=', $this->user['id']],
            ],'field' => [
                'o.id', 'o.sn', 'o.amount',
                'o.user_id',
            ],
        ];
        $orderInfo = $modelOrder->getInfo($config);
        $this->assign('orderInfo', $orderInfo);
        $unlockingFooterCart = unlockingFooterCartConfig([4]);
        $this->assign('unlockingFooterCart', $unlockingFooterCart);
        return $this->fetch();
    }

    //订单-详情页
    public function detail()
    {
        $unlockingFooterCart = unlockingFooterCartConfig([0,1,2]);
        $this->assign('unlockingFooterCart', $unlockingFooterCart);
        return $this->fetch();

    }


}