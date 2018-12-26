<?php
namespace app\store\controller;

class CustomerClient extends \common\controller\UserBase{
    /**售前
     */
    public function beforeSale(){
        if(request()->isAjax()){
            return successMsg('成功');
        }else{
            $modelChatMessage = new \common\model\ChatMessage();
            $config = [
                'field' => [
                    'cm.from_id','cm.to_id','cm.content',
                    'u.name',
                ],'join' => [
                    ['common.user u','u.id = cm.from_id','left'],
                ],'where' => [
                    ['u.status','=',0],
                    ['cm.status','=',0],
                    ['cm.type','=',1],
                    ['cm.read','=',0],
                    ['cm.to_id','=',$this->user['id']],
                ],
            ];
            $list = $modelChatMessage->getList($config);
            $fromUserIds = array_unique(array_column($list,'from_id'));
            $fromUserList = [];
            foreach ($fromUserIds as $fromUserId){
                foreach ($list as $message){
                    if($fromUserId==$message['from_id'])
                    $fromUserList[$fromUserId]['content'] = $message['content'];
                }
            }
            print_r($fromUserList);exit;
//            $this->assign('list',$list);
//            return view('list_tpl');
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