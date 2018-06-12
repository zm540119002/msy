<?php
namespace app\store\controller;

class Deploy extends \common\controller\UserBase{

    //入驻部署首页
    public function index(){
        return $this->fetch();
    }

    /**入驻登记
     */
    public function register(){
        $model = new \app\store\model\Store();
        if(request()->isAjax()){
            return $model -> edit($this -> user['id']);
        }else{
            $mobilePhone = $this -> user['mobile_phone'];
            $this->assign('mobilePhone',$mobilePhone);
            if(input('?store_id')){
                $storeId = input('store_id');
                $where = [
                    ['id','=',$storeId],
                ];
                $StoreInfo =  $model -> getInfo($where);
                $this -> assign('StoreInfo',$StoreInfo);
            }
            return $this->fetch();
        }
    }
}