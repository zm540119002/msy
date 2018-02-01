<?php
namespace Mall\Controller;
use Think\Controller;
use web\all\Lib\AuthUser;
use web\all\Controller\BaseController;
class CartController extends BaseController {
    //购物车信息
    public function index(){
        $modelGoods = D('Goods');
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
            $where = array(
                'ct.user_id' => $this->user['id'],
                'ct.type' => 1,
            );
            $field = array(
                'g.name','g.discount_price','g.price','g.special_price','g.group_price','g.main_img','g.buy_type'
            );
            $join = array(
                'left join goods g on g.id = ct.foreign_id ',
            );
            $this->cartList = $modelCart->selectCart($where,$field,$join);
        }else{//未登录
            if(!empty($cookieCart)){
                $where = array(
                    'g.id' => array('in',array_column($cookieCart,'foreign_id')),
                );
                $goodsList = $modelGoods->selectGoods($where);
                $this->cartList = GoodsNumMergeById($cookieCart,$goodsList);
            }
        }
        //购物车配置开启的项
        $this->unlockingFooterCart = unlockingFooterCartConfig(array(2,5));
        $this -> display();
    }
    //加入购物车
    public function addCart(){
        if(IS_POST){
            if(isset($_POST['goodsId']) && !empty($_POST['goodsId'])){//商品添加
                $foreignId = intval($_POST['goodsId']);
                $type = 1;
            }
            if(isset($_POST['projectId']) && !empty($_POST['projectId'])){//项目添加
                $foreignId = intval($_POST['projectId']);
                $type = 2;
            }
            $num = I('post.num',0,int);
            if(isset($_POST['foreignId']) && !empty($_POST['foreignId'])){//购物车修改数量
                $foreignId = intval($_POST['foreignId']);
                $type =  intval($_POST['type']);
            }
            //已登录
            $this -> user =  AuthUser::check();
            if( $this -> user['id']){
                $model = D('Cart');
                $cartInfo = $model -> getCartInfoByForeignId($this->user['id'],$foreignId,$type);
                if(empty($cartInfo)){//添加
                    $_POST = [];
                    $_POST['user_id'] = $this->user['id'];
                    $_POST['foreign_id'] = $foreignId;
                    $_POST['num'] = $num;
                    $_POST['create_time'] = time();
                    $res = $model->addCart();
                }else{//修改
                    if(isset($_POST['num22'])&& intval($_POST['num22'])){
                        $num = intval($_POST['num11']);
                    }else{
                        $_POST['num'] = $cartInfo['num'] + $num;
                    }
                    $where = array(
                        'user_id' => $this->user['id'],
                        'id' => $cartInfo['id'],
                        'foreign_id' => $cartInfo['foreign_id'],
                    );
                    $res = $model->saveCart($where);
                }
                $this->ajaxReturn(successMsg('加入购物车成功'));
            }else{ //没有登录
                if(isset($_POST['foreignId']) && intval($_POST['foreignId'])){//修改
                    $curCartArray = unserialize(cookie('cart'));
                    foreach ($curCartArray as $k =>$v){
                        if(intval($v['foreign_id']) == $foreignId && intval($v['type']) == $type){
                            $curCartArray[$k]['num'] = $_POST['num'];
                            cookie('cart',serialize($curCartArray));
                            $this -> ajaxReturn(successMsg('修改数量成功'));
                        }
                    }
                }else{//添加
                    $curCartArray = unserialize(cookie('cart'));
                    if($curCartArray==""){
                        $cartInfo[0]["foreign_id"]=$foreignId;//商品id保存到二维数组中
                        $cartInfo[0]["num"]=$num;//商品数量保存到二维数组中
                        $cartInfo[0]["type"]=$type;//商品数量保存到二维数组中
                        cookie('cart',serialize($cartInfo));
                    }else {
                        //返回数组键名倒序取最大
                        $ar_keys = array_keys($curCartArray);
                        $len = count($ar_keys);
                        $maxArrayKeyId = $ar_keys[$len - 1] + 1;
                        $is_exist=$this->isExist($foreignId,$num,$curCartArray,$type);
                        if($is_exist==false){
                            $curCartArray[$maxArrayKeyId]["foreign_id"] = $foreignId;
                            $curCartArray[$maxArrayKeyId]["num"] = $num;
                            $curCartArray[$maxArrayKeyId]["type"] = $type;
                            cookie('cart',serialize($curCartArray));
                        }else{
                            $arr_exist=explode("/",$is_exist);
                            $id=$arr_exist[0];
                            $num=$arr_exist[1];
                            $curCartArray[$id]["num"]=$num;
                            cookie('cart',serialize($curCartArray));
                        }
                    }
                }
                $this -> ajaxReturn(successMsg('加入购物车成功'));
            }
        }
    }


    //判断购物车是否存在相同商品
    public function isExist($id,$num,$array,$type)
    {
        $isExist=false;
        foreach($array as $key1=>$value)
        {
            if(intval($value['foreign_id']) == $id && intval($value['type'])==$type){
                $num=$value["num"]+$num;
                $isExist=$key1."/".$num;
            }
        }
        return $isExist;
    }


    //删除购物车信息
    public function delCart(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        $this->user = AuthUser::check();
        //已登录
        if( $this->user['id']){
            if(isset($_POST['foreign_ids']) && $_POST['foreign_ids']){
                $where['user_id']  = $this->user['id'];
                $where['foreign_id']  = array('in',$_POST['foreign_ids']);
                $result = D('cart') -> where($where)->delete();
            }
            if(!$result){
                $this -> ajaxReturn(errorMsg('删除失败'));
            }
            $this -> ajaxReturn(successMsg('删除成功'));
        }
        //未登录
        $cart = unserialize(cookie('cart'));
        $foreignIds =  $_POST['foreign_ids'];
        foreach ($cart as $key => &$value){
            foreach ($foreignIds as $kk => &$vv)
                if($value['foreign_id'] == $vv){
                    unset($cart[$key]);
                }
        }
        cookie('cart',serialize($cart));
        $this -> ajaxReturn(successMsg('删除成功'));
    }

    
}