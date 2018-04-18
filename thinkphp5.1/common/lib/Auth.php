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
    // 用户角色
    private $_role = [];
    // 用户节点
    private $_node = [];
    // 默认配置
    private $_config = [
        'model_user_role' => '\common\model\UserRole', // 用户-角色关系表-模型
        'model_role' => '\common\model\Role', // 角色表-模型
        'model_role_node' => '\common\model\RoleNode', // 角色-节点关系表-模型
    ];

    public function __construct(){
        $this->_config = array_merge($this->_config, !empty(config('auth'))?config('auth'):[]);
        $this->_user = session('user');
        $this->_setAllMenu();
        $this->_setDisplayMenu();
        $this->_setRole();
    }
    public function test(){
        return $this->getNode();
    }
    /**获取用户角色
     */
    public function getRole(){
        return $this->_role;
    }
    /**获取用户节点
     */
    public function getNode(){
        return $this->_node;
    }
    /**设置节点
     */
    private function _setNode(){
        $modelRoleNode = new $this->_config['model_role_node'];
        if(!empty($this->_role)){
            $response = $modelRoleNode->where('role_id','in',$this->_role)->select();
            $response = $response->toArray();
            $node = array_column($response,'node_id');
            $this->_node = $node;
        }
    }
    /**设置角色
     */
    private function _setRole(){
        $modelUserRole = new $this->_config['model_user_role'];
        $response = $modelUserRole->where('user_id','=',$this->_user['id'])->select();
        $response = $response->toArray();
        $this->_role = array_column($response,'role_id');
    }
    /**设置所有菜单
     */
    private function _setAllMenu(){
        $this->_allMenu = array_merge($this->_allMenu,!empty(config('menu.menu'))?config('menu.menu'):[]);
        $this->_allMenu = array_merge($this->_allMenu,!empty(config('sub_menu.menu'))?config('sub_menu.menu'):[]);
    }
    /**设置可显示菜单
     */
    private function _setDisplayMenu(){
        $temp = $this->_allMenu;
        if(!empty($temp)){
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
}