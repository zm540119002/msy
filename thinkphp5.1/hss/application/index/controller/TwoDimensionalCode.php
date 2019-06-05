<?php
namespace app\index\controller;

class TwoDimensionalCode extends \common\controller\UserBase {
    /**
     * 获取二维码
     * @return array
     * @throws \think\exception\PDOException
     */
    public function getUserQRcode()
    {
        if(!request()->isAjax()){
            $this->errorMsg('请求方式错误');
        }
        $url = request()->domain().'?uid='.$this->user['id'];
        $newRelativePath = config('upload_dir.hss_user_QRCode');
        $shareQRCode = createLogoQRcode($url,$newRelativePath);
        $init = [
            'save_path'=>$newRelativePath,   //保存目录  ./uploads/compose/goods....
            'name'=> $this->user['name'], //用户名
            'avatar'=> request()->domain().'/uploads/'.$this->user['avatar'],//用户头像
            'base_map'=> request()->domain().'/static/common/img/hss_base_map.jpg', // 460*534  分享底图
            'hss_share_title'=> request()->domain().'/static/common/img/hss_share_title.jpg', // 460*534  分享底图
            'hss_share_sm'=> request()->domain().'/static/common/img/hss_share_sm.jpg', // 460*534  分享底图
            'hss_share_sm1'=> request()->domain().'/static/common/img/hss_share_sm1.jpg', // 460*534  分享底图
            'qrcode'=> request()->domain().'/uploads/'.$shareQRCode, // 120*120
            'font'=>'./static/font/simhei.ttf',   //字体
        ];
        $url =  $this->compose($init);
        $this->successMsg('成功',['url'=>$url]);
    }


    /**合成商品图片
     *
     * @param array $config 合成图片参数
     * @return $img->path 合成图片的路径
     *
     */
    public function compose(array $config=[])
    {
        $init = $config;
        $avatar = $this->imgInfo($init['avatar']);
        $baseMap = $this->imgInfo($init['base_map']);
        $qrcode = $this->imgInfo($init['qrcode']);
        $hss_share_title = $this->imgInfo($init['hss_share_title']);
        $hss_share_sm = $this->imgInfo($init['hss_share_sm']);
        $hss_share_sm1 = $this->imgInfo($init['hss_share_sm1']);
        if( !$avatar || !$baseMap || !$qrcode){
            $this->errorMsg('提供的图片问题');
        }
        $im = imagecreatetruecolor(900, 1500);  //图片大小
        $gray_color = imagecolorallocate($im, 87,89,88);
        $text_color = imagecolorallocate($im, 235, 96, 3);
        imagefill($im, 0, 0, $gray_color);
        imagettftext($im, 25, 0, 200, 90, $text_color, $init['font'], $init['name']); //名字
        imagecopyresized($im,          $avatar['obj'], 60, 50, 0, 0, 100, 100, $avatar['width'], $avatar['height'] );  //
        imagecopyresized($im, $hss_share_title['obj'], 200, 110, 0, 0, 376, 28, 376,28);  //平台logo
        imagecopyresized($im,         $baseMap['obj'], 0, 200, 0, 0, 900, 628, 1000,628 );  //平台logo
        imagecopyresized($im,    $hss_share_sm['obj'], 25, 880, 0, 0, 844, 264, 844,264);  //平台logo
        imagecopyresized($im,   $hss_share_sm1['obj'], 35, 1210, 0, 0, 380, 244, 382,244);  //平台logo
        imagecopyresized($im,          $qrcode['obj'], 550, 1230, 0, 0, 200, 200, $qrcode['width'], $qrcode['width'] );  //二维
        $dir = config('upload_dir.upload_path').'/'.$init['save_path'].'compose/';
        if(!is_dir($dir)){
            mkdir($dir, 0777, true);
        }
        $filename = generateSN(5).'.jpg';
        $file = $dir.$filename;
        if( !imagejpeg($im, $file, 90) ){
            $this->errorMsg('合成图片失败');
        }
        imagedestroy($im);
//        print_r($init['save_path'].'compose/'.$filename);exit;
        return $init['save_path'].'compose/'.$filename;
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