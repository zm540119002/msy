<?php
/**
 * Created by PhpStorm.
 * User: Mr.wei
 * Date: 2018/5/17
 * Time: 11:31
 * 购物车模型
 */
namespace app\store\model;

use think\Model;
use think\Db;

/**
 * 厂商模型
 * 文件不能取名 Store(命名空间与文件名的冲突？)
 */

class Store extends Model {
    // 设置当前模型对应的完整数据表名称
    protected $table = 'store';
    // 设置当前模型的数据库连接
    protected $connection = 'db_config_factory';
    // 设置主键
    protected $pk = 'id';

    /**
     * 判断店铺是否存在、是否营业、是否正常
     * @param $id
     * @return array
     */
    public  function hasStore($id)
    {
        $ret = $this->where(['id'=>$id, 'auth_status'=>2, 'status'=>0])->count();
        if($ret){
            return successMsg('合法店铺');
        }
        return errorMsg('非法店铺');
    }
    
}