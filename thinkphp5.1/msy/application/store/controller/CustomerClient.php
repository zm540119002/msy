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
                    'cm.from_id','cm.to_id',
                    'u.name','u.avatar',
                ],'join' => [
                    ['common.user u','u.id = cm.from_id','left'],
                ],'where' => [
                    ['u.status','=',0],
                    ['cm.status','=',0],
                    ['cm.type','=',1],
                    ['cm.to_id','=',$this->user['id']],
                ],'group' => 'cm.from_id'
                ,'order' => [
                    'cm.create_time'=>'asc',
                ],
            ];
            $fromUserList = $modelChatMessage->getList($config);
            foreach ($fromUserList as &$fromUser){
                $config = [
                    'field' => [
                        'cm.id','cm.from_id','cm.to_id','cm.to_read','cm.content','cm.create_time',
                        'u.name','u.avatar',
                    ],'join' => [
                        ['common.user u','u.id = cm.from_id','left'],
                    ],'where' => 'u.status = 0 and cm.status = 0 and cm.type = 1 ' .
                        'and (cm.from_id = ' . $fromUser['from_id'] . ' and cm.to_id = ' . $this->user['id'] .') ' .
                        'or ( cm.from_id = ' . $this->user['id'] . ' and cm.to_id = ' . $fromUser['from_id'] . ')'
                    ,'order' => [
                        'cm.create_time'=>'asc',
                    ],'limit' => 10,
                ];
                $fromUser['messages'] = $modelChatMessage->getList($config);
            }
            foreach ($fromUserList as &$fromUser){
                $fromUser['unreadNum'] = 0;
                foreach ($fromUser['messages'] as $message){
                    if($message['to_read']==0){
                        $fromUser['unreadNum'] ++;
                    }
                    if($fromUser['from_id']==$message['from_id']){
                        $message['who'] = 'others';
                    }else{
                        $message['who'] = 'me';
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