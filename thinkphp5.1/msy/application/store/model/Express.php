<?php
/**
 * Created by PhpStorm.
 * User: Mr.wei
 * Date: 2018/6/11
 * Time: 10:15
 */
namespace app\store\model;

use think\Model;

class Express extends \common\model\Base
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'express';
    // 设置当前模型的数据库连接
    protected $connection = 'db_config_factory';
    // 设置主键
    protected $pk = 'id';
    // 别名
    protected $alias = 'e';
    protected $autoWriteTimestamp = true;

    /**
     * 添加物流信息
     * @param $order_id
     * @param $express_name
     * @param $express_code
     * @return array
     */
    public function addExpress($order_id, $express_name, $express_code)
    {
        $ret = $this->create([
            'order_id' => $order_id,
            'express_name' => $express_name,
            'express_code' => $express_code,
        ], ['order_id', 'express_name', 'express_code']);
        if($ret){
            return successMsg('添加物流信息成功',['express_id'=>$ret['id']]);
        }
        return errorMsg('添加物流信息失败');
    }

    public function deleteExpress($id)
    {
        $ret = $this->destroy($id);
        if($ret){
            return successMsg('删除成功');
        }
        return errorMsg('删除失败');
    }

    /**
     * 更新物流数据
     * @param $id 物流表主键ID
     * @param $express_name
     * @param $express_code
     * @return array
     */
    public function updateExpress($id, $express_name, $express_code)
    {
        $data = [
            'express_name' => $express_name,
            'express_code' => $express_code,
            'update_time' => time(),
        ];
        $ret = $this->where(['id'=>$id])->update($data);
        if($ret){
            return successMsg('修改成功');
        }
        return errorMsg('修改失败');
    }

    /**
     * 查询是否有物流信息
     * @param $store_id
     * @param $order_id
     * @return int|string
     */
    public function isExpress($store_id, $order_id)
    {
        return $this->where(['order_id'=>$order_id, 'store_id'=>$store_id])->count();
    }

    /**
     * -对多相对关联
     * @return \think\model\relation\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo('Order', 'order_id', 'id');
    }
}