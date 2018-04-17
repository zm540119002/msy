<?php
namespace app\factory\model;
use GuzzleHttp\Psr7\Request;
use think\Model;
use think\Db;
/**
 * 基础模型器
 */

class Goods extends Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'goods';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_factory';
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
		$goodsVideo = '';
		$tempGoodsVideo = explode(",",$data['goods_video']);
		array_pop($tempGoodsVideo);
		foreach ($tempGoodsVideo as $item) {
			if($item){
				$goodsVideo = moveImgFromTemp(config('upload_dir.factory_goods'),basename($item)).','.$goodsVideo;
			}
		}
		$data['goods_video'] = $goodsVideo;
		$detailsImg = '';
		$tempArray = explode(",",$data['details_img']);
		array_pop($tempArray);
		foreach ($tempArray as $item) {
			if($item){
				$detailsImg = moveImgFromTemp(config('upload_dir.factory_goods'),basename($item)).','.$detailsImg;
			}
		}
		$data['details_img'] = $detailsImg;
		$data['create_time'] = time();
		$result = $this -> allowField(true) -> save($data);
		if(false !== $result){
			return successMsg("添加成功！");
		}else{
			return errorMsg($this->getError());
		}
	}

	/**
	 * 修改
	 */
	public function edit(){
		$data = input('post.');
		$validate = validate('Goods');
		// if(!$result = $validate->scene('edit')->check($data)) {
		// 	return errorMsg($validate->getError());
		// }
		$where['id'] = $data['goods_id'];
		$file = array(
			'thumb_img','main_img','details_img',
		);
		$oldGoodsInfo = $this -> getGoods($where,$file);
		$data['thumb_img'] = moveImgFromTemp(config('upload_dir.factory_goods'),basename($data['thumb_img']));
		$data['main_img']  = moveImgFromTemp(config('upload_dir.factory_goods'),basename($data['main_img']));
		$goodsVideo = '';
		$tempGoodsVideo = explode(",",$data['goods_video']);
		array_pop($tempGoodsVideo);
		foreach ($tempGoodsVideo as $item) {
			if($item){
				$goodsVideo = moveImgFromTemp(config('upload_dir.factory_goods'),basename($item)).','.$goodsVideo;
			}
		}
		$data['goods_video'] = $goodsVideo;

		$detailsImg = '';
		$tempDetailsImg = explode(",",$data['details_img']);
		array_pop($tempDetailsImg);
		foreach ($tempDetailsImg as $item) {
			if($item){
				$detailsImg = moveImgFromTemp(config('upload_dir.factory_goods'),basename($item)).','.$detailsImg;
			}
		}
		$data['details_img'] = $detailsImg;
		$data['update_time'] = time();
		$result = $this->allowField(true)->save($data, ['id' => $data['goods_id']]);
		if(false !== $result){
			$newGoodsInfo = $this -> getGoods($where,$file);
			delImgFromPaths($oldGoodsInfo['thumb_img'],$newGoodsInfo['thumb_img']);
			delImgFromPaths($oldGoodsInfo['main_img'],$newGoodsInfo['main_img']);
			$oldDetailsImg = explode(",",$oldGoodsInfo['details_img']);
			array_pop($oldDetailsImg);
			$newDetailsImg = explode(",",$newGoodsInfo['details_img']);
			array_pop($newDetailsImg);
			delImgFromPaths($oldDetailsImg,$newDetailsImg);
			return successMsg("修改成功");
		}else{
			return errorMsg($this->getError());
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
	public function selectGoods($where=[],$field=[],$order=[],$join=[],$limit=''){
		$_where = array(
			'g.status' => 0,
		);
		$_join = array(
		);
		$where = array_merge($_where, $where);
		$_order = array(
			'id'=>'desc',
		);
		$order = array_merge($_order, $order);
		if($field){
			$list = $this->alias('g')
				->where($where)
				->field($field)
				->join(array_merge($_join,$join))
				->order($order)
				->limit($limit)
				->select();
		}else{
			$list = $this->alias('g')
				->where($where)
				->join(array_merge($_join,$join))
				->order($order)
				->limit($limit)
				->select();
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
	public function getGoods($where=[],$field=[],$join=[]){
		$_where = array(
			'g.status' => 0,
		);
		$where = array_merge($_where, $where);
		$_join = array(
		);
		if($field){
			$info = $this->alias('g')
				->field($field)
				->join(array_merge($_join,$join))
				->where($where)
				->find();
		}else{
			$info = $this->alias('g')
				->where($where)
				->join(array_merge($_join,$join))
				->find();
		}
		return $info;
	}
}