<?php
namespace web\all\Controller;

use web\all\Cache\AgentCache;

/**机构登记的控制器基类
 */
class AuthAgentController extends AuthUserController{
    protected $agent = null;
    protected $agentAuthoriseUrl = 'Home/AgentAuthorise/index';//机构登记URL

    public function __construct(){
        parent::__construct();
        AgentCache::remove($this->user['id']);
        $this->agent = AgentCache::get($this->user['id']);
        //机构登记
        if(!$this->agent || $this->agent['auth_status'] == 0){
            $this->error(C('ERROR_COMPANY_REGISTER_REMIND'),U($this->agentAuthoriseUrl));
        }
    }
}