<?php
namespace Admin\Controller;

use web\all\Controller\BaseController;

class LogisticsController extends BaseController {
    /**物流-编辑
     */
    public function logisticsEdit(){
        $modelLogistics = D('Logistics');
        if(IS_POST){
            if(isset($_POST['logisticsId']) && intval($_POST['logisticsId'])){
                $_POST['delivery_time'] = strtotime($_POST['delivery_time']);
                $_POST['status'] = 1;
                $res = $modelLogistics->saveLogistics();
            }
            $this->ajaxReturn($res);
        }else{
            if (isset($_GET['logisticsId']) && intval($_GET['logisticsId'])){
                $LogisticsId = I('get.logisticsId', 0, 'int');
                $where = array(
                    'ut.id' => $LogisticsId,
                );
                $LogisticsInfo = $modelLogistics->selectLogistics($where);
                $this->LogisticsInfo = $LogisticsInfo[0];
            }
            $this->display();
        }
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