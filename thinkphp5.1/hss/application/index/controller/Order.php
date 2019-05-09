<?php
namespace app\index\controller;
class Order extends \common\controller\UserBase
{
    //生成订单
    public function generate()
    {
        if (!request()->isPost()) {
            return errorMsg('请求方式错误');
        }
        $modelOrder = new \app\index\model\Order();
        $modelOrderDetail = new \app\index\model\OrderDetail();
        $goodsList = input('post.goodsList/a');
        if (empty($goodsList)) {
            return errorMsg('请求数据不能为空');
        }
        $goodsIds = array_column($goodsList,'goods_id');
        $config = [
            'where' => [
                ['g.status', '=', 0],
                ['g.id', 'in', $goodsIds],
            ], 'field' => [
                'g.id as goods_id','g.headline','g.thumb_img','g.bulk_price','g.specification','g.sample_price',
                'g.purchase_unit','g.store_id'
            ]
        ];
        //计算订单总价
        $modeGoods = new \app\index\model\Goods();
        $goodsListNew = $modeGoods->getList($config);
        $amount = 0;
        foreach ($goodsList as $k1 => &$goodsInfo) {
            foreach ($goodsListNew as $k2 => &$goodsInfoNew) {
                if($goodsInfo['goods_id'] == $goodsInfoNew['goods_id']){
                    $goodsList[$k1]['headline'] = $goodsInfoNew['headline'];
                    $goodsList[$k1]['thumb_img'] = $goodsInfoNew['thumb_img'];
                    $goodsList[$k1]['specification'] = $goodsInfoNew['specification'];
                    $goodsList[$k1]['purchase_unit'] = $goodsInfoNew['purchase_unit'];
                    $goodsList[$k1]['store_id'] = $goodsInfoNew['store_id'];
                    switch ($goodsInfo['buy_type']){
                        case 1:
                            $goodsList[$k1]['price'] = $goodsInfoNew['bulk_price'];
                            break;
                        case 2:
                            $goodsList[$k1]['price'] = $goodsInfoNew['sample_price'];
                             break;
                        default:
                    }
                    $totalPrices = $goodsInfo['num'] * $goodsList[$k1]['price'];
                    $amount += number_format($totalPrices, 2, '.', '');
                }
            }
        }

        //开启事务
        $modelOrder->startTrans();
        //订单编号
        $orderSN = generateSN();
        //组装父订单数组
        $data = [
                'sn' => $orderSN,
                'user_id' => $this->user['id'],
                'amount' => $amount,
                'actually_amount' => $amount,
                'create_time' =>  time(),
        ];
        //生成父订单
        $res = $modelOrder->allowField(true)->save($data);
        if (!$res) {
            $modelOrder->rollback();
            return errorMsg('失败');
        }
        $orderId = $modelOrder ->getAttr('id');
        //组装订单明细
        $dataDetail = [];
        foreach ($goodsList as $item=>&$goodsInfo) {
            $dataDetail[$item]['father_order_id'] = $orderId;
            $dataDetail[$item]['price'] = $goodsInfo['price'];
            $dataDetail[$item]['num'] = $goodsInfo['num'];
            $dataDetail[$item]['goods_id'] = $goodsInfo['goods_id'];
            $dataDetail[$item]['user_id'] = $this->user['id'];
            $dataDetail[$item]['store_id'] = $goodsInfo['store_id'];
            $dataDetail[$item]['buy_type'] = $goodsInfo['buy_type'];
            $dataDetail[$item]['brand_name'] = $goodsInfo['brand_name'];
            $dataDetail[$item]['brand_id'] = $goodsInfo['brand_id'];
        }
        //生成订单明细
        $res = $modelOrderDetail->allowField(true)->saveAll($dataDetail)->toArray();
        if (!count($res)) {
            $modelOrder->rollback();
            return errorMsg('失败');
        }
        $modelOrder->commit();
        return successMsg('生成订单成功', array('order_sn' => $orderSN));
    }

