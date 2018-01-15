<?php
namespace Mall\Controller;

use web\all\Controller\AuthUserController;

class OrderController extends AuthUserController {
    //我的订单
    public function orderManage(){
        $modelOrder = D('Order');
        $modelOrderDetail = D('OrderDetail');
        if(IS_POST){
        }else{
            $where = array(
                'o.user_id' => $this->user['id'],
            );
            //按订单状态分组统计
            $this->orderStatusCount = $modelOrder->orderStatusCount($where);
            if(isset($_GET['s']) && intval($_GET['s'])){
                $where['o.logistics_status'] = I('get.s',0,'int');
            }
            $field = array(
                'o.id','ca.id consignee_id','ca.consignee_name','ca.consignee_mobile','ca.province',
                'ca.city','ca.area','ca.detailed_address',
                'l.status as deliver_status ','l.undertake_company','l.delivery_time','l.fee',
            );
            $join = array(
                ' left join consignee_address ca on o.address_id = ca.id ',
                ' left join logistics l on o.logistics_id = l.id ',

            );
            $orderList = $modelOrder->selectOrder($where,$field,$join);
            $field = array(
                'g.id','g.sale_price','gb.name','gb.price','gb.package_unit','gb.single_specification',
            );
            $join = array(
                ' left join goods g on g.id = od.foreign_id ',
                ' left join goods_base gb on gb.id = g.goods_base_id ',
            );
            foreach ($orderList as &$item) {
                $where = array(
                    'od.order_sn' => $item['sn'],
                );
                //订单下商品列表
                $item['goodsList'] = $goodsList = $modelOrderDetail->selectOrderDetail($where,$field,$join);
            }
            $this->orderList = $orderList;
            //商品列表操作类型
            $this->goodsListOptionType = 'withNum';
            $this->display();
        }
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
            //订单绑定地址
            if(isset($_GET['consigneeAddressId']) && intval($_GET['consigneeAddressId'])){
                $_POST['address_id'] = I('get.consigneeAddressId',0,'int');
            }else{
                $modelConsigneeAddress = D('ConsigneeAddress');
                $where = array(
                    'ca.user_id' => $this->user['id'],
                    'ca.type' => 1,
                );
                $consigneeAddress = $modelConsigneeAddress->selectConsigneeAddress($where);
                $consigneeAddress = $consigneeAddress[0];
                if($consigneeAddress['id']){
                    $_POST['address_id'] = $consigneeAddress['id'];
                }
            }
            if($_POST['address_id']){
                $where = array(
                    'user_id' => $this->user['id'],
                    'id' => $orderId,
                );
                $modelOrder->saveOrder($where);
            }

            //订单信息查询
            $where = array(
                'o.user_id' => $this->user['id'],
                'o.id' => $orderId,
            );
            $join = array(
                ' left join consignee_address ca on o.address_id = ca.id ',
            );
            $field = array(
                'o.id','ca.id consignee_id','ca.consignee_name','ca.consignee_mobile','ca.province',
                'ca.city','ca.area','ca.detailed_address',
            );
            $orderList = $modelOrder->selectOrder($where,$field,$join);
            $orderInfo = $orderList[0];
            $where = array(
                'od.order_sn' => $orderInfo['sn'],
            );
            $join = array(
                ' left join goods g on g.id = od.foreign_id ',
                ' left join goods_base gb on gb.id = g.goods_base_id ',
            );
            $field = array(
                'g.id','g.sale_price','gb.name','gb.price','gb.package_unit','gb.single_specification',
            );
            //订单下商品列表
            $goodsList = $modelOrderDetail->selectOrderDetail($where,$field,$join);
            $orderInfo['goodsList'] = $goodsList;
            $this->orderInfo = $orderInfo;
            //商品列表操作类型
            $this->goodsListOptionType = 'withNum';
            //订单详情页显示添加收货人地址
            $this->addAddress = 'true';
            //购物车配置开启的项
            $this->unlockingFooterCart = unlockingFooterCartConfig(array(2,7));
            $this->display();
        }
    }

    //订单-生成
    public function generate(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        //采购商品
        $goodsList = $_POST['goodsList'];
        if(!is_array($goodsList) || empty($goodsList)){
            $this->ajaxReturn(errorMsg('未提交数据'));
        }
        $orderType = intval($_POST['orderType'])?:0;
        $groupBuyId = intval($_POST['groupBuyId'])?:0;
        //查询商品信息
        $modelGoods = D('Goods');
        $amount = 0;
        foreach ($goodsList as &$goods){
            $where = array(
                'g.id' => $goods['foreign_id'],
            );
            $goodsInfo = $modelGoods->selectGoods($where);
            $goods['price'] = $goodsInfo[0]['sale_price'];
            if($goods['num'] && $goods['price']){
                $amount += number_format($goods['num'] * $goods['price'],2,'.','');
            }
        }
        //生成物流
        $modelLogistics = D('Logistics');
        //物流编号
        $logisticsSN = generateSN();
        //开启事务
        $modelLogistics->startTrans();
        $_POST = [];
        $_POST['sn'] = $logisticsSN;
        $_POST['create_time'] = time();
        $res = $modelLogistics->addLogistics();
        $logisticsId = $res['id'];
        if(!$logisticsId){
            $modelLogistics->rollback();
            $this->ajaxReturn(errorMsg($res));
        }
        //订单编号
        $orderSN = generateSN();
        //生成订单
        $modelOrder = D('Order');
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
        $modelOrderDetail = D('OrderDetail');
        foreach ($goodsList as $item){
            $_POST = [];
            $_POST['order_sn'] = $orderSN;
            $_POST['order_id'] = $orderId;
            $_POST['price'] = $item['price'];
            $_POST['num'] = $item['num'];
            $_POST['foreign_id'] = $item['foreign_id'];
            $_POST['user_id'] = $this->user['id'];
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
                if($item['foreign_id'] && $value['foreign_id'] && $item['foreign_id']==$value['foreign_id'])
                {//提交的商品在购物车中，生成订单后禁用
                    $where = array(
                        'user_id' => $this->user['id'],
                        'foreign_id' => $value['foreign_id'],
                    );
                    $res = $modelCart->where($where)->setField('status',1);
                    if($res === false){
                        $modelLogistics->rollback();
                        $this->ajaxReturn(errorMsg($this->getError()));
                    }
                }
            }
        }
        if($orderType == 1){//团购
            $_where = array(
                'group_buy_id'=>$groupBuyId,
                'pay_status'=>2,
            );
            //判断是否已团购
            $userIdArray = D('GroupBuyDetail')->where($_where)->getField('user_id',true);
            if(in_array($this->user['id'],$userIdArray)){
                $this->ajaxReturn(errorMsg('你有参加此团购，是否重新开团',array('url'=>U('Goods/goodsDetail/',array('goodsId'=>$goodsList[0]['foreign_id'])))));
            }
            $openid =session('openid');
            print_r($openid);exit;
            D('GroupBuy')->joinGroupBuy($goodsList[0], $this->user['id'],$orderId,$groupBuyId);
        }
        $modelLogistics->commit();
        $this->ajaxReturn(successMsg('生成订单成功',array('orderId'=>$orderId)));
    }

    //确定订单
    public function confirmOrder(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        $modelOrder = D('Order');
        $where = array(
            'user_id' => $this->user['id'],
        );
        $_POST['logistics_status'] = 1;
        $res = $modelOrder->saveOrder($where);
        if(!$res['id']){
            $this->ajaxReturn(errorMsg('失败'));
        }
        $this->ajaxReturn(successMsg('成功',array('id'=>$res['id'])));
    }

    //订单-结算
    public function settlement(){
        $modelOrder = D('Order');
        $modelOrderDetail = D('OrderDetail');
        $modelGoods = D('Goods');
        $modelCouponsReceive = D('CouponsReceive');
        $modelWallet = D('Wallet');
        $modelWalletDetail = D('WalletDetail');
        if(IS_POST){
            //订单信息
            if(isset($_POST['orderId']) && intval($_POST['orderId'])){
                $orderId = I('post.orderId',0,'int');
            }
            $where = array(
                'o.id' => $orderId,
            );
            $orderInfo = $modelOrder->selectOrder($where);
            $orderInfo = $orderInfo[0];
            if(!$orderInfo['id'] || $orderInfo['amount'] <= 0){
                $this->ajaxReturn(errorMsg('订单号：'.$orderInfo['sn'].'信息有误，请检查！'));
            }
            $result = $modelOrder->checkOrderLogisticsStatus($orderInfo['logistics_status']);
            if($result['status'] == 0){
                $this ->ajaxReturn(errorMsg($result['message']));
            }
            //订单详情
            $where = array(
                'order_sn' => $orderInfo['sn'],
            );
            $orderDetail = $modelOrderDetail -> selectOrderDetail($where);
            //代金券信息
            if(isset($_POST['couponsId']) && intval($_POST['couponsId'])){
                $couponsId = I('post.couponsId',0,'int');
            }
            $where = array(
                'cr.id' => $couponsId,
                'cr.user_id' => $this->user['id'],
            );
            $couponsInfo = $modelCouponsReceive->selectCouponsReceive($where);
            $couponsInfo = $couponsInfo[0];
            //钱包信息
            $where = array(
                'w.user_id' =>  $this->user['id'],
            );
            $walletInfo = $modelWallet->selectWallet($where);
            $walletInfo = $walletInfo[0];
//            $this -> walletInfo = $walletInfo;
            $modelOrder->startTrans();//开启事务
            //代金券支付
            $unpaid = $orderInfo['amount'];
            if($couponsInfo['id'] && $couponsInfo['amount'] >= 0){
                if($unpaid<=$couponsInfo['amount']){//代金券足够支付订单
                    //更新订单(状态还是已支付)
                    //代金券支付：$unpaid
                    //账户余额支付：0:
                    //实际支付：0
                    $_POST = [];
                    $_POST['logistics_status'] = 2;
                    $_POST['coupons_pay'] = $unpaid;
                    $_POST['orderId'] = $orderId;
                    $_POST['coupons_id'] = $couponsId;
                    $where = array(
                        'user_id' =>  $this->user['id'],
                        'id' => $orderId,
                    );
                    $res = $modelOrder->saveOrder($where);
                    if(!$res['id']){
                        $modelOrder->rollback();
                        $this->ajaxReturn(errorMsg($res));
                    }
                    //更新代金券，已使用
                    $_POST = [];
                    $_POST['status'] = 1;
                    $_POST['couponsId'] = $couponsId;
                    $where = array(
                        'user_id' => $this->user['id'],
                        'id' => $couponsId,
                    );
                    $res = $modelCouponsReceive->saveCouponsReceive($where);
                    if(!$res['id']){
                        $modelOrder->rollback();
                        $this->ajaxReturn(errorMsg($res));
                    }
                    //减库存
                    $res = $modelGoods -> decGoodsNum($orderDetail);

                    if(!$res){
                        $modelOrder->rollback();
                        $this->ajaxReturn(errorMsg($res));
                    }
                    $modelOrder->commit();//提交事务
                    $this->ajaxReturn(successMsg('成功',array('wxPay'=>false,'buy_type'=>$orderDetail['type'])));
                }else{
                    $unpaid -= $couponsInfo['amount'];
                }
            }
            //账户余额支付
            $accountBalance = $walletInfo['amount'];//$walletInfo['amount'];
            if($accountBalance>=0){
                if($unpaid<=$accountBalance){//余额足够支付订单
                    //更新订单(状态还是未支付)
                    //代金券支付：$couponsInfo['amount']
                    //账户余额支付：$unpaid:
                    //实际支付：0
                    $_POST = [];
                    if($couponsInfo['id'] && $couponsInfo['amount'] >= 0){
                        $_POST['coupons_pay'] = $couponsInfo['amount'];
                        $_POST['coupons_id'] = $couponsId;
                    }
                    $_POST['logistics_status'] = 2;
                    $_POST['wallet_pay'] = $unpaid;
                    $_POST['orderId'] = $orderId;
                    $where = array(
                        'user_id' =>  $this->user['id'],
                        'id' => $orderId,
                    );
                    $res = $modelOrder->saveOrder($where);
                    if(!$res['id']){
                        $modelOrder->rollback();
                        $this->ajaxReturn(errorMsg($res));
                    }
                    //更新代金券，已使用
                    if($couponsInfo['id'] && $couponsInfo['amount'] >= 0){
                        $_POST = [];
                        $_POST['status'] = 1;
                        $_POST['couponsId'] = $couponsId;

                        $where = array(
                            'user_id' =>  $this->user['id'],
                        );
                        $res = $modelCouponsReceive->saveCouponsReceive($where);
                        if(!$res['id']){
                            $modelOrder->rollback();
                            $this->ajaxReturn(errorMsg($res));
                        }
                    }
                    //更新账户，$accountBalance-$unpaid
                    $_POST = [];
                    $_POST['amount'] = $accountBalance - $unpaid;
                    $where = array(
                        'user_id' =>  $this->user['id'],
                    );
                    $res = $modelWallet -> saveWallet($where);
                    if($res['status'] == 0){
                        $modelWallet->rollback();
                        $this->ajaxReturn(errorMsg($res));
                    }
                    //增加账户记录
                    $_POST = [];
                    $_POST['user_id'] = $this->user['id'];
                    $_POST['amount'] = $unpaid;
                    $_POST['type'] = 2;
                    $_POST['create_time'] = time();
                    $res = $modelWalletDetail -> addWalletDetail();
                    if($res['status'] == 0){
                        $modelWallet->rollback();
                        $this->ajaxReturn(errorMsg($res));
                    }
                    $modelOrder->commit();//提交事务
                    $this->ajaxReturn(successMsg('成功',array('wxPay'=>false,'buy_type'=>$orderDetail['type'])));
                }else{
                    $unpaid -= $accountBalance;
                }
            }
            if($unpaid>0){//转账支付
                //更新订单(状态还是未支付)
                //代金券支付：$couponsInfo['amount']
                //账户余额支付：$accountBalance:
                //实际支付：$unpaid
                //更新代金券，已使用
                //更新账户，0
                $_POST = [];
                if($couponsInfo['id'] && $couponsInfo['amount'] >= 0){
                    $_POST['coupons_pay'] = $couponsInfo['amount'];
                    $_POST['coupons_id'] = $couponsId;
                }else{
                    $_POST['coupons_pay'] = 0;
                }
                $_POST['wallet_pay'] = $accountBalance;
                $_POST['actually_amount'] = $unpaid;
                $_POST['orderId'] = $orderId;
                $where = array(
                    'user_id' =>  $this->user['id'],
                    'id' => $orderId,
                );
                $res = $modelOrder->saveOrder($where);
                if(!$res['id']){
                    $modelOrder->rollback();
                    $this->ajaxReturn(errorMsg($res));
                }
                $modelOrder->commit();//提交事务
                $this->ajaxReturn(successMsg('成功',array('wxPay'=>true)));
            }
        }else{
            //订单信息
            if(isset($_GET['orderId']) && intval($_GET['orderId'])){
                $orderId = I('get.orderId',0,'int');
            }
            $where = array(
                'o.id' => $orderId,
            );
            $orderInfo = $modelOrder->selectOrder($where);
            $this->orderInfo = $orderInfo[0];
            //代金券
            $where = array(
                'cr.user_id' => $this->user['id'],
            );
            $this->couponsList = $modelCouponsReceive->selectCouponsReceive($where);
            $this->couponsNum = count($this->couponsList);
            //钱包
            $where = array(
                'w.user_id' => $this->user['id'],
            );
            $wallet = $modelWallet->selectWallet($where);
            $this->wallet = $wallet[0];
            $this->display();
        }
    }




}