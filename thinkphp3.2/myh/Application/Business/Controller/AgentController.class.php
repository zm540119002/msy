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

    //完善我的商务档案
    public function completeArchive(){
        if(IS_POST){
        }else{
            //购物车配置开启的项
            $this->unlockingFooterCart = unlockingFooterCartConfig(array(21));
            //代理商信息
            $this->agentInfo = $this->agent;
            //省市数组
            $this->assign('provinceList',getProvinceCity());
            $this->display();
        }
    }

    //查看所在城市美悦会服务中心
    public function viewPartner(){
        if(IS_POST){
        }else{
            //合伙人信息
            $modelPartner = D('Partner');
            $where = array(
                'p.id' => $this->agent['partner_id'],
            );
            $partnerInfo = $modelPartner->selectPartner($where);
            $this->partnerInfo = $partnerInfo[0];
            $this->display();
        }
    }
}