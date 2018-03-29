<?php
namespace app\factory\controller;
use app\factory\model\Brand as M;
use common\controller\Base;
class Brand extends Base
{
    //商标首页
    public function index()
    {
        $model = new M();
        print_r($model ->select());exit;
        return $this->fetch();
    }

    //备案
    public function record()
    {
        if(request()->isPost()){
            $model = new M();
            return $model -> add();
        }
        $categoryList = [
            0=>['id'=>1,'name'=>'美妆'],
            1=>['id'=>2,'name'=>'美甲'],
            2=>['id'=>3,'name'=>'微整形'],
        ];
        $this->assign('categoryList',$categoryList);
        return $this->fetch();
    }



}