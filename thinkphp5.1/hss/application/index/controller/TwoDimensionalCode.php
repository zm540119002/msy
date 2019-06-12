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
        $model = new \app\index\model\TwoDimensionalCode();
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
        if(empty($info)){
            $result =  $model->compose($this->user);
            if($result['status']){
                $url = $result['url'];
            }else{
                $this -> errorMsg('失败');
            }

            $data = [
                'two_dimensional_code_url' => $url,
                'user_id' => $this->user['id'],
                'create_time' => time(),
            ];
            $id = $model->edit($data);
            if(!$id){
                $this -> errorMsg('失败');
            }
        }
        $this->successMsg('成功！',[
            'code'=> config('code.success.get_user_code.code'),
            'url' => $url,
        ]);
    }


    /**
     * 后台 生成二维码和入库
     */
    public function generatingTwoDimensionalCode(){
        $model = new \app\index\model\TwoDimensionalCode();
        $model->editTable($this->user);
    }

    /**
     * 获取url的二维码图片
     */
    public function getUrlQRcode(){

        $url = input('param_url/s');
        if(!$url){
            $url = request()->domain();
        }
        $newRelativePath= config('upload_dir.url_QRCode');
        $shareQRCodes   = createLogoQRcode($url,$newRelativePath);

        if( !isset($shareQRCodes['status']) ){
            $shareQRCodes = '/'.config('upload_dir.upload_path').'/'.$shareQRCodes;

            return successMsg('成功',['img'=>$shareQRCodes]);
        }

        return errorMsg('分享失败');
    }

}