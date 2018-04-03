<?php
namespace app\factory\controller;
use app\factory\model\Series as M;
use common\controller\Base;
class Series extends Base
{
    
    //系列编辑
    public function edit()
    {
        $model = new M();
        if(request()->isPost()){
            if(input('?post.series_id')){
                return $model -> edit();
            }else{
                return $model -> add();
            }
        }
        $seriesList = $model -> selectSeries([],[],['sort'=>'desc','id'=>'desc',]);
        $this->assign('seriesList',$seriesList);
        return $this->fetch();
    }


    //删除
    public function delete()
    {
        $model = new M();
        if(request()->isPost()){
            if(input('?post.series_id')){
                return $model -> delete();
            }
        }
    }


}