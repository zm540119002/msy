<?php
namespace app\factory\model;
use think\Model;
use think\Db;
/**
 * 基础模型器
 */

class Goods extends Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'goods';
	// 设置当前模型的数据库连接
//	protected $connection = 'db_factory';
	/**
	 * 新增
	 */
	public function add(){
		$validate = validate('Goods');
		$data = input('post.');
		if(!$result = $validate->scene('add')->check($data)) {
			return errorMsg($validate->getError());
		}
		$data['thumb_img'] = moveImgFromTemp(config('upload_dir.factory_goods'),basename($data['thumb_img']));
		$data['main_img']  = moveImgFromTemp(config('upload_dir.factory_goods'),basename($data['main_img']));
		$tempArr = array();
		foreach ($data['details_img'] as $item) {
			if($item){
				$tempArr[] = moveImgFromTemp(config('upload_dir.factory_goods'),basename($item));
			}
		}
		$data['details_img'] = implode(',',$tempArr);
		$data['create_time'] = time();
		$result = $this -> allowField(true) -> save($data);
		if(false !== $result){
			return successMsg("添加成功！");
		}else{
			return errorMsg($this->getError());
		}
	}
}