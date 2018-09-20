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
		if(!intval($factoryId)){
			return errorMsg('缺少供应商ID');
		}
		$postData = input('post.');
		//用户数据验证
		$validateUser = new \common\validate\User();
		if(!$validateUser->scene('edit')->check($postData)){
			return errorMsg($validateUser->getError());
		}
		//开启事务
		$this->startTrans();
		//新增用户
		unset($postData['id']);
		$postData['factory_id'] = $factoryId;
		//检查手机号码是否在平台注册
		$modelUserCenter = new \common\model\UserCenter();
		$result = $modelUserCenter->registerCheck($postData['mobile_phone']);
		if($result){
			$postData['id'] = $result['id'];
		}else{
			$res = $this->isUpdate(false)->save($postData);
			if($res===false){
				$this->rollback();//回滚事务
				return errorMsg('新增失败',$this->getError());
			}
			$postData['id'] = $this->getAttr('id');
		}
		//检查用户是否是本供应商的员工
		if($this->checkExist($postData['id'],$factoryId)){
			return errorMsg('该手机号码已经是本供应商的员工！');
		}
		$user = $this->get($postData['id']);
		if(!empty($user)){
			//新增用户工厂
			$data = [
				'type' => 2,
			];
			$res = $user->factories()->attach($factoryId,$data);
			if(!count($res)){
				$this->rollback();//回滚事务
				return errorMsg('新增失败');
			}
			//新增用户工厂角色
			if(is_array($postData['userFactoryRoleIds']) && !empty($postData['userFactoryRoleIds'])){
				foreach ($postData['userFactoryRoleIds'] as $val){
					$data = [
						'factory_id' => $factoryId,
					];
					$res = $user->roles()->attach($val,$data);
					if(!count($res)){
						$this->rollback();//回滚事务
						return errorMsg('新增失败');
					}
				}
			}
			//新增用户工厂组织
			if(isset($postData['userFactoryOrganizeId']) && intval($postData['userFactoryOrganizeId'])){
				$data = [
					'factory_id' => $factoryId,
				];
				$res = $user->organizes()->attach($postData['userFactoryOrganizeId'],$data);
				if(!count($res)){
					$this->rollback();//回滚事务
					return errorMsg('新增失败');
				}
			}
		}
		$postData['userFactoryStatus'] = 0;
		$modeRole = new \app\store\model\Role();
		$where = [
			['status', '=', 0],
			['factory_id', '=', $factoryId],
			['id', 'in', $postData['userFactoryRoleIds']],
		];
		$field = array(
			'id','name',
		);
		$roleList = $modeRole->where($where)->field($field)->select();
		$postData['role'] = count($roleList)?$roleList->toArray():[];
		$this->commit();//提交事务
		return successMsg('成功！',$postData);
	}
}