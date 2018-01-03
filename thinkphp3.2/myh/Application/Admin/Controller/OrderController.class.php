<?php
namespace Admin\Controller;

use web\all\Controller\BaseController;

class OrderController extends BaseController {
    /**单位-管理
     */
    public function index(){
        $this->display();

    }

    /**订单-编辑
     */
    public function OrderEdit(){
        $modelOrder = D('Order');
        if(IS_POST){
            if(isset($_POST['OrderId']) && intval($_POST['OrderId'])){
                $res = $modelOrder->saveOrder();
            }else{
                $res = $modelOrder->addOrder();
            }
            $this->ajaxReturn($res);
        }else{
            if (isset($_GET['OrderId']) && intval($_GET['OrderId'])){
                $OrderId = I('get.OrderId', 0, 'int');
                $where = array(
                    'ut.id' => $OrderId,
                );
                $OrderInfo = $modelOrder->selectOrder($where);
                $this->OrderInfo = $OrderInfo[0];
            }
            $this->display();
        }
    }

    //订单列表
    public function OrderList(){
        if(!IS_GET){
            $this->ajaxReturn(errorMsg(C('NOT_GET')));
        }
        $modelOrder = D('Order');
        $where['o.status'] = 0;
        $logistics_status = $_GET['logistics_status'];
        if($logistics_status){
            $where['o.logistics_status'] = $logistics_status;
        }
        $keyword = I('get.keyword','','string');
        if($keyword){
            $_where['o.sn']  = array('like', '%' . trim($keyword) . '%');
            $_where['u.name']  = array('like', '%' . trim($keyword) . '%');
            $_where['_logic'] = 'or';
            $where['_complex'] = $_where;
        }

        $field = array(
            'o.id','o.sn','o.pay_sn','o.logistics_status','o.payment_code','o.amount',
            'o.coupons_pay','o.wallet_pay','o.user_id',  'o.actually_amount','o.remark','o.address_id',
            'o.logistics_id','o.create_time','o.payment_time','o.finished_time', 'u.name',

        );
        $join = array(
            ' left join ucenter.user u on o.user_id = u.id ',
        );
        $order = 'o.id desc';
        $group = "";
        $pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ? I('get.pageSize',0,'int') : C('DEFAULT_PAGE_SIZE');
        $orderList = page_query($modelOrder,$where,$field,$order,$join,$group,$pageSize,$alias='o');
        $this->orderList = $orderList['data'];
        $this->pageList = $orderList['pageList'];
        $this->display();
    }

    /**订单-删除
     */
    public function delOrder(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        $modelOrder = D('Order');
        $res = $modelOrder->delOrder();
        $this->ajaxReturn($res);
    }

    public function orderDetail(){
        if(!isset($_GET['orderId']) || !intval($_GET['orderId'])){
            $this->error('缺少订单ID');
        }
        $model = D('OrderDetail');
        $where['od.id'] = intval($_GET['orderId']);
        $field=['od.order_sn','od.type','od.status','od.price','od.num','od.foreign_id','od.user_id',

        ];
        $join=[ ' left join order_detail od on o.sn = od.order_sn ',
            'left join goods od on o.sn = od.order_sn'];
        $orderDetail = $model->selectOrderDetail($where,$field,$join);
        print_r($orderDetail);
        $this->display();
    }
}