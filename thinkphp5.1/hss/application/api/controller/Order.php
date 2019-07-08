<?php
namespace app\api\controller;
class Order extends \common\controller\UserBase
{
    //生成订单
    public function generate()
    {
        $memberModel = new \app\api\model\Member();
        if(!$member = $memberModel->getMemberInfo($this->user['id'])){
           $data = [
                'user_id'=>$this->user['id'],
                'create_time'=>time(),
                'update_time'=>time(),
            ];
            $memberModel->edit($data);
            $member['type'] = config('custom.member_level.1.level');
        }

        if (!request()->isPost()) {
            $this->errorMsg('请求方式错误');
        }

        $goodsList = input('post.data/a');
        if (empty($goodsList)) {
            $this->errorMsg('请求数据不能为空');
        }

        $order_type = input('post.product_type/d');

        $goodsIds = array_column($goodsList,'goods_id','goods_id');

        if(empty($goodsIds)){
            $this->errorMsg('请求数据不能为空');
        }

        // 订单
        if( $order_type==2 ){

            $promotion = reset($goodsList);
            $modelPromotionGoods = new \app\api\model\PromotionGoods();
            $config = [
                'where' => [
                    ['p.status', '=', 0],
                    ['pg.promotion_id', '=', $promotion['goods_id']],
                ], 'field' => [
                    'pg.goods_id',"pg.goods_num*{$promotion['num']} num",'p.name',"p.franchise_price*{$promotion['num']} price",'p.id','p.belong_to_member_buy','p.is_company_info'
                ],'join' => [
                    ['promotion p','pg.promotion_id=p.id','left']
                ]
            ];
            $goodsList= $modelPromotionGoods->getList($config);

            if(empty($goodsList)){
                $this->errorMsg('套餐已失效 !');
            }
            $promotion = reset($goodsList);

            $goodsIds = array_column($goodsList,'goods_id');

            // 购买权限
            if( !($promotion['belong_to_member_buy']&$member['type']) ){

                $error = config("code.error.for_members_only");
                $this->errorMsg($error['msg'][$promotion['belong_to_member_buy']], $error);
            }
            
            // 是否需要验证公司信息
            if( ($member['type']==config('custom.member_level.1.level')) && $promotion['is_company_info'] ) {
                $modelCompany = new \app\api\model\Franchise();

                $condition = [
                    'field' => [
                        'id'
                    ],
                    'where' => [
                        ['user_id','=',$this->user['id']],
                        ['status','=',0]
                    ]
                ];
                $res = $modelCompany->getInfo($condition);

                if (!$res) {
                    $error = config('code.error.need_beforehand_register');
                    $this->errorMsg($error['msg'], $error);
                }
            }

        }

        // 更新套餐总价
        $config = [
            'where' => [
                ['g.status', '=', 0],
                ['g.id', 'in', $goodsIds],
            ], 'field' => [
                'g.id as goods_id','g.name','g.headline','g.thumb_img','g.franchise_price','g.specification','g.sample_price','g.belong_to_member_buy',
                'g.purchase_unit','g.store_id'
            ]
        ];

        //计算订单总价
        $modeGoods = new \app\api\model\Goods();
        $goodsListNew = $modeGoods->getList($config);

        if(empty($goodsListNew)){
            $this->errorMsg('商品已失效');
        }

        $permission = false;
        $amount = 0;
        foreach ($goodsList as $k1 => &$goodsInfo) {
            foreach ($goodsListNew as $k2 => &$goodsInfoNew) {

                if($goodsInfo['goods_id'] == $goodsInfoNew['goods_id']){

                    // 商品购买权限
                    if(  ($order_type!=2) && (!($goodsInfoNew['belong_to_member_buy']&$member['type'])) ){
//                    $error = config("code.error.for_members_only");
//                    $this->errorMsg($error['msg'][$goodsInfoNew['belong_to_member_buy']], $error);
                        unset($goodsList[$k1]);
                        $permission = true;
                        break;
                    }

                    $goodsList[$k1]['headline'] = $goodsInfoNew['headline'];
                    $goodsList[$k1]['thumb_img'] = $goodsInfoNew['thumb_img'];
                    $goodsList[$k1]['specification'] = $goodsInfoNew['specification'];
                    $goodsList[$k1]['purchase_unit'] = $goodsInfoNew['purchase_unit'];
                    $goodsList[$k1]['store_id'] = $goodsInfoNew['store_id'];
                    $goodsList[$k1]['name'] = $goodsInfoNew['name'];
                    switch ($goodsInfo['buy_type']){
                        case 2:
                            $goodsList[$k1]['price'] = $goodsInfoNew['sample_price'];
                             break;
                        default:
                            $goodsList[$k1]['price'] = $goodsInfoNew['franchise_price'];
                            break;
                    }
                    $totalPrices = $goodsInfo['num'] * $goodsList[$k1]['price'];
                    $amount += number_format($totalPrices, 2, '.', '');
                }
            }
        }

        $modelOrder = new \app\api\model\Order();
        $modelOrderDetail = new \app\api\model\OrderDetail();
        //开启事务
        $modelOrder->startTrans();
        //订单编号
        $orderSN = generateSN();
        //组装父订单数组

        if(isset($promotion)){
            $data = [
                'sn' => $orderSN,
                'user_id' => $this->user['id'],
                'amount' => $promotion['price'],
                'actually_amount' => $promotion['price'],
                'create_time' =>  time(),
                'type' => 2,
                'type_id' => $promotion['id'],
            ];

        }else{
            $data = [
                'sn' => $orderSN,
                'user_id' => $this->user['id'],
                'amount' => $amount,
                'actually_amount' => $amount,
                'create_time' =>  time(),
                'type' => 1,
            ];
        }

        //生成父订单
        $res = $modelOrder->allowField(true)->save($data);
        if (!$res) {
            $modelOrder->rollback();
            $this->errorMsg('失败');
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
            $dataDetail[$item]['buy_type'] = $goodsInfo['buy_type'] ? $goodsInfo['buy_type'] : 1;
            $dataDetail[$item]['brand_name'] = $goodsInfo['brand_name'] ? $goodsInfo['brand_name'] : '';
            $dataDetail[$item]['brand_id'] = $goodsInfo['brand_id'] ? $goodsInfo['brand_id'] : 0;
            $dataDetail[$item]['goods_img'] = $goodsInfo['thumb_img'];
            $dataDetail[$item]['goods_name'] = $goodsInfo['name'];
            $dataDetail[$item]['specification'] = $goodsInfo['specification'];
        }

        if( empty($goodsList)&&$permission){
            $error = config("code.error.for_members_only.msg.6");
            $this->errorMsg($error);
        }

        //生成订单明细
        $res = $modelOrderDetail->allowField(true)->saveAll($dataDetail)->toArray();
        if (!count($res)) {
            $modelOrder->rollback();
            $this->errorMsg('失败');
        }
        $modelOrder->commit();

        $data = [
            'code'=> config('code.success.default.code'),
            'url' => url('Order/confirmOrder',['order_sn'=>$orderSN]),
        ];
        $this->successMsg('生成订单成功',$data);
    }

