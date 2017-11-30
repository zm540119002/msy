<?php
namespace Myms\Controller;
use UserCenter\Controller\BaseAuthUserController;
class AddressController extends BaseAuthUserController {
    //增加修改地址页面
    public function addAddress(){
        if(IS_POST){
            $uid = $this->user['id'];
            $addressModel = D('address');
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
                    $result = M('address') -> where($where)->save($data);
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
                    $addressId = M('address')->add($data);
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
                $address = D('address') -> getUserAddressById($uid,$id);
                $this -> address = $address;
            }
            if(isset($_GET['goodsId'])){
                $this -> goodsId = $_GET['goodsId'];
            }
            if( isset($_GET['cartIds'])){
                $this -> cartIds = $_GET['cartIds'];
            }
            $this -> display();
        }

    }

   
    //地址列表
    public function addressList(){
        $userId = $this->user['id'];
        $cartIds = $_GET['cartIds'];
        $this -> cartIds = $cartIds;
        $goodsId =$_GET['goodsId'];
        $this -> goodsId = $goodsId;
        $projectId =$_GET['projectId'];
        $this -> projectId = $projectId;
        $addressList = D('address') -> getUserAddressListByUid($userId);
        $this -> addressList = $addressList;
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