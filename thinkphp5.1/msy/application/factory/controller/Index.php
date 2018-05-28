<?php
namespace app\factory\controller;
use common\controller\UserBase;
use think\facade\Session;
class Index extends UserBase
{
    /**首页
     */
    public function index()
    {

        file_put_contents(config('upload_dir.upload_path').'/a.txt',"Hello World. Testing!");
        $a=filectime(config('upload_dir.upload_path').'/a.txt');
        echo $a;exit;
        $model = new \app\factory\model\UserFactory();
        $uid = $this -> user['id'];
        $where = [ ['user_id','=',$uid] ];
        $factoryCount = $model -> where($where)->count('id');
        $this -> assign('factoryCount',$factoryCount);
        $file = [
            'u.id,u.factory_id,u.is_default,f.name'
        ];
        $join =[
            ['factory f','f.id = u.factory_id'],
        ];
        $where_new = [ ['u.user_id','=',$uid] ];
        if($factoryCount > 1){
            $_where = [
              ['u.user_id','=',$uid],
              ['u.is_default','=',1],
            ];
            $factoryInfo = $model -> getInfo($_where,$file,$join);
            $factoryList = $model -> getList($where_new,$file,$join);
            $this -> assign('factoryList',$factoryList);
            if(!$factoryInfo){
                $this -> assign('notDefaultFactory',1);
            }
            $this -> assign('factoryInfo',$factoryInfo);
        }elseif ($factoryCount == 1){
            $factoryInfo = $model -> getInfo($where_new,$file,$join);
            $this -> assign('factoryInfo',$factoryInfo);
        }
        Session::set('factory',$factoryInfo);
        //我的店铺
        if(!empty($factoryInfo)){
            $modelStore = new \app\factory\model\Store();
            $storeList = $modelStore -> getStoreList($factoryInfo['factory_id']);
            $this -> assign('storeList',$storeList);
        }
        return $this->fetch();
    }

    
}