    // 订单确认页
    public function confirmOrder()
    {
        if (request()->isPost()) {
            // 更新订单状态并清除订单里购物车里的商品
            $fatherOrderId = input('post.order_id',0,'int');

            $modelOrder = new \app\api\model\Order();
            $condition = [
                'where' => [
                    ['user_id','=',$this->user['id']],
                    ['id','=',$fatherOrderId],
                ],'field' => [
                    'o.id', 'o.sn', 'o.amount', 'o.user_id', 'order_status'
                 ],
            ];

            $orderInfo  = $modelOrder->getInfo($condition);

            if(!$orderInfo){
                //return errorMsg('没有此订单',['code'=>1]);
                $this->errorMsg('没有此订单',['code'=>1]);
            }
            if($orderInfo['order_status']>1){
                //return errorMsg('订单已支付',['code'=>1]);
                $this->errorMsg('订单已支付',['code'=>1]);
            }

            $data = input('post.');
            $data['order_status'] = 1;
            $modelOrder ->startTrans();
            $address = json_decode($data['address'],true);

            if(!$address){
                //return false;
                $this->errorMsg('请先选择收货地址 ');
            }

            unset($data['address']);
            $data = array_merge($data,$address);

            $res = $modelOrder -> allowField(true) -> save($data,$condition['where']);

            //根据订单号查询关联的购物车的商品
            if(false !== $res){
                $model = new \app\api\model\Cart();
                $res = $model->clearCartGoodsByOrder($fatherOrderId,$this->user['id']);
            }

            if(false === $res){
                $modelOrder ->rollback();
                ///return errorMsg('失败');
                $this->errorMsg('失败');
            }

            $modelOrder -> commit();
            $data = [
                'code'=> config('code.success.default.code'),
                //'url' => url('Order/confirmOrder',['order_sn'=>$orderSN]),
            ];
            //return successMsg( '成功');
            $this->successMsg('成功',$data);

        }else{
            $orderSn = input('order_sn/s');

            if(empty($orderSn)){
                $this->error('没有找到该订单');
            }

            $where = [
                ['o.status', '=', 0],
                ['o.sn', '=', $orderSn],
                ['o.user_id', '=', $this->user['id']],
            ];

            $this->assignOrderInfo($where);

/*            $config = [
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

            $modelOrder = new \app\api\model\Order();
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

            $this->assignWalletInfo();*/

            return $this->fetch('confirm_order');
        }
    }

