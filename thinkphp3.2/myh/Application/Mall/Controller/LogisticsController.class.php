<?php
namespace Mall\Controller;

use web\all\Controller\BaseController;

class LogisticsController extends BaseController {
    /**确认收货
     */
    public function logisticsEdit(){
        $modelLogistics = D('Logistics');
        if(IS_POST){
            if(isset($_POST['logisticsId']) && intval($_POST['logisticsId'])){
                $_POST['delivery_time'] = strtotime($_POST['delivery_time']);
                $_POST['status'] = 3;
                $res = $modelLogistics->saveLogistics();
            }
            $this->ajaxReturn($res);
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