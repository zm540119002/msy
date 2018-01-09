<?php
namespace Admin\Controller;

use web\all\Controller\BaseController;

class OrderController extends BaseController {
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
        $where['o.logistics_status'] = array('gt',0);
        $logistics_status = $_GET['logistics_status'];
        if($logistics_status){
            $where['o.logistics_status'] = $logistics_status;
        }
        $keyword = I('get.keyword','','string');
        if($keyword) {
            $_where['o.sn'] = array('like', '%' . trim($keyword) . '%');
            $_where['u.name'] = array('like', '%' . trim($keyword) . '%');
            $_where['_logic'] = 'or';
            $where['_complex'] = $_where;
        }
        $field = array(
            'o.id','o.sn','o.logistics_status','o.amount','o.type',
            'o.remark','o.address_id', 'o.logistics_id','o.create_time','u.name',
            'l.status as deliver_status ',
        );
        $join = array(
            ' left join ucenter.user u on o.user_id = u.id ',
            ' left join logistics l on o.logistics_id = l.id ',
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

    /**
     * 订单详情
     */
    public function orderDetail(){
        if(!isset($_GET['orderId']) || !intval($_GET['orderId'])){
            $this->error('缺少订单ID');
        }
        $model = D('OrderDetail');
        $where['od.order_id'] = intval($_GET['orderId']);
        $field=[ 'o.id','o.sn','o.status as o_status','o.logistics_status','o.after_sale_status','o.payment_code',
            'o.amount','o.coupons_pay','o.wallet_pay','o.actually_amount','o.create_time','o.payment_time',
            'o.user_id','o.address_id','o.logistics_id','o.coupons_id','o.finished_time',
            'g.id as goods_id','g.goods_base_id','g.buy_type','g.sale_price','g.status',
            'gb.name as goods_name','gb.thumb_img', 'gb.main_img','gb.single_specification',
            'ca.user_id','ca.province','ca.city','ca.area',
            'ca.detailed_address','ca.consignee_name','ca.consignee_mobile',
        ];
        $join=[ ' left join orders o on od.order_id = o.id ',
            ' left join goods g on od.foreign_id = g.id ',
            ' left join goods_base gb on g.goods_base_id = gb.id ',
            ' left join consignee_address ca on o.address_id = ca.id ',];
        $this->orderDetail = $model->selectOrderDetail($where,$field,$join);
        $this->display();
    }

    /**
     * 导出订单
     */
    function expOrderList(){//导出Excel
        $xlsName  = "orderList";
        $xlsCell  = array(
            array('type','订单类型'),
            array('sn','订单号'),
            array('create_time','下单时间'),
            array('name','客户名称'),
            array('amount','金额'),
            array('deliver_status','出货状态'),
            array('logistics_status','订单状态'),
            array('pay_status','支付状态'),
        );
        $model = D('Order');
        $where['o.status'] = 0;
        $where['o.logistics_status'] = array('gt',0);
        $logistics_status = $_GET['logistics_status'];
        if($logistics_status){
            $where['o.logistics_status'] = $logistics_status;
        }
        $field = array(
            'o.id','o.sn','o.logistics_status','o.amount','o.type',
            'o.remark','o.address_id', 'o.logistics_id','o.create_time','u.name',
            'l.status as deliver_status ',
        );
        $join = array(
            ' left join ucenter.user u on o.user_id = u.id ',
            ' left join logistics l on o.logistics_id = l.id ',
        );
        $xlsData = $model->selectOrder($where,$field,$join);
        foreach ($xlsData as $k => $v)
        {
            $xlsData[$k]['sn'] = "'".$v['sn'];
            $xlsData[$k]['create_time'] = date("Y-m-d H:i:s",$v['create_time']);
            foreach (C('ORDER_CATEGORY') as $k1 => $v1){
                if($v['type'] == $k1){
                    $xlsData[$k]['type'] = $v1;
                }
            }
            foreach (C('ORDER_STATUS') as $k2 => $v2){
            //<!--订单状态1:待付款 2:待收货 3:待评价 4:已完成 5:已取消',-->
                if($v['logistics_status'] == $k2){
                    $xlsData[$k]['logistics_status'] = $v2;
                }
            }
            foreach (C('DELIVER_STATUS') as $k3 => $v3){
                if($v['deliver_status'] == $k3){
                    $xlsData[$k]['deliver_status'] = $v3;
                }
            }
            if($v['logistics_status'] != 1 && $v['logistics_status'] != 5){
                $xlsData[$k]['pay_status'] ='已支付';
            }else{
                $xlsData[$k]['pay_status'] ='未支付';
            }
        }
        exportExcel($xlsName,$xlsCell,$xlsData);
    }
}