<?php
namespace app\store\controller;

class Manager extends \common\controller\FactoryBase
{
    /**首页
     */
    public function index(){
        if(request()->isAjax()){
            $modelManager = new \common\model\Manager();
            $list = $modelManager->getList($this->factory['id']);
            $this->assign('list',$list);
            return view('list_tpl');
        }else{
            return $this->fetch();
        }
    }

    /**店长设置
     */
    public function set(){
        if(request()->isAjax()){
            $modelStore = new \common\model\Store();
            $config = [
                'field' => [
                    's.id','s.name','s.store_type','s.run_type',
                    'f.id','f.name factory_name',
//                    'u.id','u.nickname','u.mobile_phone'
                ],'leftJoin' => [
                    ['factory f','f.id = s.factory_id'],
//                    ['user_factory uf','uf.factory_id = s.factory_id'],
//                    ['user u','u.id = uf.user_id'],
                ],'where' => [
                    ['s.status','=',0],
//                    ['uf.type','=',3],
                ],
            ];
            $list = $modelStore->getList($config);
//            print_r($modelStore->getLastSql());exit;
            $this->assign('list',$list);
            return view('store_list_tpl');
        }else{
            return $this->fetch();
        }
    }

    /**编辑管理员
     */
    public function edit(){
        if(request()->isAjax()){
            $modelManager = new \common\model\Manager();
            $info = $modelManager->edit($this->factory['id']);
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
            $modelManager = new \common\model\Manager();
            return $modelManager->del($this->factory['id']);
        }
    }
}