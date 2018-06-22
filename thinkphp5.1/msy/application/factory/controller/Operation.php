<?php
namespace app\factory\controller;

class Operation extends StoreBase
{
    //运营管理首页
    public function Index(){
        return $this->fetch();
    }
    //店铺管理设置页
    public function set(){
        if(request()->isPost()){
            $logoImg = input('post.logo_img');
            if(empty($logoImg)){
                return errorMsg('参数错误');
            }
            $model = new \app\factory\model\Store();
            $where = [
                ['id','=', $this->store['id']]
            ];
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