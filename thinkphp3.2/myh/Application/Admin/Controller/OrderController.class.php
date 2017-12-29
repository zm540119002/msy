<?php
namespace Admin\Controller;

use web\all\Controller\BaseController;

class OrderController extends BaseController {
    /**单位-管理
     */
    public function index(){
        $modelOrder = D('Order');
        if(IS_POST){
            $this->OrderList = $modelOrder->selectOrder();
            $this->display('OrderList');
        }else{
            $this->OrderList = $modelOrder->selectOrder();
            $this->display();
        }
    }

    /**单位-编辑
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

    //单位列表
    public function OrderList(){
        if(!IS_GET){
            $this->ajaxReturn(errorMsg(C('NOT_GET')));
        }

        $modelOrder = D('Order');
        $where = array(
            'ut.status' => 0,
        );
        $keyword = I('get.keyword','','string');
        if($keyword){
            $where['_complex'] = array(
                'ut.value' => array('like', '%' . trim($keyword) . '%'),
            );
        }
        $field = array(
        );
        $join = array(
        );

        $order = 'ut.key,ut.id';
        $group = "";
        $pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ? I('get.pageSize',0,'int') : C('DEFAULT_PAGE_SIZE');

        $OrderList = page_query($modelOrder,$where,$field,$order,$join,$group,$pageSize,$alias='ut');

        $this->OrderList = $OrderList['data'];
        $this->pageList = $OrderList['pageList'];
        $this->display();
    }

    /**单位-删除
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