    // 订单确认页
    public function confirmOrder()
    {echo 123;exit;
        if (request()->isPost()) {
            // 更新订单状态并清除订单里购物车里的商品
            $fatherOrderId = input('post.order_id',0,'int');


            $modelOrder = new \app\index\model\Order();
            $condition = [
                'where' => [
                    ['user_id','=',$this->user['id']],
                    ['id','=',$fatherOrderId],
                    ['order_status','<',2],
                ]
            ];

            $orderInfo  = $modelOrder->getInfo($condition);

            if(!$orderInfo){
                return errorMsg('订单已支付',['code'=>1]);
            }

            $data = input('post.');
            $data['order_status'] = 1;
            $data['pay_code'] = $data['pay_code'];
            $modelOrder ->startTrans();
            $res = $modelOrder -> allowField(true) -> save($data,$condition['where']);

            //根据订单号查询关联的购物车的商品
            if(false !== $res){
                $model = new \app\index\model\Cart();
                $res = $model->clearCartGoodsByOrder($fatherOrderId,$this->user['id']);
            }

            if(false === $res){
                $modelOrder ->rollback();
                return errorMsg('失败');
            }

            $modelOrder -> commit();
//            $orderSn = input('post.order_sn','','string');
//
//            // 各支付方式的处理方式 //做到这里
//            switch($data['pay_code']){
//                // 支付中心处理
//                case config('custom.pay_code.WeChatPay.code') :
//                case config('custom.pay_code.Alipay.code') :
//                case config('custom.pay_code.UnionPay.code') :
//                    $url = config('custom.pay_gateway').$orderSn;
//                    break;
//            }
            return successMsg( '成功');

        }else{
            $modelOrder = new \app\index\model\Order();
            $orderSn = input('order_sn/s');
            $config = [
                'where' => [
                    ['o.status', '=', 0],
                    ['o.sn', '=', $orderSn],
                    ['o.user_id', '=', $this->user['id']],
                ],'join' => [
                    ['order_detail od','od.father_order_id = o.id','left'],
                    ['goods g','g.id = od.goods_id','left']
                ],'field' => [
                    'o.id', 'o.sn', 'o.amount','o.consignee','o.mobile','o.province','o.city','o.area','o.detail_address',
                    'o.user_id', 'od.goods_id','od.num','od.price','od.buy_type','od.brand_id','od.brand_name','od.id as order_detail_id',
                    'g.headline','g.thumb_img','g.specification', 'g.purchase_unit'
                ],
            ];
            $orderGoodsList = $modelOrder->getList($config);
            if(empty($orderGoodsList)){
                $this->error('没有找到该订单');
            }


            $this ->assign('orderGoodsList',$orderGoodsList);

            $orderInfo = reset($orderGoodsList);
            // 显示地址
            $this->getOrderAddressInfo($orderInfo);

            $unlockingFooterCart = unlockingFooterCartConfig([0,111,11]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);

            $this->assignWalletInfo();

            return $this->fetch();
        }

    }

    // 去支付
    public function toPay()
    {
        if (!request()->isPost()) {
            return errorMsg('请求方式错误');
        }
        $postData = input('post.');
        $modelOrder = new \app\index\model\Order();
        $condition = [
            'where' => [
                ['user_id','=',$this->user['id']],
                ['sn','=',$postData['sn']],
                ['order_status','=',0],
            ], 'field'=>[
                'id','sn','actually_amount'
            ]
        ];
        $orderInfo  = $modelOrder->getInfo($condition);
        //先查找支付表是否有数据
        $modelPay = new \app\index\model\Pay();
        $condition = [
            'where' => [
                ['user_id','=',$this->user['id']],
                ['sn','=',$postData['sn']],
                ['pay_status','=',1],
                ['type','=',config('custom.pay_type')['orderPay']['code']]
            ], 'field'=>[
                'id','sn','actually_amount'
            ]
        ];
        $payInfo  = $modelPay->getInfo($condition);
        if(empty($payInfo)){
            //增加
            $data = [
                'sn' => $orderInfo['sn'],
                'actually_amount' =>$orderInfo['actually_amount'],
                'user_id' => $this->user['id'],
                'pay_code' => $postData['pay_code'],
                'type' => config('custom.pay_type')['orderPay']['code'],
            ];
            $result  = $modelPay->isUpdate(false)->save($data);
            if(!$result){
                return errorMsg('失败');
            }
            // 各支付方式的处理方式 //做到这里
            switch($data['pay_code']){
                // 支付中心处理
                case config('custom.pay_code.WeChatPay.code') :
                case config('custom.pay_code.Alipay.code') :
                case config('custom.pay_code.UnionPay.code') :
                    $url = config('custom.pay_gateway').$orderInfo['sn'];
                    break;
            }
            return successMsg( '成功',['url'=>$url]);
        }else{
            //修改
            $updateData = [
                'actually_amount' =>$orderInfo['actually_amount'],
                'pay_code' => $postData['pay_code'],
            ];
            $where = [
                'sn' => $orderInfo['sn'],
                'user_id' => $this->user['id'],
            ];
            $result  = $modelPay->isUpdate(true)->save($updateData,$where);
            if($result === false){
                $modelPay ->rollback();
                return errorMsg('失败');
            }
        }


    }

