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
                ],'group' => 'cm.from_id',
            ];
            $fromUserList = $modelChatMessage->getList($config);
            foreach ($fromUserList as &$fromUser){
                $config = [
                    'field' => [
                        'cm.id','cm.from_id','cm.to_id','cm.to_read','cm.content','cm.create_time',
                        'u.name','u.avatar',
                    ],'join' => [
                        ['common.user u','u.id = cm.from_id','left'],
                    ],'where' => [
                        ['u.status','=',0],
                        ['cm.status','=',0],
                        ['cm.type','=',1],
                        [
                            'cm.from_id|cm.to_id', ['=',$this->user['id']],
                        ],
                    ],'order' => [
                        'cm.create_time'=>'asc',
                    ],'limit' => 10,
                ];
                $fromUser['messages'][] = $modelChatMessage->getList($config);
            }
            print_r($fromUserList);exit;
            $config = [
                'field' => [
                    'cm.id','cm.from_id','cm.to_id','cm.to_read','cm.content','cm.create_time',
                    'u.name','u.avatar',
                ],'join' => [
                    ['common.user u','u.id = cm.from_id','left'],
                ],'where' => [
                    ['u.status','=',0],
                    ['cm.status','=',0],
                    ['cm.type','=',1],
                    [
                        'cm.from_id|cm.to_id', ['=',$this->user['id']],
                    ],
                ],'order' => [
                    'cm.create_time'=>'asc',
                ],'limit' => 200,
            ];
            $list = $modelChatMessage->getList($config);
            $fromUserIds = array_unique(array_column($list,'from_id'));
            $fromUserList = [];
            foreach ($fromUserIds as $fromUserId){
                foreach ($list as $message){
                    if($fromUserId==$message['from_id'] && $message['from_id']!=$this->user['id']){
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
                $fromUser['unreadNum'] = 0;
                foreach ($list as $message){
                    if($fromUser['from_id']==$message['from_id']){
                        if($message['to_read']==0){
                            $fromUser['unreadNum'] ++;
                        }
                        $fromUser['messages'][] = [
                            'id' => $message['id'],
                            'name' => $message['name'],
                            'avatar' => $message['avatar'],
                            'content' => $message['content'],
                            'create_time' => $message['create_time'],
                            'to_read' => $message['to_read'],
                            'who' => 'others',
                        ] ;
                    }
                    if($fromUser['from_id']==$message['to_id']){
                        $fromUser['messages'][] = [
                            'id' => $message['id'],
                            'name' => $this->user['name'],
                            'avatar' => $this->user['avatar'],
                            'content' => $message['content'],
                            'create_time' => $message['create_time'],
                            'to_read' => $message['to_read'],
                            'who' => 'me',
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