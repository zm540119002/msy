<?php
namespace web\all\Controller;

use web\all\Cache\PartnerCache;

class AuthPartnerController extends AuthUserController{
    protected $partner = null;
    protected $partnerAuthoriseUrl = 'Home/PartnerAuthorise/index';//机构登记URL

    public function __construct(){
        parent::__construct();
        PartnerCache::remove($this->user['id']);
        $this->partner = PartnerCache::get($this->user['id']);
        //机构登记
        if(!$this->partner || $this->partner['auth_status'] == 0){
            $this->error(C('ERROR_COMPANY_REGISTER_REMIND'),U($this->partnerAuthoriseUrl));
        }
    }
}