<?php
namespace common\model;

use think\Model;
use think\Db;

class User extends Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'user';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_common';

	/**登录
	 */
	public function login(){
		$data = input('post.');
		$validate = new \common\validate\User;
		if(!$result = $validate->scene('login')->check($data)) {
			return errorMsg($validate->getError());
		}
	}
}