<?php
namespace app\store\controller;

class ManagerManage extends \common\controller\UserBase{
    public function __construct(){
        parent::__construct();
        if($this->factory){
            //获取厂商店铺详情列表
            \common\cache\Store::remove($this->factory['id']);
            $list = \common\cache\Store::get($this->factory['id']);
            $this -> assign('storeList', $list);
            $count = count($list);
            if ($count > 1) {
                //多家店判断是否有默认店铺
                foreach ($list as $val){
                    if($val['is_default']){
                        $this->store = $val;
                    }
                }
            }elseif($count == 1){
                $this->store = $list[0];
            }elseif(!$count) {
                $this -> success('没有店铺，请申请', 'Store/edit');
            }
            $this -> assign('store', $this->store);
            return $this -> storeList;
        }
    }

    /**首页
     */
    public function index(){
        if(request()->isAjax()){
            $modelManagerManage = new \app\store\model\ManagerManage();
            $list = $modelManagerManage->getList($this->factory['id']);
            $this->assign('list',$list);
            return view('list_tpl');
        }else{
            return $this->fetch();
        }
    }

    /**编辑管理员
     */
    public function edit(){
        if(request()->isAjax()){
            $modelManagerManage = new \app\store\model\ManagerManage();
            $info = $modelManagerManage->edit($this->factory['id'],$this->factory['type']);
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
            return $modelManagerManage->del($this->factory['id']);
        }
    }
}