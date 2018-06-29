<?php
namespace app\factory\model;
use think\Model;
use think\Db;
/**
 * 基础模型器
 */

class Brand extends Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'brand';
	// 设置主键
	protected $pk = 'id';
//	// 设置当前模型的数据库连接
	protected $connection = 'db_config_factory';

	/**
	 * 编辑 新增和修改
	 */
	public function edit($factoryId = ''){
		$data = input('post.');
		$validate = validate('Brand');
		if(!$result = $validate ->check($data)) {
			return errorMsg($validate->getError());
		}
		$data['brand_img'] = moveImgFromTemp(config('upload_dir.factory_brand'),basename($data['brand_img']));
		$data['certificate'] = moveImgFromTemp(config('upload_dir.factory_brand'),basename($data['certificate']));
		$data['authorization'] = moveImgFromTemp(config('upload_dir.factory_brand'),basename($data['authorization']));
		$data['factory_id'] = $factoryId;
		if(input('?post.brand_id')){//修改
			$data['update_time'] = time();
			$data['auth_status'] = 0;
			$result = $this->allowField(true)->save($data, ['id' => $data['brand_id']]);
		}else{
			$data['create_time'] = time();
			$result = $this->allowField(true)->save($data);
		}
		if(false !== $result){
			return successMsg("成功");
		}else{
			return errorMsg('失败');
		}
	}

	/**
	 * @param array $where
	 * @param array $field
	 * @param array $order
	 * @param array $join
	 * @param string $limit
	 * @return array|\PDOStatement|string|\think\Collection
	 * 查询多条数据
	 */
	public function getList($where=[],$field=['*'],$join=[],$order=[],$limit=''){
		$_where = array(
			'b.status' => 0,
		);
		$_join = array(
		);
		$where = array_merge($_where, $where);
		$list = $this->alias('b')
			->where($where)
			->field($field)
			->join(array_merge($_join,$join))
			->order($order)
			->limit($limit)
			->select();
		return count($list)?$list->toArray():[];
	}

	/**
	 * @param array $where
	 * @param array $field
	 * @param array $join
	 * @return array|null|\PDOStatement|string|Model
	 * 查找一条数据
	 */
	public function getInfo($where=[],$field=['*'],$join=[]){
		$_where = array(
			'b.status' => 0,
		);
		$where = array_merge($_where, $where);
		$_join = array(
		);
		$info = $this->alias('b')
			->field($field)
			->join(array_merge($_join,$join))
			->where($where)
			->find();
		return $info?$info->toArray():[];
	}

}