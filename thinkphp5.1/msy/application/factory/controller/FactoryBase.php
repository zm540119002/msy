<?php
namespace app\factory\controller;
use common\controller\UserBase;
use think\facade\Session;
/**用户信息验证控制器基类
 */
class FactoryBase extends UserBase{
    protected $factory = null;

    public function __construct(){
        parent::__construct();
        \common\cache\Factory::remove($this->user['id']);
        $factoryList = \common\cache\Factory::get($this->user['id']);
        $this->assign('factoryList',$factoryList);
        $factoryCount = count($factoryList);
        if ($factoryCount==0){//没有入住供应商
            $this->error('没有入住供应商，请入住', 'Deploy/register');
        }elseif($factoryCount==1){//入住一家供应商
            $this->factory = $factoryList[0];
        }elseif($factoryCount>1){//入住多家供应商
            foreach ($factoryList as $factory){
                if($factory['is_default']){//默认供应商
                    $this->factory = $factory;
                    break;
                }
            }
        }
        if(!$this->factory){
            //获取用户-工厂-角色-权限节点
            $nodeList = getUserFactoryRoleNode($this->user['id'],$this->factory['id']);
            $this->assign('nodeIds',array_column($nodeList,'node_id'));
        }
        $this->assign('factory',$this->factory);
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