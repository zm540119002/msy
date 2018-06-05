<?php
namespace app\factory\model;

class UserFactory extends \think\model\Pivot {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'user_factory';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_factory';

	//设置默认厂商
	public function setDefaultFactory($userId=0){
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
			$id = (int)input('post.id');
			if($id){
				$where[] = ['id','=',$id];
			}
			$factoryId = input('post.factoryId');
			if(intval($factoryId)){
				$where[] = ['factory_id','=',$factoryId];
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

	/**查找一条数据
	 */
	public function getInfo($where=[],$field=['*'],$join=[]){
		$_where = [
			['uf.status', '=', 0],
		];
		$_join = array(
		);
		$info = $this->alias('uf')
			->field($field)
			->join(array_merge($_join,$join))
			->where(array_merge($_where, $where))
			->find();
		return $info?$info->toArray():[];
	}

	//删除
	public function setStatus($factoryId){
		if(!intval($factoryId)){
			return errorMsg('参数错误');
		}
		$postData = input('post.');
		if(!intval($postData['userId'])){
			return errorMsg('参数错误');
		}
		$where = [
			['user_id', '=', $postData['userId']],
			['factory_id', '=', $factoryId],
		];
		$res = $this->where($where)->setField('status',$postData['status']);
		if(!$res){
			return errorMsg('失败',$this->getError());
		}
		return successMsg('成功');
	}
}