<?php
namespace app\store\controller;
use app\store\model\Series as M;
use common\controller\Base;
class Series extends StoreBase
{
    
    //系列编辑
    public function edit()
    {
        $model = new M();
        if(request()->isPost()){
            if(input('?post.series_id')){
                return $model -> edit($this->store['id']);
            }else{
                return $model -> add($this->store['id']);
            }
        }
        $seriesList = $model -> getList([],[],['sort'=>'desc']);
        $this->assign('seriesList',$seriesList);
        return $this->fetch();
    }

    //移动
    public function move(){
        $model = new M();
        if(request()->isPost()){
            return $model -> move($this->store['id']);
        }
    }


    //删除
    public function delete()
    {
        $model = new M();
        if(request()->isPost()){
            if(input('?post.series_id')){
                return $model -> del($this->store['id']);
            }
        }
    }


}