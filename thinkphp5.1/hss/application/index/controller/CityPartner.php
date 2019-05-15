<?php
namespace app\index\controller;

/**
 * 城市合伙人控制器
 */
class CityPartner extends \common\controller\Base {

    /**
     * 首页
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
     * 城市合伙人申请条件
     */
    public function city(){

        return $this->fetch();
    }
    /**
     * 合伙人申请
     */
    public function registered(){

        return $this->fetch();
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