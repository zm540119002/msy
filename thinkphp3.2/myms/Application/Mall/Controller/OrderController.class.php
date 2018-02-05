<?php
namespace Mall\Controller;
use Think\Controller;
use web\all\Controller\AuthUserController;


class OrderController extends AuthUserController {
    //生成订单
    public function generate(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        $goodsList = $_POST['goodsList'];
        if(empty($goodsList)){
            $this->ajaxReturn(errorMsg('请求数据不能为空'));
        }
        $orderType = intval($_POST['orderType'])?:0;
        $groupBuyId = intval($_POST['groupBuyId'])?:0;
        //计算订单总价
        $amount = 0;
        foreach ($goodsList as $k=>&$v){
            if($v['goods_type'] == 1){
                $goodsInfo = D('Goods')->getGoodsInfoByGoodsId($v['foreign_id']);
                $goodsNum = intval($v['num']);
                $goodsList[$k]['price'] =  $goodsInfo['real_price'];
                $totalPrice = $goodsInfo['real_price'] * $goodsNum;
                if($goodsInfo['buy_type'] == 3){
                    $orderType = 1;//团购
                }
            }
            if($v['goods_type'] == 2){
                $projectInfo = D('Project')->getProjectInfoByProjectId($v['foreign_id']);
                $projectNum = intval($v['num']);
                $projectList[$k]['price'] =  $projectInfo['real_price'];
                $totalPrice = $projectInfo['real_price'] * $projectNum;
                if($projectInfo['buy_type'] == 3){
                    $orderType = 1;//团购
                }
            }
            $amount += number_format($totalPrice,2,'.','');
        }
        $modelOrder = D('Order');
        $modelLogistics = D('Logistics');
        $modelOrderDetail = D('OrderDetail');
        //开启事务
        $modelOrder->startTrans();
        //开启事务
        $modelLogistics->startTrans();
        $_POST = [];
        $_POST['create_time'] = time();
        //生成物流
        $res = $modelLogistics->addLogistics();
        $logisticsId = $res['id'];
        if(!$logisticsId){
            $modelLogistics->rollback();
            $this->ajaxReturn(errorMsg($res));
        }
        //订单编号
        $orderSN = generateSN();
        //生成订单
        $_POST = [];
        $_POST['sn'] = $orderSN;
        $_POST['user_id'] = $this->user['id'];
        $_POST['logistics_id'] = $logisticsId;
        $_POST['amount'] = $amount;
        $_POST['type'] = $orderType;
        $_POST['create_time'] = time();
        $res = $modelOrder->addOrder();
        $orderId = $res['id'];
        if(!$orderId){
            $modelOrder->rollback();
            $this->ajaxReturn(errorMsg($res));
        }
        //生成订单明细
        foreach ($goodsList as $item){
            $_POST = [];
            $_POST['order_sn'] = $orderSN;
            $_POST['order_id'] = $orderId;
            $_POST['price'] = $item['price'];
            $_POST['num'] = $item['num'];
            $_POST['foreign_id'] = $item['foreign_id'];
            $_POST['user_id'] = $this->user['id'];
            $_POST['goods_type'] = $item['goods_type'];
            $res = $modelOrderDetail->addOrderDetail();
            if(!$res['id']){
                $modelLogistics->rollback();
                $this->ajaxReturn(errorMsg($res));
            }
        }
        //禁用购物车
        $modelCart = D('Cart');
        $where = array(
            'ct.user_id' => $this->user['id'],
        );
        $cartList = $modelCart->selectCart($where);
        foreach ($goodsList as $item){
            foreach ($cartList as $value){
                if($item['foreign_id'] && $value['foreign_id'] && $item['goods_type']==$value['goods_type'])
                {//提交的商品在购物车中，生成订单后禁用
                    $where = array(
                        'user_id' => $this->user['id'],
                        'foreign_id' => $value['foreign_id'],
                        'goods_type' => $value['goods_type'],
                    );
                    $res = $modelCart->where($where)->setField('status',1);
                    if($res === false){
                        $modelLogistics->rollback();
                        $this->ajaxReturn(errorMsg($this->getError()));
                    }
                }
            }
        }
        if($orderType == 1) {//团购
            $_where = array(
                'group_buy_id' => $groupBuyId,
                'pay_status' => 2,
            );
            //判断是否已团购
            $userIdArray = D('GroupBuyDetail')->where($_where)->getField('user_id', true);
            if (in_array($this->user['id'], $userIdArray)) {
                $openid = session('openid');
                D('GroupBuy')->joinGroupBuy($goodsList[0], $this->user['id'], $orderId, $groupBuyId, $openid);
            }
        }
        $modelLogistics->commit();
        $this->ajaxReturn(successMsg('生成订单成功', array('orderId' => $orderId)));
    }
    //订单-详情页
    public function orderDetail(){
        $modelOrder = D('Order');
        $modelOrderDetail = D('OrderDetail');
        if(IS_POST){
        }else{
            if(!isset($_GET['orderId']) || !intval($_GET['orderId'])){
                $this->error('缺少订单ID');
            }
            $orderId = I('get.orderId',0,'int');
            $userId = $this->user['id'];
            //收货人地址
            if(isset($_GET['addressId'])){
            $addressId = $_GET['addressId'];
            $address = D('Address')->getUserAddressById($userId,$addressId);

            }else{
            $address = D('Address')->getUserAddressByUid($userId);
            }
            $this->address=$address;
            //查询产品列表
            $gWhere = array(
                'od.user_id' => $this->user['id'],
                'od.goods_type' => 1,
                'od.order_id' => $orderId,
            );
            $gField = array(
                'g.name','g.discount_price','g.price','g.special_price','g.group_price','g.main_img','g.buy_type'
            );
            $gJoin = array(
                'left join goods g on g.id = od.foreign_id ',
            );
            $gOrderList = $modelOrderDetail->selectOrderDetail($gWhere,$gField,$gJoin);
            //查询项目列表
            $pWhere = array(
                'od.user_id' => $this->user['id'],
                'od.goods_type' => 2,
                'od.order_id' => $orderId,
            );
            $pField = array(
                'p.name',  'p.price','p.group_price','p.discount_price','p.main_img',
            );
            $pJoin = array(
                'left join project p on p.id = od.foreign_id ',
            );
            $pOrderList = $modelOrderDetail->selectOrderDetail($pWhere,$pField,$pJoin);
            //合并列表
            $this -> orderList = array_merge($gOrderList,$pOrderList);
            print_r(  $this -> orderList);

//            $where = array(
//                'od.order_sn' => $orderInfo['sn'],
//            );
//            $join = array(
//                ' left join myh.goods g on g.id = od.foreign_id ',
//                ' left join myh.goods_base gb on gb.id = g.goods_base_id ',
//            );
//            $field = array(
//                'g.id','g.sale_price','gb.name','gb.price','gb.package_unit','gb.single_specification',
//            );
//            //订单下商品列表
//            $goodsList = $modelOrderDetail->selectOrderDetail($where,$field,$join);
//            $orderInfo['goodsList'] = $goodsList;
//            $this->orderInfo = $orderInfo;
//            //商品列表操作类型
//            $this->goodsListOptionType = 'withNum';
//            //订单详情页显示添加收货人地址
//            $this->addAddress = 'true';
//            //购物车配置开启的项
            $this->unlockingFooterCart = unlockingFooterCartConfig(array(2,7));
            $this->display();
        }
    }
    //订单-结算
    public function settlement(){
//        $modelOrder = D('Order');
//        $modelGoods = D('Goods');
//        $modelCouponsReceive = D('CouponsReceive');
//        $modelWallet = D('Wallet');
//        $modelWalletDetail = D('WalletDetail');
        if(IS_POST){
//            //订单信息
//            if(isset($_POST['orderId']) && intval($_POST['orderId'])){
//                $orderId = I('post.orderId',0,'int');
//            }
//            $where = array(
//                'o.id' => $orderId,
//            );
//            $orderInfo = $modelOrder->selectOrder($where);
//            $orderInfo = $orderInfo[0];
//            if(!$orderInfo['id'] || $orderInfo['amount'] <= 0){
//                $this->ajaxReturn(errorMsg('订单号：'.$orderInfo['sn'].'信息有误，请检查！'));
//            }
//            $result = $modelOrder->checkOrderLogisticsStatus($orderInfo['logistics_status']);
//            if($result['status'] == 0){
//                $this ->ajaxReturn(errorMsg($result['message']));
//            }
//            //订单详情
//            $where = array(
//                'order_sn' => $orderInfo['sn'],
//            );
//            $orderDetail = $modelOrderDetail -> selectOrderDetail($where);
//            //代金券信息
//            if(isset($_POST['couponsId']) && intval($_POST['couponsId'])){
//                $couponsId = I('post.couponsId',0,'int');
//            }
//            $where = array(
//                'cr.id' => $couponsId,
//                'cr.user_id' => $this->user['id'],
//            );
//            $couponsInfo = $modelCouponsReceive->selectCouponsReceive($where);
//            $couponsInfo = $couponsInfo[0];
//            //钱包信息
//            $where = array(
//                'w.user_id' =>  $this->user['id'],
//            );
//            $walletInfo = $modelWallet->selectWallet($where);
//            $walletInfo = $walletInfo[0];
////            $this -> walletInfo = $walletInfo;
//            $modelOrder->startTrans();//开启事务
//            //代金券支付
//            $unpaid = $orderInfo['amount'];
//            if($couponsInfo['id'] && $couponsInfo['amount'] >= 0){
//                if($unpaid<=$couponsInfo['amount']){//代金券足够支付订单
//                    //更新订单(状态还是已支付)
//                    //代金券支付：$unpaid
//                    //账户余额支付：0:
//                    //实际支付：0
//                    $_POST = [];
//                    $_POST['logistics_status'] = 2;
//                    $_POST['coupons_pay'] = $unpaid;
//                    $_POST['orderId'] = $orderId;
//                    $_POST['coupons_id'] = $couponsId;
//                    $where = array(
//                        'user_id' =>  $this->user['id'],
//                        'id' => $orderId,
//                    );
//                    $res = $modelOrder->saveOrder($where);
//                    if(!$res['id']){
//                        $modelOrder->rollback();
//                        $this->ajaxReturn(errorMsg($res));
//                    }
//                    //更新代金券，已使用
//                    $_POST = [];
//                    $_POST['status'] = 1;
//                    $_POST['couponsId'] = $couponsId;
//                    $where = array(
//                        'user_id' => $this->user['id'],
//                        'id' => $couponsId,
//                    );
//                    $res = $modelCouponsReceive->saveCouponsReceive($where);
//                    if(!$res['id']){
//                        $modelOrder->rollback();
//                        $this->ajaxReturn(errorMsg($res));
//                    }
//                    if($orderInfo['type'] == 1){//团购订单处理
//                        $this -> groupBuyHandle($modelOrder,$orderInfo);
//                    }
//                    //减库存
//                    $res = $modelGoods -> decGoodsNum($orderDetail);
//
//                    if(!$res){
//                        $modelOrder->rollback();
//                        $this->ajaxReturn(errorMsg($res));
//                    }
//                    $modelOrder->commit();//提交事务
//                    $this->ajaxReturn(successMsg('支付成功',array('wxPay'=>false,'buy_type'=>$orderDetail['type'])));
//                }else{
//                    $unpaid -= $couponsInfo['amount'];
//                }
//            }
//            //账户余额支付
//            $accountBalance = $walletInfo['amount'];//$walletInfo['amount'];
//            if($accountBalance>=0){
//                if($unpaid<=$accountBalance){//余额足够支付订单
//                    //更新订单(状态还是未支付)
//                    //代金券支付：$couponsInfo['amount']
//                    //账户余额支付：$unpaid:
//                    //实际支付：0
//                    $_POST = [];
//                    if($couponsInfo['id'] && $couponsInfo['amount'] >= 0){
//                        $_POST['coupons_pay'] = $couponsInfo['amount'];
//                        $_POST['coupons_id'] = $couponsId;
//                    }
//                    $_POST['logistics_status'] = 2;
//                    $_POST['wallet_pay'] = $unpaid;
//                    $_POST['orderId'] = $orderId;
//                    $where = array(
//                        'user_id' =>  $this->user['id'],
//                        'id' => $orderId,
//                    );
//                    $res = $modelOrder->saveOrder($where);
//                    if(!$res['id']){
//                        $modelOrder->rollback();
//                        $this->ajaxReturn(errorMsg($res));
//                    }
//                    //更新代金券，已使用
//                    if($couponsInfo['id'] && $couponsInfo['amount'] >= 0){
//                        $_POST = [];
//                        $_POST['status'] = 1;
//                        $_POST['couponsId'] = $couponsId;
//
//                        $where = array(
//                            'user_id' =>  $this->user['id'],
//                        );
//                        $res = $modelCouponsReceive->saveCouponsReceive($where);
//                        if(!$res['id']){
//                            $modelOrder->rollback();
//                            $this->ajaxReturn(errorMsg($res));
//                        }
//                    }
//                    //更新账户，$accountBalance-$unpaid
//                    $_POST = [];
//                    $_POST['amount'] = $accountBalance - $unpaid;
//                    $where = array(
//                        'user_id' =>  $this->user['id'],
//                    );
//                    $res = $modelWallet -> saveWallet($where);
//                    if($res['status'] == 0){
//                        $modelWallet->rollback();
//                        $this->ajaxReturn(errorMsg($res));
//                    }
//                    //增加账户记录
//                    $_POST = [];
//                    $_POST['user_id'] = $this->user['id'];
//                    $_POST['amount'] = $unpaid;
//                    $_POST['type'] = 2;
//                    $_POST['recharge_status'] = 1;
//                    $_POST['create_time'] = time();
//                    $res = $modelWalletDetail -> addWalletDetail();
//                    if($res['status'] == 0){
//                        $modelWallet->rollback();
//                        $this->ajaxReturn(errorMsg($res));
//                    }
//                    if($orderInfo['type'] == 1){//团购订单处理
//                        $successBackUrl = $this -> groupBuyHandle($modelOrder,$orderInfo);
//                    }
//                    $modelOrder->commit();//提交事务
//                    $this->ajaxReturn(successMsg('支付成功',array('wxPay'=>false,'successBackUrl'=>$successBackUrl)));
//                }else{
//                    $unpaid -= $accountBalance;
//                }
//            }
//            if($unpaid > 0){//转账支付
//                //更新订单(状态还是未支付)
//                //代金券支付：$couponsInfo['amount']
//                //账户余额支付：$accountBalance:
//                //实际支付：$unpaid
//                //更新代金券，已使用
//                //更新账户，0
//                $_POST = [];
//                if($couponsInfo['id'] && $couponsInfo['amount'] >= 0){
//                    $_POST['coupons_pay'] = $couponsInfo['amount'];
//                    $_POST['coupons_id'] = $couponsId;
//                }else{
//                    $_POST['coupons_pay'] = 0;
//                }
//                $_POST['wallet_pay'] = $accountBalance;
//                $_POST['actually_amount'] = $unpaid;
//                $_POST['orderId'] = $orderId;
//                $where = array(
//                    'user_id' =>  $this->user['id'],
//                    'id' => $orderId,
//                );
//                $res = $modelOrder->saveOrder($where);
//                if(!$res['id']){
//                    $modelOrder->rollback();
//                    $this->ajaxReturn(errorMsg($res));
//                }
//                $modelOrder->commit();//提交事务
//                $this->ajaxReturn(successMsg('成功',array('wxPay'=>true)));
//            }
        }else{
            //订单信息
//            if(isset($_GET['orderId']) && intval($_GET['orderId'])){
//                $orderId = I('get.orderId',0,'int');
//            }
//            $where = array(
//                'o.id' => $orderId,
//            );
//            $orderInfo = $modelOrder->selectOrder($where);
//            $this->orderInfo = $orderInfo[0];
//            //代金券
//            $where = array(
//                'cr.user_id' => $this->user['id'],
//            );
//            $this->couponsList = $modelCouponsReceive->selectCouponsReceive($where);
//            $this->couponsNum = count($this->couponsList);
//            //钱包
//            $where = array(
//                'w.user_id' => $this->user['id'],
//            );
//            $wallet = $modelWallet->selectWallet($where);
//            $this->wallet = $wallet[0];
            $this->display();
        }
    }
    //订单列表
    public function orderList(){
        // 0(已取消)10(默认):未付款;20:已付款;30:已发货;40:已收货;',50:'售后服务
        if(IS_GET){
            $orderState = intval($_GET['orderState']);
            $this -> orderState = $orderState;
            $userId = $this->user['id'];

            $where['user_id']  = $userId;
            if(isset($_GET['orderState2'])){
                $orderState2 = intval($_GET['orderState2']);
                $where['_complex'] = array(
                    'order_state' => array(
                        array('eq',$orderState),
                        array('eq',$orderState2),
                        array('or'),
                    ),
                );
            }else{
                $where['order_state'] = $orderState;
            }
            $orderList = M('order')->where($where)->order('id desc')->select();
            foreach ($orderList as $k=>$v){
                $where = array(
                  'order_sn' => $v['order_sn'],
                );
                $groupBuyInfo = M('groupbuy') -> where($where) ->find();
                if(!empty($groupBuyInfo)){
                    $orderList[$k]['isGroupBuy'] = $groupBuyInfo['shipments'];
                }else{
                    $orderList[$k]['isGroupBuy'] = 0;
                }
            }
            $this -> orderList = $orderList;
            
        }
        $this->display();
    }
    //订单详情
    public function orderInfo(){
        // 0(已取消)10(默认):未付款;20:已付款;30:已发货;40:已收货;',50:'售后服务
        if(IS_GET){
            $userId = $this->user['id'];
            $orderSn = $_GET['orderSn'];
            $orderInfo = D('Order')->getOrderInfoByOrderNoAndUid($orderSn,$userId);
            $this -> orderInfo = $orderInfo;
            $addressId = $orderInfo['address_id'];
            $this -> address = D('Address')->getUserAddressById($userId,$addressId);
            $orderSn = $orderInfo['order_sn'];
            $orderGoods = D('Order')->getOrderGoodsByOrderSn($orderSn);
            $this -> orderGoods = $orderGoods;

            if($orderGoods[0]['buy_type'] == 3){//团购产品
                //查询是否团购成功
                $where = array(
                    'order_sn' => $orderSn,
                );
                $this -> groupBuy = M('groupbuy') -> where($where) ->find();
            }
            //获取用户地址列表
            $this -> addressList = D('Address') -> getUserAddressListByUid($userId);

        }
        $this->display();
    }
    //修改订单地址
    public function changeOrderAddress(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        $userId = $this->user['id'];
        $orderSn = I('post.orderSn','','string');
        $addressId = I('post.addressId','','int');
        $result = D('Order') -> updateOrderAddress($orderSn,$addressId,$userId);
        if(false === $result){
            $this ->ajaxReturn(errorMsg('修改地址失败！'));
        }else{
            $this -> ajaxReturn(successMsg('修改地址成功！'));
        }
    }
    //通知平台已发货，修改order状态为40；
    public function informToPlatform(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        $userId = $this->user['id'];
        $orderSn = I('post.orderSn','','string');
        $orderState = 40;
        $result = D('Order')->updateOrderStatus($orderSn,$orderState,$userId);
        if(false === $result){
            $this ->ajaxReturn(errorMsg('通知失败！'));
        }else{
            $this -> ajaxReturn(successMsg('已通知！'));
        }
    }
}