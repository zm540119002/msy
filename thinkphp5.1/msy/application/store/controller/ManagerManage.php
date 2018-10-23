<?php
namespace app\store\controller;

class ManagerManage extends \common\controller\UserBase{
    protected $_store = null;
    protected $_managerFactoryList = null;
    public function __construct(){
        parent::__construct();
        //获取厂商店铺详情列表
        \common\cache\Store::removeManagerStore($this->user['id']);
        $storeList = \common\cache\Store::getManagerStore($this->user['id']);
        if(count($storeList)==1){
            $this->_store = $storeList[0];
        }elseif(count($storeList)>1){
            foreach ($storeList as $item) {
                $storeInfoArr = [
                    'id' => $item['id'],
                    'store_name' => $item['store_name'],
                    'logo_img' => $item['logo_img'],
                    'store_type' => $item['store_type'],
                    'run_type' => $item['run_type'],
                    'operational_model' => $item['operational_model'],
                    'is_default' => $item['is_default'],
                ];
                $factory_id_arr = array_column($this->_managerFactoryList,'factory_id');
                if(!in_array($item['factory_id'],$factory_id_arr)){//factory不存在
                    $this->_managerFactoryList[] = [
                        'factory_id' => $item['factory_id'],
                        'name' => $item['name'],
                        'type' => $item['type'],
                        'store_list' => [$storeInfoArr],
                    ];
                }else{//factory存在
                    foreach ($this->_managerFactoryList as &$key){
                        if($key['factory_id'] == $item['factory_id']){
                            $key['store_list'][] = $storeInfoArr;
                        }
                    }
                }
            }
//            $this->assign('managerFactoryList', $this->_managerFactoryList);
        }
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
            $this->assign('store', $this->_store);
            return $this->fetch();
        }
    }

    /**编辑管理员
     */
    public function edit(){
        if(request()->isAjax()){
            $modelManagerManage = new \app\store\model\ManagerManage();
            $info = $modelManagerManage->edit($this->user['id'],$this->user['type']);
            if($info['status']==0){
                return $info;
            }else{
                $this->assign('info',$info);
                return view('info_tpl');
            }
        }
    }

    /**删除管理员
     */
    public function del(){
        if(request()->isAjax()){
            $modelManagerManage = new \app\store\model\ManagerManage();
            return $modelManagerManage->del($this->user['id']);
        }
    }
}