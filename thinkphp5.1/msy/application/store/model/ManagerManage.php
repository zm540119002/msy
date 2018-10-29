<?php
namespace app\store\model;

class ManagerManage extends \common\model\Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'user';
	// 设置主键
	protected $pk = 'id';
	// 别名
	protected $alias = 'u';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_common';

	//编辑
	public function storeEmployeeEdit($storeId){
		if(!intval($storeId)){
			return errorMsg('缺少店铺ID');
		}
		$postData = input('post.');
		//用户数据验证
		$validateUser = new \common\validate\User();
		if(!$validateUser->scene('employee')->check($postData)){
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
			$userId = $this->checkUserExistByMobilePhone($postData['mobile_phone']);
			$this->startTrans();//事务开启
			if(!$userId){//不存在
				unset($postData['id']);
				$postData['nickname'] = trim($postData['name']);
				$postData['create_time'] = time();
				$res = $this->isUpdate(false)->save($postData);
				if($res===false){
					$this->rollback();//事务回滚
					return errorMsg('失败',$this->getError());
				}
				$userId = $this->getAttr('id');
			}
			//检查员工是否存在
			$userStoreId = $this->_checkEmployeeExist($userId,$storeId);
			if(!$userStoreId){//不存在
				$postData['type'] = 4;
				$postData['user_id'] = $userId;
				$postData['store_id'] = $storeId;
				$modelUserStore = new \common\model\UserStore();
				$res = $modelUserStore->isUpdate(false)->save($postData);
				if($res===false){
					$this->rollback();//事务回滚
					return errorMsg('失败',$modelUserStore->getError());
				}
				$userStoreId = $modelUserStore->getAttr('id');
				if(!empty($postData['nodeIds'])){
					//新增权限节点
					$list = [];
					foreach ($postData['nodeIds'] as $value){
						$list[] = [
							'user_id'=>$userId,
							'store_id'=>$storeId,
							'node_id'=>$value,
						];
					}
					$modelUserStoreNode = new \common\model\UserStoreNode();
					$res = $modelUserStoreNode->isUpdate(false)->saveAll($list);
					if($res===false){
						$this->rollback();//事务回滚
						return errorMsg('失败',$modelUserStoreNode->getError());
					}
				}
			}
			$this->commit();//事务提交
			$postData['id'] = $userId;
			$postData['user_store_id'] = $userStoreId;

		}
		return successMsg('成功！',$postData);
	}

	//列表
	public function getList($storeId){
		$modelUserStore = new \common\model\UserStore();
		$where = [
			['uf.store_id','=',$storeId],
			['uf.status','=',0],
			['uf.type','=',2],
			['u.status','=',0],
		];
		$field = [
			'u.id','u.name','u.mobile_phone',
			'uf.id user_store_id',
		];
		$join = [
			['user u','u.id = uf.user_id','left'],
		];
		$list = $modelUserStore->alias('uf')->field($field)->join($join)->where($where)->select();
		return count($list)!=0?$list->toArray():[];
	}

	//删除
	public function del($storeId,$tag=true){
		$id = input('post.id',0);
		if(!$id){
			return errorMsg('参数错误');
		}
		$userStoreId = input('post.userStoreId',0);
		if(!$userStoreId){
			return errorMsg('参数错误');
		}
		$where = [
			['user_id', '=', $id],
			['id', '=', $userStoreId],
			['store_id', '=', $storeId],
			['status', '=', 0],
			['type', '=', 2],
		];
		$modelUserStore = new \common\model\UserStore();
		if($tag){//标记删除
			$result = $modelUserStore->where($where)->setField('status',2);
		}else{
			$result = $modelUserStore->where($where)->delete();
		}
		if(!$result){
			return errorMsg('失败',$modelUserStore->getError());
		}
		return successMsg('成功');
	}

	/**检查员工是否存在
	 */
	private function _checkEmployeeExist($userId,$storeId){
		$modelUserStore = new \common\model\UserStore();
		$where = [
			['user_id','=',$userId],
			['store_id','=',$storeId],
			['status','<>',2],
			['type','=',4],
		];
		return $modelUserStore->where($where)->value('id');
	}
}