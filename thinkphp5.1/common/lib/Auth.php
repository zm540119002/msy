<?php
namespace common\lib;
/**
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '名称',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态 0：正常； 1：禁用 ；2：删除',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '类型 0:管理员 1：普通',
  `remark` varchar(512) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='角色表';
-- ----------------------------
DROP TABLE IF EXISTS `user_role`;
CREATE TABLE `user_role` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`role_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '角色ID：role.id',
`user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID：user.id',
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COMMENT='用户-角色-关联表';
-- ----------------------------
DROP TABLE IF EXISTS `role_node`;
CREATE TABLE `role_node` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`role_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '角色ID：role.id',
`node_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '节点ID：node.id',
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='角色-节点-关联表';
 **/
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
    //默认配置
    private $config = [
        'auth_type' => 1, // 认证方式，1为实时认证；2为登录认证。
        'auth_role' => 'role', // 角色表
        'auth_role_node' => 'role_node', // 角色-节点关系表
        'auth_user_role' => 'user_role', // 用户-角色关系表
    ];

    public function __construct(){
        $this->_user = session('user');
        $this->_setAllMenu();
        $this->_setDisplayMenu();
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
    public function test(){
        return $this->_displayMenu;
    }
}