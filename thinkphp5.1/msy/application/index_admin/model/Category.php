<?php
namespace app\index_admin\model;
use think\Model;
use think\Db;
/**
 * 基础模型器
 */

class Category extends Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'category';
//	// 设置当前模型的数据库连接
	protected $connection = 'db_config_msy';
	/**
	 * 新增
	 */
	public function add(){
	}

	/**
	 * @return array|mixed|\PDOStatement|string|\think\Collection
	 * 获取第一级分类
	 */
	public function selectFirstCategory(){
		$list = $this->where(['status'=>0,'parent_id_1'=>0])->field('id,name,img,level')->select();
		return count($list)?$list->toArray():[];
	}

	/**
	 * @param $id
	 * @return array|\PDOStatement|string|\think\Collection
	 * 根据第一级分类id查二级分类
	 */
	public function getSecondCategoryById($id){
		$list = $this->where(['status'=>0,'parent_id_1'=>$id])->field('id,name,img,level')->select();
		return count($list)?$list->toArray():[];

	}
}