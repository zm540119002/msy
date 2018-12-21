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

    /**绑定用户ID
     */
    public function bindUid(){
        if(request()->isAjax()){
            $postData = input('post.');
            // client_id与uid绑定
            Gateway::bindUid($postData['client_id'], $this->user['id']);
            if(!Gateway::isUidOnline($this->user['id'])){
                return successMsg('绑定失败！');
            }
        }
    }

    /**发送消息
     */
    public function sendMessage(){
        if(request()->isAjax()){
            $postData = input('post.');
            if(Gateway::isUidOnline($this->user['id'])){
                $msg = [
                    'type' => 'msg',
                    'msg' => $postData['msg'],
                ];
                Gateway::sendToUid($postData['user_id'],json_encode($msg));
            }else{
                return successMsg('发送失败！');
            }
        }
    }
}