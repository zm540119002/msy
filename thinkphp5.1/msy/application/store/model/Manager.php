<?php
namespace app\store\model;

class Manager extends \common\model\Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'user';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_common';

	/**检查管理员账号
	 */
	public function checkManager($userId,$factoryId){
		$modelUserFactory = new \common\model\UserFactory();
		$where = [
			['user_id','=',$userId],
			['factory_id','=',$factoryId],
			['status','<>',2],
			['type','=',2],
		];
		return $modelUserFactory->where($where)->value('id');
	}

	//编辑
	public function edit($factoryId){
		if(!intval($factoryId)){
			return errorMsg('缺少采购商ID');
		}
		$postData = input('post.');
		$modelUserFactory = new \common\model\UserFactory();
		//用户数据验证
		$validateUser = new \common\validate\User();
		if(!$validateUser->scene('manager')->check($postData)){
			return errorMsg($validateUser->getError());
		}
		if($postData['id'] && intval($postData['id'])){//修改
			$postData['update_time'] = time();
			$res = $this->isUpdate(true)->save($postData);
			if($res===false){
				return errorMsg('失败',$this->getError());
			}
		}else{//新增
			//验证用户是否存在
			$userId = $this->checkUserByMobilePhone($postData['mobile_phone']);
			$this->startTrans();//事务开启
			if(!$userId){//不存在
				unset($postData['id']);
				$postData['type'] = 0;
				$postData['nickname'] = trim($postData['name']);
				$postData['create_time'] = time();
				$res = $this->save($postData);
				if($res===false){
					$this->rollback();//事务回滚
					return errorMsg('失败',$this->getError());
				}
				$userId = $this->getAttr('id');
			}
			//验证用户是否为管理员
			$userFactoryId = $this->checkManager($userId,$factoryId);
			if(!$userFactoryId){//不是管理员
				$postData['type'] = 2;
				$postData['user_id'] = $userId;
				$postData['factory_id'] = $factoryId;
				$res = $modelUserFactory->save($postData);
				if($res===false){
					$this->rollback();//事务回滚
					return errorMsg('失败',$this->getError());
				}
				$userFactoryId = $modelUserFactory->getAttr('id');
			}
			$this->commit();//事务提交
			$postData['id'] = $userId;
			$postData['user_factory_id'] = $userFactoryId;
		}
		return successMsg('成功！',$postData);
	}

	//列表
	public function getList($factoryId){
		$modelUserFactory = new \common\model\UserFactory();
		$where = [
			['uf.factory_id','=',$factoryId],
			['uf.status','=',0],
			['uf.type','=',2],
			['u.status','=',0],
		];
		$field = [
			'u.id','u.name','u.mobile_phone',
			'uf.id user_factory_id',
		];
		$join = [
			['user u','u.id = uf.user_id','left'],
		];
		$list = $modelUserFactory->alias('uf')->field($field)->join($join)->where($where)->select();
		return count($list)!=0?$list:[];
	}

	//删除
	public function del($factoryId,$tag=true){
		$id = input('post.id',0);
		if(!$id){
			return errorMsg('参数错误');
		}
		$userFactoryId = input('post.userFactoryId',0);
		if(!$userFactoryId){
			return errorMsg('参数错误');
		}
		$where = [
			['user_id', '=', $id],
			['id', '=', $userFactoryId],
			['factory_id', '=', $factoryId],
			['status', '=', 0],
			['type', '=', 2],
		];
		$modelUserFactory = new \common\model\UserFactory();
		if($tag){//标记删除
			$result = $modelUserFactory->where($where)->setField('status',2);
		}else{
			$result = $modelUserFactory->where($where)->delete();
		}
		if(!$result){
			return errorMsg('失败',$modelUserFactory->getError());
		}
		return successMsg('成功');
	}
}