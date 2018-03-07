<?php
namespace common\Controller;

use common\Cache\PartnerCache;

class AuthPartnerController extends AuthUserController{
    protected $partner = null;
    protected $partnerAuthoriseUrl = 'Home/PartnerAuthorise/index';
    protected $partnerSeatDepositUrl = 'Home/PartnerAuthorise/seatDeposit';
    protected $partnerSeniorityUrl = 'Home/PartnerAuthorise/seniority';

    public function __construct(){
        parent::__construct();
        PartnerCache::remove($this->user['id']);
        $this->partner = PartnerCache::get($this->user['id']);
        //合伙人认证
        if(!$this->partner){
            $this->error('您还不是合伙人！',U($this->partnerAuthoriseUrl));
        }
        if($this->partner['auth_status'] == 1){
            $this->error('请支付席位订金！',U($this->partnerSeatDepositUrl));
        }
        if($this->partner['auth_status'] == 2){
            $this->error('请支付资格余款！',U($this->partnerSeniorityUrl));
        }
    }
}