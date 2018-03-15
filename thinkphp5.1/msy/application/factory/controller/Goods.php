<?php
namespace app\factory\controller;
use think\Controller;
use app\common\model\Factory as M;
use common\controller\Base;
class Goods extends Base
{
    public function index()
    {
        return $this->fetch();
    }

    public function add()
    {
        return $this->fetch();
    }

}