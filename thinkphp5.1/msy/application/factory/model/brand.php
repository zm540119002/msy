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
	// 设置当前模型的数据库连接
//	protected $connection = 'db_factory';
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
		$result = $this->allowField(true)->save($data);
		if(false !== $result){
			return successMsg("已提交申请");
		}else{
			return errorMsg($this->getError());
		}
	}
}