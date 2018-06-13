<?php
namespace app\store\controller;

class Record extends StoreBase
{

    //产商档案编辑
    public function edit(){
        $model = new \app\store\model\Record();
        if(request()->isPost()){
            return $model -> edit($this->store['id']);
        }else{
            $where = [
                ['store_id','=',$this->store['id']],
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
        $model = new \app\store\model\Record();
        $where = [
            ['r.store_id','=',$this->store['id']],
        ];
        $file = ['r.id,r.shop_name,r.introduction,r.store_video,r.logo_img,r.rb_img,r.license,r.glory_img,r.provinces,r.detail_address,
        r.company_img,r.create_time,r.update_time,f.name'];
        $join = [
            ['store f','f.id = r.store_id'],
        ];
        $recordInfo = $model -> getInfo($where,$file,$join);
        if(!empty($recordInfo)){
            $recordInfo['store_video'] = json_decode($recordInfo['store_video'],true);
            $recordInfo['rb_img'] = json_decode($recordInfo['rb_img'],true);
            $recordInfo['license'] = json_decode($recordInfo['license'],true);
            $recordInfo['glory_img'] = json_decode($recordInfo['glory_img'],true);
        }
        $this -> assign('recordInfo',$recordInfo);
        return $this->fetch();
    }


}