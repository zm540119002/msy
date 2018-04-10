<?php
namespace app\index_admin\controller;

class Goods extends \common\controller\UserBase
{
    public function goodsManage()
    {
//        return dump(config());
        return $this->fetch();
    }

    public function hello()
    {
        return $this->fetch();
    }
}