    //订单管理
    public function manage(){
        if(input('?order_status')){
            $orderStatus = input('order_status');
            $this ->assign('order_status',$orderStatus);
        }
       return $this->fetch();
    }

    //订单-详情页
    public function detail()
    {
        $model = new \app\index\model\Order();
        $orderSn = input('order_sn/s');
        $config=[
            'where'=>[
                ['o.status', '=', 0],
                ['o.order_status', '<>', 0],
                ['o.user_id', '=', $this->user['id']],
                ['o.sn', '=', $orderSn],
            ],
            'field'=>[
                'o.id','o.pay_sn','o.sn','o.order_status','o.pay_code','o.amount','o.actually_amount','o.remark',
                'o.consignee','o.mobile','o.province','o.city','o.area','o.detail_address','o.create_time','o.payment_time',
                'o.finished_time',
                'u.name','u.mobile_phone'
            ],'join'=>[
                ['common.user u','u.id = o.user_id','left'],
            ],'order'=>[
                'o.id'=>'desc'
            ]
        ];
        $info = $model->getInfo($config);

        if(empty($info)){
            $this->error('没有找到该订单');
        }

        $info =  $info!=0?$info->toArray():[];
        $modelOrderDetail = new \app\index\model\OrderDetail();
        $config=[
            'where'=>[
                ['od.status', '=', 0],
                ['od.father_order_id','=',$info['id']]
            ],
            'field'=>[
                'od.goods_id', 'od.price', 'od.num', 'od.buy_type','od.brand_id','od.brand_name',
                'g.name','g.thumb_img','g.specification'
            ],
            'join'=>[
                ['goods g','g.id = od.goods_id','left'],
            ],

        ];
        $goodsList = $modelOrderDetail -> getList($config);

        $info['goods_list']= $goodsList;
        $info['goods_num'] = array_sum(array_column($goodsList,'num'));


/*        $goodsNum = 0;
        foreach ($goodsList as &$goods){
            $goodsNum+=$goods['num'];
        }
        $info['goods_list'] = $goodsList;
        $info['goods_num'] = $goodsNum;*/
/*        p($info);
        exit;*/
        $this->assign('orderInfo',$info);

        // 显示的地址信息
        $this->getOrderAddressInfo($info);

        // 钱包余额
        $this->assignWalletInfo();

        // 底部按钮
        // 0：临时 1:待付款 2:待收货 3:待评价 4:已完成 5:已取消 6:售后',
        switch ($info['order_status'])
        {
            case "1":
                //$configFooter = [5];
                $configFooter = [0,111,11];
                break;
            case "2":
                $configFooter = [12];
                break;
            case "3":
                $configFooter = [13];
                break;
            case "4":
                $configFooter = [14];
                break;
            case "5":
                $configFooter = [14];
                break;
            case "6":
                $configFooter = [];
                break;
            default:
                $configFooter = [];
        }

        $unlockingFooterCart = unlockingFooterCartConfig($configFooter);
        $this->assign('unlockingFooterCart', $unlockingFooterCart);
        return $this->fetch();

    }

