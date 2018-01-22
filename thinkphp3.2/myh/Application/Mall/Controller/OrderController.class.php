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
                'o.id','o.create_time as order_start_time','ca.id consignee_id','ca.consignee_name','ca.consignee_mobile','ca.province',
                'ca.city','ca.area','ca.detailed_address',
                'l.status as deliver_status ','l.undertake_company','l.delivery_time','l.fee',
                'gbd.group_buy_id','gbd.goods_id','gb.tag','gb.overdue_time as group_buy_overdue_time'
            );
            $join = array(
                ' left join consignee_address ca on o.address_id = ca.id ',
                ' left join logistics l on o.logistics_id = l.id ',
                ' left join group_buy_detail gbd on o.id = gbd.order_id ',
                ' left join group_buy gb on gb.id = gbd.group_buy_id ',
            );
            $orderList = $modelOrder->selectOrder($where,$field,$join);
            $field = array(
                'g.id','g.sale_price','gb.name','gb.price','gb.package_unit','gb.single_specification',
                'gb.main_img',
            );
            $join = array(
                ' left join goods g on g.id = od.foreign_id ',
                ' left join goods_base gb on gb.id = g.goods_base_id ',
            );
            foreach ($orderList as $k=>&$item) {
                $item['order_overdue_time'] = $item['order_start_time'] + 60;
                $item['order_overdue_time1'] =  date("Y-m-d H:i:s", $item['order_start_time'] + 60);
                $where = array(
                    'od.order_sn' => $item['sn'],
                );
                //订单下商品列表
                $item['goodsList'] = $goodsList = $modelOrderDetail->selectOrderDetail($where,$field,$join);
            }
            $this->orderList = $orderList;
            //商品列表操作类型
            $this->goodsListOptionType = 'withNum';
            $this -> current_time = time();
            $this -> current_time1 = date("Y-m-d H:i:s");
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
                $this->ajaxReturn(errorMsg('你已参加此团购，不能再参加！是否重新开团',array('joined'=>1)));
            }
            $openid = session('openid');
            D('GroupBuy')->joinGroupBuy($goodsList[0], $this->user['id'],$orderId,$groupBuyId,$openid);
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
                    if($orderInfo['type'] == 1){//团购订单处理
                        $this -> groupBuyHandle($modelOrder,$orderInfo);
                    }
                    //减库存
                    $res = $modelGoods -> decGoodsNum($orderDetail);

                    if(!$res){
                        $modelOrder->rollback();
                        $this->ajaxReturn(errorMsg($res));
                    }
                    $modelOrder->commit();//提交事务
                    $this->ajaxReturn(successMsg('支付成功',array('wxPay'=>false,'buy_type'=>$orderDetail['type'])));
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
                    if($orderInfo['type'] == 1){//团购订单处理
                        $successBackUrl = $this -> groupBuyHandle($modelOrder,$orderInfo);
                    }
                    $modelOrder->commit();//提交事务
                    $this->ajaxReturn(successMsg('支付成功',array('wxPay'=>false,'successBackUrl'=>$successBackUrl)));
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


    //团购订单处理
    private function groupBuyHandle($modelOrder,$orderInfo){
        $modelGroupBuy = D('GroupBuy');
        $modelGroupBuyDetail = D('GroupBuyDetail');
        $modelWallet = D('Wallet');
        $modelWalletDetail = D('WalletDetail');
        //更新团购表和团购详情表
        //1.先更新团购详情表
        $_POST = [];
        $_POST['pay_status'] = 2;
        $_POST['pay_time'] = date('YmdHis');
        $where = array(
            'user_id' => $orderInfo['user_id'],
            'order_id' => $orderInfo['id'],
        );
        $returnData = $modelGroupBuyDetail-> saveGroupBuyDetail($where);
        if ($returnData['status'] == 0) {
            $modelGroupBuy->rollback();
            //返回状态给微信服务器
            $this->errorReturn($orderInfo['sn'], $modelGroupBuyDetail->getLastSql());
        }
        $groupBuyDetail = $modelGroupBuyDetail->selectGroupBuyDetail($where);
        $groupBuyDetail = $groupBuyDetail[0];
        $ownOpenid = $groupBuyDetail['openid'];//自己的openid
        $groupBuyId = $groupBuyDetail['group_buy_id']; //团购ID
        //2.查看团购详情表此次团购有几人
        unset($where);
        $where = array(
            'group_buy_id' => $groupBuyId,
            'pay_status' => 2,
        );
        $groupBuyNum = $modelGroupBuyDetail->where($where)->count();
        $field=[ 'g.cash_back','g.goods_base_id','g.commission',
            'gb.name','wxu.headimgurl','wxu.nickname','o.sn as order_sn'
        ];
        $join=[ ' left join goods g on g.id = gbd.goods_id',
            ' left join goods_base gb on g.goods_base_id = gb.id ',
            ' left join wx_user wxu on wxu.openid = gbd.openid',
            ' left join orders o on o.id = gbd.order_id',
        ];
        $templateMessageList = $modelGroupBuyDetail->selectGroupBuyDetail($where,$field,$join);
        $cashBack = $templateMessageList[0]['cash_back'];//团购完成后返现
        $goodsName = $templateMessageList[0]['name'];//产品名称
        foreach ($templateMessageList as &$item){
            if($item['type'] == 1){
                $header = $item['nickname'];//团长呢称
                break;
            }
        }
        //团购成功通知
//        $templateBase = array(
//            'touser'=>$ownOpenid,
//            'template_id'=>'u7WmSYx2RJkZb-5_wOqhOCYl5xUKOwM99iEz3ljliyY',
//            'url'=>$this->host.U('Goods/goodsDetail',array(
//                    'goodsId'=>$groupBuyDetail['goods_id'],
//                    'groupBuyId'=> $groupBuyId,
//                    'shareType'=>'groupBuy' )),
//        );
//        $data = array(
//            'first'=>'亲，您已成功参加团购！',
//            'product_name'=>$goodsName,
//            'header'=>$header,
//            'remark'=>'三人可以成团，团长发起团三天有效，团购人数不限哦，快点击详情，邀请好友参团',
//        );
//        $this -> sendTemplateMessageGroupBuySuccess($templateBase,$data);
        $template = array(
            'touser'=>$ownOpenid,
            'template_id'=>'u7WmSYx2RJkZb-5_wOqhOCYl5xUKOwM99iEz3ljliyY',//参加团购通知模板Id
            "url"=>$this->host.U('Goods/goodsDetail',array(
                    'goodsId'=>$groupBuyDetail['goods_id'],
                    'groupBuyId'=> $groupBuyId,
                    'shareType'=>'groupBuy' )),
            'data'=>array(
                'first'=>array(
                    'value'=>'亲，您已成功参加团购！','color'=>'#173177',
                ),
                'Pingou_ProductName'=>array(
                    'value'=>$goodsName,'color'=>'#173177',
                ),
                'Weixin_ID'=>array(
                    'value'=>$header,'color'=>'#173177',
                ),
                'Remark'=>array(
                    'value'=>'三人可以成团，团长发起团三天有效，团购人数不限哦，快点击详情，邀请好友参团','color'=>'#FF0000',
                ),
            ),
        );
        $rst =  $this->sendTemplateMessage($template);
        if($rst['errmsg'] != 'ok'){
            \Think\Log::write('团购成功通知失败', 'NOTIC');
        }
        if($groupBuyNum == 3){//修改团购表
            $_POST = [];
            $_POST['tag'] = 1;
            unset($where);
            $where = array(
                'id' => $groupBuyId,
            );
            $returnData = $modelGroupBuy-> saveGroupBuy($where);
            if ($returnData['status'] == 0) {
                $modelOrder->rollback();
                //返回状态给微信服务器
                $this->errorReturn($orderInfo['sn'], $modelGroupBuy->getLastSql());
            }
            //返现 //返现退三个
            //更新账户
            unset($where);
            $where['user_id'] = array('in',array_column($templateMessageList,"user_id"));
            $where['status'] = 0;
            $res = $modelWallet->where($where)->setInc('amount',$cashBack);
            if(!$res){
                $modelOrder->rollback();
                //返回状态给微信服务器
                $this->errorReturn($orderInfo['sn'], $modelWallet->getLastSql());
            }
            //增加账户记录
            foreach (array_column($templateMessageList,"user_id") as &$useId){
                $_POST = [];
                $_POST['user_id'] = $useId;
                $_POST['amount'] = $cashBack;
                $_POST['type'] = 3;
                $_POST['create_time'] = time();
                $res = $modelWalletDetail->addWalletDetail();
                if ($res['status'] == 0) {
                    $modelWallet->rollback();
                    //返回状态给微信服务器
                    $this->errorReturn($orderInfo['sn'], $modelWalletDetail->getLastSql());
                }
            }
            foreach (array_column($templateMessageList,"openid","order_sn") as $order_sn =>&$openid){
                //返现通知
                //团购成功通知
//                $templateBase = array(
//                    'touser'=>$openid,
//                    'template_id'=>'IO1uGEVfncBlJMVHuDqG8FnE2vuxbnI3C_8Ke1v3Mnk',
//                    'url'=>$this->host.U('Goods/goodsDetail',array(
//                            'goodsId'=>$groupBuyDetail['goods_id'],
//                            'groupBuyId'=> $groupBuyId,
//                            'shareType'=>'groupBuy' )),
//                );
//                $data = array(
//                    'first'=>'亲，您好，你有一笔团购返现金额已经充值到您的账户，请查收！',
//                    'keyword1'=>$order_sn,
//                    'keyword2'=>$orderInfo['amount'],
//                    'keyword3'=>$cashBack,
//                    'remark'=>'祝您购物愉快！',
//                );
//                $this ->  sendTemplateMessageCashBack($templateBase,$data);

                $template = array(
                    'touser'=>$openid,
                    'template_id'=>'IO1uGEVfncBlJMVHuDqG8FnE2vuxbnI3C_8Ke1v3Mnk',//参加团购通知模板Id
                    "url"=>$this->host.U('Goods/goodsDetail',array(
                            'goodsId'=>$groupBuyDetail['goods_id'],
                            'groupBuyId'=> $groupBuyId,
                            'shareType'=>'groupBuy' )),
                    'data'=>array(
                        'first'=>array(
                            'value'=>'亲，您好，你有一笔团购返现金额已经充值到您的账户。','color'=>'#173177',
                        ),
                        'keyword1'=>array(
                            'value'=>$order_sn,'color'=>'#173177',
                        ),
                        'keyword2'=>array(
                            'value'=>$orderInfo['amount'].'元','color'=>'#173177',
                        ),
                        'keyword3'=>array(
                            'value'=>$cashBack.'元','color'=>'#173177',
                        ),
                        'remark'=>array(
                            'value'=>'祝您购物愉快！','color'=>'#FF0000',
                        ),
                    ),
                );
                $rst =  $this->sendTemplateMessage($template);
                if($rst['errmsg'] != 'ok'){
                    \Think\Log::write('发送团购通知失败', 'NOTIC');
                }
            }
        }
        //只返现自己
        if($groupBuyNum > 3){
            //更新账户
            unset($where);
            $where['user_id'] = $orderInfo['user_id'];
            $where['status'] = 0;
            $res = $modelWallet->where($where)->setInc('amount',$cashBack);
            if(!$res){
                $modelOrder->rollback();
                //返回状态给微信服务器
                $this->errorReturn($orderInfo['sn'], $modelWallet->getLastSql());
            }
            //增加账户记录
            $_POST = [];
            $_POST['user_id'] = $orderInfo['user_id'];
            $_POST['amount'] = $cashBack;
            $_POST['type'] = 3;
            $_POST['create_time'] = time();
            $res = $modelWalletDetail->addWalletDetail();
            if ($res['status'] == 0) {
                $modelWallet->rollback();
                //返回状态给微信服务器
                $this->errorReturn($orderInfo['sn'], $modelWalletDetail->getLastSql());
            }
            //返现通知
//            $templateBase = array(
//                'touser'=>$ownOpenid,
//                'template_id'=>'IO1uGEVfncBlJMVHuDqG8FnE2vuxbnI3C_8Ke1v3Mnk',
//                'url'=>$this->host.U('Goods/goodsDetail',array(
//                        'goodsId'=>$groupBuyDetail['goods_id'],
//                        'groupBuyId'=> $groupBuyId,
//                        'shareType'=>'groupBuy' )),
//            );
//            $data = array(
//                'first'=>'亲，您好，你有一笔团购返现金额已经充值到您的账户，请查收！',
//                'keyword1'=>$orderInfo['sn'],
//                'keyword2'=>$orderInfo['amount'],
//                'keyword3'=>$cashBack,
//                'remark'=>'祝您购物愉快！',
//            );
//            $this ->  sendTemplateMessageCashBack($templateBase,$data);
            $template = array(
                'touser'=>$ownOpenid,
                'template_id'=>'IO1uGEVfncBlJMVHuDqG8FnE2vuxbnI3C_8Ke1v3Mnk',//参加团购通知模板Id
                "url"=>$this->host.U('Goods/goodsDetail',array(
                        'goodsId'=>$groupBuyDetail['goods_id'],
                        'groupBuyId'=> $groupBuyId,
                        'shareType'=>'groupBuy' )),
                'data'=>array(
                    'first'=>array(
                        'value'=>'亲，您好，你有一笔团购返现金额已经充值到您的账户。','color'=>'#173177',
                    ),
                    'keyword1'=>array(
                        'value'=>$orderInfo['sn'],'color'=>'#173177',
                    ),
                    'keyword2'=>array(
                        'value'=>$orderInfo['amount'].'元','color'=>'#173177',
                    ),
                    'keyword3'=>array(
                        'value'=>$cashBack.'元','color'=>'#173177',
                    ),
                    'remark'=>array(
                        'value'=>'祝您购物愉快！','color'=>'#FF0000',
                    ),
                ),
            );
            $rst = $this->sendTemplateMessage($template);
            if($rst['errmsg'] != 'ok'){
                \Think\Log::write('发送返现通知失败', 'NOTIC');
            }
        }

        if (strpos(session('returnUrl'), 'groupBuyId') == true) {
            if(strpos(session('returnUrl'), '?') == true){
                $shLinkBase = substr(session('returnUrl'),0,strrpos(session('returnUrl'),'?'));
            }else{
                $shLinkBase =  session('returnUrl');
            }
            $successBackUrl = $shLinkBase. '/shareType/groupBuy';
        }else{
            if (strpos(session('returnUrl'), 'html') == true){
                $shLinkBase = substr(session('returnUrl'),0,strrpos(session('returnUrl'),'.html'));
            }else{
                $shLinkBase = session('returnUrl');
            }
            $successBackUrl = $shLinkBase. '/groupBuyId/'.$groupBuyId.'/shareType/groupBuy';
        }
        return $successBackUrl;
    }




}