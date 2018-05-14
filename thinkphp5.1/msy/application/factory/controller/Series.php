<?php
namespace app\factory\controller;
use app\factory\model\Series as M;
use common\controller\Base;
class Series extends FactoryBase
{
    
    //系列编辑
    public function edit()
    {
        $model = new M();
        if(request()->isPost()){
            if(input('?post.series_id')){
                return $model -> edit($this->factory['factory_id']);
            }else{
                return $model -> add($this->factory['factory_id']);
            }
        }
        $seriesList = $model -> selectSeries([],[],['sort'=>'desc']);
        $this->assign('seriesList',$seriesList);
        return $this->fetch();
    }

    //移动
    public function move(){
        $model = new M();
        if(request()->isPost()){
            return $model -> move($this->factory['factory_id']);
        }
    }


    //删除
    public function delete()
    {
        $model = new M();
        if(request()->isPost()){
            if(input('?post.series_id')){
                return $model -> del($this->factory['factory_id']);
            }
        }
    }


}