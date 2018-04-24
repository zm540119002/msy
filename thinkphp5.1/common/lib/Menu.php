<?php
namespace common\lib;

class Menu
{
    // 用户信息
    private $_user = [];
    // 用户角色
    private $_role = [];
    // 用户节点
    private $_node = [];
    // 所有菜单
    private $_allMenu = [];
    // 用户菜单
    private $_ownMenu = [];
    // 默认配置
    private $_config = [
        'model_user_role' => '\common\model\UserRole', // 用户-角色关系表-模型
        'model_role' => '\common\model\Role', // 角色表-模型
        'model_role_node' => '\common\model\RoleNode', // 角色-节点关系表-模型
    ];

    public function __construct(){
        $this->_config = array_merge($this->_config,!empty(config('auth'))?config('auth'):[]);
        $this->_user = checkLogin()?:[];
        $this->_setAllMenu();
    }

    /**获取用户可显示菜单
     */
    public function getOwnDisplayMenu(){
        //获取用户可显示菜单先获取用户菜单
        $this->getOwnMenu();
        return $this->_filterDisplayMenu($this->_ownMenu);
    }

    /**获取所有可显示菜单
     */
    public function getAllDisplayMenu(){
        return $this->_filterDisplayMenu($this->_allMenu);
    }

    /**获取所有菜单
     */
    public function getAllMenu(){
        return $this->_allMenu;
    }

    /**获取所有角色
     */
    public function getAllRole(){
        $modelRole = new $this->_config['model_role'];
        $response = $modelRole->where('status','=',0)->select();
        return $response->toArray()?:[];
    }

    /**获取用户菜单
     */
    public function getOwnMenu(){
        //获取菜单先获取用户节点
        $this->getRoleNode();
        $temp = !empty($this->_allMenu)?$this->_allMenu:[];
        if(is_array($temp) && !empty($temp)){
            foreach ($temp as &$value){
                foreach ($value['sub_menu'] as $key=>$val){
                    if(!in_array($val['id'],$this->_node)){
                        unset($value['sub_menu'][$key]);
                    }
                }
            }
        }
        return $this->_ownMenu = $temp;
    }

    /**获取用户角色
     */
    public function getUserRole(){
        $modelUserRole = new $this->_config['model_user_role'];
        $response = $modelUserRole->where('user_id','=',$this->_user['id'])->select();
        $response = $response->toArray();
        $this->_role = array_column($response,'role_id');
        return $this->_role;
    }

    /**获取用户节点
     */
    public function getRoleNode(){
        //获取用户节点先获取角色
        $this->getUserRole();
        $modelRoleNode = new $this->_config['model_role_node'];
        if(!empty($this->_role)){
            $response = $modelRoleNode->where('role_id','in',$this->_role)->select();
            $response = $response->toArray();
            $node = array_unique(array_column($response,'node_id'));
            $this->_node = $node;
        }
        return $this->_node;
    }

    /**设置所有菜单
     */
    private function _setAllMenu(){
        $this->_allMenu = array_merge($this->_allMenu,!empty(config('menu.menu'))?config('menu.menu'):[]);
        $this->_allMenu = array_merge($this->_allMenu,!empty(config('sub_menu.menu'))?config('sub_menu.menu'):[]);
    }
    
    /**过滤不显示菜单
     */
    private function _filterDisplayMenu($menu){
        if(is_array($menu) && !empty($menu)){
            foreach ($menu as &$value){
                foreach ($value['sub_menu'] as $key=>$val){
                    if(!$val['display']){
                        unset($value['sub_menu'][$key]);
                    }
                }
            }
        }
        return $menu;
    }
}