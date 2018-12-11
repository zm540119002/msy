<?php
namespace app\store\controller;

use common\component\GatewayClient\Gateway;

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
            $postData = input('post.');
            // client_id与uid绑定
            Gateway::$registerAddress = '127.0.0.1:1238';
            Gateway::bindUid($postData['client_id'], $this->user['id']);
            return successMsg($this->user['id']);
        }else{
            return $this->fetch();
        }
    }

    /**发送消息
     */
    public function sendMessage(){
        if(request()->isAjax()){
            Gateway::$registerAddress = '127.0.0.1:1238';
            $postData = input('post.');
            $msg = [
                'type' => 'msg',
                'msg' => $postData['msg'],
            ];
            Gateway::sendToUid($postData['user_id'],json_encode($msg));
            return successMsg($postData['user_id']);
        }else{
            return $this->fetch();
        }
    }
}