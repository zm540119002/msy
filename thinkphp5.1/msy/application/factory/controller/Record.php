<?php
namespace app\factory\controller;
use common\controller\Base;

class Record extends FactoryBase
{
    //产商档案编辑
    public function edit(){
        $model = new \app\factory\model\Record();
        $factoryInfo = $this->factory;
        if(request()->isAjax()){
            if( isset($_POST['rb_img'])){
                return 1;
            }
            return 2;
            if(input('?post.record_id') && !input('?post.record_id') == ''){
                return $model -> edit($factoryInfo['id']);
            }
            return $model -> add($factoryInfo['id']);
        }else{
            if(input('?record_id')){
                $recordId = input('record_id');
                $where = array(
                    'id' => $recordId,
                    'factory_id' => $factoryInfo['id'],
                );
                $recordInfo =  $model -> getRecord($where);
                $this -> assign('recordInfo',$recordInfo);
            }
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