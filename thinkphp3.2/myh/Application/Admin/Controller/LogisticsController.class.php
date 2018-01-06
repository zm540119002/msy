<?php
namespace Admin\Controller;

use web\all\Controller\BaseController;

class LogisticsController extends BaseController {
    /**单位-管理
     */
    public function index(){
        $this->display();

    }

    /**促销-编辑
     */
    public function logisticsEdit(){
        $modelLogistics = D('Logistics');
        if(IS_POST){
            if(isset($_POST['LogisticsId']) && intval($_POST['LogisticsId'])){
                $res = $modelLogistics->saveLogistics();
            }else{
                $modelLogistics->startTrans();
                $_POST['delivery_time'] = strtotime($_POST['delivery_time']);
                $_POST['status'] = 1;
                $res = $modelLogistics->addLogistics();
                if(!$res['id']){
                    $modelLogistics->rollback();
                }
                unset( $_POST['status']);
                $_POST['logistics_id'] = $res['id'];
                $_POST['logistics_status'] = 3;
                $res = D('Order')->saveOrder();
                if(!$res['id']){
                    $modelLogistics->rollback();
                }
                $modelLogistics->commit();
            }
            $this->ajaxReturn($res);
        }else{
            if (isset($_GET['LogisticsId']) && intval($_GET['LogisticsId'])){
                $LogisticsId = I('get.LogisticsId', 0, 'int');
                $where = array(
                    'ut.id' => $LogisticsId,
                );
                $LogisticsInfo = $modelLogistics->selectLogistics($where);
                $this->LogisticsInfo = $LogisticsInfo[0];
            }
            $this->display();
        }
    }

    //订单列表
    public function logisticsList(){
        if(!IS_GET){
            $this->ajaxReturn(errorMsg(C('NOT_GET')));
        }
        $modelLogistics = D('Logistics');
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

        $Logistics = 'o.id desc';
        $group = "";
        $pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ? I('get.pageSize',0,'int') : C('DEFAULT_PAGE_SIZE');
        $LogisticsList = page_query($modelLogistics,$where,$field,$Logistics,$join,$group,$pageSize,$alias='o');
        $this->LogisticsList = $LogisticsList['data'];
        $this->pageList = $LogisticsList['pageList'];
        $this->display();
    }

    /**订单-删除
     */
    public function delLogistics(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        $modelLogistics = D('Logistics');
        $res = $modelLogistics->delLogistics();
        $this->ajaxReturn($res);
    }
}