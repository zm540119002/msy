<?php
namespace web\all\Controller;

use web\all\Cache\CompanyCache;

/**机构登记的控制器基类
 */
class AuthAgentController extends AuthUserController{
    protected $company = null;
    protected $companyRegisterUrl = 'Home/AgentAuthorise/index';//机构登记URL

    public function __construct(){
        parent::__construct();
        CompanyCache::remove($this->user['id']);
        $this->company = CompanyCache::get($this->user['id']);
        //机构登记
        if(!$this->company || $this->company['auth_status'] == 0){
            $this->error(C('ERROR_COMPANY_REGISTER_REMIND'),U($this->companyRegisterUrl));
        }
    }
}