<?php
namespace app\factory\controller;
use common\controller\Base;

class Record extends FactoryBase
{
    //产商档案编辑
    public function edit(){
        $factoryInfo = $this->factory;
        if(request()->isAjax()){
            $model = new \app\factory\model\Record();
            return $model -> add($factoryInfo['id']);
        }else{
            $this ->assign('factoryInfo',$factoryInfo);
            return $this->fetch();
        }
    }

    /**产商档案预览
     */
    public function preview()
    {
        return $this->fetch();
    }


}