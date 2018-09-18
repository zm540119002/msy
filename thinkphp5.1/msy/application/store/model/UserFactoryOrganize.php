<?php
namespace app\store\model;

class UserFactoryOrganize extends \think\model\Pivot {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'user_factory_organize';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_factory';
}