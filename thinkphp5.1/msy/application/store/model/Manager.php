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
	public function checkUserFactory($userId,$factoryId){
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
	public function edit($factoryId){
		if(!intval($factoryId)){
			return errorMsg('缺少采购商ID');
		}
		$postData = input('post.');
		$modelUserFactory = new \common\model\UserFactory();
		if($postData['id'] && intval($postData['id'])){//修改
			//用户数据验证
			$validateUser = new \common\validate\User();
			if(!$validateUser->scene('edit')->check($postData)){
				return errorMsg($validateUser->getError());
			}
			$postData['update_time'] = time();
			$res = $this->isUpdate(true)->save($postData);
			if($res===false){
				return errorMsg('失败',$this->getError());
			}
		}else{//新增
			//验证用户是否存在
			$userId = $this->checkUserByMobilePhone($postData['mobile_phone']);
			$this->startTrans();//事务开启
			if(!$userId){
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
			$postData['type'] = 2;
			$postData['user_id'] = $userId;
			$postData['factory_id'] = $factoryId;
			$res = $modelUserFactory->save($postData);
			if($res===false){
				$this->rollback();//事务回滚
				return errorMsg('失败',$this->getError());
			}
			$this->commit();//事务提交
			$postData['id'] = $userId;
			$postData['user_factory_id'] = $modelUserFactory->getAttr('id');
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
	public function del($tag=true){
		$where = [
			['status', '=', 0],
		];
		$id = input('post.id',0);
		if(!$id){
			return errorMsg('参数错误');
		}
		$where[] = ['id', '=', $id];
		if($tag){//标记删除
			$result = $this->where($where)->setField('status',2);
		}else{
			$result = $this->where($where)->delete();
		}
		if(!$result){
			return errorMsg('失败',$this->getError());
		}
		return successMsg('成功');
	}
}