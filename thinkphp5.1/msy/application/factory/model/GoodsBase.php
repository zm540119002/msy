<?php
namespace app\factory\model;
use GuzzleHttp\Psr7\Request;
use think\Model;
use think\Db;
/**
 * 基础模型器
 */

class GoodsBase extends Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'goods_base';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_factory';

	/**
	 * 编辑 新增和修改
	 * @param string $store_id
	 * @return array
	 */
	public function edit($storeId =''){
		$data = input('post.');
		$validate = validate('GoodsBase');
		// if(!$result = $validate->scene('edit')->check($data)) {
		// 	return errorMsg($validate->getError());
		// }

		if(!empty($data['thumb_img'])){
			$data['thumb_img'] = moveImgFromTemp(config('upload_dir.factory_goods'),basename($data['thumb_img']));
		}
		if(!empty($data['main_img'])){
			$mainImg = '';
			$tempMainImg = explode(",",$data['main_img']);
			array_pop($tempMainImg);
			foreach ($tempMainImg as $item) {
				if($item){
					$mainImg = moveImgFromTemp(config('upload_dir.factory_goods'),basename($item)).','.$mainImg;
				}
			}
			$data['main_img'] = $mainImg;
		}
		if(!empty($data['goods_video'])){
			$data['goods_video'] = moveImgFromTemp(config('upload_dir.factory_goods'),basename($data['goods_video']));
		}
		if(!empty($data['details_img'])){
			$detailsImg = '';
			$tempArray = explode(",",$data['details_img']);
			array_pop($tempArray);
			foreach ($tempArray as $item) {
				if($item){
					$detailsImg = moveImgFromTemp(config('upload_dir.factory_goods'),basename($item)).','.$detailsImg;
				}
			}
			$data['details_img'] = $detailsImg;
		}
		if(input('?post.goods_base_id')){//修改
			$where = [
				['id','=',$data['goods_base_id']],
			];
			$file = array(
				'thumb_img','main_img','details_img','goods_video'
			);
			$oldGoodsBaseInfo = $this -> getGoodsBase($where,$file);
			if(empty($oldGoodsBaseInfo)){
				return errorMsg('没有数据');
			}

			$data['update_time'] = time();
			$result = $this->allowField(true)->save($data, ['id' => $data['goods_base_id']]);
			$goodsBaseId = $data['goods_base_id'];
		}else{
			$data['create_time'] = time();
			$result = $this -> allowField(true) -> save($data);
			$goodsBaseId =  $this->getAttr('id');
			if(!$result){
				$this -> rollback();
				return errorMsg('失败');
			}

		}
		if(false !== $result){
			if(input('?post.goods_base_id')){//删除旧图片
				delImgFromPaths($oldGoodsBaseInfo['thumb_img'],$data['thumb_img']);
				//删除就图片
				$oldMainImg = explode(",",$oldGoodsBaseInfo['main_img']);
				array_pop($oldMainImg);
				$newMainImg = explode(",",$data['main_img']);
				array_pop($newMainImg);
				delImgFromPaths($oldMainImg,$newMainImg);

				$oldDetailsImg = explode(",",$oldGoodsBaseInfo['details_img']);
				array_pop($oldDetailsImg);
				$newDetailsImg = explode(",",$data['details_img']);
				array_pop($newDetailsImg);
				delImgFromPaths($oldDetailsImg,$newDetailsImg);

				delImgFromPaths($oldGoodsBaseInfo['goods_video'],$data['goods_video']);
			}
			return successMsg("成功",array('id'=>$goodsBaseId));
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
			'gb.status' => 0,
		);
		$_join = array(
		);
		$where = array_merge($_where, $where);
		$_order = array(
			'gb.id'=>'desc',
		);
		$order = array_merge($_order, $order);
		$list = $this->alias('gb')
			->where($where)
			->field($field)
			->join(array_merge($_join,$join))
			->order($order)
			->limit($limit)
			->select();
		if(!empty($list)){
			$list = $list->toArray();
		}
		return $list;
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
			'gb.status' => 0,
		);
		$where = array_merge($_where, $where);
		$_join = array(
		);
		$info = $this->alias('gb')
			->field($field)
			->join(array_merge($_join,$join))
			->where($where)
			->find();
		if(!empty($info)){
			$info = $info->toArray();
		}
		return $info;
	}
}