    // 去结算
    public function toPay()
    {
        if (!request()->isPost()) {
            return errorMsg('请求方式错误');
        }
        $postData = input('post.');
        $modelOrder = new \app\api\model\Order();
        $condition = [
            'where' => [
                ['user_id','=',$this->user['id']],
                ['sn','=',$postData['order_sn']],
                ['order_status','<',2],
            ], 'field'=>[
                'id','sn','actually_amount'
            ]
        ];
        $orderInfo  = $modelOrder->getInfo($condition);
        //先查找支付表是否有数据
        $modelPay = new \app\api\model\Pay();
        $condition = [
            'where' => [
                ['user_id','=',$this->user['id']],
                ['sn','=',$orderInfo['sn']],
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
                $modelPay ->rollback();
                return errorMsg('失败');
            }

        }else{
            //修改
            $updateData = [
                'actually_amount' =>$orderInfo['actually_amount'],
                'pay_code' => $postData['pay_code'],
            ];
            $where = [
                'sn' => $orderInfo['sn'],
                'user_id' => $this->user['id'],
                'status' => 0,
            ];
            $result  = $modelPay->isUpdate(true)->save($updateData,$where);
            if($result === false){
                $modelPay ->rollback();
                return errorMsg('失败');
            }
        }
        // 各支付方式的处理方式 //做到这里
        switch($postData['pay_code']){
            // 支付中心处理
            case config('custom.pay_code.WeChatPay.code') :
            case config('custom.pay_code.Alipay.code') :
            case config('custom.pay_code.UnionPay.code') :
                $url = config('custom.pay_gateway').$orderInfo['sn'];
                break;
        }
        return successMsg( '成功',['url'=>$url]);

    }

    //订单管理
    public function manage(){
        if(input('?order_status')){
            $orderStatus = input('order_status');
            $this ->assign('order_status',$orderStatus);
        }
        $modelOrder = new \app\api\model\Order();
        $data = $modelOrder->statusSum($this->user['id']);
        $this->assign('orderStatusSum',$data);

       return $this->fetch();
    }

    //订单-详情页
    public function detail()
    {


        $orderSn = input('order_sn/s');

        if(!$orderSn){
            $this->error('没有找到该订单');
        }

        $where = [
            ['o.status', '=', 0],
            ['o.order_status', '<>', 0],
            ['o.user_id', '=', $this->user['id']],
            ['o.sn', '=', $orderSn],
        ];

        $this->assignOrderInfo($where);
        return $this->fetch();
    }

    /**
     * 设置状态
     */
    public function setOrderStatus(){

        if(!request()->isPost()){
            return config('custom.not_post');
        }

        $id = input('post.order_id/d');
        $sn = input('post.order_sn/s');
        $orderStatus = input('post.order_status/d');

        if( $id && $sn){
            $where = [
                'where' => [
                    ['id','=',$id],
                    ['sn','=',$sn],
                    ['user_id','=',$this->user['id']],
                ]
            ];

            $model = new \app\api\model\Order();
            $orderInfo = $model->getInfo($where);

            $type = true;
            // 订单状态：1:待付款 2:待发货 3:待收货 4:待评价 5:已完成 6:已取消
            switch($orderStatus){
                case 4 : // 去确定收货
                    $where[] = ['order_status','in','2,3'];
                    $msg = '收货成功';
                    break;
                case 6 : // 取消订单
                    $where[] = ['order_status','=',6];
                    $msg = '订单取消成功';
                    break;
                default:
                    $type = false;
                    break;
            }

            if($type){

                $data = [
                    'order_status' => $orderStatus,
                ];
                $rse = $model->where($where['where'])->setField($data);

                if($rse){
                    $result = [
                        'code'=> config('code.success.default.code'),
                        'url' => url('order/detail',['order_sn'=>$orderInfo['sn']]),
                    ];
                    $this->successMsg($msg,$result);
                }
            }

        }
        $this->errorMsg('失败');


        //$this->successMsg('收货成功 ！',config('code.success.default'));
    }

