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
        $config = [
            'field' => [
                'id','area_id','province_name','city_name','area_parent_id parentId','cpmi_id level'
            ]
        ];
        $data = $modelCityArea->getList($config);

        $city = [];
        $province = [];
        foreach($data as $k => $v){

            //unset();
            if(!isset($province[$v['parentId']])){
                //$v['parentId'] = 10000;
                $province[$v['parentId']] = $v;
                //$i = $v;
                $p['id'] = $v['parentId'];
                $p['name'] = $v['province_name'];
                $p['parentId'] = 100000;
                $p['level'] = 0;
                $city[] = $p;
            }
            $c['id'] = $v['area_id'];
            $c['name'] = $v['city_name'];
            $c['parentId'] = $v['parentId'];
            $c['level'] = $v['level'];
            $city[] = $c;

        }

        $cityData = 'var cityData='.json_encode($city);

        file_put_contents('static/index/js/mobileSelector/js/json2.js',$cityData);


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

        $list = $modelCityArea->pageQuery($condition);

        $this->assign('list',$list);

        return view('list_tpl');
    }

    // 城市生成json文件  // {"id":"542600","name":"林芝地区","shortName":"林芝","parentId":"540000","level":"2"}
    public function putDataJson(){
        $modelCityArea = new \app\index_admin\model\CityArea();
        $config = [
            'field' => [
                'id','area_id','province_name','city_name','area_parent_id parentId','cpmi_id level'
            ]
        ];
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