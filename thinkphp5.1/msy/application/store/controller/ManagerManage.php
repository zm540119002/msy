<?php
namespace app\store\controller;

class ManagerManage extends ManagerManageBase{
    public function __construct(){
        parent::__construct();
    }

    /**首页
     */
    public function index(){
        if(request()->isAjax()){
            $modelManagerManage = new \app\store\model\ManagerManage();
            $this->_managerFactoryList = $modelManagerManage->getList($this->user['id']);
            $this->assign('list',$this->_managerFactoryList);
            return view('list_tpl');
        }else{
            return $this->fetch();
        }
    }

    /**店铺管理
     */
    public function manage(){
        if(request()->isAjax()){
        }else{
            //岗位
            $post = config('permission.post');
            $this->assign('post', $post);
            //职务
            $duty = config('permission.duty');
            $this->assign('duty', $duty);
            //鉴权
            $authentication = config('permission.authentication');
            $this->assign('authentication', $authentication);

            return $this->fetch();
        }
    }

    /**店铺员工-编辑
     */
    public function storeEmployeeEdit(){
        $currentStore = \common\cache\Store::getCurrentStoreInfo();
        if(!($currentStore['id'])){
            return errorMsg('请选择店铺！');
        }
        if(request()->isAjax()){
            $modelManagerManage = new \app\store\model\ManagerManage();
            $info = $modelManagerManage->storeEmployeeEdit($currentStore['id']);
            if($info['status']==0){
                return $info;
            }else{
                $this->assign('info',$info);
                return view('store_employee_info_tpl');
            }
        }
    }

    /**删除管理员
     */
    public function del(){
        $currentStore = \common\cache\Store::getCurrentStoreInfo();
        if(!($currentStore['id'])){
            return errorMsg('请选择店铺！');
        }
        if(request()->isAjax()){
            $modelManagerManage = new \app\store\model\ManagerManage();
            return $modelManagerManage->del($this->user['id']);
        }
    }
}