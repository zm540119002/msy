<?php
namespace app\store\controller;

class Operation extends \common\controller\FactoryStoreBase
{
    //运营管理首页
    public function Index(){
        if(request()->isAjax()){
            return view('public/factory_store_list_tpl');
        }
        return $this->fetch();
    }
    //店铺管理设置页
    public function set(){
        if(request()->isPost()){
            $logoImg = input('post.logo_img');
            if(empty($logoImg)){
                return errorMsg('参数错误');
            }
            $model = new \common\model\Store();
            $where = [
                ['id','=', $this->store['id']]
            ];
            $logoImg = moveImgFromTemp(config('upload_dir.factory_store'),basename($logoImg));
            $result = $model->where($where)->setField('logo_img',$logoImg);
            if(false == $result){
                return errorMsg('优化失败');
            }else{
                return successMsg('成功');
            }
        }
        return $this->fetch();
    }

}