<?php
namespace app\api\controller;

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
        $model = new \app\api\model\TwoDimensionalCode();
        $config = [
            'where' => [
                ['status', '=', 0],
                ['user_id', '=', $this->user['id']],
            ], 'field' => [
                'id','two_dimensional_code_url'
            ]
        ];
        $info = $model->getInfo($config);
        $url = $info['two_dimensional_code_url'];
        if(empty($url)){
            $result =  $model->compose($this->user);
            if($result['status']){
                $url = $result['url'];
            }else{
                $this->errorMsg('失败');
            }
        }
        $this->successMsg('成功！',[
            'code'=> config('code.success.default.code'),
            'url' => $url,
        ]);
    }


    /**
     * 后台 生成二维码和入库
     */
    public function generatingTwoDimensionalCode(){
        $model = new \app\api\model\TwoDimensionalCode();
        $model->compose($this->user);
    }

    /**
     * 获取url的二维码图片
     */
    public function getUrlQRcode(){
        $url = input('param_url/s');
        if(!$url){
            $url = request()->domain();
        }
        $upload_path  = config('upload_dir.upload_path');
        $file_path    = config('upload_dir.url_QRCode');
        $file_name    = $upload_path.'/'.$file_path.md5($url).'.png';
        //$file_name    = $upload_path.'/'.$file_path.md5($url);

        if(is_file($file_name)){
            //return $file_name;
            return successMsg('成功',['img'=>'/'.$file_name]);

        }else{
            $shareQRCodes = createLogoQRcode($url,$file_path);

            if( !isset($shareQRCodes['status']) ){
                $old_name     = $upload_path.'/'.$shareQRCodes;
                //$file_name    = $file_name.'.'.pathinfo($shareQRCodes)['extension'];
                $res = rename($old_name,$file_name);
                if(!$res){
                    $file_name = $old_name;
                }
                return successMsg('成功',['img'=>'/'.$file_name]);
            }
        }

        return errorMsg('分享失败');
    }

}