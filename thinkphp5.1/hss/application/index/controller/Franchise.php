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

//    /**
//     * 申请加盟
//     * @return array|mixed
//     * @throws \think\exception\PDOException
//     *
//     */
//    public function applyFranchise()
//    {
//        if(request()->isAjax()){
//            $postData = input('post.');
//            $validate = new \app\index\validate\Franchise();
//            if(!$validate->scene('add')->check($postData)) {
//                return errorMsg($validate->getError());
//            }
//            $modelFranchise = new \app\index\model\Franchise();
//            $modelFranchise -> startTrans();
//            $sn = generateSN(); //内部支付编号
//            $postData['sn'] = $sn;
//            $postData['user_id'] = $this->user['id'];
//            $postData['franchise_fee'] = config('custom.franchise_fee');
//            $postData['create_time'] = time();
//            $result  = $modelFranchise->isUpdate(false)->save($postData);
//            if(!$result){
//                $modelFranchise ->rollback();
//                return errorMsg('失败');
//            }
//
//            //生成支付表数据
//            $modelPay = new \app\index\model\Pay();
//            $data = [
//                'sn' => $sn,
//                'actually_amount' =>config('custom.franchise_fee'),
//                'user_id' => $this->user['id'],
//                'pay_code' => $postData['pay_code'],
//                'type' => config('custom.pay_type')['franchisePay']['code'],
//                'create_time' => time(),
//            ];
//            $result  = $modelPay->isUpdate(false)->save($data);
//            if(!$result){
//                $modelPay ->rollback();
//                return errorMsg('失败');
//            }
//            $modelFranchise -> commit();
//            return successMsg('成功',['url'=>config('custom.pay_franchise').$sn]);
//        }else{
//            $unlockingFooterCart = unlockingFooterCartConfig([10,0,9]);
//            $this->assign('unlockingFooterCart', $unlockingFooterCart);
//            return $this->fetch();
//        }
//    }

    /**
     * 申请加盟
     * @return array|mixed
     * @throws \think\exception\PDOException
     *
     */
    public function applyFranchise()
    {
        if(request()->isAjax()){
//            $postData = input('post.');
//            $validate = new \app\index\validate\Franchise();
//            if(!$validate->scene('add')->check($postData)) {
//                return errorMsg($validate->getError());
//            }
//            $modelFranchise = new \app\index\model\Franchise();
//            $modelFranchise -> startTrans();
//            $sn = generateSN(); //内部支付编号
//            $postData['sn'] = $sn;
//            $postData['user_id'] = $this->user['id'];
//            $postData['franchise_fee'] = config('custom.franchise_fee');
//            $postData['create_time'] = time();
//            $result  = $modelFranchise->isUpdate(false)->save($postData);
//            if(!$result){
//                $modelFranchise ->rollback();
//                return errorMsg('失败');
//            }
//
//            //生成支付表数据
//            $modelPay = new \app\index\model\Pay();
//            $data = [
//                'sn' => $sn,
//                'actually_amount' =>config('custom.franchise_fee'),
//                'user_id' => $this->user['id'],
//                'pay_code' => $postData['pay_code'],
//                'type' => config('custom.pay_type')['franchisePay']['code'],
//                'create_time' => time(),
//            ];
//            $result  = $modelPay->isUpdate(false)->save($data);
//            if(!$result){
//                $modelPay ->rollback();
//                return errorMsg('失败');
//            }
//            $modelFranchise -> commit();
//            return successMsg('成功',['url'=>config('custom.pay_franchise').$sn]);
        }else{
            $unlockingFooterCart = unlockingFooterCartConfig([10,0,9]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);
            return $this->fetch();
        }
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