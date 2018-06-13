<?php
namespace app\factory\controller;

class Record extends FactoryBase
{

    //产商档案编辑
    public function edit(){
        $model = new \app\factory\model\Record();
        if(request()->isPost()){
            return $model -> edit($this->factory['id']);
        }else{
            $where = [
                ['factory_id','=',$this->factory['id']],
            ];
            $recordInfo =  $model -> getInfo($where);
            $this -> assign('recordInfo',$recordInfo);
            return $this->fetch();
        }
    }

    /**产商档案预览
     */
    public function preview()
    {
        $model = new \app\factory\model\Record();
        $where = [
            ['r.factory_id','=',$this->factory['id']],
        ];
        $file = ['r.id,r.shop_name,r.introduction,r.factory_video,r.logo_img,r.rb_img,r.license,r.glory_img,r.provinces,r.detail_address,
        r.company_img,r.create_time,r.update_time,f.name'];
        $join = [
            ['factory f','f.id = r.factory_id'],
        ];
        $recordInfo = $model -> getInfo($where,$file,$join);
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