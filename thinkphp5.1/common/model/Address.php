<?php
namespace common\model;
use think\Model;
use think\Db;
use think\Route;

/**
 * 基础模型器
 */

class Address extends Base{
	// 设置当前模型对应的完整数据表名称
	protected $table = 'address';
	// 设置主键
	protected $pk = 'id';
	// 别名
	protected $alias = 'a';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_common';

    // 定义全局的查询条件 测试中，看需不需要使用 2019-04-28
    protected function base($query)
    {
        $user = session('user');
        $query->where('status','=',0);
        $query->where('user_id',$user['id']);
    }
}