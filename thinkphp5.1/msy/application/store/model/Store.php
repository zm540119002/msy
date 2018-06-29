<?php
namespace app\store\model;
use think\Model;
use think\Db;
/**
 * 基础模型器
 */

class Store extends Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'store';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_store';

	/**编辑
	 */
	public function edit($uid=''){
		$data = input('post.');
		$data['user_id'] = $uid;
		$where['id'] = $data['store_id'];
		$file = array(
			'business_license','auth_letter',
		);
		$oldstoreInfo = $this -> getInfo($where,$file);
		$validate = validate('store');
		if(!$result = $validate->check($data)) {
			return errorMsg($validate->getError());
		}
		$data['business_license'] = moveImgFromTemp(config('upload_dir.store_auto'),basename($data['business_license']));
		$data['auth_letter'] = moveImgFromTemp(config('upload_dir.store_auto'),basename($data['auth_letter']));
		if(input('?post.store_id')){
			$data['update_time'] = time();
			$result = $this->allowField(true)->save($data,['id' => $data['store_id']]);
			if(false !== $result){
				delImgFromPaths($oldstoreInfo['business_license'],$data['business_license']);
				delImgFromPaths($oldstoreInfo['auth_letter'],$data['auth_letter']);
				return successMsg("成功");
			}else{
				return errorMsg('失败');
			}
		}else{
			$data['create_time'] = time();
			$this -> startTrans();
			$result = $this->allowField(true)->save($data);
			if(!$result){
				$this ->rollback();
				return errorMsg('失败');
			}
			$storeUserModel =  new \app\store\model\UserStore;
			$data['user_id'] = $uid;
			$data['store_id'] = $this->getAttr('id');
			$result = $storeUserModel -> allowField(true) -> save($data);
			if(!$result){
				$this ->rollback();
				return errorMsg('失败');
			}
			$this ->commit();
			return successMsg('提交申请成功');
		}
	}

	/**查询多条数据
	 */
	public function getList($where=[],$field=['*'],$join=[],$order=[],$limit=''){
		$_where = array(
			's.status' => 0,
		);
		$_join = array(
		);
		$where = array_merge($_where, $where);
		$list = $this->alias('s')
			->where($where)
			->field($field)
			->join(array_merge($_join,$join))
			->order($order)
			->limit($limit)
			->select();
		return count($list)?$list->toArray():[];
	}

	/**查找一条数据
	 */
	public function getInfo($where=[],$field=['*'],$join=[]){
		$_where = array(
			's.status' => 0,
		);
		$where = array_merge($_where, $where);
		$_join = array(
		);
		$info = $this->alias('s')
			->field($field)
			->join(array_merge($_join,$join))
			->where($where)
			->find();
		return $info?$info->toArray():[];
	}
}