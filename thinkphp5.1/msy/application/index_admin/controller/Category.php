<?php
namespace app\index_admin\controller;

use think\Controller;
use think\Request ;
use think\Db;
use app\index_admin\model\Category as M;

class Category extends Controller
{
    //获取一级菜单
    public function getFirstCategory()
    {
        $model = new M();
        print_r($model ->select());exit;
    }

    public function hello()
    {
        return $this->fetch();
    }
}
