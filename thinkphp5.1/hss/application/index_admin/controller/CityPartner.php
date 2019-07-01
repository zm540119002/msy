<?php
namespace app\index_admin\controller;

/**
 * 城市合伙人控制器
 */
class CityPartner extends Base {

    public function manage(){

        $city_id = input('id/d');

        if($city_id){
            $modelCityArea = new \app\index_admin\model\CityArea();
            $condition = [
                'where' => [
                    ['id','=',$city_id]
                ],
            ];
            $info = $modelCityArea->getInfo($condition);

            $this->assign('info',$info);
        }

        return $this->fetch();
    }

    /**
     *  分页查询
     */
    public function getList(){

        $modelCityPartner = new \app\index_admin\model\CityPartner();

        $condition = [
            'field' => [
                'id','sn','user_id','company_name','applicant','mobile','earnest','amount','payment_time','pay_sn','pay_code','balance_sn','apply_status','create_time','update_time','earnest_sn',
            ],
            'where' => [
                ['status','=',0],
            ],
            'order'=>[
                'update_time' => 'desc',
            ],
        ];

        // 条件
        $keyword = input('get.keyword/s');
        $city_id = input('get.city_id/d');
        if($keyword) $condition['where'][] = ['sn','like', '%' . trim($keyword) . '%'];
        if($city_id) $condition['where'][] = ['city_area_id','=',$city_id];

        $condition['where'][] = ['apply_status','>',1];

        $list = $modelCityPartner->pageQuery($condition);

        $this->assign('list',$list);

        return view('list_tpl');
    }

    public function setApplyStatus(){

        $id  = input('post.id/d');
        $apply_status = input('post.apply_status/d');

        if (!$id || !$apply_status){
            return errorMsg('失败');
        }

        $modelCityPartner = new \app\index_admin\model\CityPartner();

        if($apply_status==4){
            $info = ['apply_status'=>4];

            $where = [
                ['id','=',$id],
                ['apply_status','=',3],
                ['status','=',0],
            ];
            $buildSql = $modelCityPartner->where($where)->field('city_area_id')->buildSql();

            $condition = [
                'field' => [
                    'cp.id'
                ],
                'where' => [
                    ['cp.status','=',0],
                    ['cp.apply_status','in','4,5'],
                    ['cp.city_area_id','EXP','='.$buildSql],
                ],
            ];
            $res = $modelCityPartner->getInfo($condition);

            if($res){
                return errorMsg('当前城市已有合伙人或有已通过审核通过的合伙人 !');
            }

        }else{
            $info = ['apply_status'=>-1];
        }


        $rse = $modelCityPartner->where(['id'=>$id])->setField($info);

        if(!$rse){
            return errorMsg('失败');
        }
        $this->putDataJson();
        return successMsg('成功');

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