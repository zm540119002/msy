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
            //自己提交的申请
            $modelCityPartner = new \app\index\model\CityPartner();
            $condition=[
                'where'=>[
                    ['cp.status', '=', 0],
                    ['cp.user_id','=',$this->user['id']],
                    ['cp.apply_status','>',0],
                    ['cp.is_partner','=',0],
                ], 'field'=>[
                    'cp.id','cp.province','cp.city','cp.company_name','cp.applicant',
                    'cp.mobile','cp.city_level','cp.earnest','cp.amount','cp.apply_status','cp.payment_time'
                    ,'p.sn','p.id as pay_id'
                ]
                ,'join' => [
                     ['pay p','p.sn = cp.earnest_sn','left'],
                ]
            ];
            $selfApplyList = $modelCityPartner -> getList($condition);
            //申请中
            $apply = [];
            //已申请
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
     * 提交申请
     * @return array
     * @throws \think\exception\PDOException
     */
    public function submitApplicant()
    {
        if(!request()->isAjax()){
            $this->errorMsg('请求方式错误');
        }


        $postData = input('post.');
        if(!$postData){
            $this->errorMsg('失败');
        }
/*        p($postData);
        exit;*/
        $validate = new \app\index\validate\CityPartner();
        $postData['apply_status'] = $postData['step'];
        $validateName = 'step'.$postData['apply_status'];
        if(!$validate->scene($validateName)->check($postData)) {
            $this->errorMsg($validate->getError());
        }

        $modelCityArea = new \app\index_admin\model\CityArea();
        $info = $modelCityArea->getPartner($postData['province'],$postData['city']);
        if(!$info){
            $this->errorMsg('已有合伙人',['status'=>1000]);
        }

        $modelCityPartner = new \app\index\model\CityPartner();
        //$modelCityPartner -> startTrans();

        $data = [];
        // 1、城市查询 2、登记资料 3、支付定金 4、支付尾款
        switch ($postData['step']){
            case 1:
                // 查询状态，设置不开放&&已签约
/*                $condition = [
                    'field' => [
                        'ca.city_code','ca.province_name','ca.city_name','ca.city_status','ca.alone_amount','ca.alone_earnest',
                        'cp.company_name','cp.applicant','cp.mobile','cp.user_id'
                    ],
                    'join' => [
                        //['city_partner cp','ca.id = cp.city_area_id','left'],
                        ['city_area ca','ca.id = cp.city_area_id','right'],
                    ],
                    'where' => [
                        ['ca.city_status','=',0],
                        ['ca.province_code','=',$postData['province']],
                        ['ca.city_code','=',$postData['city']],
                        ['cp.is_partner','=',0],
                    ],
                ];
                $res = $modelCityPartner->getInfo($condition);
                if($res){
                    $this->errorMsg('失败',['status'=>1000]);
                }*/

                break;

            case 2:
                // 添加记录
  /*              p($postData);
                exit;*/
/*                if($postData['old_apply_status']> $postData['apply_status']){
                    unset($postData['apply_status']);
                }*/


                //p($postData);
                //p($info);
                //exit;
                //exit;

/*                $postData['user_id'] = $this->user['id'];
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
                }*/
                $postData['user_id']     = $this->user['id'];
                $postData['city_level']  = $info['level'];
                $postData['earnest']     = $info['earnest'];
                $postData['amount']      = $info['amount'];
                $postData['apply_status']= 2;
                $postData['city_area_id']= $info['id'];
                $postData['create_time'] = time();
                //p($postData);
                //exit;
                $res = $modelCityPartner->edit($postData);

                if(!$res){
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
                $payId = $modelPay->edit($data);
                if(!$payId){
                    $modelCityPartner ->rollback();
                    return errorMsg('失败');
                };
        }

        $this->successMsg('成功',$data);
        //$this->successMsg($result);
        //$modelCityPartner -> commit();
        //$this->successMsg('成功',['url'=>config('custom.pay_gateway').$paySn,'id'=>$id]);
    }

    /**
     * 城市查询
     */
/*    public function searchCity(){

        if(!request()->isAjax()){
            $this->errorMsg('请求方式错误');
        }

        $postData = input('post.');

        if(!$postData){
            $this->errorMsg('城市不能为空');
        }
        p($postData);
        // 做到这里
        $postData['apply_status'] = $postData['step'];
        $validateName = 'step'.$postData['apply_status'];
        $validate = new \app\index\validate\CityPartner();
        if(!$validate->scene($validateName)->check($postData)) {
            $this->errorMsg($validate->getError());
        }


        $this->successMsg('成功');
        $this->errorMsg('失败');
        p($postData);
        exit;
    }*/

}