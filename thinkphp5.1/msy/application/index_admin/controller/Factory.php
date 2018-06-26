<?php
namespace app\index_admin\controller;

/**供应商验证控制器基类
 */
class Factory extends Base {

    /*
     *
     */
    public function auditList(){
//        $model = new \app\index_admin\model\Factory();
//        print_r($model -> getList([['auth_status','=',0]])) ;
        return $this->fetch();
    }
    public function info(){

    }

}