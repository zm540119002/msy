<?php
namespace web\all\Controller;

use web\all\Cache\AgentCache;

/**机构登记的控制器基类
 */
class AuthAgentController extends OrderBaseController{
    protected $agent = null;
    protected $agentAuthoriseUrl = 'Home/AgentAuthorise/index';

    public function __construct(){
        parent::__construct();
        $this->agentAuthoriseUrl .= '/agentType/' . I('get.agentType',0,'int');
        //先判断是否被授权
        AgentCache::removeByMobilePhone($this->user['mobile_phone']);
        $this->agent = AgentCache::getByMobilePhone($this->user['mobile_phone']);
        //如果被授权
        if(!$this->agent || $this->agent['auth_status'] != 1){
            //再判断是否开通
            AgentCache::remove($this->user['id']);
            $this->agent = AgentCache::get($this->user['id']);
            //代理商认证
            if(!$this->agent || $this->agent['auth_status'] != 1){
                $this->error('您还不是代理商！',U($this->agentAuthoriseUrl));
            }
        }
    }
}