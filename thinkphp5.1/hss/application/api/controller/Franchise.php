<?php
namespace app\api\controller;

class Franchise extends \common\controller\UserBase {
    /**
     * 申请加盟
     * @return array|mixed
     * @throws \think\exception\PDOException
     *
     */
    public function applyFranchise()
    {
        if (request()->isAjax()) {
        } else {
            //自己提交的申请
            $modelFranchise = new \app\index\model\Franchise();
            $condition=[
                'where'=>[
                    ['f.status', '=', 0],
                    ['f.user_id','=',$this->user['id']],
                    ['f.apply_status','>',0],
                ],
                'field'=>[
                    'f.id','f.province','f.city','f.area','f.detail_address','f.name','f.applicant','f.mobile','f.franchise_fee','f.apply_status',
                    'p.sn','p.id as pay_id'
                ],'join' => [
                    ['pay p','p.sn = f.sn','left'],
                ],
            ];
            $selfApplyList = $modelFranchise -> getList($condition);
            //申请中
            $apply = [];
            //已申请
            $applied = [];
            if($selfApplyList){
                foreach ($selfApplyList as $selfapply){
                    if ($selfapply['apply_status']>0&&$selfapply['apply_status']<2){
                        $apply[] = $selfapply;
                    }else{
                        $applied[] = $selfapply;
                    }
                }
            }
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
    public function franchiseSettlement()
    {
        if(!request()->isAjax()){
            return errorMsg('请求方式错误');
        }
        $postData = input('post.');

        $scene = 'add';
        // 预约登记
        if(isset($postData['type']) && $postData['type']==2){
            $scene = 'reservation';
        }

        $validate = new \app\index\validate\Franchise();
        if(!$validate->scene($scene)->check($postData)) {
            return errorMsg($validate->getError());
        }

        $modelFranchise = new \app\index\model\Franchise();
        $modelFranchise -> startTrans();
        $postData['user_id'] = $this->user['id'];
        $postData['franchise_fee'] = config('custom.franchise_fee');
        $postData['create_time'] = time();
        $sn = generateSN(); //内部支付编号
        $postData['sn'] = $sn;
        if(isset($postData['id']) && $postData['id']){
            $where = [
                'id'=>$postData['id'],
                'user_id'=>$this->user['id'],
                'status'=>0,
            ];
        }

        if(isset($postData['step'])){
            $postData['apply_status'] = 1; //待付款
            if($postData['step'] == 1){
                $id = $modelFranchise->edit($postData,$where);
                if(!$id){
                    $modelFranchise ->rollback();
                    return errorMsg('失败');
                }
            }
            if($postData['step'] == 2){
                $id = $modelFranchise->edit($postData,$where);
                if(!$id){
                    $modelFranchise ->rollback();
                    return errorMsg('失败');
                }
                //生成支付表数据
                $modelPay = new \app\index\model\Pay();
                $data = [
                    'sn' => $sn,
                    'actually_amount' =>config('custom.franchise_fee'),
                    'user_id' => $this->user['id'],
                    'pay_code' => $postData['pay_code'],
                    'type' => config('custom.pay_type')['franchisePay']['code'],
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
                if(!$payId){
                    $modelFranchise ->rollback();
                    return errorMsg('失败');
                };
            }

        }else{
            $postData['apply_status'] = 1; //预登记
            $id = $modelFranchise->edit($postData);
            if(!$id){
                $modelFranchise ->rollback();
                return errorMsg('失败');
            }
        }
        $modelFranchise -> commit();
        $data = [
            'code'=> config('code.success.default.code'),
            'url' => config('custom.pay_gateway').$sn,'id'=>$id,
        ];

        //$this->successMsg('成功',['url'=>config('custom.pay_gateway').$sn,'id'=>$id]);
        $this->successMsg('成功',$data);
    }

    /**
     * 创客管理
     * @return mixed
     */
    public function makersManage()
    {
        return $this->fetch();
    }


}