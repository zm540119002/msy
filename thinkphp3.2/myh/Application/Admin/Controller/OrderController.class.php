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
        $where = array(
            'o.status' => 0,
        );
        $logistics_status = $_GET['logistics_status'];
//        $logistics_status = 3;
        if($logistics_status){
            $_where=array(
                'o.logistics_status' => $logistics_status,
            );
        }
        $where =  array_merge($_where,$where);
        $keyword = I('get.keyword','','string');
        if($keyword){
            $where['_complex'] = array(
                'o.sn' => array('like', '%' . trim($keyword) . '%'),
            );
//            $where['_complex'] = array(
//                'm.name' => array('like', '%' . trim($keyword) . '%'),
//            );
        }
        $field = array(
        );
        $join = array(
            ' left join member m on o.user_id = m.user_id ',
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
}