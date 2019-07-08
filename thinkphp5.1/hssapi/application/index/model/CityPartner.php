<?php
namespace app\index\model;

class CityPartner extends \common\model\Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'city_partner';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_1';
	//表的别名
	protected $alias = 'cp';


    public function paymentUpdateBeforeInfo($postData,$info){

        $postData['province_code']= $postData['province'];
        $postData['city_code']   = $postData['city'];
        $postData['city_level']  = $info['level'];
        $postData['earnest']     = $info['earnest'];
        $postData['amount']      = $info['amount'];
        $postData['city_area_id']= $info['id'];
        $postData['market_name'] = $info['market_name'];
        $postData['update_time'] = time();
        $postData['apply_status']= 2;

        $where = [
            'where' => [
                //'sn'      => $postData['sn'],
                'user_id' => $postData['user_id'],
                'status'  => 0,
                'apply_status' => 2, // 每个用户只能有一条还没付款的记录
            ],
        ];

        $res = $this->getInfo($where);

        if($res){
            return $this->allowField(true)->isUpdate(true)->save($postData,$where['where']);

        }else{
            $postData['sn']         = 1115 . generateSN(14);
            $postData['create_time']= time();

            return $this->allowField(true)->isUpdate(false)->save($postData);
        }

    }

}