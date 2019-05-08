<?php
namespace app\index\controller;

class Franchise extends \common\controller\UserBase {
    /**首页
     */
    public function index(){
        if(request()->isAjax()){
        }else{
            // 底部菜单，见配置文件custom.footer_menu
            $this->assign('currentPage',request()->controller().'/'.request()->action());
            return $this->fetch();
        }
    }

    /**
     * 申请加盟
     * @return array|mixed
     * @throws \think\exception\PDOException
     *
     */
    public function applyFranchise()
    {
        if(request()->isAjax()){
            $postData = input('post.applicantData/a');
//            $validate = new \app\index\validate\Franchise();
//            if(!$validate->scene('add')->check($postData)) {
//                return errorMsg($validate->getError());
//            }
//            return $postData;
            $modelFranchise = new \app\index\model\Franchise();
            $modelFranchise -> startTrans();

            $sn = generateSN(); //内部支付编号
            $postData['sn'] = $sn;
            $result  = $modelFranchise->isUpdate(false)->save($postData);
            if(!$result){
                $modelFranchise ->rollback();
                return errorMsg('失败');
            }
            $modelPay = new \app\index\model\Pay();
            $data = [
                'sn' => $sn,
                'actually_amount' => $postData['franchise_fee'],
                'user_id' => 1,
                'payment_code' => $postData['payment_code'],
                'type' => config('custom.pay_type')['franchisePay']['code'],
            ];
            $result  = $modelPay->isUpdate(false)->save($data);
            if(!$result){
                $modelPay ->rollback();
                return errorMsg('失败');
            }
            $modelFranchise -> commit();
            return successMsg('成功',['url'=>config('custom.pay_franchise')]);
        }else{
            $unlockingFooterCart = unlockingFooterCartConfig([10,0,9]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);
            return $this->fetch();
        }
    }


}