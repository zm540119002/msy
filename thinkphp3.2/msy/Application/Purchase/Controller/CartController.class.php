<?php
namespace Purchase\Controller;

use web\all\Controller\BaseController;
use web\all\Lib\AuthUser;

class CartController extends BaseController {
    //商品管理
    public function index(){
        $modelGoods = D('Goods');
        $modelCart = D('Cart');
        if(IS_POST){
            //用户信息
            $this->user = AuthUser::check();
            if($this->user['id']){//已登录
                $where = array(
                    'ct.user_id' => $this->user['id'],
                );
                $cart = $modelCart->selectCart($where);
            }else{//未登录
                $cart = unserialize(cookie('cart'));
            }
            if(!empty($cart)){
                $where = array(
                    'g.id' => array('in',array_column($cart,'foreign_id')),
                );
                $goodsList = $modelGoods->selectGoods($where);
                $this->goodsList = GoodsNumMergeById($cart,$goodsList);
                $this->display('Goods/goodsListTpl');
            }
        }else{
            $this->display();
        }
    }

    //联合采购
    public function jointPurchase(){
        if(IS_POST){
        }else{
            //购物车配置开启的项
            $this->unlockingFooterCart = unlockingFooterCartConfig(array(2,3,4));
            $this->display();
        }
    }

    //加入购物车
    public function addGoodsToCart(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        //用户信息
        $this->user = AuthUser::check();
        if($this->user['id']){//已登录
            $modelCart = D('Cart');
            $where = array(
                'ct.user_id' => $this->user['id'],
            );
            $carts = $modelCart->selectCart($where);
            $goodsList = $_POST['goodsList'];
            foreach ($goodsList as $goods){
                //假定没找到
                $find = false;
                foreach ($carts as $cart){
                    if($goods['foreign_id'] == $cart['foreign_id']){//找到了，则更新记录
                        $find = true;
                        $where = array(
                            'user_id' => $this->user['id'],
                            'id' => $cart['id'],
                            'foreign_id' => $cart['foreign_id'],
                        );
                        $_POST['num'] = $goods['num'] + $cart['num'];
                        $res = $modelCart->saveCart($where);
                        if($res['status']== 0){
                            break 2;
                        }
                    }
                }
                if(!$find){//如果没找到，则新增
                    $_POST = [];
                    $_POST['user_id'] = $this->user['id'];
                    $_POST['foreign_id'] = $goods['foreign_id'];
                    $_POST['num'] = $goods['num'];
                    $_POST['create_time'] = time();
                    $res = $modelCart->addCart();
                    if($res['status']== 0){
                        break;
                    }
                }
            }
        }else{//未登录
            cookie('cart',serialize(goodsMergeById(unserialize(cookie('cart')),$_POST['goodsList'])));
        }
        $this->ajaxReturn(successMsg('加入购物车成功'));
    }

    //增减单个商品
    public function replaceOneGoodsToCart(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        //用户信息
        $this->user = AuthUser::check();
        if($this->user['id']){//已登录
            $goods = $_POST;
            $modelCart = D('Cart');
            $where = array(
                'ct.user_id' => $this->user['id'],
            );
            $carts = $modelCart->selectCart($where);
            foreach ($carts as $cart){
                if($goods['foreign_id'] == $cart['foreign_id']){//找到了，则更新记录
                    $where = array(
                        'user_id' => $this->user['id'],
                        'id' => $cart['id'],
                        'foreign_id' => $cart['foreign_id'],
                    );
                    $res = $modelCart->saveCart($where);
                    if($res['status']== 0){
                        $this->ajaxReturn(errorMsg($res['info']));
                    }
                }
            }
        }else{//未登录
            $cart = unserialize(cookie('cart'));
            foreach ($cart as &$value){
                if($value['foreign_id'] == $_POST['foreign_id']){
                    $value['num'] = $_POST['num'];
                }
            }
            cookie('cart',serialize($cart));
        }
        $this->ajaxReturn(successMsg('加入购物车成功'));
    }
}