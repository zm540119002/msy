<?php
namespace app\factory\model;
use think\Model;
use think\Db;
/**
 * 基础模型器
 */

class Factory extends Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'factory';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_factory';

	/**编辑
	 */
	public function edit($uid=''){
		$data = input('post.');
		$data['user_id'] = $uid;
		$where['id'] = $data['factory_id'];
		$file = array(
			'business_license','auth_letter',
		);
		$oldFactoryInfo = $this -> getInfo($where,$file);
		$validate = validate('Factory');
		if(!$result = $validate->check($data)) {
			return errorMsg($validate->getError());
		}
		$data['business_license'] = moveImgFromTemp(config('upload_dir.factory_auto'),basename($data['business_license']));
		$data['auth_letter'] = moveImgFromTemp(config('upload_dir.factory_auto'),basename($data['auth_letter']));
		if(input('?post.factory_id')){
			$data['update_time'] = time();
			$result = $this->allowField(true)->save($data,['id' => $data['factory_id']]);
			if(false !== $result){
				delImgFromPaths($oldFactoryInfo['business_license'],$data['business_license']);
				delImgFromPaths($oldFactoryInfo['auth_letter'],$data['auth_letter']);
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
			$factoryUserModel =  new \app\factory\model\UserFactory;
			$data['user_id'] = $uid;
			$data['factory_id'] = $this->getAttr('id');
			$result = $factoryUserModel -> allowField(true) -> save($data);
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
	public function selectFactory($where=[],$field=['*'],$join=[],$order=[],$limit=''){
		$_where = array(
			'f.status' => 0,
		);
		$_join = array(
		);
		$where = array_merge($_where, $where);
		$list = $this->alias('f')
			->where($where)
			->field($field)
			->join(array_merge($_join,$join))
			->order($order)
			->limit($limit)
			->select();
		if(!empty($list)){
			$list = $list ->toArray();
		}
		return $list;
	}

	/**查找一条数据
	 */
	public function getInfo($where=[],$field=['*'],$join=[]){
		$_where = array(
			'f.status' => 0,
		);
		$where = array_merge($_where, $where);
		$_join = array(
		);
		$info = $this->alias('f')
			->field($field)
			->join(array_merge($_join,$join))
			->where($where)
			->find();
		if(!empty($info)){
			$info = $info ->toArray();
		}
		return $info;
	}
}