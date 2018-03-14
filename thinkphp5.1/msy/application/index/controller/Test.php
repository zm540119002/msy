<?php
namespace app\index\controller;

use common\controller\Base;

class Test extends Base
{
    public function index()
    {
        return $this->fetch();
    }
}