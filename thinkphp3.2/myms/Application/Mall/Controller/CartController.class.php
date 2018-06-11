<?php
namespace Mall\Controller;
use Think\Controller;
use web\all\Lib\AuthUser;
use web\all\Controller\BaseController;
class CartController extends BaseController {
    //购物车信息
    public function index(){
        $modelGoods = D('Goods');
        $modelProject = D('Project');
        $modelCart = D('Cart');
        //用户信息
        $this->user = AuthUser::check();
        $cookieCart = unserialize(cookie('cart'));
        if($this->user['id']){//已登录
            $where = array(
                'ct.user_id' => $this->user['id'],
            );
            $cartList = $modelCart->selectCart($where);
            if(!empty($cookieCart)){
                $modelCart->addCartByCookie($cartList,$cookieCart,$this->user['id']);
            }
            //查询产品列表
            $gWhere = array(
                'ct.user_id' => $this->user['id'],
                'ct.goods_type' => 1,
            );
            $gField = array(
                'g.name','g.discount_price','g.price','g.special_price','g.group_price','g.main_img','g.buy_type'
            );
            $gJoin = array(
                'left join goods g on g.id = ct.foreign_id ',
            );
            $gCartList = $modelCart->selectCart($gWhere,$gField,$gJoin);
            //查询项目列表
            $pWhere = array(
                'ct.user_id' => $this->user['id'],
                'ct.goods_type' => 2,
            );
            $pField = array(
                'p.name','p.discount_price','p.price','p.group_price','p.main_img',
            );
            $pJoin = array(
                'left join project p on p.id = ct.foreign_id ',
            );
            $pCartList = $modelCart->selectCart($pWhere,$pField,$pJoin);
            //合并列表
            $this -> cartList = array_merge($gCartList,$pCartList);
        }else{//未登录
            if(!empty($cookieCart)){
                $gCartList =  [];
                $pCartList =  [];
                foreach ($cookieCart as $k => &$v){
                   if($v['goods_type'] == 1){
                       $gCartList[$k]['num'] = $v['num'];
                       $gCartList[$k]['foreign_id'] = $v['foreign_id'];
                   }
                    if($v['goods_type'] == 2){
                        $pCartList[$k]['num'] = $v['num'];
                        $pCartList[$k]['foreign_id'] = $v['foreign_id'];
                    }
                }
                $goodsList = [];
                $projectList = [];
                if(!empty($gCartList)){
                    $gWhere = array(
                        'g.id' => array('in',array_column($gCartList,'foreign_id')),
                    );
                    $goodsList = $modelGoods->selectGoods($gWhere);
                }
                if(!empty($pCartList)){
                    $pWhere = array(
                        'g.id' => array('in',array_column($pCartList,'foreign_id')),
                    );
                    $projectList = $modelProject->selectProject($pWhere);
                }
                $cartList = array_merge($goodsList,$projectList);
                $this -> cartList = GoodsNumMergeById($cookieCart,$cartList);
            }
        }
        //购物车配置开启的项
        $this->unlockingFooterCart = unlockingFooterCartConfig(array(2,5));
        $this -> display();
    }
    //加入购物车
    public function addCart(){
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
                    if($goods['foreign_id'] == $cart['foreign_id'] && $goods['goods_type'] == $cart['goods_type'] ){//找到了，则更新记录
                        $find = true;
                        $where = array(
                            'user_id' => $this->user['id'],
                            'id' => $cart['id'],
                            'foreign_id' => $cart['foreign_id'],
                            'goods_type' => $cart['goods_type'],
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
        $this->ajaxReturn(successMsg('添加成功'));
    }
    //增减单个商品
    public function replaceOneGoodsToCart(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        //用户信息
        $this->user = AuthUser::check();
        $goods = $_POST;
        if(empty($goods)){
            $this->ajaxReturn(errorMsg('请求的数据不能为空'));
        }
        if($this->user['id']){//已登录
            $modelCart = D('Cart');
            $where = array(
                'user_id' => $this->user['id'],
                'foreign_id' => $goods['foreign_id'],
                'goods_type' => $goods['goods_type'],
            );
            $res = $modelCart->saveCart($where);
            if($res['status']== 0){
                $this->ajaxReturn(errorMsg($res['info']));
            }
        }else{//未登录
            $cart = unserialize(cookie('cart'));
            foreach ($cart as &$value){
                if($value['foreign_id'] == $goods['foreign_id'] && $value['goods_type'] == $goods['goods_type']){
                    $value['num'] = $goods['num'];
                }
            }
            cookie('cart',serialize($cart));
        }
        $this->ajaxReturn(successMsg('添加成功'));
    }
    //删除购物车信息
    public function delCart(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        $this->user = AuthUser::check();
        $goodsList = $_POST['goodsList'];
        if(empty($goodsList)){
            $this->ajaxReturn(errorMsg('请求的数据不能为空'));
        }
        //已登录
        if( $this->user['id']){
            foreach ($goodsList as $k=> &$v){
                $where['user_id']  = $this->user['id'];
                $where['foreign_id']  = $v['foreign_id'];
                $where['goods_type']  = $v['goods_type'];
                $result = D('cart') -> where($where)->delete();
                if(!$result){
                    $this -> ajaxReturn(errorMsg('删除失败'));
                }
                $this -> ajaxReturn(successMsg('删除成功'));
            }
        }
        //未登录
        $cart = unserialize(cookie('cart'));
        foreach ($cart as $key => &$value){
            foreach ($goodsList as $kk => &$vv)
                if($value['foreign_id'] == $vv['foreign_id'] && $value['goods_type'] == $vv['goods_type'] ){
                    unset($cart[$key]);
                }
        }
        cookie('cart',serialize($cart));
        $this -> ajaxReturn(successMsg('删除成功'));
    }

    
}