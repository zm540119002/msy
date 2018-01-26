<?php
namespace Business\Controller;

use web\all\Controller\BaseController;
use web\all\Lib\AuthUser;

class PersonalCenterController extends BaseController {
    //个人中心-首页
    public function index(){
        if(IS_POST){
        }else{
            //用户信息
            $this->user = AuthUser::check();
            if($this->user['id']){
                $where = array(
                    'o.user_id' => $this->user['id'],
                );
                //按订单状态分组统计
                $modelOrder = D('Order');
                $this->orderStatusCount = $modelOrder->orderStatusCount($where);
                //购物车统计
                $modelCart = D('Cart');
                $where = array(
                    'ct.user_id' => $this->user['id'],
                );
                $this->cartCount = $modelCart->cartCount($where);
            }else{
                $cart = unserialize(cookie('cart'));
                $this->cartCount = count($cart);
            }
            $this->display();
        }
    }
}