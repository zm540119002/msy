<?php
namespace app\store\model;

class Account extends \think\Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'user';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_common';

	/**检查账号
	 */
	public function checkExist($userId,$factoryId){
		$modelUserFactory = new \app\store\model\UserFactory();
		$where = [
			['user_id','=',$userId],
			['factory_id','=',$factoryId],
			['status','<>',2],
		];
		$res = $modelUserFactory->where($where)->count('id');
		return $res?true:false;
	}

	//编辑
	public function edit($factoryId){
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

	//用户-工厂-关联模型
	public function factories(){
		return $this->belongsToMany('Factory','UserFactory','factory_id','user_id');
	}

	//用户-工厂角色-关联模型
	public function roles(){
		return $this->belongsToMany('Role','UserFactoryRole','role_id','user_id');
	}

	//用户-工厂-组织-关联模型
	public function organizes(){
		return $this->belongsToMany('Organize','UserFactoryOrganize','organize_id','user_id');
	}

	//详情
	public function detail($factoryId){
		if(!intval($factoryId)){
			return errorMsg('参数错误');
		}
		//用户信息
		$where = [
			['status', '<>', 2],
		];
		$id = input('id');
		if(!intval($id)){
			return errorMsg('参数错误');
		}
		$where[] = ['id','=',$id];
		$field = [
			'u.id','u.name','u.nickname','u.mobile_phone',
		];
		$info = $this->alias('u')->field($field)->where($where)->find();
		//用户工厂关系表
		$modelUserFactory = new \app\store\model\UserFactory();
		$where = [
			['status', '<>', 2],
			['user_id', '=', $id],
			['factory_id', '=', $factoryId],
		];
		$field = [
			'status',
		];
		$userFactory = $modelUserFactory->field($field)->where($where)->find();
		$userFactory = count($userFactory)?$userFactory->toArray():[];
		if(count($userFactory)){
			$info['status'] = $userFactory['status'];
		}
		//用户工厂角色
		$modelUserFactoryRole = new \app\store\model\UserFactoryRole();
		$info['role'] = $modelUserFactoryRole->getRole($info['id'],$factoryId);
		return $info?$info->toArray():[];
	}

	//获取列表
	public function getList($factoryId){
		$modelUserFactory = new \app\store\model\UserFactory();
		$where = [
			['u.status','<>',2],
			['uf.status','<>',2],
			['uf.factory_id','=',$factoryId],
			['uf.type','=',2],
		];
		$keyword = input('get.keyword','');
		if($keyword){
			$where[] = ['u.name|u.mobile_phone', 'like', '%'.trim($keyword).'%',];
		}
		$field = [
			'u.id','u.name','u.nickname','u.mobile_phone','uf.status userFactoryStatus','uf.is_default',
		];
		$join = [
			['common.user u','u.id = uf.user_id','LEFT'],
		];
		$list = $modelUserFactory->alias('uf')->where($where)->field($field)->join($join)->select();
		$modelUserFactoryRole = new \app\store\model\UserFactoryRole();
		foreach ($list as &$value){
			$value['role'] = $modelUserFactoryRole->getRole($value['id'],$factoryId);
		}
		return count($list)?$list:[];
	}

	//用户工厂角色编辑
	public function editRole($factoryId){
		$userId = input('post.userId');
		if(!intval($userId) || !intval($factoryId)){
			return errorMsg('参数错误');
		}
		$newRoleIds = input('post.roleIds/a');
		if(empty($newRoleIds)){
			return errorMsg('请选择角色');
		}
		$modelUserFactoryRole = new \app\store\model\UserFactoryRole();
		$userFactoryRole = $modelUserFactoryRole->getRole($userId,$factoryId);
		$oldRoleIds = array_unique(array_column($userFactoryRole,'id'));
		$modelUserFactoryRole->startTrans();//开启事务
		//新增角色
		$addRoleIds = array_diff($newRoleIds,$oldRoleIds);

		if(!empty($addRoleIds)){
			$data = [];
			foreach ($addRoleIds as $value){
				$data[] = [
					'factory_id' => $factoryId,
					'user_id' => $userId,
					'role_id' => $value,
				];
			}
			$res = $modelUserFactoryRole->saveAll($data);
			if(false===$res){
				$modelUserFactoryRole->rollback();//回滚事务
				return errorMsg('失败');
			}
		}
		//删除角色
		$delRoleIds = array_diff($oldRoleIds,$newRoleIds);
		if(!empty($delRoleIds)){
			$where = [
				['user_id','=',$userId],
				['factory_id','=',$factoryId],
			];
			$where[] = ['role_id','in',$delRoleIds];
			$res = $modelUserFactoryRole->where($where)->delete();
			if(!$res){
				$modelUserFactoryRole->rollback();//回滚事务
				return errorMsg('失败',$this->getError());
			}
		}
		$modelUserFactoryRole->commit();//提交事务
		return successMsg('成功');
	}
}