<?php
namespace app\store\model;

class UserShopNode extends \common\model\Base{
	// 设置当前模型对应的完整数据表名称
	protected $table = 'user_shop_node';
	// 设置主键
	protected $pk = 'id';
	// 别名
	protected $alias = 'usn';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_common';
}