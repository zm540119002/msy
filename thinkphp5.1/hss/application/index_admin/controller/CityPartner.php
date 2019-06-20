<?php
namespace app\index_admin\controller;

/**
 * 城市合伙人控制器
 */
class CityPartner extends Base {

    protected $obj;

    protected $beforeActionList = [
        'currentModelClass'  =>  ['only'=>'edit,getList'],
    ];

    protected  function currentModelClass(){
        $this->obj = new \app\index_admin\model\CityPartner();
    }

    public function manage(){
        return $this->fetch();
    }

    /**
     *  分页查询
     */
    public function getList(){

/*        $condition = [
            'field'=>['id','name','shelf_status'],
        ];*/

        $modelCityArea = new \app\index_admin\model\CityArea();

        $condition = [
            'field' => [
                'ca.area_id id','ca.province_name','ca.city_name','ca.city_status','ca.alone_amount','ca.alone_earnest',
                'cpmi.amount','cpmi.earnest','cpmi.name market_name',
                'cp.company_name','cp.applicant','cp.mobile','cp.user_id'
            ],
            'join' => [
                ['city_partner_market_info cpmi','ca.cpmi_id = cpmi.id','left'],
                ['city_partner cp','ca.id = cp.city_area_id','left'],
            ],
            'order'=>[
                'ca.id'=>'asc',
            ],
        ];

        // 条件
        $keyword = input('get.keyword/s');
        if($keyword) $condition['where'][] = ['ca.city_name','like', '%' . trim($keyword) . '%'];
        //p($condition);
        //exit;
        $list = $modelCityArea->pageQuery($condition);
        //p($modelCityArea->getLastSql());
        //p($list);
        //exit;
        $this->assign('list',$list);

        return view('list_tpl');
    }
}