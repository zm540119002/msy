<?php
namespace app\index\controller;

class Franchise extends \common\controller\UserBase{
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

    public function applyFranchise()
    {
        if(request()->isAjax()){
            $model = new \app\index\model\Franchise();
            $data = [
                'name'=>'ygb',
                'mobile'=>'18664368697',
                'province'=>'1',
                'city'=>'1',
                'detail_address'=>'hhhhhhh',
                'franchise_fee' =>'0.01',
            ];
            $data['sn'] = generateSN();
            $result  = $model->isUpdate(false)->save($data);
            if(!$result){
                return e('成功',['url'=>config('custom.pay_franchise')]);
            }else{
                return successMsg('成功',['url'=>config('custom.pay_franchise')]);
            }
        }else{
            $unlockingFooterCart = unlockingFooterCartConfig([10,0,9]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);
            return $this->fetch();
        }
    }


}