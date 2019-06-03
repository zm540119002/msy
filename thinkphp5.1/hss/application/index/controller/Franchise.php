<?php
namespace app\index\controller;

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
        print_r(input());exit;
        if(!request()->isAjax()){
            return errorMsg('请求方式错误');
        }
        $postData = input('post.');
        $validate = new \app\index\validate\Franchise();
        if(!$validate->scene('add')->check($postData)) {
            return errorMsg($validate->getError());
        }
        $modelFranchise = new \app\index\model\Franchise();
        $modelFranchise -> startTrans();
        $sn = generateSN(); //内部支付编号
        $postData['sn'] = $sn;
        $postData['user_id'] = $this->user['id'];
        $postData['franchise_fee'] = config('custom.franchise_fee');
        $postData['create_time'] = time();
        $result  = $modelFranchise->isUpdate(false)->save($postData);
        if(!$result){
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
        $result  = $modelPay->isUpdate(false)->save($data);
        if(!$result){
            $modelPay ->rollback();
            return errorMsg('失败');
        }
        $modelFranchise -> commit();
        return successMsg('成功',['url'=>config('custom.pay_gateway').$sn]);
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