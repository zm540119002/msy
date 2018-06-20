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
	public function checkExist($userId,$storeId){
		$modelUserStore = new \app\store\model\UserStore();
		$where = [
			['user_id','=',$userId],
			['store_id','=',$storeId],
			['status','<>',2],
		];
		$res = $modelUserStore->where($where)->count('id');
		return $res?true:false;
	}

	//编辑
	public function edit($storeId){
		if(!intval($storeId)){
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
		$postData['store_id'] = $storeId;
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
		if($this->checkExist($postData['id'],$storeId)){
			return errorMsg('该手机号码已经是本供应商的员工！');
		}
		$user = $this->get($postData['id']);
		if(!empty($user)){
			//新增用户工厂
			$data = [
				'type' => 2,
			];
			$res = $user->factories()->attach($storeId,$data);
			if(!count($res)){
				$this->rollback();//回滚事务
				return errorMsg('新增失败');
			}
			//新增用户工厂角色
			if(is_array($postData['userStoreRoleIds']) && !empty($postData['userStoreRoleIds'])){
				foreach ($postData['userStoreRoleIds'] as $val){
					$data = [
						'store_id' => $storeId,
					];
					$res = $user->roles()->attach($val,$data);
					if(!count($res)){
						$this->rollback();//回滚事务
						return errorMsg('新增失败');
					}
				}
			}
			//新增用户工厂组织
			if(isset($postData['userStoreOrganizeId']) && intval($postData['userStoreOrganizeId'])){
				$data = [
					'store_id' => $storeId,
				];
				$res = $user->organizes()->attach($postData['userStoreOrganizeId'],$data);
				if(!count($res)){
					$this->rollback();//回滚事务
					return errorMsg('新增失败');
				}
			}
		}
		$postData['userStoreStatus'] = 0;
		$modeRole = new \app\store\model\Role();
		$where = [
			['status', '=', 0],
			['store_id', '=', $storeId],
			['id', 'in', $postData['userStoreRoleIds']],
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
		return $this->belongsToMany('Store','UserStore','store_id','user_id');
	}

	//用户-工厂角色-关联模型
	public function roles(){
		return $this->belongsToMany('Role','UserStoreRole','role_id','user_id');
	}

	//用户-工厂-组织-关联模型
	public function organizes(){
		return $this->belongsToMany('Organize','UserStoreOrganize','organize_id','user_id');
	}

	//详情
	public function detail($storeId){
		if(!intval($storeId)){
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
		$modelUserStore = new \app\store\model\UserStore();
		$where = [
			['status', '<>', 2],
			['user_id', '=', $id],
			['store_id', '=', $storeId],
		];
		$field = [
			'status',
		];
		$userStore = $modelUserStore->field($field)->where($where)->find();
		$userStore = $userStore->toArray();
		$info['status'] = $userStore['status'];
		//用户工厂角色
		$modelUserStoreRole = new \app\store\model\UserStoreRole();
		$info['role'] = $modelUserStoreRole->getRole($info['id'],$storeId);
		return $info?$info->toArray():[];
	}

	//获取列表
	public function getList($storeId){
		$modelUserStore = new \app\store\model\UserStore();
		$where = [
			['u.status','<>',2],
			['uf.status','<>',2],
			['uf.store_id','=',$storeId],
			['uf.type','=',2],
		];
		$keyword = input('get.keyword','');
		if($keyword){
			$where[] = ['u.name|u.mobile_phone', 'like', '%'.trim($keyword).'%',];
		}
		$field = [
			'u.id','u.name','u.nickname','u.mobile_phone','uf.status userStoreStatus','uf.is_default',
		];
		$join = [
			['common.user u','u.id = uf.user_id','LEFT'],
		];
		$list = $modelUserStore->alias('uf')->where($where)->field($field)->join($join)->select();
		$modelUserStoreRole = new \app\store\model\UserStoreRole();
		foreach ($list as &$value){
			$value['role'] = $modelUserStoreRole->getRole($value['id'],$storeId);
		}
		return count($list)?$list:[];
	}

	//用户角色编辑
	public function editRole($storeId){
		$userId = input('post.userId');
		if(!intval($userId) || !intval($storeId)){
			return errorMsg('参数错误');
		}
		$newRoleIds = input('post.roleIds/a');
		if(empty($newRoleIds)){
			return errorMsg('请选择角色');
		}
		$modelUserStoreRole = new \app\store\model\UserStoreRole();
		$userStoreRole = $modelUserStoreRole->getRole($userId,$storeId);
		$oldRoleIds = array_column($userStoreRole,'id');
		$modelUserStoreRole->startTrans();//开启事务
		//新增角色
		$addRoleIds = array_diff($newRoleIds,$oldRoleIds);
		if(!empty($addRoleIds)){
			$data = [];
			foreach ($addRoleIds as $value){
				$data[] = [
					'store_id' => $storeId,
					'user_id' => $userId,
					'role_id' => $value,
				];
			}
			$res = $modelUserStoreRole->saveAll($data);
			if(false===$res){
				$modelUserStoreRole->rollback();//回滚事务
				return errorMsg('失败');
			}
		}
		//删除角色
		$delRoleIds = array_diff($oldRoleIds,$newRoleIds);
		if(!empty($delRoleIds)){
			$where = [
				['user_id','=',$userId],
				['store_id','=',$storeId],
			];
			$where[] = ['role_id','in',$delRoleIds];
			$res = $modelUserStoreRole->where($where)->delete();
			if(!$res){
				$modelUserStoreRole->rollback();//回滚事务
				return errorMsg('失败',$this->getError());
			}
		}
		$modelUserStoreRole->commit();//提交事务
		return successMsg('成功');
	}
}