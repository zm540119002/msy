<?php
namespace Business\Controller;

use web\all\Controller\AuthAgentController;

class AgentController extends AuthAgentController {
    //代理商-首页
    public function index(){
        //用户信息
        $this->assign('user',$this->user);
        //购物车配置开启的项
        $this->unlockingFooterCart = unlockingFooterCartConfig(array(1,2,5));
        $this->agentType = I('get.agentType',0,'int');
        if($this->agentType==1){//实体店代理商
        }elseif ($this->agentType==2){//微商代理商
        }elseif ($this->agentType==3){//礼品采购代理商
        }
        $this->display();
    }

    //代理商-列表
    public function agentList(){
        if(IS_POST){
            $modelAgent = D('Agent');
            $where = array(
                'a.partner_id' => $this->partner['id'],
            );
            $keyword = I('get.keyword','','string');
            if($keyword){
                $where['_complex'] = array(
                    '_logic' => 'or',
                    'a.name' => array('like', '%' . trim($keyword) . '%'),
                    'a.mobile_phone' => array('like', '%' . trim($keyword) . '%'),
                );
            }
            $this->agentList = $modelAgent->selectAgent($where);
            $this->display();
        }else{
        }
    }
}