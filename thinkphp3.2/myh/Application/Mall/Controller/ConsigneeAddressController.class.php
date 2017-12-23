<?php
namespace Mall\Controller;

use web\all\Controller\AuthUserController;

class ConsigneeAddressController extends AuthUserController {
    //机构登记-收货人地址编辑页
    public function registerAddressEdit(){
        $modelConsigneeAddress = D('ConsigneeAddress');
        if(IS_POST){
            $this->editAddress();
        }else{
            //默认收货人地址
            $where = array(
                'ca.user_id' => $this->user['id'],
                'ca.type' => 1,
            );
            $consigneeAddressInfo = $modelConsigneeAddress->selectConsigneeAddress($where);
            $this->consigneeAddressInfo = $consigneeAddressInfo[0];
            $this->display();
        }
    }

    //收货人地址-列表页
    public function addressList(){
        $modelConsigneeAddress = D('ConsigneeAddress');
        if(IS_POST){
        }else{
            $where = array(
                'ca.user_id' => $this->user['id'],
            );
            $this->consigneeAddressList = $modelConsigneeAddress->selectConsigneeAddress($where);
            $this->display();
        }
    }

    //收货人地址-编辑页
    public function addressEdit(){
        $modelConsigneeAddress = D('ConsigneeAddress');
        if(IS_POST){
            if( isset($_POST['region']) && !empty($_POST['region']) ){
                $_POST['province'] = $_POST['region'][0];
                $_POST['city'] = $_POST['region'][1];
                $_POST['area'] = $_POST['region'][2];
            }
            $this->editAddress();
        }else{
            if( isset($_GET['consigneeAddressId']) && intval($_GET['consigneeAddressId']) ){
                $where = array(
                    'ca.user_id' => $this->user['id'],
                    'ca.id' => I('get.consigneeAddressId',0,'int'),
                );
                $consigneeAddress = $modelConsigneeAddress->selectConsigneeAddress($where);
                $this->consigneeAddressInfo = $consigneeAddress[0];
            }
            $this->display();
        }
    }

    //收货人地址-删除
    public function addressDel(){
        $modelConsigneeAddress = D('ConsigneeAddress');
        if( isset($_POST['consigneeAddressId']) && !empty($_POST['consigneeAddressId']) ){
            $where = array(
                'user_id' => $this->user['id'],
            );
            $res = $modelConsigneeAddress->delConsigneeAddress($where);
            $this->ajaxReturn($res);
        }
        $this->ajaxReturn(errorMsg('未删除'));
    }

    //收货人地址-设为默认地址
    public function setDefaultAddress(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        if( !isset($_POST['id']) || empty($_POST['id']) ){
            $this->ajaxReturn(errorMsg('缺少参数ID'));
        }
        $id = I('post.id',0,'int');
        $modelConsigneeAddress = D('ConsigneeAddress');
        $where = array(
            'user_id' => $this->user['id'],
        );
        //先把机构下所有地址类型置为0
        $_POST['type'] = 0;
        $res = $modelConsigneeAddress->saveConsigneeAddress($where);
        if($res['status']==0){
            $this->ajaxReturn(errorMsg($modelConsigneeAddress->getError()));
        }
        //设置为默认地址
        $where['id'] = $id;
        $_POST['type'] = 1;
        $res = $modelConsigneeAddress->saveConsigneeAddress($where);
        if($res['status']==0){
            $this->ajaxReturn(errorMsg($modelConsigneeAddress->getError()));
        }
        $this->ajaxReturn($res);
    }

    //编辑收货人地址
    private function editAddress(){
        $modelConsigneeAddress = D('ConsigneeAddress');
        //如果有设置为默认地址，则需要先把本机构下所有收货地址type置为0
        if( isset($_POST['type']) && intval($_POST['type']) ){
            $res = $modelConsigneeAddress->setTypeZeroByCompanyId($this->user['id']);
            if($res['status']==0){
                $this->ajaxReturn($res['info']);
            }
        }
        if( isset($_POST['consigneeAddressId']) && intval($_POST['consigneeAddressId']) ){
            $where = array(
                'user_id' => $this->user['id'],
            );
            $res = $modelConsigneeAddress->saveConsigneeAddress($where);
        }else{
            $_POST['user_id'] = $this->user['id'];
            $res = $modelConsigneeAddress->addConsigneeAddress();
        }
        $this->ajaxReturn($res);
    }
}