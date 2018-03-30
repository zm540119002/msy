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
		$data = input('post.');
		$validate = validate('Brand');
		if(!$result = $validate->scene('add')->check($data)) {
			return errorMsg($validate->getError());
		}
		$data['brand_img'] = moveImgFromTemp(config('upload_dir.factory_brand'),basename($data['brand_img']));
		$data['certificate'] = moveImgFromTemp(config('upload_dir.factory_brand'),basename($data['certificate']));
		$data['authorization'] = moveImgFromTemp(config('upload_dir.factory_brand'),basename($data['authorization']));
		$data['create_time'] = time();
		$result = $this->allowField(true)->save($data);
		if(false !== $result){
			return successMsg("已提交申请");
		}else{
			return errorMsg($this->getError());
		}
	}

	/**
	 * @return array|mixed|\PDOStatement|string|\think\Collection
	 * 获取第一级分类
	 */
	public function selectFirstCategory(){
		return $this->where(['status'=>0,'parent_id_1'=>0])->field('id,name,img,level')->select();
	}

	/**
	 * @param $id
	 * @return array|\PDOStatement|string|\think\Collection
	 * 根据第一级分类id查二级分类
	 */
	public function getSecondCategoryById($id){
		return $this->where(['status'=>0,'parent_id_1'=>$id])->field('id,name,img,level')->select();
	}
}