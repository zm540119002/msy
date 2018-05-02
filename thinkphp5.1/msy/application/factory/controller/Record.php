<?php
namespace app\factory\controller;
use common\controller\Base;

class Record extends FactoryBase
{
    //部署首页
    public function deployIndex(){
        $factoryId =  $this->factory['id'];
        $this ->assign('factoryId',$factoryId);
        return $this->fetch('deploy/index');
    }

    //产商档案编辑
    public function edit(){
        $model = new \app\factory\model\Record();
        $factoryInfo = $this->factory;
        if(request()->isAjax()){
            if(input('?post.record_id') && !input('?post.record_id') == ''){
                return $model -> edit($factoryInfo['id']);
            }
            return $model -> add($factoryInfo['id']);
        }else{
            if(input('?record_id')){
                $recordId = input('record_id');
                $where = [
                    ['id','=',$recordId],
                    ['factory_id','=',$factoryInfo['id']],
                ];
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
        $model = new \app\factory\model\Record();
        $factoryInfo = $this->factory;
        $where['factory_id'] = $factoryInfo['id'];
        $recordInfo = $model::hasWhere('factory',['id'=>$factoryInfo['id']])->field('factory.name')->find()->toArray();
        $recordInfo['factory_video'] = json_decode($recordInfo['factory_video'],true);
        $recordInfo['rb_img'] = json_decode($recordInfo['rb_img'],true);
        $recordInfo['license'] = json_decode($recordInfo['license'],true);
        $recordInfo['glory_img'] = json_decode($recordInfo['glory_img'],true);
        $this -> assign('recordInfo',$recordInfo);
        return $this->fetch();
    }


}