<?php
namespace Mall\Controller;

use web\all\Controller\AuthUserController;

class OrderController extends AuthUserController {
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
        //订单编号
        $orderSN = generateSN();
        //生成订单
        $modelOrder = D('Order');
        $modelOrder->startTrans();
        $_POST = [];
        $_POST['sn'] = $orderSN;
        $_POST['pay_status'] = 1;
        $_POST['user_id'] = $this->user['id'];
        $_POST['amount'] = $amount;
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
                $modelOrder->rollback();
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
                        $modelOrder->rollback();
                        $this->ajaxReturn(errorMsg($this->getError()));
                    }
                }
            }
        }
        $modelOrder->commit();
        $this->ajaxReturn(successMsg('生成订单成功',array('id'=>$orderId)));
    }

    //我的订单
    public function orderManage(){
        $modelOrder = D('Order');
        if(IS_POST){
        }else{
            $where = array(
                'o.user_id' => $this->user['id'],
            );
            if(isset($_GET['pay_status']) && intval($_GET['pay_status'])){
                $where['o.pay_status'] = I('get.pay_status',0,'int');
            }
            $this->orderList = $modelOrder->selectOrder($where);
            $this->display();
        }
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
        $_POST['pay_status'] = 10;
        $res = $modelOrder->saveOrder($where);
        if(!$res['id']){
            $this->ajaxReturn(errorMsg('失败'));
        }
        $this->ajaxReturn(successMsg('成功',array('id'=>$res['id'])));
    }

    //订单-详情页
    public function orderDetail(){
        $modelOrder = D('Order');
        $modelOrderDetail = D('OrderDetail');
        if(IS_POST){
        }else{
            //订单绑定地址
            if(isset($_GET['orderId']) && intval($_GET['orderId']) &&
                isset($_GET['consigneeAddressId']) && intval($_GET['consigneeAddressId'])){
                $where['id'] = I('get.orderId',0,'int');
                $_POST['address_id'] = I('get.consigneeAddressId',0,'int');
                $modelOrder->saveOrder($where);
            }
            //订单信息查询
            $where = array(
                'o.user_id' => $this->user['id'],
            );
            if(isset($_GET['orderId']) && intval($_GET['orderId'])){
                $where['o.id'] = I('get.orderId',0,'int');
            }
            $where['o.pay_status'] = $_GET['payStatus']?I('get.payStatus',0,'int'):1;
            $join = array(
                ' left join consignee_address ca on o.address_id = ca.id ',
            );
            $field = array(
                'o.id','ca.id consignee_id','ca.consignee_name','ca.consignee_mobile','ca.province','ca.city','ca.area','ca.detailed_address',
            );
            $orderList = $modelOrder->selectOrder($where,$field,$join);
            $this->orderInfo = $orderList[0];
            $where = array(
                'od.order_sn' => $this->orderInfo['sn'],
            );
            $join = array(
                ' left join goods g on g.id = od.foreign_id ',
                ' left join goods_base gb on gb.id = g.goods_base_id ',
            );
            $field = array(
                'g.id','g.sale_price','gb.name','gb.price','gb.package_unit','gb.single_specification',
            );
            //商品列表
            $this->goodsList = $modelOrderDetail->selectOrderDetail($where,$field,$join);
            //商品列表操作类型
            $this->goodsListOptionType = 'withNum';
            //购物车配置开启的项
            $this->unlockingFooterCart = unlockingFooterCartConfig(array(2,7));
            $this->display();
        }
    }

    //订单-结算支付
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
                $this->ajaxReturn(errorMsg('订单信息有误！'));
            }
            $result = $modelOrder -> checkOrderStatus($orderInfo);
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
            $this -> walletInfo = $walletInfo;
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
                    $_POST['pay_status'] = 20;
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
                    $this->ajaxReturn(successMsg('成功',array('wxPay'=>false)));
                }else{
                    $unpaid -= $couponsInfo['amount'];
                }
            }
            //账户余额支付
            $accountBalance = 0;//$walletInfo['amount'];
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
                    $_POST['pay_status'] = 20;
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
                    $this->ajaxReturn(successMsg('成功',array('wxPay'=>false)));
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