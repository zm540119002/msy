<?php
namespace Myms\Controller;
use Think\Controller;
use web\all\Controller\AuthUserController;


class OrderController extends AuthUserController {
    //获取订单详情页面
    public function addOrder(){
        if(IS_POST){
            $orderModel = D('Order');
            $uid = $this->user['id'];
            //立即购买的商品信息
            if(isset($_POST['goodsId'])){
                $goodsInfo = D('Goods')->getGoodsInfoByGoodsId($_POST['goodsId']);
                $goodsInfo['type'] = 1;
                $goodsNum = intval($_POST['goodsNum']);
                $totalPrice = $goodsInfo['real_price'] * $goodsNum;
            }
            if(isset($_POST['projectId']) && intval($_POST['projectId'])){
                $goodsInfo = D('Project')->getProjectInfoByProjectId($_POST['projectId']);
                $goodsInfo['type'] = 2;
                $goodsNum = intval($_POST['goodsNum']);
                $totalPrice = $goodsInfo['real_price'] * $goodsNum;
            }
            //订单类型
            if($goodsInfo['buy_type'] == 3){
                $orderType = 1;//页面需要分享
            }else{
                $orderType = 0;
            }
            // 选中购物车商品信息
            if(isset($_POST['cartIds'])){
                $cartIds = $_POST['cartIds'];
                $this -> cartIds = $cartIds;
                $cartIds = rtrim($cartIds, ',');
                $cartIds = explode(',',$cartIds);
                if(in_array('undefined',$cartIds) || in_array('',$cartIds)){
                    $cartList = D('Cart')->getCartListBySession();
                }else{
                    $cartList = D('Cart') -> getCartListByCartIds($uid,$cartIds);
                }
                //检查库存
                $this -> checkGoodsStock($cartList);
                $cartInfo   = D('Cart') -> getCartInfo($cartList);
                $totalPrice  = $cartInfo['total'];//商品总价格
            }

            //订单各类价格
            $car_price['goods_amount']  = $totalPrice;//商品总价格
            $car_price['shipping_fee']    = 0;//运费
            $car_price['pd_amount']       = 0;//预存款支付金额
            $car_price['coupon_price']    = 0;//优惠券
            $car_price['coupon_id']       = 0;//优惠id
            $car_price['order_amount']    = $car_price['goods_amount']+$car_price['shipping_fee'];//订单总价格
            $car_price['actually_amount'] = $car_price['order_amount']-$car_price['pd_amount'];//实际价格
            //开启事务
            $orderModel -> startTrans();
            try{
                if(!isset($_POST['addressId'])){
                    $data = array(
                        'user_id'   => $uid,
                        'consignee' => $_POST['consignee'],
                        'district'  => $_POST['district'],
                        'address'   => $_POST['address'],
                        'mobile'    => $_POST['mobile'],
                        'is_default'=> 1
                    );
                    $result = M('address')->add($data);
                    if(!$result){
                        throw new \Exception($this ->ajaxReturn(errorMsg('增加地址失败')));
                    }
                }
                $addressId = isset($_POST['addressId']) ? intval($_POST['addressId']) : $result;
                $orderSn  = date('YmdHis') . rand(1000, 9999);
                //加入订单
                $orderId =$orderModel -> addOrder($orderSn,$orderType,$uid,$addressId,$car_price,$user_note='');
                if(!$orderId){
                    throw new \Exception( $this->ajaxReturn(errorMsg('增加订单失败')));
                }
                //加入订单商品
                if(isset($_POST['cartIds'])){//购物车结算
                    $orderGoodsId = $orderModel->addOrderGoods($uid,$cartList,$orderSn);
                }
                if(isset($_POST['goodsId']) || isset($_POST['projectId'])){//立即购买
                    $orderGoodsId = $orderModel->directlyAddOrderGoods($uid,$goodsInfo,$orderSn,$goodsNum);
                }

                if(!$orderGoodsId){
                    throw new \Exception( $this->ajaxReturn(errorMsg('增加订单商品失败')));
                }
                //删除购物车相对于商品
                if(isset($_POST['cartIds'])){
                    if((in_array('undefined',$cartIds) || in_array('',$cartIds)) === false){
                        $result = D('cart')->delCartByCartIds($uid,$cartIds);
                        if(!$result){
                            throw new \Exception( $this->ajaxReturn(errorMsg('删除购物车失败')));
                        }
                    }
                }

                if($goodsInfo['buy_type'] == 3){//团购或闺蜜行
                    if(isset($_POST['groupBuySn']) && !empty($_POST['groupBuySn'])){
                        $groupBuySn = $_POST['groupBuySn'];
                        session('groupBuySn',$groupBuySn);
                        $where = array(
                            'groupbuy_sn' => $groupBuySn,
                            'status' => 1,
                        );
                        $groupBuyNum  =  M('groupbuy') -> where($where) -> count();
                        //如果已有三人购买或团长的购买？？？？？？？？？？？？？？？？就重新开团
                        if($groupBuyNum == $goodsInfo['groupbuy_num']){
                            $groupBuySn = date('ymd').substr(time(),-5).substr(microtime(),2,5);
                            session('groupBuySn',$groupBuySn);
                        }
                    }else{
                        $groupBuySn = date('ymd').substr(time(),-5).substr(microtime(),2,5);
                        session('groupBuySn',$groupBuySn);
                    }

                    $data = array(
                        'user_id'  => $uid,
                        'groupbuy_sn' => $groupBuySn,
                        'order_sn' => $orderSn,
                        'create_time'=> time(),
                    );
                    $result = M('groupbuy')->add($data);
                    if(!$result){
                        throw new \Exception( $this->ajaxReturn(errorMsg('增加团购失败')));
                    }
                }
                
                $orderModel->commit();
                $orderSn = $orderSn.',myms';
                $this->ajaxReturn(successMsg($orderSn));
            }catch (Exception $e){
                $orderModel->rollback();
                $this->ajaxReturn($e->getMessage());
            }
        }else{
            $userId = $this->user['id'];
            //收货人地址
            if(isset($_GET['addressId'])){
                $addressId = $_GET['addressId'];
                $address = D('Address')-> getUserAddressById($userId,$addressId);
            }else{
                $address = D('Address')-> getUserAddressByUid($userId);
            }
            $this -> address = $address;
            //点击结算选中购物车商品信息
            if(isset($_GET['cartIds'])){
                $cartIds = $_GET['cartIds'];
                $this -> cartIds = $cartIds;
                $cartIds = rtrim($cartIds, ',');
                $cartIds = explode(',',$cartIds);
                if(in_array('undefined',$cartIds) || in_array('',$cartIds)){
                    $cartList = D('Cart')->getCartListBySession();
                }else{
                    $cartList = D('Cart') -> getCartListByCartIds($userId,$cartIds);
                }

                if(empty($cartList)){
                    $this->error('你选中的商品已删除，请重新选择！');
                }
                $this -> cartList = $cartList;
                //订单总价
                $cartInfo = D('Cart') -> getCartInfo($cartList);
                $this -> cartInfo = $cartInfo;
            }

            //点击立即购买产品
            $this->num = intval($_GET['num']);
            $this-> groupBuySn = $_GET['groupBuySn'];
            if(isset($_GET['goodsId'])){
                if(empty($_GET['goodsId']) && intval($_GET['goodsId'])){
                    $this->error('请选择你要购买的商品');
                }
                $goodsId = $_GET['goodsId'];
                $this->goodsId = $goodsId;
                $goodsInfo = D('Goods')-> getGoodsInfoByGoodsId($goodsId);
                $this -> goodsInfo = $goodsInfo;

            }
            //点击立即购买项目
            if(isset($_GET['projectId'])){
                if(empty($_GET['projectId']) && intval($_GET['projectId'])){
                    $this->error('请选择你要购买的商品');
                }
                $projectId = $_GET['projectId'];
                $this->projectId = $projectId;
                $projectInfo = D('Project')->getProjectInfoByProjectId($projectId);
                $this -> projectInfo = $projectInfo;
            }
            $this->display();
        }
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