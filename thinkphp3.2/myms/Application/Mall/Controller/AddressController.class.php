<?php
namespace Mall\Controller;
use web\all\Controller\AuthUserController;
class AddressController extends AuthUserController {
    //增加修改地址页面
    public function addAddress(){
        $addressModel = D('Address');
        if(IS_POST){
            $uid = $this->user['id'];
            $data = $_POST;
            if(isset($_POST['addressId']) && !empty($_POST['addressId']) ){
                //修改
                //开启事务
                $addressModel -> startTrans();
                try{
                    $addressId = $_POST['addressId'];
                    $where = array(
                        'id' => $addressId,
                        'user_id' => $uid,
                    );
                    $result = $addressModel -> where($where)->save($data);
                    if(false === $result ){
                        throw new \Exception($this ->ajaxReturn(errorMsg('修改地址失败')));
                    }
                    if($_POST['is_default'] == 1){
                        $result = $addressModel -> updateAddressNotDefault($uid,$addressId);
                        if(false === $result){
                            throw new \Exception($this ->ajaxReturn(errorMsg('修改其他地址默认值失败')));
                        }
                    }
                    $addressModel->commit();
                    show(1,'修改地址成功',$addressId);
                }catch (Exception $e){
                    $addressModel->rollback();
                    $this->ajaxReturn($e->getMessage());
                }

            }else{
                //增加
                //开启事务
                $addressModel -> startTrans();
                try{
                    $data['user_id'] = $uid;
                    $addressId = $addressModel->add($data);
                    if(!$addressId){
                        throw new \Exception($this ->ajaxReturn(errorMsg('增加地址失败')));
                    }
                    if($_POST['is_default'] == 1){
                        $result = $addressModel -> updateAddressNotDefault($uid,$addressId);
                        if(false === $result){
                            throw new \Exception($this ->ajaxReturn(errorMsg('修改其他地址默认值失败')));
                        }
                    }
                    $addressModel->commit();
                    show(1,'增加地址成功',$addressId);
                }catch (Exception $e){
                    $addressModel->rollback();
                    $this->ajaxReturn($e->getMessage());
                }
            }
        }else{
            $uid = $this->user['id'];
            if(isset($_GET['addressId'])){
                $id = $_GET['addressId'];
                $address = $addressModel -> getUserAddressById($uid,$id);
                $this -> address = $address;
            }
            $this->unlockingFooterCart = unlockingFooterCartConfig(array(24));
            if(isset($_GET['orderId'])){
                $this -> orderId = $_GET['orderId'];
                $this->unlockingFooterCart = unlockingFooterCartConfig(array(25));
            }
            $this -> display();
        }

    }

    //地址列表
    public function addressList(){
        $userId = $this->user['id'];
        $this -> orderId = $_GET['orderId'];
        $addressList = D('address') -> getUserAddressListByUid($userId);
        $this -> addressList = $addressList;
        $this->unlockingFooterCart = unlockingFooterCartConfig(array(26));
        $this->display();
    }

    //删除地址
    public function delAddress(){
        if(IS_POST){
            $where['user_id'] =$this->user['id'];
            $where['id'] = $_POST['addressId'];
            $result = M('address') -> where($where)->delete();
            if($result){
                $this->ajaxReturn(successMsg('删除成功'));
            }else{
                $this->ajaxReturn(errorMsg('删除失败'));
            }
        }
    }

}