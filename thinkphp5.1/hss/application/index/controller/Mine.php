<?php
namespace app\index\controller;

class Mine extends \common\controller\Base{

    //我的首页
    public function index(){
        $user = session('user');
        $this->assign('user',$user);
        $wallet = array();
        if($user){
            $model = new \app\index\model\Wallet();;
            $condition = [
                'where' => [
                    ['user_id','=',$user['id']]
                ],'field' => [
                    'id','amount',
                ]
            ];
            $wallet = $model->getInfo($condition);
        }
        $this->assign('wallet',$wallet);

        $modelOrder = new \app\index\model\Order();
        $data = $modelOrder->statusSum($user['id']);
        $this->assign('orderStatusSum',$data);

        // 底部菜单，见配置文件custom.footer_menu
        $this->assign('currentPage',request()->controller().'/'.request()->action());

        return $this->fetch();
    }

    //修改头像
    public function editAvatar(){
        if(!request()->isPost()){
            return errorMsg('请求方式错误');
        }
        $user = session('user');
        if(empty($user)){
            return errorMsg('未登录');
        }
        $oldAvatar = $user['avatar'];
        $fileBase64 = input('post.fileBase64');
        $upload = config('upload_dir.user_avatar');
        $newAvatar = $this ->_uploadSingleFileToTemp($fileBase64,$upload);
        if($newAvatar['status'] == 0 && !$newAvatar){
            return errorMsg('失败');
        }
        $user['avatar'] = $newAvatar;
        $modelUser = new \common\model\User();
        $result = $modelUser->allowField(['avatar'])->save($user, ['id' => $user['id']]);
        if(!$result){
            return errorMsg('失败');
        }
        //删除旧详情图
        delImgFromPaths($oldAvatar,$newAvatar);
        setSession($user);
        //生成平台二维码
        TwoDimensionalCode::compose(['avatar'=>request()->domain().'/uploads/'. $user['avatar']]);
        return successMsg('成功',['avatar'=>$newAvatar]);
    }

    //修改名字
    public function editName(){
        if(!request()->isPost()){
            return errorMsg('请求方式错误');
        }
        $modelUser = new \common\model\User();
        $user = session('user');
        $newName = preg_replace('# #','',input('post.name'));
        $user['name'] = $newName;
        $result = $modelUser->allowField(['name'])->save($user, ['id' => $user['id']]);
        if(!$result){
            return errorMsg('失败');
        }
        setSession($user);
        return successMsg('成功',['name'=>$newName]);
    }

}