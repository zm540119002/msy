<?php
namespace Myms\Controller;
use Think\Controller;
use UserCenter\Controller\BaseAuthUserController;
class OrderController extends BaseAuthUserController {
    //获取订单详情页面
    public function addOrder(){
        if(IS_POST){
            $orderModel = D('order');
            $uid = $this->user['id'];
            //立即购买的商品信息
            $goodsNums = $_POST['goodsNums'];
            $goodsNums = rtrim($goodsNums, ',');
            $goodsNums = explode(',',$goodsNums);
            $totalPrice =0;
            $buyType = intval($_POST['buyType']);
            if(isset($_POST['goodsId']) && intval($_POST['goodsId'])){
                $goodsInfo = D('goods')->getGoodsInfoByGoodsId($_POST['goodsId'],$buyType);
                $goodsInfo['type'] = 1;
                $goodsNum = intval($goodsNums[0]);
                $totalPrice = $goodsInfo['real_price'] * $goodsNum;
            }
            if(isset($_POST['projectId']) && intval($_POST['projectId'])){
                $goodsInfo = D('project')->getProjectInfoByProjectId($_POST['projectId'],$buyType);
                $goodsInfo['type'] = 2;
                $goodsNum = intval($goodsNums[0]);
                $totalPrice = $goodsInfo['real_price'] * $goodsNum;
            }

            // 选中购物车商品信息
            if(isset($_POST['cartIds'])){
                $cartIds = $_POST['cartIds'];
                $this -> cartIds = $cartIds;
                $cartIds = rtrim($cartIds, ',');
                $cartIds = explode(',',$cartIds);
                foreach ($cartIds as $k => $v)
                {
                    $cartIds[$k] = intval($v);
                }
                if(false === arrayHasOnlyInts($cartIds)){
                    $this -> ajaxReturn(errorMsg('商品参数错误'));
                }

                $cartList = D('cart') -> getCartListByCartIds($uid,$cartIds);
                //检查库存
                $this -> checkGoodsStock($cartList);
                //获取到商品数量
                $newCartList = [];
                foreach ($cartList as $k => $v ){
                    foreach ($goodsNums as  $kk => $vv)
                        if($k == $kk){
                            $v['num'] = $vv;
                            $minTotal = $v['num'] * $v['price'];
                            $totalPrice  += $minTotal;
                            $newCartList[] = $v;
                        }
                }
                $cartList = $newCartList;
            }

            //订单各类价格
            $car_price['goods_amount']  = $totalPrice;//商品总价格
            $car_price['shipping_fee']    = 0;//运费
            $car_price['pd_amount']       = 0;//预存款支付金额
            $car_price['coupon_price']    = 0;//优惠券
            $car_price['coupon_id']       = 0;//优惠id
            $car_price['order_amount']    = $car_price['goods_amount']+$car_price['shipping_fee'];//订单总价格
            $car_price['actually_amount'] = $car_price['order_amount']-$car_price['pd_amount'];//实际价格

            $invoice_title =  $_POST['invoice_title'];
            //开启事务
            $orderModel -> startTrans();
            try{
                if(!isset($_POST['addressId'])){
                    $data = array(
                        'user_id'   => $uid,
                        'consignee' =>$_POST['consignee'],
                        'district'  => $_POST['district'],
                        'address'   =>$_POST['address'],
                        'mobile'    =>$_POST['mobile'],
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
                $orderId =$orderModel -> addOrder($orderSn,$uid,$addressId,$invoice_title,$car_price,$user_note='');
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
                $address = D('address')-> getUserAddressById($userId,$addressId);
            }else{
                $address = D('address')-> getUserAddressByUid($userId);
            }
            $this -> address = $address;
            //点击结算选中购物车商品信息
            if(isset($_GET['cartIds'])){
                $cartIds = $_GET['cartIds'];
                $this -> cartIds = $cartIds;
                $cartIds = rtrim($cartIds, ',');
                $cartIds = explode(',',$cartIds);
                if(in_array('undefined',$cartIds) || in_array('',$cartIds)){
                    $cartList = D('cart')->getCartListBySession();
                    //入库
                    $cartIds = D('cart')->addCartBySession($userId);
                    $this -> cartIds = $cartIds;
                }else{
                    $cartList = D('cart') -> getCartListByCartIds($userId,$cartIds);
//                    $cartList = D('cart') -> getCartListByTime($userId);
                }

                if(empty($cartList)){
                    $this->error('你选中的商品已删除，请重新选择！');
                }
                $this -> cartList = $cartList;
                //订单总价
                $cartInfo = D('cart') -> getCartInfo($cartList);
                $this -> cartInfo = $cartInfo;

            }

            //点击传的参数立即购买产品
            $buyType = intval($_GET['buyType']);
            $this->num = intval($_GET['num']);
            if(isset($_GET['goodsId'])){
                if(empty($_GET['goodsId']) && intval($_GET['goodsId'])){
                    $this->error('请选择你要购买的商品');
                }
                $goodsId = $_GET['goodsId'];
                $this->goodsId = $goodsId;
                $goodsInfo = D('goods')->getGoodsInfoByGoodsId($goodsId,$buyType);
                $this -> goodsInfo = $goodsInfo;

            }
            //点击传的参数立即购买项目
            if(isset($_GET['projectId'])){
                if(empty($_GET['projectId']) && intval($_GET['projectId'])){
                    $this->error('请选择你要购买的商品');
                }
                $projectId = $_GET['projectId'];
                $this->projectId = $projectId;
                $projectInfo = D('project')->getProjectInfoByProjectId($projectId,$buyType);
                $this -> projectInfo = $projectInfo;
            }
            C('HTTP_CACHE_CONTROL',"no-cache, no-store, must-revalidate");
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
}