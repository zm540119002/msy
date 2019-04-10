<?php
namespace app\index\controller;
class CityPartner extends \common\controller\Base{


    public function getSigningInfo(){
        $province_id = 1;
        $city_id = 1;
        if(!$province_id OR !$city_id){
            return errorMsg('请求数据不能为空');

        }else{
            // 到时看下需不需要按补齐尾款的时间来判断
            $model = new \app\index\model\CityPartner;

            $condition = [
                'where'=>[
                    ['status',['=', 2], ['=', 3],'OR'],
                ],
                'field'=>['id'],
            ];

            $res = $model->getInfo($condition);

            p($model->getLastSql());
            exit;

            return true;
        }

    }
}