    /**
     * @return array|mixed
     * 查出产商相关产品 分页查询
     */
    public function getList(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }
        $model = new \app\api\model\Order();
        $config=[
            'where'=>[
                ['o.status', '=', 0],
                ['o.user_id', '=', $this->user['id']],
            ],
            'field'=>[
                'o.id','o.pay_sn','o.sn','o.order_status','o.pay_code','o.amount','o.actually_amount','o.remark',
                'o.consignee','o.mobile','o.province','o.city','o.area','o.detail_address','o.create_time','o.payment_time',
                'o.finished_time','o.complete_address'
            ],'order' => [
                'o.create_time'=>'desc',
            ],
        ];
        if(input('?get.order_status') && input('get.order_status/d')){
            $order_status = input('get.order_status/d');
            if($order_status==2){
                $config['where'][] = ['o.order_status', 'in', '2,3'];
            }else{
                $config['where'][] = ['o.order_status', '=', $order_status];
            }

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
            $modelOrderDetail = new \app\api\model\OrderDetail();
            $config=[
                'field' => [
                    'od.goods_id','od.price', 'od.num', 'od.buy_type','od.brand_id','od.brand_name',
                    'g.name','g.thumb_img',
                ], 'where' => [
                    ['od.status', '=', 0],
                    ['od.father_order_id','=',$item['id']]
                ], 'join'  => [
                    ['goods g','g.id = od.goods_id','left'],
                ]
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
        $modelWallet = new \app\api\model\Wallet();
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

    /**
     * 详情
     */
    private function assignOrderInfo($where=[]){

        $config=[
            'where'=>[
                ['o.status', '=', 0],
                ['o.user_id', '=', $this->user['id']],
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
        if($where){
            $config['where'] = $where;
        }

        $model= new \app\api\model\Order();
        $info = $model->getInfo($config);

        if(empty($info)){
            $this->error('没有找到该订单');
        }

        $info =  $info!=0?$info->toArray():[];
        $modelOrderDetail = new \app\api\model\OrderDetail();
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

        $this->assign('orderInfo',$info);

        // 显示的地址信息
        $this->getOrderAddressInfo($info);

        // 钱包余额
        $this->assignWalletInfo();

        $unlockingFooterCart = false;
        // 底部按钮
        // 0：临时 1:待付款 2:待收货(没有待发货) 3:待收货 4:待评价 5:已完成 6:已取消',
        switch ($info['order_status'])
        {
            case 0:
                $configFooter = [0,20];
                $unlockingFooterCart = unlockingFooterCartConfigTest($configFooter);
                array_push($unlockingFooterCart['menu'][0]['class'],'group_btn70');
                array_push($unlockingFooterCart['menu'][1]['class'],'group_btn30');
                break;
            case 1:
                $configFooter = [0,23,20];
                $unlockingFooterCart = unlockingFooterCartConfigTest($configFooter);
                array_push($unlockingFooterCart['menu'][0]['class'],'group_btn30');
                array_push($unlockingFooterCart['menu'][1]['class'],'group_btn30');
                array_push($unlockingFooterCart['menu'][2]['class'],'group_btn30');
                break;
            case "2":
            case "3":
                $configFooter = [12];
                break;
            case "4":
                $configFooter = [13];
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

        if(!$unlockingFooterCart){
            $unlockingFooterCart = unlockingFooterCartConfigTest($configFooter);

            $num = floor(100/count($configFooter));

            foreach($configFooter as $k => $v){
                array_push($unlockingFooterCart['menu'][$k]['class'],'group_btn'.$num);
            }
        }
/*        p($unlockingFooterCart);
        exit;*/
        $this->assign('unlockingFooterCart',json_encode($unlockingFooterCart));
    }


}