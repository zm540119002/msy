<?php
namespace Mall\Controller;
use Think\Controller;
use web\all\Controller\AuthUserController;


class OrderController extends AuthUserController {
    //获取订单详情页面
    public function generate(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        $goodList = $_POST['goodsList'];
        if(empty($goodList)){
            $this->ajaxReturn(errorMsg('请求数据不能为空'));
        }
        //计算订单总价
        $goodsTotalPrice = 0;
        foreach ($goodList as $k=>&$v){
            if($v['goods_type'] == 1){
                $goodsInfo = D('Goods')->getGoodsInfoByGoodsId($v['foreign_id']);
                $goodsNum = intval($v['num']);
                $totalPrice = $goodsInfo['real_price'] * $goodsNum;
            }
            if($v['goods_type'] == 2){
                $projectInfo = D('Project')->getProjectInfoByProjectId($v['foreign_id']);
                $projectNum = intval($v['num']);
                $totalPrice = $projectInfo['real_price'] * $projectNum;
            }
            $goodsTotalPrice += number_format($totalPrice,2,'.','');
        }
        $modelOrder = D('Order');
        //开启事务
        $modelOrder->startTrans();
//        if(IS_POST){
//            $orderModel = D('Order');
//            $uid = $this->user['id'];
//
//            //立即购买的商品信息
//            if(isset($_POST['goodsId'])){
//                $goodsInfo = D('Goods')->getGoodsInfoByGoodsId($_POST['goodsId']);
//                $goodsInfo['type'] = 1;
//                $goodsNum = intval($_POST['goodsNum']);
//                $totalPrice = $goodsInfo['real_price'] * $goodsNum;
//            }
//            if(isset($_POST['projectId']) && intval($_POST['projectId'])){
//                $goodsInfo = D('Project')->getProjectInfoByProjectId($_POST['projectId']);
//                $goodsInfo['type'] = 2;
//                $goodsNum = intval($_POST['goodsNum']);
//                $totalPrice = $goodsInfo['real_price'] * $goodsNum;
//            }
//            //订单类型
//            if($goodsInfo['buy_type'] == 3){
//                $orderType = 1;//页面需要分享
//            }else{
//                $orderType = 0;
//            }
//            // 选中购物车商品信息
//            if(isset($_POST['cartIds'])){
//                $cartIds = $_POST['cartIds'];
//                $this -> cartIds = $cartIds;
//                $cartIds = rtrim($cartIds, ',');
//                $cartIds = explode(',',$cartIds);
//                if(in_array('undefined',$cartIds) || in_array('',$cartIds)){
//                    $cartList = D('Cart')->getCartListBySession();
//                }else{
//                    $cartList = D('Cart') -> getCartListByCartIds($uid,$cartIds);
//                }
//                //检查库存
//                $this -> checkGoodsStock($cartList);
//                $cartInfo   = D('Cart') -> getCartInfo($cartList);
//                $totalPrice  = $cartInfo['total'];//商品总价格
//            }
//
//            //订单各类价格
//            $car_price['goods_amount']  = $totalPrice;//商品总价格
//            $car_price['shipping_fee']    = 0;//运费
//            $car_price['pd_amount']       = 0;//预存款支付金额
//            $car_price['coupon_price']    = 0;//优惠券
//            $car_price['coupon_id']       = 0;//优惠id
//            $car_price['order_amount']    = $car_price['goods_amount']+$car_price['shipping_fee'];//订单总价格
//            $car_price['actually_amount'] = $car_price['order_amount']-$car_price['pd_amount'];//实际价格
//            //开启事务
//            $orderModel -> startTrans();
//            try{
//                if(!isset($_POST['addressId'])){
//                    $data = array(
//                        'user_id'   => $uid,
//                        'consignee' => $_POST['consignee'],
//                        'district'  => $_POST['district'],
//                        'address'   => $_POST['address'],
//                        'mobile'    => $_POST['mobile'],
//                        'is_default'=> 1
//                    );
//                    $result = D('Address')->add($data);
//                    if(!$result){
//                        throw new \Exception($this ->ajaxReturn(errorMsg('增加地址失败')));
//                    }
//                }
//                $addressId = isset($_POST['addressId']) ? intval($_POST['addressId']) : $result;
//                $orderSn  = date('YmdHis') . rand(1000, 9999);
//                //加入订单
//                $orderId =$orderModel -> addOrder($orderSn,$orderType,$uid,$addressId,$car_price,$user_note='');
//                if(!$orderId){
//                    throw new \Exception( $this->ajaxReturn(errorMsg('增加订单失败')));
//                }
//                //加入订单商品
//                if(isset($_POST['cartIds'])){//购物车结算
//                    $orderGoodsId = $orderModel->addOrderGoods($uid,$cartList,$orderSn);
//                }
//                if(isset($_POST['goodsId']) || isset($_POST['projectId'])){//立即购买
//                    $orderGoodsId = $orderModel->directlyAddOrderGoods($uid,$goodsInfo,$orderSn,$goodsNum);
//                }
//
//                if(!$orderGoodsId){
//                    throw new \Exception( $this->ajaxReturn(errorMsg('增加订单商品失败')));
//                }
//                //删除购物车相对于商品
//                if(isset($_POST['cartIds'])){
//                    if((in_array('undefined',$cartIds) || in_array('',$cartIds)) === false){
//                        $result = D('cart')->delCartByCartIds($uid,$cartIds);
//                        if(!$result){
//                            throw new \Exception( $this->ajaxReturn(errorMsg('删除购物车失败')));
//                        }
//                    }
//                }
//
//                if($goodsInfo['buy_type'] == 3){//团购或闺蜜行
//                    if(isset($_POST['groupBuySn']) && !empty($_POST['groupBuySn'])){
//                        $groupBuySn = $_POST['groupBuySn'];
//                        session('groupBuySn',$groupBuySn);
//                        $where = array(
//                            'groupbuy_sn' => $groupBuySn,
//                            'status' => 1,
//                        );
//                        $groupBuyNum  =  M('groupbuy') -> where($where) -> count();
//                        //如果已有三人购买或团长的购买？？？？？？？？？？？？？？？？就重新开团
//                        if($groupBuyNum == $goodsInfo['groupbuy_num']){
//                            $groupBuySn = date('ymd').substr(time(),-5).substr(microtime(),2,5);
//                            session('groupBuySn',$groupBuySn);
//                        }
//                    }else{
//                        $groupBuySn = date('ymd').substr(time(),-5).substr(microtime(),2,5);
//                        session('groupBuySn',$groupBuySn);
//                    }
//
//                    $data = array(
//                        'user_id'  => $uid,
//                        'groupbuy_sn' => $groupBuySn,
//                        'order_sn' => $orderSn,
//                        'create_time'=> time(),
//                    );
//                    $result = M('groupbuy')->add($data);
//                    if(!$result){
//                        throw new \Exception( $this->ajaxReturn(errorMsg('增加团购失败')));
//                    }
//                }
//
//                $orderModel->commit();
//                $this->ajaxReturn(successMsg($orderId));
//            }catch (Exception $e){
//                $orderModel->rollback();
//                $this->ajaxReturn($e->getMessage());
//            }
//        }else{
//            $userId = $this->user['id'];
//            //收货人地址
//            if(isset($_GET['addressId'])){
//                $addressId = $_GET['addressId'];
//                $address = D('Address')-> getUserAddressById($userId,$addressId);
//            }else{
//                $address = D('Address')-> getUserAddressByUid($userId);
//            }
//            $this -> address = $address;
//            //点击结算选中购物车商品信息
//            if(isset($_GET['cartIds'])){
//                $cartIds = $_GET['cartIds'];
//                $this -> cartIds = $cartIds;
//                $cartIds = rtrim($cartIds, ',');
//                $cartIds = explode(',',$cartIds);
//                if(in_array('undefined',$cartIds) || in_array('',$cartIds)){
//                    $cartList = D('Cart')->getCartListBySession();
//                }else{
//                    $cartList = D('Cart') -> getCartListByCartIds($userId,$cartIds);
//                }
//
//                if(empty($cartList)){
//                    $this->error('你选中的商品已删除，请重新选择！');
//                }
//                $this -> cartList = $cartList;
//                //订单总价
//                $cartInfo = D('Cart') -> getCartInfo($cartList);
//                $this -> cartInfo = $cartInfo;
//            }
//
//            //点击立即购买产品
//            $this->num = intval($_GET['num']);
//            $this-> groupBuySn = $_GET['groupBuySn'];
//            if(isset($_GET['goodsId'])){
//                if(empty($_GET['goodsId']) && intval($_GET['goodsId'])){
//                    $this->error('请选择你要购买的商品');
//                }
//                $goodsId = $_GET['goodsId'];
//                $this->goodsId = $goodsId;
//                $goodsInfo = D('Goods')-> getGoodsInfoByGoodsId($goodsId);
//                $this -> goodsInfo = $goodsInfo;
//            }
//            //点击立即购买项目
//            if(isset($_GET['projectId'])){
//                if(empty($_GET['projectId']) && intval($_GET['projectId'])){
//                    $this->error('请选择你要购买的商品');
//                }
//                $projectId = $_GET['projectId'];
//                $this->projectId = $projectId;
//                $projectInfo = D('Project')->getProjectInfoByProjectId($projectId);
//                $this -> projectInfo = $projectInfo;
//            }
//            $this->unlockingFooterCart = unlockingFooterCartConfig(array(2,7));
//            $this->display();
//        }
    }
    //下单前检查订单商品库存
    public function checkGoodsStock($cartList){
        foreach ( $cartList as $v ) {
            $where['id'] = $v['foreign_id'];
            if($v['type'] == 1){//商品
                $storage = M('goods') -> where($where) -> getField('inventory');
            }
            if($v['type'] == 2){
                $storage = M('project') -> where($where) -> getField('inventory');
            }
            if($storage < $v['num']){
                $this->error($v['foreign_name']."只有".$storage.'件，库存不足');
            }
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