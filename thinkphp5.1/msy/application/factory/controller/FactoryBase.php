<?php
namespace app\factory\controller;
use common\controller\UserBase;
use think\facade\Session;
/**用户信息验证控制器基类
 */
class FactoryBase extends UserBase{
    protected $factory = null;
    protected $factoryList = null;

    public function __construct(){
        parent::__construct();

        $modelUserFactory = new \app\factory\model\UserFactory();
        $where = [
            ['status','=',0],
            ['user_id','=',$this->user['id']],
        ];
        $field = [
            'factory_id','is_default',
        ];
        $list = $modelUserFactory->where($where)->field($field)->select();
        $list = $list->toArray();
        $factoryCount = count($list);
        $info = [];
        if ($factoryCount==0){//没有入住供应商
            $this->error('没有入住供应商，请入住', 'Deploy/register');
        }elseif($factoryCount==1){//入住一家供应商
            $info = $list[0];
        }elseif($factoryCount>1){//入住多家供应商，有默认供应商的情况
            foreach ($list as $val){
                if($val['is_default']){
                    $info = $val;
                    break;
                }
            }
        }
        $onlyOneFactory = false;
        if(!empty($info)){
            $onlyOneFactory = true;
            //获取工厂信息
            \common\cache\Factory::remove($info['factory_id']);
            $this->factory = \common\cache\Factory::get($info['factory_id']);
            //获取用户-工厂-角色-权限节点
            $nodeList = getUserFactoryRoleNode($this->user['id'],$this->factory['id']);
            $nodeIds = array_column($nodeList,'node_id');
            $this->assign('nodeIds',$nodeIds);
        }
        $this->factoryList = \common\cache\Factory::get(array_column($list,'factory_id'));
        $this->assign('factoryList',$this->factoryList);
        $this->assign('onlyOneFactory',$onlyOneFactory);
    }

    //设置默认供应商
    public function setDefaultFactory(){
        $modelUserFactory = new \app\factory\model\UserFactory();
        return $modelUserFactory->setDefaultFactory($this->user['id']);
    }

    //获取厂家详细信息
    public function getFactoryInfo(){
        $model = new \app\factory\model\Factory();
        $where = [
            ['f.id','=',$this->factory['id']],
        ];
        $file = [
            'f.id,f.name,r.logo_img'
        ];
        $join =[
            ['record r','r.factory_id = f.id',],
        ];
        return $model->getInfo($where,$file,$join);
    }
}