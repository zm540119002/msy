<?php
namespace common\model;
use think\Model;
use think\Db;
/**
 * 基础模型器
 */

class Store extends Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'store';
	// 设置主键
	protected $pk = 'id';
	// 别名
	protected $alias = 's';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_common';

	/**编辑
	 */
	public function edit($factoryId=''){
		$data = input('post.');
		$data['factory_id'] = $factoryId;
		$validate = validate('\common\validate\Store');
		if(!$result = $validate->check($data)) {
			return errorMsg($validate->getError());
		}
		if(input('?post.id')){
			$data['update_time'] = time();
			$result = $this->allowField(true)->save($data,['id' => $data['store_id'],'factory_id'=>$factoryId]);
			if(false !== $result){
				return successMsg("成功");
			}
			return errorMsg('失败',$this->getError());
		}else{
			$data['create_time'] = time();
			$result = $this->allowField(true)->save($data);
			if(!$result){
				return errorMsg('失败');
			}
			return successMsg('提交申请成功');
		}
	}
	
	//设置默认店铺
	public function setDefaultStore($factoryId=''){
		if(request()->isAjax()){
			$id = (int)input('post.id');
			if(!$id){
				return errorMsg('参数错误');
			}
			$this->startTrans();
			$data = array('is_default' => 1);
			$result = $this->allowField(true)->save($data,['id' => $id,'factory_id'=>$factoryId]);
			if(false === $result){
				$this->rollback();
				return errorMsg('修改默认失败');
			}
			$where = [
				['id','<>',$id],
				['factory_id','=',$factoryId],
			];
			$result = $this ->where($where)->setField('is_default',0);
			if(false === $result){
				$this->rollback();
				return errorMsg('修改失败');
			}
			$this->commit();
			return successMsg("已选择");
		}
	}

	/**检查店铺是否属于此厂商
	 */
	public function checkStoreExist($id,$factoryId){
		$where = [
			['id','=',$id],
			['factory_id','=',$factoryId],
		];
		$count = $this->where($where)->count();
		if($count){
			return true;
		}else{
			return false;
		}
	}

	//设置店长
	public function setManager($factoryId){
		$postData = input('post.');
		$storeId = (int)$postData['id'];
		if(!$storeId){
			return errorMsg('缺少店铺ID');
		}
		if($postData['mobile_phone']){//手机号存在
			if(!isMobile($postData['mobile_phone'])){
				return errorMsg('请输入正确的手机号码');
			}
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
			//验证用户是否为店长
			$userStoreId = $this->checkManager($userId,$factoryId,$storeId);
			if(!$userStoreId){//不是管理员
				$postData['type'] = 3;
				$postData['user_id'] = $userId;
				$postData['factory_id'] = $factoryId;
				$postData['store_id'] = $storeId;
				$modelUserStore = new \common\model\UserStore();
				$res = $modelUserStore->save($postData);
				if($res===false){
					$this->rollback();//事务回滚
					return errorMsg('失败',$this->getError());
				}
				$userStoreId = $modelUserStore->getAttr('id');
			}
			$this->commit();//事务提交
			$postData['id'] = $userId;
			$postData['user_store_id'] = $userStoreId;
			return successMsg('成功！',$postData);
		}else{//手机号不存在
		}
	}

	/**检查账号
	 */
	public function checkManager($userId,$factoryId,$storeId){
		$modelUserStore = new \common\model\UserStore();
		$where = [
			['user_id','=',$userId],
			['storeId','=',$storeId],
			['factory_id','=',$factoryId],
			['status','<>',2],
			['type','=',3],
		];
		return $modelUserStore->where($where)->value('id');
	}
}