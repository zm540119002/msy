<?php
namespace app\index_admin\model;

class CityArea extends \common\model\Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'city_area';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_1';
	//表的别名
	protected $alias = 'ca';


    /**
     * 查询城市合伙人记录 false 不行，$res 可以
     * @param $province
     * @param $city
     * @return false || $res
     */
    public function getPartner($province,$city){



/*        $config = [
            'field' => [
                'ca.id','ca.city_code','ca.province_name','ca.city_name','ca.city_status','ca.alone_amount','ca.alone_earnest','ca.level',
                'ca.province_code parentId','ca.cpmi_id class',
                'cpmi.amount','cpmi.earnest','cpmi.name market_name',
                'cp.company_name','cp.applicant','cp.mobile','cp.user_id','cp.is_partner'
            ],
            'join' => [
                ['city_partner_market_info cpmi','ca.cpmi_id = cpmi.id','left'],
                ['city_partner cp','ca.id = cp.city_area_id','left'],
            ],
            'where' => [
                ['ca.city_status','=',0],
                ['ca.province_code','=',$province],
                ['ca.city_code','=',$city],
                //['cp.is_partner','=',0],
            ],
        ];
        $res = $this->getInfo($config);*/

        $config = [
            'field' => [
                'ca.id','ca.city_code','ca.province_name','ca.city_name','ca.city_status','ca.alone_amount','ca.alone_earnest','ca.level',
                'ca.province_code parentId','ca.cpmi_id class',
                'cpmi.amount','cpmi.earnest','cpmi.name market_name',
                'cp.company_name','cp.applicant','cp.mobile','cp.user_id','cp.apply_status'
            ],
            'join' => [
                ['city_partner_market_info cpmi','ca.cpmi_id = cpmi.id','left'],
                ['city_partner cp','ca.id = cp.city_area_id','left'],
            ],
            'where' => [
                ['ca.city_status','=',0],
                ['ca.province_code','=',$province],
                ['ca.city_code','=',$city],
                //['cp.is_partner','=',0],
            ],
        ];
        $res = $this->getInfo($config);
        //p($res);
        //exit;
        if( !$res || $res['apply_status']==5){
            return false;
        }else{

            $res['amount']  = $res['alone_amount']>0 ? $res['alone_amount'] : $res['amount'];    // 资格款
            $res['earnest'] = $res['alone_earnest']>0 ? $res['alone_earnest'] : $res['earnest']; // 定金

            return $res;
        }

    }

}