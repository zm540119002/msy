<?php
namespace app\store\model;

class Shop extends \common\model\Base{
	// 设置当前模型对应的完整数据表名称
	protected $table = 'shop';
	// 设置主键
	protected $pk = 'id';
	// 别名
	protected $alias = 's';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_store';

	/**编辑
	 */
	public function edit($factoryId=''){
		$data = input('post.');
		$data['factory_id'] = $factoryId;
		//数据验证
		$validateShop = new \common\validate\Shop();
		if(!$validateShop->scene('edit')->check($data)){
			return errorMsg($validateShop->getError());
		}
		if(input('?post.id')){
			$data['update_time'] = time();
			$result = $this->allowField(true)->save($data,['id' => $data['shop_id'],'factory_id'=>$factoryId]);
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

	/**检查店铺是否属于此厂商
	 */
	public function checkShopExist($id,$factoryId){
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
		$shopId = (int)$postData['id'];
		if(!$shopId){
			return errorMsg('缺少店铺ID');
		}
		if($postData['mobile_phone']){//手机号存在
			//验证用户是否存在
			$userId = $this->checkUserByMobilePhone($postData['mobile_phone']);
			$this->startTrans();//事务开启
			if(!$userId){//不存在
				unset($postData['id']);
				$postData['type'] = 0;
				$postData['nickname'] = trim($postData['name']);
				$postData['create_time'] = time();
				$modelUser = new \common\model\User();
				$res = $modelUser->save($postData);
				if($res===false){
					$modelUser->rollback();//事务回滚
					return errorMsg('失败',$modelUser->getError());
				}
				$userId = $modelUser->getAttr('id');
			}
			//验证是否存在店长
			$userShopId = $this->checkManagerExist($factoryId,$shopId);
			$modelUserShop = new \common\model\UserShop();
			if($userShopId){//存在测删除
				$modelUserShop->delById($userShopId,false);
			}
			$postData['type'] = 3;
			$postData['user_id'] = $userId;
			$postData['factory_id'] = $factoryId;
			$postData['shop_id'] = $shopId;
			$res = $modelUserShop->save($postData);
			if($res===false){
				$this->rollback();//事务回滚
				return errorMsg('失败',$this->getError());
			}
			$userShopId = $modelUserShop->getAttr('id');
			$this->commit();//事务提交

			$postData['id'] = $userId;
			$postData['user_shop_id'] = $userShopId;
			return successMsg('成功！',$postData);
		}else{//手机号不存在
			//验证是否存在店长
			$userShopId = $this->checkManagerExist($factoryId,$shopId);
			$modelUserShop = new \common\model\UserShop();
			if($userShopId){//存在测删除
				$modelUserShop->delById($userShopId,false);
			}
			return successMsg('删除成功！');
		}
	}

	/**验证用户是否为店长
	 */
	private function checkManagerExist($factoryId,$shopId){
		$modelUserShop = new \common\model\UserShop();
		$where = [
			['shop_id','=',$shopId],
			['factory_id','=',$factoryId],
			['status','<>',2],
			['type','=',3],
		];
		return $modelUserShop->where($where)->value('id');
	}
}