<?php
namespace Common\Controller;

/**机构认证的控制器基类
 */
class AuthCompanyAuthoriseController extends AuthCompanyRegisterController{
    protected $companyAuthoriseUrl = 'UserCenter/CompanyAuthorise/index';    //机构认证URL

    public function __construct(){
        parent::__construct();

        //机构认证
        $dueTime = C('DEFAULT_DUE_TIME');   //到期时间
        if($this->company['auth_status'] == 1 &&  time() - $this->company['create_time']  > $dueTime ){
            $this->error(C('ERROR_COMPANY_AUTHORISE_REMIND'),U($this->companyAuthoriseUrl));
        }
    }
}