    /**
     * 设置状态
     */
    public function setOrderStatus(){

        if(!request()->isPost()){
            return config('custom.not_post');
        }



        $id = input('post.id/d');
        $orderStatus = input('post.order_status/d');
        if(!input('?post.id') && !$id){
            return errorMsg('失败');
        }

        $where = [
            'where' => [
                ['id','=',$id],
                ['user_id','=',$this->user['id']],
            ]
        ];
        $model = new \app\index\model\Order();
        $orderInfo = $model->getInfo($where);
        //$orderInfo['sn'] = '20190412170757362998811738229639';
        $type = true;
        switch($orderStatus){
            case 3 : // 确定收货
                $where['order_status'] = 2;
                break;
            case 5 : // 取消订单
                $where['order_status'] = 1;
                break;
            case 7 : // 申请退款
                // system_id,order_sn
                $curl = new \common\component\curl\Curl();
                $res = $curl->get('https://msy.meishangyun.com/index/Order/refundOrder',array('system_id'=>3,'order_sn'=>$orderInfo['sn']));
/*                p($res);
                exit;*/
                $res = json_decode($res,true);
                if(!$res['status'] || $res==null){
                    $type = false;
                }

                break;
        }

        if(!$type){
            return errorMsg('失败');
        }

        $data = [
            'order_status' => $orderStatus,
        ];
        $rse = $model->where($where['where'])->setField($data);
        if(!$rse){
            return errorMsg('失败');
        }
        return successMsg('成功');
    }

    /**
     * @return array|mixed
     * 查出产商相关产品 分页查询
     */
    public function getList(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }
        $model = new \app\index\model\Order();
        $config=[
            'where'=>[
                ['o.status', '=', 0],
                ['o.user_id', '=', $this->user['id']],
            ],
            'field'=>[
                'o.id','o.pay_sn','o.sn','o.order_status','o.pay_code','o.amount','o.actually_amount','o.remark',
                'o.consignee','o.mobile','o.province','o.city','o.area','o.detail_address','o.create_time','o.payment_time',
                'o.finished_time',
            ],'order'=>[
            'o.id'=>'desc'
        ]

        ];
        if(input('?get.order_status') && input('get.order_status/d')){
            $config['where'][] = ['o.order_status', '=', input('get.order_status/d')];
        }else{
            $config['where'][] = ['o.order_status', '>', 0];
        }
        if(input('?get.category_id') && input('get.category_id/d')){
            $config['where'][] = ['o.category_id_1', '=', input('get.category_id/d')];
        }
        $keyword = input('get.keyword','');
        if($keyword) {
            $config['where'][] = ['o.name', 'like', '%' . trim($keyword) . '%'];
        }

        $list = $model -> pageQuery($config)->each(function($item, $key){
            $modelOrderDetail = new \app\index\model\OrderDetail();
            $config=[
                'where'=>[
                    ['od.status', '=', 0],
                    ['od.father_order_id','=',$item['id']]
                ],
                'field'=>[
                    'od.goods_id','od.price', 'od.num', 'od.buy_type','od.brand_id','od.brand_name',
                    'g.name','g.thumb_img',
                ],
                'join'=>[
                    ['goods g','g.id = od.goods_id','left'],
                ],

            ];
            $goodsList = $modelOrderDetail -> getList($config);
            $goodsNum = 0;
            foreach ($goodsList as &$goods){
                $goodsNum+=$goods['num'];
            }
            $item['goods_list'] = $goodsList;
            $item['goods_num'] = $goodsNum;
            return $item;
        });

        $currentPage = input('get.page/d');
        $this->assign('currentPage',$currentPage);
        $this->assign('list',$list);
        if(isset($_GET['pageType'])){
            $pageType = $_GET['pageType'];
            $this->fetch($pageType);
        }
        return $this->fetch('list_tpl');
    }

    // 获取订单地址的默认值
    private function getOrderAddressInfo($orderInfo){

        // 显示地址
        if( !empty($orderInfo['mobile']) && !empty($orderInfo['consignee']) ){
            $addressInfo = $orderInfo;

        }else{
            $modelAddress =  new \common\model\Address();

            $condition = [
                'where' => [
                    ['is_default','=',1]
                ]
            ];
            $addressInfo = $modelAddress->getInfo($condition);
        }

        $this->assign('addressInfo', $addressInfo);
    }

    // 获取钱包余额
    private function assignWalletInfo(){
        //钱包
        $modelWallet = new \app\index\model\Wallet();
        $config = [
            'where' => [
                ['status', '=', 0],
                ['user_id', '=', $this->user['id']],
            ],'field' => [
                'id','amount',
            ],
        ]; // 做到这里
        $walletInfo = $modelWallet->getInfo($config);

        $this->assign('walletInfo', $walletInfo);
    }


}