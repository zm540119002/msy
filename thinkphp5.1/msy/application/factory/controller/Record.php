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
        $model = new \app\factory\model\Record();
        $factoryInfo = $this->factory;
        $where['factory_id'] = $factoryInfo['id'];
        $join = array(
            'factory f','f.id = r.factory_id'
        );
        $field = array(
            'r.id','r.factory_id','r.shop_name','r.introduction','r.factory_video','r.logo_img','r.rb_img',
            'r.license','r.glory_img','r.provinces','r.detail_address','r.company_img','r.create_time','r.update_time',
            'f.name'
        );
        $recordInfo = $model -> getRecord($where,$field,$join);
        return $model->getLastSql();

        $recordInfo['factory_video'] = json_decode($recordInfo['factory_video'],true);
        $recordInfo['rb_img'] = json_decode($recordInfo['rb_img'],true);
        $recordInfo['license'] = json_decode($recordInfo['license'],true);
        $recordInfo['glory_img'] = json_decode($recordInfo['glory_img'],true);
        $this -> assign('recordInfo',$recordInfo);
        return $this->fetch();
    }


}