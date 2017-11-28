<?php
namespace Purchase\Controller;
use Think\Controller;
use  Common\Lib\AuthUser;
use UserCenter\Controller\BaseAuthUserController;
class OrderController extends BaseAuthUserController {
    //获取订单详情页面
    public function addOrder(){
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
            $cartList = D('cart') -> getCartListByCartIds($userId,$cartIds);
            if(empty($cartList)){
                $this->error('你选中的商品已删除，请重新选择！');
            }
            $this -> cartList = $cartList;
            //订单总价
            $cartInfo = D('cart') -> getCartInfo($cartList);
            if($cartInfo['count']>0){
                $this -> cartInfo = $cartInfo;
            }
        }
        //点击传的参数立即购买
        if(isset($_GET['goodsId'])){
            $goodsId = $_GET['goodsId'];
            $this->goodsId = $goodsId;
            $goodsInfo = D('goods')->getGoodsInfoByGoodsId($goodsId);
            $this -> goodsInfo = $goodsInfo;
        }
        if(empty($_GET['goodsId']) && empty($_GET['cartIds'])){
            $this->error('请选择你要购买的商品');
        }
        $this->display();
    }
    //提交，生成订单
    public function addOrder1(){
       if(IS_POST){
           $orderModel = D('order');
           $uid = $this->user['id'];
           //立即购买的商品信息
           if(isset($_POST['goodsId']) && intval($_POST['goodsId'])){
               $goodsInfo = D('goods')->getGoodsInfoByGoodsId($_POST['goodsId']);
               $goodsNum = intval($_POST['goodsNum']);
               $totalPrice = $goodsInfo['price'] * $goodsNum;
           }

           // 选中购物车商品信息
           if(isset($_POST['cartIds'])){
               $cartIds = $_POST['cartIds'];
               $cartIds = explode(',',$cartIds);
               foreach ($cartIds as $k => $v)
               {
                   $cartIds[$k] = intval($v);
               }
               if(false === arrayHasOnlyInts($cartIds)){
                   $this -> ajaxReturn(errorMsg('商品参数错误'));
               }

               $orderGoods = D('cart') -> getCartListByCartIds($uid,$cartIds);
               //检查库存
               $this -> checkGoodsStock($orderGoods);
               $cartInfo   = D('cart') -> getCartInfo($orderGoods);
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
                   $goodsList = D('cart') -> getCartListByCartIds($uid,$cartIds);
                   $orderGoodsId = $orderModel->addOrderGoods($uid,$goodsList,$orderSn);
               }
               if(isset($_POST['goodsId'])){//立即购买
                   $orderGoodsId = $orderModel->addOrderGoods1($uid,$goodsInfo,$orderSn,$goodsNum);
               }

               if(!$orderGoodsId){
                   throw new \Exception( $this->ajaxReturn(errorMsg('增加订单商品失败')));
               }
               //删除购物车相对于商品
               if(isset($_POST['cartIds'])){
                   $result = D('cart')->delCartByCartIds($uid,$cartIds);
                   if(!$result){
                       throw new \Exception( $this->ajaxReturn(errorMsg('删除购物车失败')));
                   }
               }
               
               $orderModel->commit();
               $orderSn = $orderSn.',purchase';
               $this->ajaxReturn(successMsg($orderSn));
           }catch (Exception $e){
              $orderModel->rollback();
               $this->ajaxReturn($e->getMessage());
           }
       }
    }


    //增加修改地址页面
    public function addAddress(){
        $uid = $this->user['id'];
        if(isset($_GET['addressId'])){
            $id = $_GET['addressId'];
            $address = D('address') -> getUserAddressById($uid,$id);
            $this -> address = $address;
        }
        if(isset($_GET['goodsId'])){
            $this -> goodsId = $_GET['goodsId'];
        }

        if( isset($_GET['cartIds'])){
            $this -> cartIds = $_GET['cartIds'];
        }
        $this -> display();
    }

    //增加修改地址
    public function addEditAddress(){
        if(IS_POST){
            $uid = $this->user['id'];
            $addressModel = D('address');
            $data = $_POST;
            if(isset($_POST['addressId']) && !empty($_POST['addressId']) ){
                //修改
                //开启事务
                $addressModel -> startTrans();
                try{
                    $addressId = $_POST['addressId'];
                    $where = array(
                        'id' => $addressId,
                        'user_id' => $uid,
                    );
                    $result = M('address') -> where($where)->save($data);
                    if(false === $result ){
                        throw new \Exception($this ->ajaxReturn(errorMsg('修改地址失败')));
                    }
                    if($_POST['is_default'] == 1){
                        $result = $addressModel -> updateAddressNotDefault($uid,$addressId);
                        if(false === $result){
                            throw new \Exception($this ->ajaxReturn(errorMsg('修改其他地址默认值失败')));
                        }
                    }

                    $addressModel->commit();
//                    $this ->ajaxReturn(successMsg('修改地址成功'));
                    show(1,'修改地址成功',$addressId);

                }catch (Exception $e){
                    $addressModel->rollback();
                    $this->ajaxReturn($e->getMessage());
                }

            }else{
                //增加
                //开启事务
                $addressModel -> startTrans();
                try{
                    $data['user_id'] = $uid;
                    $addressId = M('address')->add($data);
                    if(!$addressId){
                        throw new \Exception($this ->ajaxReturn(errorMsg('增加地址失败')));
                    }
                    if($_POST['is_default'] == 1){
                        $result = $addressModel -> updateAddressNotDefault($uid,$addressId);
                        if(false === $result){
                            throw new \Exception($this ->ajaxReturn(errorMsg('修改其他地址默认值失败')));
                        }
                    }
                    $addressModel->commit();
                    show(1,'修改地址成功',$addressId);
                }catch (Exception $e){
                    $addressModel->rollback();
                    $this->ajaxReturn($e->getMessage());
                }
            }
        }
    }
    //地址列表
    public function addressList(){
        $userId = $this->user['id'];
        $cartIds = $_GET['cartIds'];
        $this -> cartIds = $cartIds;
        $goodsId =$_GET['goodsId'];
        $this -> goodsId = $goodsId;
        $addressList = D('address') -> getUserAddressListByUid($userId);
        $this -> addressList = $addressList;
        $this->display();
    }

    //删除地址
    public function delAddress(){
        if(IS_POST){
            $where['user_id'] =$this->user['id'];
            $where['id'] = $_POST['addressId'];
            $result = M('address') -> where($where)->delete();
            if($result){
                $this->ajaxReturn(successMsg('删除成功'));
            }else{
                $this->ajaxReturn(errorMsg('删除失败'));
            }
        }
    }


    //下单前检查订单商品库存
    public function checkGoodsStock($orderGoods){
        foreach ( $orderGoods as $v ) {
            $where['id'] = $v['goods_id'];
            $storage = M('goods') -> where($where) -> getField('storage');
            if($storage < $v['goods_num']){
                $this->error($v['goods_name']."只有".$storage.'件，库存不足');
            }
        }
    }
}