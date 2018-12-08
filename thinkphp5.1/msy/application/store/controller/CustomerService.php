<?php
namespace app\store\controller;

require_once dirname(__DIR__) . '../../../GatewayClient/Gateway.php';
use GatewayClient\Gateway;

class CustomerService extends \common\controller\UserBase{
    /**售前
     */
    public function beforeSale(){
        if(request()->isAjax()){
            return successMsg(123);
        }else{
            return $this->fetch();
        }
    }

    /**售后
     */
    public function afterSale(){
        if(request()->isAjax()){
            return successMsg(123);
        }else{
            return $this->fetch();
        }
    }

    /**售后
     */
    public function bindUid(){
        if(request()->isAjax()){
            $postData = input('.post');
            // client_id与uid绑定
            Gateway::bindUid($postData['client_id'], $this->user['id']);
            return successMsg(123);
        }else{
            return $this->fetch();
        }
    }
}