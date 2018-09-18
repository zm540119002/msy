<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/17
 * Time: 17:21
 */

namespace app\store\model;

use think\Model;

class Finance extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'finance';
    // 设置当前模型的数据库连接
    protected $connection = 'db_config_factory';
    // 设置主键
    protected $pk = 'id'; // $pk = 'finance_id';

    /**
     * 获取财务单据支出或收入类型获取器
     * @param $value
     * @return mixed
     */
    public function getTypeAttr($value)
    {
        $type = [0=>'收入', 1=>'支出'];
        return $type[$value];
    }

    public function getOrderFinance($store_id)  // select * from orders limit 1; run  app->get  app
    {
        $ret = $this->alias('a')
            ->field('a.finance_sn, a.create_time, a.relation_bill, c.amount, c.status_unpack')
            ->join('order b', 'a.relation_bill=b.order_sn and a.finance_id=b.or', 'INNER')
            ->join('order_unpack c', 'a.store_id=c.store_id and b.order_id=c.order_id','INNER')
            ->where(['a.store_id'=>$store_id])
            ->order('a.create_time desc')
            ->select();
        if(count($ret)>0){
            return successMsg( '获取订单财务单据成功',['data'=>$ret] );
        }
        return errorMsg('获取订单财务单据失败',['data'=>$ret]);
    }

    public function getFinance(){
        return false;
    
    }   

    public function getBase(){
        return false;
    }

    /**
     * 创建财务单据
     * @return string
     */
    public function createFinanceId()
    {
        $finance_id = '8'.time().mt_rand(1000, 9999);
        $data = $this->where(['finance_id'=>$finance_id])->count();
        while(!$data){
            $finance_id = '8'.time().mt_rand(1000, 9999); 
            $data = $this->where(['finance_id'=>$finance_id])->count();
        }
        return $finance_id;
    }

    public function getOne(){
        //
        return $this;
    }

}