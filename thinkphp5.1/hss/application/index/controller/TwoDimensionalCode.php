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


    public function generatingTwoDimensionalCode(){
        $model = new \app\index\model\TwoDimensionalCode();
        $a = $model->editTable($this->user);
        print_r($a);
    }
}