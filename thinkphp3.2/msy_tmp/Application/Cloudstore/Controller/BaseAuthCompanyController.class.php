<?php
namespace Cloudstore\Controller;

use Common\Controller\AuthCompanyAuthoriseController;

/**云店机构认证基类
 */
class BaseAuthCompanyController extends AuthCompanyAuthoriseController{
    public function __construct(){
        parent::__construct();
    }

    /**查询门店
     * @param array $where
     * @param array $field
     * @return array|false|mixed|\PDOStatement|string|\think\Collection
     */
    protected function selectShop($where=[],$field=[],$join=[]){
        $model = M('shop s');
        $_where = array(
            's.status' => 0,
        );
        $_field = array(
            's.id','s.name','s.type','s.address','s.receptionist_mobile','s.fixed_phone','s.logo',
            's.company_id','c.name company_name','c.type company_type','s.identified',
        );
        $_join = array(
            ' left join company c on s.company_id = c.id ',
        );
        $shopList = $model
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return empty($shopList) ? [] : $shopList;
    }

    /**查询员工
     * @param array $where
     * @param array $field
     * @param array $join
     * @return array|false|mixed|\PDOStatement|string|\think\Collection
     */
    protected function selectEmployee($where=[],$field=[],$join=[]){
        $model = M('employee e');
        $_where = array(
            'e.status' => 0,
        );
        $_field = array(
            'e.id','e.name','e.nickname','e.user_id','e.company_id','e.shop_id','e.position_id','e.mobile_phone',
        );
        $_join = array(
        );
        $employeeList = $model
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return empty($employeeList) ? [] : $employeeList;
    }
}


