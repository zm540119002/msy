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

        $modelCityArea = new \app\index_admin\model\CityArea();
        $condition = [
            'field' => [
                'province_name','province_code'
            ],
            'where' => [
                ['status','=',0],
            ],
            'group' => 'province_code'
        ];
        $provinceList = $modelCityArea->getList($condition);

        $modelCityPartnerMarketInfo = new \app\index_admin\model\CityPartnerMarketInfo();
        $condition2 = [
            'field' => [
                'id','name','amount','earnest'
            ],
            'where' => [
                ['status','=',0]
            ],
        ];
        $marketList = $modelCityPartnerMarketInfo->getList($condition2);

        $this->assign('provinceList',$provinceList);
        $this->assign('marketList',$marketList);

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
                'ca.id','ca.province_name','ca.city_name','ca.city_status','ca.alone_amount','ca.alone_earnest',
                'cpmi.amount','cpmi.earnest','cpmi.name market_name',
                'cp.company_name','cp.applicant','cp.mobile','cp.user_id'
            ],
            'join' => [
                ['city_partner_market_info cpmi','ca.cpmi_id = cpmi.id','left'],
                ['city_partner cp','ca.id = cp.city_area_id','left'],
            ],'where' => [
                //['ca.id','=',25]
            ],
            'order'=>[
                'ca.id'=>'asc',
            ],
        ];

        // 条件
        $keyword = input('get.keyword/s');
        if($keyword) $condition['where'][] = ['ca.city_name','like', '%' . trim($keyword) . '%'];

        $list = $modelCityArea->pageQuery($condition);
        //$list = $modelCityArea->getList($condition);

        //$cityIds = array_column($list,'province_name');  //键值

/*        foreach($list as $k => $v){

            unset($v['']);
            $list[$k]['child'][] = $v;
            p($v['id']);

        }
        exit;*/

        $this->assign('list',$list);

        return view('list_tpl');
    }

    public function getInfo(){
        $id = input('id/d');
        if (!$id){
            return errorMsg('失败');
        }

        $condition = [
            'field' => [
                '*'
            ],
            'where' => [
                ['cp.status','=',0],
            ],'order' => [
                'cp.update_time' => 'desc'
            ],
        ];

        $modelCityPartner = new \app\index_admin\model\CityPartner();
        $list = $modelCityPartner->getList($condition);

        $this->assign('list',$list);
        return $this->fetch('city_partner_list');
    }


    public function setField(){

        $id  = input('post.id/d');
        if (!$id){
            return errorMsg('失败');
        }

        $info= array();
        // 上下架
        if (input('?city_status')){
            $city_status = input('post.city_status/d')==0 ? 1 : 0 ;

            $info = ['city_status'=>$city_status];
        }

        $modelCityArea = new \app\index_admin\model\CityArea();
        $rse = $modelCityArea->where(['id'=>$id])->setField($info);

        if(!$rse){
            return errorMsg('失败');
        }
        $this->putDataJson();
        return successMsg('成功');

    }


    public function editMarket(){
        $data = input('post.');

        $modelCityPartnerMarketInfo = new \app\index_admin\model\CityPartnerMarketInfo();
        $data['update_time'] = time();
        $res = $modelCityPartnerMarketInfo->edit($data);
        if($res){
            $this->putDataJson();

            return successMsg();
        }else{
            return errorMsg();
        }
    }



    // 城市生成json文件  // {"id":"542600","name":"林芝地区","shortName":"林芝","parentId":"540000","level":"2"}
    public function putDataJson(){
        $modelCityArea = new \app\index_admin\model\CityArea();
        $config = [
            'field' => [
                'ca.id','ca.city_code','ca.province_name','ca.city_name','ca.city_status','ca.alone_amount','ca.alone_earnest','ca.level',
                'ca.province_code parentId','ca.cpmi_id class',
                'cpmi.amount','cpmi.earnest','cpmi.name market_name',
                //'cp.company_name','cp.applicant','cp.mobile','cp.user_id'
            ],
            'where' => [
                ['ca.status','=',0]
            ],
            'join' => [
                ['city_partner_market_info cpmi','ca.cpmi_id = cpmi.id','left'],
                //['city_partner cp','ca.id = cp.city_area_id','left'],
            ],'group' => 'city_code',
        ];


        $data = $modelCityArea->getList($config);
        $modelCityPartner = new \app\index_admin\model\CityPartner();
        $config = [
            'field' => [
                'cp.company_name','cp.applicant','cp.mobile','cp.user_id','cp.city_code'
            ],'where' => [
                ['cp.apply_status','=',5]
            ],
        ];
        $dataPartner = $modelCityPartner->getList($config);

        $temp_key   = array_column($dataPartner,'city_code');  //键值
        $dataPartner= array_combine($temp_key,$dataPartner) ;

        $city = [];
        $province = [];
        foreach($data as $k => $v){

            if(!isset($province[$v['parentId']])){

                $province[$v['parentId']] = $v;
                $p['id']      = $v['parentId'];
                $p['name']    = $v['province_name'];
                $p['parentId']= 100000;
                $p['class']   = 0;
                $p['level']   = 0;
                $p['amount']  = 0;
                $p['earnest'] = 0;
                $p['have']    = 0;
                $city[] = $p;
            }
            $c['id']      = $v['city_code'];
            $c['name']    = $v['city_name'];
            $c['parentId']= $v['parentId'];
            $c['class']   = $v['class']==3 ? 'C' : ($v['class']==2 ? 'B' : 'A') ;  // 市场类别
            $c['level']   = $v['level'];                                           // 城市等级
            $c['amount']  = $v['alone_amount']>0 ? $v['alone_amount'] : $v['amount'];    // 资格款
            $c['earnest'] = $v['alone_earnest']>0 ? $v['alone_earnest'] : $v['earnest']; // 定金

            $c['have']    = isset($dataPartner[$v['city_code']]) ? 1 : 0;  // 已有合伙人
            $city[] = $c;

        }
/*        p($city);
        exit;*/
        $cityData = 'var cityData='.json_encode($city);

        file_put_contents('static/index/js/mobileSelector/js/json2.js',$cityData);
    }
}