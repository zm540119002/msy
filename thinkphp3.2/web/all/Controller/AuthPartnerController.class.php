<?php
namespace web\all\Controller;

use web\all\Cache\PartnerCache;

class AuthPartnerController extends AuthUserController{
    protected $partner = null;
    protected $partnerAuthoriseUrl = 'Home/PartnerAuthorise/index';

    public function __construct(){
        parent::__construct();
        PartnerCache::remove($this->user['id']);
        $this->partner = PartnerCache::get($this->user['id']);
        //合伙人认证
        if(!$this->partner || $this->partner['auth_status'] != 3){
            $this->error('您还不是合伙人！',U($this->partnerAuthoriseUrl));
        }
    }
}