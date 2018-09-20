<?php
namespace app\factory\controller;

class Organize extends \common\controller\FactoryBase
{
    /**首页
     */
    public function index(){
        $modelOrganize = new \app\factory\model\Organize();
        if(request()->isAjax()){
            $info = $modelOrganize->edit($this->factory['id']);
            if($info['status']==1){
                $this->assign('info',$info);
                return view('info_tpl');
            }else{
                return $info;
            }
        }else{
            return $this->fetch();
        }
    }

    /**获取组织列表
     */
    public function  getOrganizeList(){
        if(!request()->isGet()){
            return config('custom.not_get');
        }
        if(intval($this->factory['id'])){
            $modelOrganize = new \app\factory\model\Organize();
            $list = $modelOrganize->getOrganizeList($this->factory['id']);
        }
        return count($list)?$list:[];
    }

    /**删除
     */
    public function  del(){
        if(!request()->isAjax()){
            return errorMsg(config('custom.not_ajax'));
        }
        $modelOrganize = new \app\factory\model\Organize();
        return $modelOrganize->del($this->factory['id'],true);
    }
}