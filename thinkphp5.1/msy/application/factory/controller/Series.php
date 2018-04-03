<?php
namespace app\factory\controller;
use app\factory\model\Series as M;
use common\controller\Base;
class Series extends Base
{
    //商标首页
    public function index()
    {
        $model = new M();
        return $model ->selectBrand([],[],['id'=>'desc'],[],'2,3');
        return $this->fetch();
    }

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
//        $categoryModel = new categoryModel;
//
        if(input('?series_id')){
            $brandId = input('series_id');
            $where = array(
                'id' => $brandId,
            );
            $field = array(
                'id'
            );
            $brandInfo =  $model -> getBrand($where,$field);
            $this -> assign('brandInfo',$brandInfo);
        }
        $seriesList = $model -> selectSeries([],[],['id'=>'desc']);
//        return $seriesList;
        $this->assign('seriesList',$seriesList);
        return $this->fetch();
    }



}