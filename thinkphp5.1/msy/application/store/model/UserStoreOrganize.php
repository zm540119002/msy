<?php
namespace app\store\model;

class UserStoreOrganize extends \think\model\Pivot {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'user_store_organize';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_store';
}