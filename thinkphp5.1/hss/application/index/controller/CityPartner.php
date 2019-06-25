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
            // 做到这里
            $sn = addslashes(trim(input('sn/s')));
            if($sn){

                $where = [
                    ['cp.status', '=', 0],
                    ['cp.user_id','=',$this->user['id']],
                    ['cp.apply_status','>',0],
                    //['cp.is_partner','=',0],
                    ['cp.sn','=',$sn],
                ];
            //自己提交的申请
/*            $modelCityPartner = new \app\index\model\CityPartner();
            $condition=[
                'where'=>[
                    ['cp.status', '=', 0],
                    ['cp.user_id','=',$this->user['id']],
                    ['cp.apply_status','>',0],
                    ['cp.is_partner','=',0],
                    ['cp.id','=',$id],
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

            $selfApplyInfo = reset($selfApplyList);*/
                //申请中
/*                $apply = [];
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
                $this->assign('applied',json_encode($applied));*/

            }else{
                $where = [
                    ['cp.status', '=', 0],
                    ['cp.user_id','=',$this->user['id']],
                    ['cp.apply_status','=',2],
                    //['cp.apply_status','<>',5],
                    //['cp.sn','=',$sn],
                ];
            }

            $modelCityPartner = new \app\index\model\CityPartner();
            $condition = [
                'field' => [
                    'cp.id','cp.sn','cp.province_code','cp.city_code','cp.company_name','cp.applicant','cp.market_name','cp.mobile','cp.city_level',
                    'cp.earnest','cp.amount','cp.apply_status','cp.update_time','cp.city_level',
                    'ca.province_name','ca.city_name'
                    //,'p.sn pay_sn','p.id as pay_id'
                ],
                'where' => $where,
                'join'  => [
                    //['pay p','p.sn = cp.earnest_sn','left'],
                    ['city_area ca','cp.city_code = ca.city_code','left'],
                ],
                'order' => [
                    'update_time' => 'desc'
                ],
            ];
            $info = $modelCityPartner -> getInfo($condition);

            // 申请中的记录 apply_status：2:已提交资料 3:待审核（已交定金） 4审核通过  5 交清尾款|已授权的城市
            $condition = [
                'field' => [
                    'cp.city_code','cp.apply_status',
                    'ca.city_name',
                ],'where' => [
                  ['cp.user_id','=', $this->user['id']],
                  ['cp.apply_status','in', '2,3,4,5'],
                  ['cp.status','=', 0],
                ],'join' => [
                    ['city_area ca','cp.city_area_id=ca.id','left']
                ],
            ];
            $list = $modelCityPartner->getList($condition);

            // 已授权城市&&申请中的城市数量
            $apply_count = 0;
            if($list){
                foreach($list as $k => $v){
                    if($v['apply_status']!=5){
                        if(in_array($v['apply_status'],[3,4])){
                            $apply_count++;
                        }
                        unset($list[$k]);
                    }
                }
            }

            $this->assign('info',$info);
            $this->assign('auth_city',$list);
            $this->assign('apply_count',$apply_count);

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

        $validate = new \app\index\validate\CityPartner();
        $validateName = 'step'.$postData['step'];
        if(!$validate->scene($validateName)->check($postData)) {
            $this->errorMsg($validate->getError());
        }

        $modelCityArea = new \app\index_admin\model\CityArea();
        $info = $modelCityArea->getPartner($postData['province'],$postData['city']);
        if(!$info){
            $this->errorMsg('已有合伙人',['status'=>1000]);
        }

        ///$data = ['url'=>config('custom.pay_gateway').$paySn,'id'=>$id];

        $modelCityPartner = new \app\index\model\CityPartner();
        $modelCityPartner -> startTrans();

        $data = [];
        // 1、城市查询 2、登记资料 3、支付定金 4、支付尾款
        switch ($postData['step']){
            case 1:
                break;

            case 2: // 更新资料
                $postData['user_id'] = $this->user['id'];
                $res = $modelCityPartner->paymentUpdateBeforeInfo($postData,$info);

                if(false===$res){
                    $this->errorMsg('失败');
                }

                break;

            case 3: // 支付定金
                $paySn = generateSN(); //内部支付编号
                $postData['earnest_sn']  = $paySn; //内部支付编号
                $postData['user_id'] = $this->user['id'];

                $res = $modelCityPartner->paymentUpdateBeforeInfo($postData,$info);

                if(false===$res){
                    $modelCityPartner ->rollback();
                    $this->errorMsg('失败');
                }
                //生成支付表数据
                $modelPay = new \app\index\model\Pay();
                $data = [
                    'sn' => $paySn,
                    'actually_amount' => $info['earnest'],
                    'user_id'  => $this->user['id'],
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

                //$data = ['url'=>config('custom.pay_gateway').$paySn];
                $data['url'] = config('custom.pay_gateway').$paySn;
                break;

            case 4: // 支付尾款
                if(!isset($postData['sn']) || empty($postData['sn'])){
                    $this->errorMsg('失败');
                }

                $where = [
                    'where' => [
                        'sn' => $postData['sn'],
                        'user_id'=>$this->user['id'],
                        'status'=>0,
                        'apply_status'=> 4,
                    ]
                ];

                $res = $modelCityPartner->getInfo($where);
                if(!$res){
                    $modelCityPartner ->rollback();
                    $this->errorMsg('失败');
                }

                $paySn = generateSN(); //内部支付编号
                $data = ['balance_sn' => $paySn];

                $res = $modelCityPartner->allowField(true)->isUpdate(true)->save($data,$where['where']);
                if(false===$res){
                    $modelCityPartner ->rollback();
                    $this->errorMsg('失败');
                }
                //生成支付表数据
                $modelPay = new \app\index\model\Pay();
                $data = [
                    'sn' => $paySn,
                    'actually_amount' => (double)($info['amount']-$info['earnest']),
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
                $data['url'] = config('custom.pay_gateway').$paySn;
                //$data = ['url'=>config('custom.pay_gateway').$paySn,'id'=>$id];
        }

        $this->successMsg('成功',$data);
    }

    /**
     * 申请中
     */
    public function applicationList(){
        return $this->fetch();
    }

    public function getList(){

        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }

        $condition = [
            'field' => [
                'cp.id','cp.sn','cp.apply_status','cp.create_time','cp.update_time',
                'ca.city_name',
            ],'where' => [
                ['cp.status','=',0],
                ['cp.apply_status','in','3,4,-1'],
                ['cp.user_id','=',$this->user['id']],
            ],'join' => [
                ['city_area ca','cp.city_area_id=ca.id','left']
            ],
        ];

        $modelCityPartner = new \app\index\model\CityPartner();
        $list = $modelCityPartner->pageQuery($condition);
        //p($condition);
        //exit;
        $this->assign('list',$list);
        return $this->fetch('list_tpl');

    }


}