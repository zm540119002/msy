<?php
namespace app\store\model;

class Manager extends \common\model\Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'user';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_common';

	/**检查账号
	 */
	public function checkExist($userId,$factoryId){
		$modelUserFactory = new \common\model\UserFactory();
		$where = [
			['user_id','=',$userId],
			['factory_id','=',$factoryId],
			['status','<>',2],
		];
		$res = $modelUserFactory->where($where)->count('id');
		return $res?true:false;
	}

	//编辑
	public function edit($userId,$factoryId){
		if(!intval($userId)){
			return errorMsg('缺少用户ID');
		}
		if(!intval($factoryId)){
			return errorMsg('缺少采购商ID');
		}
		$postData = input('post.');
//		//用户数据验证
//		$validateUser = new \common\validate\User();
//		if(!$validateUser->scene('edit')->check($postData)){
//			return errorMsg($validateUser->getError());
//		}
		return successMsg('成功！',$postData);
	}
}