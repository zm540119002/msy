<?php
namespace web\all\Controller;

use web\all\Cache\AgentCache;

class AuthPartnerController extends AuthUserController{
    protected $partner = null;
    protected $partnerAuthoriseUrl = 'Home/PartnerAuthorise/index';//机构登记URL

    public function __construct(){
        parent::__construct();
        AgentCache::remove($this->user['id']);
        $this->partner = AgentCache::get($this->user['id']);
        //机构登记
        if(!$this->partner || $this->partner['auth_status'] == 0){
            $this->error(C('ERROR_COMPANY_REGISTER_REMIND'),U($this->partnerAuthoriseUrl));
        }
    }
}