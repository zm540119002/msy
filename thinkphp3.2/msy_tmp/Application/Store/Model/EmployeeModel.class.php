<?php
namespace Store\Model;
use Think\Model;

class EmployeeModel extends Model{
    private $_db = '';

    public function __construct() {
        parent::__construct();
        $this->_db = M('Employee');
    }

    protected $_validate        =   array(
        array('name',           'require', '请填写员工姓名！'), //默认情况下用正则进行验证
        array('mobile',          'require', '请填写员工手机号码！'), //默认情况下用正则进行验证
        array('mobile','isMobile','手机号码格式不正确',0,'function'),
        array('password', 'require', '密码不能为空！'), //默认情况下用正则进行验证
    );


    /**
     *  根据uid查找门店信息
     */
    public function getEmployeeList(){
        $organization_id =  D('Company') -> getCompanyID();
        $where = array( 'organization_id' => $organization_id, 'status' => 0);
        $empoyeeList = $this->_db -> where($where) -> select();
        return $empoyeeList;
    }

}