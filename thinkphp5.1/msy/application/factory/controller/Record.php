<?php
namespace app\factory\controller;

class Record extends FactoryBase
{

    //产商档案编辑
    public function edit(){
        $model = new \app\factory\model\Record();
        if(request()->isAjax()){
            return $model -> edit($this->factory['factory_id']);
        }else{
//            if(input('?record_id')){
//                $recordId = input('record_id');
//                $where = [
//                    ['id','=',$recordId],
//                    ['factory_id','=',$this->factory['factory_id']],
//                ];
//                $recordInfo =  $model -> getRecord($where);
//                $this -> assign('recordInfo',$recordInfo);
//            }
            $where = [
                ['factory_id','=',$this->factory['factory_id']],
            ];
            $recordInfo =  $model -> getRecord($where);
            $this -> assign('recordInfo',$recordInfo);
            return $this->fetch();
        }
    }

    /**产商档案预览
     */
    public function preview()
    {
        $model = new \app\factory\model\Record();
        $recordInfo = $model::hasWhere('factory',['id'=>$this->factory['factory_id']])->field('factory.name')->find();
        if(!empty($recordInfo)){
            $recordInfo['factory_video'] = json_decode($recordInfo['factory_video'],true);
            $recordInfo['rb_img'] = json_decode($recordInfo['rb_img'],true);
            $recordInfo['license'] = json_decode($recordInfo['license'],true);
            $recordInfo['glory_img'] = json_decode($recordInfo['glory_img'],true);
        }
        $this -> assign('recordInfo',$recordInfo);
        return $this->fetch();
    }


}