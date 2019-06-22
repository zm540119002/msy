<?php
namespace app\index\model;

class TwoDimensionalCode extends \common\model\Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'two_dimensional_code';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_1';
	//表的别名
	protected $alias = 'tdc';

    /**
     * @param $user
     * @return array 生成新的二维码和入库
     */
    public function editTable($user)
    {
        $result =  $this->compose($user);
        if($result['status']){
            $url = $result['url'];
        }else{
            return errorMsg('失败');
        }
        $config = [
            'where' => [
                ['status', '=', 0],
                ['user_id', '=', $user['id']],
            ], 'field' => [
                'id','two_dimensional_code_url'
            ]
        ];
        $info = $this->getInfo($config);
        $data = [
            'two_dimensional_code_url' => $url,
            'user_id' => $user['id'],
            'create_time' => time(),
        ];
        if(!empty($info['two_dimensional_code_url'] || !empty($info))){
            //修改
            $where1 = [
                'id' => $info['id'],
                'status' => 0,
                'user_id' => $user['id'],
            ];
        }
        $id = $this->edit($data,$where1);
        if(!$id){
            return errorMsg('失败');
        }
        if(!empty($info['two_dimensional_code_url'])){
            unlink( request()->domain().'/uploads/'.$info['two_dimensional_code_url']);
        }
        return successMsg('成功');

	}

    /**合成商品图片
     *
     * @param array $config 合成图片参数
     * @return $img->path 合成图片的路径
     *
     */

    public function compose($user)
    {
//        $url = request()->domain().'?uid='.$user['id'];
//        $shareQRCode = createLogoQRcode($url,config('upload_dir.hss_user_QRCode'));
        $config = [
            'where' => [
                ['user_id','=',$user['id']]
            ],'field'=>[
                'code_url','id'
            ]
        ];
        $shareQRCodeInfo = $this -> getInfo($config);
        if($shareQRCodeInfo['code_url']){
            $shareQRCode = $shareQRCodeInfo['code_url'];
        }
        if(($shareQRCodeInfo && $shareQRCodeInfo['code_url']) || empty($shareQRCodeInfo)){
            $mineTools = new \common\component\payment\weixin\Jssdk(config('wx_config.appid'), config('wx_config.appsecret'));
            $a = $mineTools-> create_qrcode('QR_LIMIT_SCENE', $user['id']);
            $shareQRCode = createLogoQRcode($a['url'],config('upload_dir.hss_user_QRCode'));
            if($shareQRCodeInfo && $shareQRCodeInfo['code_url']){
                $data = [
                    'code_url' => $shareQRCode,
                ];
            }
            if(empty($shareQRCodeInfo)){
                $data = [
                    'id' => $shareQRCodeInfo['id'],
                    'code_url' => $shareQRCode,
                ];
            }
            $this->edit($data);
        }
        if(empty($user['avatar'])){
            $user['avatar'] = request()->domain().'/static/common/img/default/chat_head.jpg';
        }else{
            $user['avatar']  =  request()->domain().'/uploads/'.$user['avatar'];
        }
        $init = [
            'name'=> $user['name'], //用户名
            'avatar'=> $user['avatar'],//用户头像
            'save_path'=>config('upload_dir.hss_user_QRCode'),   //保存目录  ./uploads/compose/goods....
            'hss_1'=> request()->domain().'/static/index/img/hss_1.jpg', // 460*534  分享底图
            'hss_2'=> request()->domain().'/static/index/img/hss_2.jpg', // 460*534  分享底图
            'hss_3'=> request()->domain().'/static/index/img/hss_3.jpg', // 460*534  分享底图
            'qrcode'=> request()->domain().'/uploads/'.$shareQRCode, // 120*120
            'font'=>'./static/font/simhei.ttf',   //字体
        ];
        $avatar = $this->imgInfo($init['avatar']);
        $qrcode = $this->imgInfo($init['qrcode']);
        $hss_1 = $this->imgInfo($init['hss_1']);
        $hss_2 = $this->imgInfo($init['hss_2']);
        $hss_3 = $this->imgInfo($init['hss_3']);
        if( !$avatar || !$hss_1 || !$qrcode || !$hss_2 || !$hss_3){
            return errorMsg('提供的图片问题');
        }
        $im = imagecreatetruecolor(850, 1511);  //图片大小
        $gray_color = imagecolorallocate($im, 87,89,88);
        $text_color = imagecolorallocate($im, 255, 255, 255);
        imagefill($im, 0, 0, $gray_color);
        imagettftext($im, 25, 0, 200, 130, $text_color, $init['font'], $init['name']); //名字
        imagecopyresampled ($im, $avatar['obj'], 60, 97, 0, 0, 100, 100, $avatar['width'], $avatar['height'] );  //
        imagecopyresampled ($im, $hss_1['obj'], 180, 152, 0, 0, 662, 82, $hss_1['width'],$hss_1['height'] );  //平台logo
        imagecopyresampled ($im, $hss_2['obj'], 0, 240, 0, 0, 850, 963, $hss_2['width'],$hss_2['height'] );  //平台logo
        imagecopyresampled ($im, $hss_3['obj'], 0, 1200, 0, 0, 538, 320, $hss_3['width'],$hss_3['height'] );  //平台logo
        imagecopyresampled ($im, $qrcode['obj'], 580, 1230, 0, 0, 160, 160, $qrcode['width'], $qrcode['width'] );  //二维
        $dir = config('upload_dir.upload_path').'/'.$init['save_path'];
        if(!is_dir($dir)){
            mkdir($dir, 0777, true);
        }
        $filename = generateSN(5).'.jpg';
        $file = $dir.$filename;
        if( !imagejpeg($im, $file) ){
            return errorMsg('合成图片失败');
        }
        imagedestroy($im);
        unlink($shareQRCode);
        return successMsg('成功',['url'=>$init['save_path'].$filename]);
    }

    private function imgInfo($path)
    {
        $info = getimagesize($path);
        //检测图像合法性
        if (false === $info) {
            return false; //图片不合法
        }
        if($info[2]>3){
            return false; //不支持此图片类型
        }
        $type = image_type_to_extension($info[2], false);
        $fun = "imagecreatefrom{$type}";

        //返回图像信息
        if(!$fun) return false;
        return [
            'width'  => $info[0],
            'height' => $info[1],
            'type'   => $type,
            'mime'   => $info['mime'],
            'obj'    => $fun($path),
        ];
    }
}