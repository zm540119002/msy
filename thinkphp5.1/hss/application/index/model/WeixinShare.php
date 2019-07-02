<?php
namespace app\index\model;

/**
 * 广告基础模型器
 */

class WeixinShare extends \common\model\Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'weixin_share';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_1';
	// 别名
	protected $alias = 'w';

    public function getShareInfo()
    {
        $link = strtolower(request()->module() . '/' . request()->controller() . '/' . request()->action());
        $config = [
            'where'=>[
                ['status','=',0],
                ['link','=',$link],
            ],'field'=>[
                'id','link','desc','title','thumb_img'
            ]
        ];
        $info = $this -> getInfo($config);
        $shareInfo = [
            'title'=>$info['title'], //分享的标题
            'shareLink'=>$this->host.'/'.$info['link'], //分享的url
            'desc'=> $info['desc'], //分享的描述
            'shareImgUrl'=>$this->host.'/'.config('upload_dir.upload_path').'/'.$info['top']['thumb_img'], //分享的图片
            'backUrl'=>$this->host.'/'.$info['link'] //分享完成后跳转的url
        ];
         return $shareInfo;
	}
}