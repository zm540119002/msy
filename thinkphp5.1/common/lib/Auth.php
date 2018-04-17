<?php
namespace common\lib;

class Auth
{
    // 被赋权菜单
    public static $ownMenu = [];
    // 所有菜单
    private $_allMenu = [];
    // 可显示的菜单
    private $_displayMenu = [];
    // 用户信息
    private $_user = [];
    // 默认配置
    private $_config = [
        'user_role_model' => '\common\model\user_role', // 用户-角色关系表-模型
        'role_model' => '\common\model\role', // 角色表-模型
        'role_node_model' => '\common\model\role_node', // 角色-节点关系表-模型
    ];

    public function __construct(){
        $this->_config = array_merge($this->_config, config('auth'));
        $this->_user = session('user');
        $this->_setAllMenu();
        $this->_setDisplayMenu();
    }
    public function test(){
        return $this->_displayMenu;
    }
    /**设置所有菜单
     */
    private function _setAllMenu(){
        $this->_allMenu = array_merge($this->_allMenu,config('menu.menu'));
        $this->_allMenu = array_merge($this->_allMenu,config('sub_menu.menu'));
    }
    /**设置可显示菜单
     */
    private function _setDisplayMenu(){
        $temp = $this->_allMenu;
        foreach ($temp as &$value){
            foreach ($value['sub_menu'] as $key=>$val){
                if(!$val['display']){
                    unset($value['sub_menu'][$key]);
                }
            }
        }
        $this->_displayMenu = $temp;
    }
}