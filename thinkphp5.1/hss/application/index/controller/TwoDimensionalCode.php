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
                $this -> errorMsg('11111失败');
            }
            $data = [
                'two_dimensional_code_url' => $url,
                'user_id' => $this->user['id'],
                'create_time' => time(),
            ];
            $id = $model->edit($data);
            if(!$id){
                $this -> errorMsg('2222失败');
            }
        }
        $this->successMsg('成功！',[
            'code'=> config('code.success.get_user_code.code'),
            'url' => $url,
        ]);
    }


}