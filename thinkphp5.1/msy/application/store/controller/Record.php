<?php
namespace app\store\controller;

class Record extends \common\controller\FactoryBase
{
    //产商档案编辑
    public function edit(){
        $model = new \common\model\Record();
        if(request()->isPost()){
            return $model -> edit($this->factory['id']);
        }else{
            $config = [
                'where' => [
                    ['factory_id','=',$this->factory['id']],
                ],
            ];
            $recordInfo =  $model -> getInfo($config);
            $this -> assign('recordInfo',$recordInfo);
            $this -> assign('factoryName',$this->factory['name']);
            return $this->fetch();
        }
    }

    /**产商档案预览
     */
    public function preview()
    {
        $model = new \common\model\Record();
        $config = [
            'where' => [
                ['r.factory_id','=',$this->factory['id']],
            ],'order' => [
                'id' => 'desc',
            ],'join' => [
                ['factory f','f.id = r.factory_id'],
            ],'field' => ['r.id,r.introduction,r.factory_video,r.logo_img,r.rb_img,r.license,r.glory_img,r.province,r.city,r.area,r.detail_address,
                            r.team_activity,r.company_img,r.create_time,r.update_time,r.short_name,f.name'],
        ];
        $recordInfo = $model -> getInfo($config);
        if(!empty($recordInfo)){
            $recordInfo['factory_video'] = json_decode($recordInfo['factory_video'],true);
            $recordInfo['rb_img'] = json_decode($recordInfo['rb_img'],true);
            $recordInfo['license'] = json_decode($recordInfo['license'],true);
            $recordInfo['glory_img'] = json_decode($recordInfo['glory_img'],true);
            $recordInfo['team_activity'] = json_decode($recordInfo['team_activity'],true);
        }
        $this -> assign('recordInfo',$recordInfo);
        return $this->fetch();
    }
}