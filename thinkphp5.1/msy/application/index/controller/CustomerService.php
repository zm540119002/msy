<?php
namespace app\index\controller;

use common\component\GatewayClient\Gateway;

class CustomerService extends \common\controller\UserBase{
    /**绑定用户ID
     */
    public function bindUid(){
        if(request()->isAjax()){
            $postData = input('post.');
            // client_id与uid绑定
            Gateway::bindUid($postData['client_id'], $this->user['id']);
            return successMsg('成功！');
        }
    }
    /**发送消息
     */
    public function sendMessage(){
        if(request()->isAjax()){
            $postData = input('post.');
            $modelChatMessage = new \common\model\ChatMessage();
            $saveData = [
                'from_id' => $this->user['id'],
                'to_id' => $postData['to_user_id'],
                'content' => $postData['msg'],
                'create_time' => time(),
            ];
            $info = $modelChatMessage->edit($saveData);
            if($info['status']==0){
                return errorMsg('失败！');
            }
            if(Gateway::isUidOnline($postData['to_user_id'])){
                $msg = [
                    'type' => 'msg',
                    'msg' => $postData['msg'],
                ];
                Gateway::sendToUid($postData['to_user_id'],json_encode($msg));
            }
            return successMsg('成功！');
        }
    }
    /**设置消息已读
     */
    public function setMessageRead(){
        if(request()->isAjax()){
            $postData = input('post.');
            $modelChatMessage = new \common\model\ChatMessage();
            $where = [
                ['cm.status','=',0],
                ['cm.read','=',0],
                ['cm.from_id','=',$postData['from_id']],
                ['cm.id','in',$postData['messageId']],
            ];
            return errorMsg('设置已读出错',$where);
            $res = $modelChatMessage->where($where)->setField('read',1);
            return errorMsg('设置已读出错',$modelChatMessage->getLastSql());
            if($res==false){
                return errorMsg('设置已读出错',$modelChatMessage->getError());
            }
            return successMsg('成功！');
        }
    }
}