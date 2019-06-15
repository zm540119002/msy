<?php
namespace app\index\controller;

class MemberBeforehandRegister extends \common\controller\UserBase {
    /**首页
     */
    public function index(){

        return $this->fetch();
    }

    public function add(){

        if(!request()->isPost()){
            return errorMsg('请求方式错误');
        }
        $postData = input('post.');

        $validate = new \app\index\validate\MemberBeforehandRegister();
        if(!$validate->scene('add')->check($postData)) {
            return errorMsg($validate->getError());
        }

        $modelCompany = new \app\index\model\MemberBeforehandRegister();

        $data = [
            'user_id' => $this->user['id'],
            'company_name' => $postData['company_name'],
            'name' => $postData['name'],
            'mobile_phone' => $postData['mobile_phone'],
            'create_time'=>time(),
            'update_time'=>time(),
        ];

        if($modelCompany->edit($data)){
            return $this->errorMsg('提交失败，请稍后再重新提交!',config('code.error.default.code'));

        }else{
            $res = config('code.success.default');
            return $this->successMsg($res['msg'],$res);
        }

    }
}