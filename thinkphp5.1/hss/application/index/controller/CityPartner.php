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
            //平台已审核通过的申请
            $modelCityPartner = new \app\index\model\CityPartner();
            $condition=[
                'where'=>[
                    ['status', '=', 0],
                    ['apply_status','=',5]
                ],
                'field'=>[
                    'province','city',
                ],
            ];
            $cityList = $modelCityPartner -> getList($condition);
            //$this->assign('cityList',json_encode($cityList));
            //自己提交的申请
            $modelCityPartner = new \app\index\model\CityPartner();
            $condition=[
                'where'=>[
                    ['cp.status', '=', 0],
                    ['cp.user_id','=',$this->user['id']]
                ], 'field'=>[
                    'cp.id','cp.province','cp.city','cp.company_name','cp.applicant','cp.mobile','cp.city_level','cp.earnest','cp.amount','cp.apply_status'
                    ,'p.sn','p.id as pay_id'
                ]
                ,'join' => [
                     ['pay p','p.sn = cp.earnest_sn','left'],
                ]
            ];
            $selfApplyList = $modelCityPartner -> getList($condition);
            //申请中
            $apply = [];
            //已交定金或尾款申请
            $applied = [];
            if($selfApplyList){
                foreach ($selfApplyList as $selfapply){
                    if ($selfapply['apply_status']<6){
                        $apply[] = $selfapply;
                    }else{
                        $applied[] = $selfapply;
                    }
                }
            }
            $this->assign('apply1',$apply);
            $this->assign('apply',json_encode($apply));
            $this->assign('applied',json_encode($applied));
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
            $this->errorMsg('请求方式错误');
        }
        $modelCityPartner = new \app\index\model\CityPartner();
        $postData = input('post.');
        $validate = new \app\index\validate\CityPartner();
        $modelCityPartner -> startTrans();
        $postData['apply_status'] = $postData['step'];
        $validateName = 'step'.$postData['apply_status'];
        if(!$validate->scene($validateName)->check($postData)) {
            $this->errorMsg($validate->getError());
        }
        switch ($postData['step']){
            case 1:
            case 2:
                if($postData['old_apply_status']> $postData['apply_status']){
                    unset($postData['apply_status']);
                }
                $postData['user_id'] = $this->user['id'];
                $postData['create_time'] = time();
                if($postData['id']){
                    $where = [
                        ['id','=',$postData['id']],
                        ['user_id','=',$this->user['id']],
                        ['status','=',0],
                    ];

                }
                $id  = $modelCityPartner->edit($postData,$where);
                if(false===$id){
                    $modelCityPartner ->rollback();
                    $this->errorMsg('失败');
                }
                break;
            case 3:
                $paySn = generateSN(); //内部支付编号
                $postData['earnest_sn'] = $paySn;
                $postData['earnest'] = config('custom.cityPartner_fee')[1]['earnest'];
                $postData['amount'] = config('custom.cityPartner_fee')[1]['amount'];
                $postData['create_time'] = time();
                if($postData['id']){
                    $where = [
                        'id'=>$postData['id'],
                        'user_id'=>$this->user['id'],
                        'status'=>0,
                    ];
                }
                $id  = $modelCityPartner->edit($postData,$where);
                if(false===$id){
                    $modelCityPartner ->rollback();
                    $this->errorMsg('失败');
                }
                //生成支付表数据
                $modelPay = new \app\index\model\Pay();
                $data = [
                    'sn' => $paySn,
                    'actually_amount' =>config('custom.cityPartner_fee')[1]['earnest'],
                    'user_id' => $this->user['id'],
                    'pay_code' => $postData['pay_code'],
                    'type' => config('custom.pay_type')['cityPartnerSeatPay']['code'],
                    'create_time' => time(),
                ];
                if(isset($postData['pay_id']) && $postData['pay_id']){
                    $where1 = [
                        'id'=>$postData['pay_id'],
                        'user_id'=>$this->user['id'],
                        'status'=>0,
                    ];
                }
                $payId = $modelPay->edit($data,$where1);
                if(false===$payId){
                    $modelCityPartner ->rollback();
                    return errorMsg('失败');
                };
                break;
            //尾款支付
            case 4:
                $paySn = generateSN(); //内部支付编号
                if($postData['id']){
                    $data = [
                        'balance_sn' => $paySn
                    ];
                    $where = [
                        'id'=>$postData['id'],
                        'user_id'=>$this->user['id'],
                        'status'=>0,
                    ];
                }
                $id  = $modelCityPartner->edit($data,$where);
                if(false===$id){
                    $modelCityPartner ->rollback();
                    $this->errorMsg('失败');
                }
                //生成支付表数据
                $modelPay = new \app\index\model\Pay();
                $data = [
                    'sn' => $paySn,
                    'actually_amount' =>config('custom.cityPartner_fee')[1]['amount'],
                    'user_id' => $this->user['id'],
                    'pay_code' => $postData['pay_code'],
                    'type' => config('custom.pay_type')['cityPartnerBalancePay']['code'],
                    'create_time' => time(),
                ];

//                if(isset($postData['pay_id']) && $postData['pay_id']){
//                    $where1 = [
//                        'id'=>$postData['pay_id'],
//                        'user_id'=>$this->user['id'],
//                        'status'=>0,
//                    ];
//                }
                $payId = $modelPay->edit($data);
                if(!$payId){
                    $modelCityPartner ->rollback();
                    return errorMsg('失败');
                };
        }
        $modelCityPartner -> commit();
        $this->successMsg('成功',['url'=>config('custom.pay_gateway').$paySn,'id'=>$id]);
    }

    //尾款结算
    public function balance()
    {
        if(!request()->isAjax()){
            $this->errorMsg('请求方式错误');
        }


    }
}