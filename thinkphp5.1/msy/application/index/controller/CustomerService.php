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
    public function setToMessageRead(){
        if(request()->isAjax()){
            $postData = input('post.');
            $modelChatMessage = new \common\model\ChatMessage();
            $where = [
                ['status','=',0],
                ['to_read','=',0],
                ['from_id','=',$postData['from_id']],
                ['to_id','=',$this->user['id']],
                ['id','in',$postData['messageIds']],
            ];
            $whereOr = [
                ['from_id','=',$this->user['id']],
                ['to_id','=',$postData['from_id']],
            ];
            $res = $modelChatMessage->where($where)->whereOr($whereOr)->setField('to_read',1);
            return errorMsg($modelChatMessage->getLastSql());
            if($res==false){
                return errorMsg('设置已读出错',$modelChatMessage->getError());
            }
            return successMsg('成功！');
        }
    }
}