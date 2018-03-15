<?php
namespace app\factory\controller;
use think\Controller;
use app\common\model\Factory as M;
use common\controller\Base;
class Index extends Base
{
    public function index()
    {
        return $this->fetch();
    }
    public function enters()
    {
        if(requst()->isPost()){
            $m = new M();
            $rs = $m->add();
            return $rs;
        }
        return $this->fetch();
    }

}