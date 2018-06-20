<?php
namespace app\store\model;

class UserStore extends \think\model\Pivot {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'user_store';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_store';

	//设置默认厂商
	public function setDefaultStore($userId=0){
		if(request()->isAjax()){
			$this->startTrans();//开启事务
			$where = [
				['status','=',0],
				['user_id','=',$userId],
			];
			$result = $this->where($where)->setField('is_default',0);
			if(false === $result){
				$this->rollback();//回滚事务
				return errorMsg('失败');
			}
			$storeId = input('post.storeId');
			if(intval($storeId)){
				$where[] = ['store_id','=',$storeId];
			}
			$result = $this->where($where)->setField('is_default',1);
			if(false === $result){
				$this->rollback();//回滚事务
				return errorMsg('失败');
			}
			$this->commit();//提交事务
			return successMsg("成功");
		}
	}

	//设置用户机构状态
	public function setStatus($storeId){
		if(!intval($storeId)){
			return errorMsg('参数错误');
		}
		$postData = input('post.');
		if(!intval($postData['userId'])){
			return errorMsg('参数错误');
		}
		$where = [
			['user_id', '=', $postData['userId']],
			['store_id', '=', $storeId],
			['status', '<>', 2],
		];
		$this->startTrans();//开启事务
		$res = $this->where($where)->setField('status',$postData['status']);
		if(false === $res){
			$this->rollback();//回滚事务
			return errorMsg('失败');
		}
		//设置用户机构角色状态
		$roleIds = array_unique($postData['roleIds']);
		if(is_array($roleIds) && !empty($roleIds)){
			$modelUserStoreRole = new \app\store\model\UserStoreRole();
			$where = [
				['user_id', '=', $postData['userId']],
				['store_id', '=', $storeId],
				['status', '<>', 2],
				['role_id', 'in', $roleIds],
			];
			$res = $modelUserStoreRole->where($where)->setField('status',$postData['status']);
			if(false === $res){
				$this->rollback();//回滚事务
				return errorMsg('失败');
			}
		}
		$this->commit();//提交事务
		return successMsg('成功');
	}
}