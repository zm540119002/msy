<?php
namespace app\index\controller;

/**
 * 城市合伙人控制器
 */
class CityPartner extends \common\controller\UserBase {
    /**
     * 城市合伙人申请条件
     */
    public function city(){
        return $this->fetch();
    }
    /**
     * 合伙人申请
     */
    public function registered(){
        if (request()->isAjax()) {
        } else {
            $modelCityPartner = new \app\index\model\CityPartner();
            $config=[
                'where'=>[
                    ['status', '=', 0],
                    ['apply_status','=',3]
                ],
                'field'=>[
                    'province','city',
                ],
            ];
            $cityList = $modelCityPartner -> getList($config);
            $this->assign('cityList',$cityList);
            $unlockingFooterCart = unlockingFooterCartConfig([10, 0, 9]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);
            return $this->fetch();
        }
    }

    /**
     * 申请提交
     * @return array
     * @throws \think\exception\PDOException
     */
    public function submitApplicant()
    {
        if(!request()->isAjax()){
            return errorMsg('请求方式错误');
        }
        $postData = input('post.');
        $validate = new \app\index\validate\CityPartner();
        if(!$validate->scene('add')->check($postData)) {
            return errorMsg($validate->getError());
        }
        $modelCityPartner = new \app\index\model\CityPartner();
        $modelCityPartner -> startTrans();
        $sn = generateSN(); //内部支付编号
        $postData['sn'] = $sn;
        $postData['user_id'] = $this->user['id'];
        $postData['earnest'] = config('custom.cityPartner_fee')[1]['earnest'];
        $postData['amount'] = config('custom.cityPartner_fee')[1]['amount'];
        $postData['create_time'] = time();
        $result  = $modelCityPartner->isUpdate(false)->save($postData);
        if(!$result){
            $modelCityPartner ->rollback();
            return errorMsg('失败');
        }

        //生成支付表数据
        $modelPay = new \app\index\model\Pay();
        $data = [
            'sn' => $sn,
            'actually_amount' =>config('custom.cityPartner_fee')[1]['earnest'],
            'user_id' => $this->user['id'],
            'pay_code' => $postData['pay_code'],
            'type' => config('custom.pay_type')['cityPartnerSeatPay']['code'],
            'create_time' => time(),
        ];
        $result  = $modelPay->isUpdate(false)->save($data);
        if(!$result){
            $modelPay ->rollback();
            return errorMsg('失败');
        }
        $modelCityPartner -> commit();
        return successMsg('成功',['url'=>config('custom.pay_gateway').$sn]);
    }

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