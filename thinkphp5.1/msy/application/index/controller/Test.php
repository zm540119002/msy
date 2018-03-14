<?php
namespace app\index\controller;

use common\controller\Base;
use Config;

class Test extends Base
{
    public function index()
    {
        return $this->fetch();
//        return dump(config());
//        return dump(env());
    }
}