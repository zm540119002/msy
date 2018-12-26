<?php
namespace app\store\controller;

class CustomerClient extends \common\controller\UserBase{
    /**售前
     */
    public function beforeSale(){
        if(request()->isAjax()){
            $modelChatMessage = new \common\model\ChatMessage();
            $config = [
                'field' => [
                    'cm.from_id','cm.to_id','cm.content','cm.create_time',
                    'u.name','u.avatar',
                ],'join' => [
                    ['common.user u','u.id = cm.from_id','left'],
                ],'where' => [
                    ['u.status','=',0],
                    ['cm.status','=',0],
                    ['cm.type','=',1],
                    ['cm.read','=',0],
                    ['cm.to_id','=',$this->user['id']],
                ],'whereOr' => [
                    ['cm.from_id','=',$this->user['id']],
                ],'order' => [
                    'cm.create_time'=>'asc',
                ],
            ];
            $list = $modelChatMessage->getList($config);
//            print_r($list);exit;
            $fromUserIds = array_unique(array_column($list,'from_id'));
            $fromUserList = [];
            foreach ($fromUserIds as $fromUserId){
                foreach ($list as $message){
                    if($fromUserId==$message['from_id']){
                        $fromUserList[] = [
                            'from_id' => $message['from_id'],
                            'to_id' => $message['to_id'],
                            'name' => $message['name'],
                            'avatar' => $message['avatar'],
                        ] ;
                        break;
                    }
                }
            }
            foreach ($fromUserList as &$fromUser){
                foreach ($list as $message){
                    if($fromUser['from_id']==$message['from_id']){
                        $fromUser['messages'][] = [
                            'content' => $message['content'],
                            'create_time' => $message['create_time'],
                        ] ;
                    }
                }
            }
            $this->assign('list',$fromUserList);
            return view('list_tpl');
        }else{
            return $this->fetch();
        }
    }

    /**售后
     */
    public function afterSale(){
        if(request()->isAjax()){
            return successMsg('成功');
        }else{
            return $this->fetch();
        }
    }
}