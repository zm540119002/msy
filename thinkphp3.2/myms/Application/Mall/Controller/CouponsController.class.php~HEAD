<?php
namespace Mall\Controller;
use Think\Controller;
use web\all\Controller\AuthUserController;
use web\all\Controller\BaseController;
class CouponsController extends AuthUserController {
    public function coupons(){
        //已领取
        $couponsReceiveModel = D('CouponsReceive');
        $where['user_id'] = $this->user['id'];
        $where['failure_time']  = array('egt',time());
        $couponsReceiveList = $couponsReceiveModel->selectCouponsReceive($where);
        $this->couponsReceiveList=$couponsReceiveList;
        // 已领取优惠券Ids
        $couponsReceiveIds = [];
        foreach ($couponsReceiveList as $key => $value){
            $couponsReceiveIds[] = $value['coupons_id'];
        }
        //待领取
        $couponsModel = D('Coupons');
        if(!empty($couponsReceiveIds)){
            $map['id']  = array('not in',$couponsReceiveIds);
        }
        $map['failure_time']  = array('egt',time());
        $couponsUnReceive = $couponsModel->selectCoupons($map);
        $this -> couponsUnReceive = $couponsUnReceive;
        $this->display();
    }

    /**
     * @return array
     */
    public function couponsReceive(){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        $couponsId = I('post.couponsId',0,'int');
        $couponsModel = D('Coupons');
        $where = array(
            'id' => $couponsId,
        );
        $couponInfo = $couponsModel->selectCoupons($where);
        $couponInfo = $couponInfo[0];

        $_POST=array(
            'user_id'=>$this->user['id'],
            'coupons_id'=>$couponInfo['id'],
            'type'=>$couponInfo['type'],
            'amount'=>$couponInfo['amount'],
            'failure_time'=>$couponInfo['failure_time'],
            'acquire_time'=>time(),
        );
        $couponsReceiveModel = D('CouponsReceive');
        $rst = $couponsReceiveModel->addCouponsReceive();
        if($rst['status'] == 1){
            $couponInfo['acquire_time'] = time();
            $this -> couponInfo = $couponInfo;
            $this ->display('Coupons/couponsReceiveTmpl');
        }else{
            $this->ajaxReturn($rst);
        }

    }
    
}