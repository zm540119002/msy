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
                'content' => $postData['content'],
                'create_time' => time(),
            ];
            if(Gateway::isUidOnline($postData['to_user_id'])){
                $saveData['send_sign'] = 1;
            }
            $res = $modelChatMessage->edit($saveData);
            if($res['status']==0){
                return errorMsg('保存失败！',$res);
            }
            if(Gateway::isUidOnline($postData['to_user_id'])){
                $msg = [
                    'type' => 'msg',
                    'content' => $postData['content'],
                    'from_id' => $this->user['id'],
                    'from_name' => $this->user['name'],
                    'avatar' => $this->user['avatar'],
                    'id' => $res['id'],
                ];
                Gateway::sendToUid($postData['to_user_id'],json_encode($msg));
            }
            $postData['who'] = 'me';
            $postData['name'] = $this->user['name'];
            $postData['avatar'] = $this->user['avatar'];
            $postData['id'] = $res['id'];
            $this->assign('info',$postData);
            return view('customer_client/info_tpl');
        }
    }
    /**设置消息已读
     */
    public function setToMessageRead(){
        if(request()->isAjax()){
            $postData = input('post.');
            $modelChatMessage = new \common\model\ChatMessage();
//            $where = [
//                ['status','=',0],
//                ['to_read','=',0],
//                ['id','in',$postData['messageIds']],
//            ];
//            $whereOr = [
//                [
//                    ['from_id','=',$this->user['id']],
//                    ['to_id','=',$postData['from_id']],
//                ],[
//                    ['from_id','=',$postData['from_id']],
//                    ['to_id','=',$this->user['id']],
//                ],
//            ];
            $where = 'status = 0 and to_read = 0 and id in ' . $postData['messageIds'] .
                'and (cm.from_id = ' . $postData['from_id'] . ' and cm.to_id = ' . $this->user['id'] .') ' .
                'or ( cm.from_id = ' . $this->user['id'] . ' and cm.to_id = ' . $postData['from_id'] . ')';
            return successMsg($where);
            $res = $modelChatMessage->where($where)->setField('to_read',1);
            if($res==false){
                return errorMsg('设置已读出错',$modelChatMessage->getError());
            }
            return successMsg('成功！');
        }
    }
    /**删除
     */
    public function delMessage(){
        if(request()->isAjax()){
            $postData = input('post.');
            $modelChatMessage = new \common\model\ChatMessage();
            $where = [
                ['status','=',0],
                ['id','in',$postData['messageIds']],
            ];
            $whereOr = [
                [
                    ['from_id','=',$this->user['id']],
                    ['to_id','=',$postData['from_id']],
                ],[
                    ['from_id','=',$postData['from_id']],
                    ['to_id','=',$this->user['id']],
                ],
            ];
            $res = $modelChatMessage->where($where)->whereOr($whereOr)->setField('status',2);
            if($res==false){
                return errorMsg('删除失败！',$modelChatMessage->getError());
            }
            return successMsg('删除成功！');
        }
    }
}