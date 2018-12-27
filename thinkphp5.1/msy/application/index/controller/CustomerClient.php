<?php
namespace app\index\controller;

class CustomerClient extends \common\controller\UserBase{
    //首页
    public function index(){
        if(request()->isAjax()){
            $modelChatMessage = new \common\model\ChatMessage();
            $config = [
                'field' => [
                    'cm.id','cm.from_id','cm.to_id','cm.content','cm.create_time',
                    'u.name','u.avatar',
                ],'join' => [
                    ['common.user u','u.id = cm.from_id','left'],
                ],
//                'where' => 'u.status = 0 and cm.status = 0 and cm.type = 1 and cm.to_read = 0 ' .
//                    'and (cm.from_id = ' .$this->user['id'] . ' and cm.to_id = 17) ' .
//                    'or ( cm.from_id = 17' . ' and cm.to_id = ' .$this->user['id'] . ')'
//                ,
                'where' => 'u.status = :u.status and cm.status = :cm.status and cm.type = :cm.type and cm.to_read = 0 ' .
                    'and (cm.from_id = ' .$this->user['id'] . ' and cm.to_id = 17) ' .
                    'or ( cm.from_id = 17' . ' and cm.to_id = ' .$this->user['id'] . ')',
                ['u.status'=>0,'cm.status'=>0,'cm.type'=>1,]
                ,
                'order' => [
                    'cm.create_time'=>'asc',
                ],
            ];
            $list = $modelChatMessage->getList($config);
            print_r($modelChatMessage->getLastSql());exit;
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
                foreach ($list as $message){
                    if($fromUser['from_id']==$message['from_id']){
                        $fromUser['messages'][] = [
                            'id' => $message['id'],
                            'name' => $message['name'],
                            'avatar' => $message['avatar'],
                            'content' => $message['content'],
                            'create_time' => $message['create_time'],
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
}