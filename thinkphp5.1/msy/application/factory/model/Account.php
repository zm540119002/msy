<?php
namespace app\factory\model;

class Account extends \think\Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'user';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_common';

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
		if($postData['id'] && intval($postData['id'])){//修改用户
			$res = $this->isUpdate(true)->save($postData);
			if($res===false){
				$this->rollback();//回滚事务
				return errorMsg('更新失败',$this->getError());
			}
		}else{//新增用户
			$postData['factory_id'] = $factoryId;
			unset($postData['id']);
			$res = $this->isUpdate(false)->save($postData);
			if($res===false){
				$this->rollback();//回滚事务
				return errorMsg('新增失败',$this->getError());
			}
			$postData['id'] = $this->getAttr('id');
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
		}
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
		$modelUserFactory = new \app\factory\model\UserFactory();
		$where = [
			['status', '<>', 2],
			['user_id', '=', $id],
			['factory_id', '=', $factoryId],
		];
		$field = [
			'status',
		];
		$userFactory = $modelUserFactory->field($field)->where($where)->find();
		$userFactory = $userFactory->toArray();
		$info['status'] = $userFactory['status'];
		//用户工厂角色
		$modelUserFactoryRole = new \app\factory\model\UserFactoryRole();
		$info['role'] = $modelUserFactoryRole->getRole($info['id'],$factoryId);
		return $info?$info->toArray():[];
	}

	//获取列表
	public function getList($factoryId){
		$modelUserFactory = new \app\factory\model\UserFactory();
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
			'u.id','u.name','u.nickname','u.mobile_phone','uf.status','uf.is_default',
		];
		$join = [
			['common.user u','u.id = uf.user_id','LEFT'],
		];
		$list = $modelUserFactory->alias('uf')->where($where)->field($field)->join($join)->select();
		$modelUserFactoryRole = new \app\factory\model\UserFactoryRole();
		foreach ($list as &$value){
			$value['role'] = $modelUserFactoryRole->getRole($value['id'],$factoryId);
		}
		return count($list)?$list:[];
	}

	//用户角色编辑
	public function editRole($factoryId){
		$userId = input('post.userId');
		if(!intval($userId) || !intval($factoryId)){
			return errorMsg('参数错误');
		}
		$newRoleIds = input('post.ids/a');
		if(empty($newRoleIds)){
			return errorMsg('请选择角色');
		}
		$modelUserFactoryRole = new \app\factory\model\UserFactoryRole();
		$where = [
			['status','=',0],
			['user_id','=',$userId],
			['factory_id','=',$factoryId],
		];
		$userFactoryRole = $modelUserFactoryRole->getRole($userId,$factoryId);
		$oldRoleIds = array_column($userFactoryRole,'role_id');
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