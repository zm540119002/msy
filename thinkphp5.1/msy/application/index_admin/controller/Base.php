<?php
namespace app\index_admin\controller;

class Base extends \common\controller\Base{
    public function __construct(){
        parent::__construct();
        $menu = new \common\lib\Menu;
        $allDisplayMenu = $menu->getAllDisplayMenu();
        $this->assign('allDisplayMenu',$allDisplayMenu);
    }
}