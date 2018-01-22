<?php
namespace Business\Controller;

use web\all\Cache\AgentCache;
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
            if(isset($_POST['agentId']) && intval($_POST['agentId'])){//修改
                $rules = array(
                    array('name','require','姓名必须！'),
                    array('mobile_phone','isMobile','请输入正确的手机号码',0,'function'),
                    array('province','require','所在区域必须！'),
                    array('city','require','所在区域必须！'),
                );
                $modelAgent = D('Agent');
                $modelAgentType = D('AgentType');
                $modelAgent->startTrans();//开启事务
                $agentId = I('post.agentId',0,'int');
                $where = array(
                    'agent_id' => $agentId,
                );
                $agentTypeList = $modelAgentType->selectAgentType($where);
                //数据库原来的类型
                $agentTypeMysql = array_column($agentTypeList,'type');
                $agentTypeAdd = array_diff($_POST['agentType'],$agentTypeMysql);//新增的类型
                if(!empty($agentTypeAdd)){
                    foreach ($agentTypeAdd as $value){
                        $dataList[] = array(
                            'type' => $value,
                            'agent_id' => $agentId,
                        );
                    }
                    $res = $modelAgentType->addAll($dataList);
                    if($res===false){
                        $modelAgent->rollback();//事务回滚
                        $this->ajaxReturn(errorMsg($modelAgentType->getLastSql()));
                    }
                }
                $agentTypeDel = array_diff($agentTypeMysql,$_POST['agentType']);//删除的类型
                if(!empty($agentTypeDel)){
                    $where = array(
                        'type' => array('in',$agentTypeDel),
                        'agent_id' => $agentId,
                    );
                    $res = $modelAgentType->delAgentType($where);
                    if($res['status']==0){
                        $modelAgent->rollback();//事务回滚
                        $this->ajaxReturn($res);
                    }
                }
                $where = array(
                    'id' => $agentId,
                );
                $res = $modelAgent->saveAgent($where,$rules);
                AgentCache::remove($this->user['id']);
                if($res['status']==0){
                    $modelAgent->rollback();//事务回滚
                    $this->ajaxReturn($res);
                }
                $modelAgent->commit();//事务提交
                $this->ajaxReturn($res);
            }
        }else{
            //购物车配置开启的项
            $this->unlockingFooterCart = unlockingFooterCartConfig(array(21));
            //代理商信息
            $this->agentInfo = $this->agent;
            //代理商类型
            $modelAgentType = D('AgentType');
            $where = array(
                'at.agent_id' => $this->agent['id'],
            );
            $this->agentTypeList = $modelAgentType->select($where);
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