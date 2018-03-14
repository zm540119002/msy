<?php
namespace app\factory\controller;
use think\Controller;
use app\common\model\Factory as M;
class Index extends Base
{
    public function index()
    {
        return $this->fetch();
    }
    public function enters()
    {
        if(request()->isPost()){
            $m = new M();
            $rs = $m->add();
            return $rs;
            
            $this->success('1');
        }
        
        return $this->fetch();

    }

}