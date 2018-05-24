<?php
namespace app\index_admin\controller;

class Base extends \common\controller\Base{
    public function __construct(){
        parent::__construct();
        \common\cache\Menu::removeAllDisplayMenu($this->user['id']);
        $allDisplayMenu = \common\cache\Menu::getAllDisplayMenu($this->user);
        $this->assign('allDisplayMenu',$allDisplayMenu);
    }
}