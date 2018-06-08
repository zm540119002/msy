<?php
//获取用户-工厂-角色-权限节点ID
function getUserFactoryRoleNode($userId,$factoryId){
    if(!intval($userId) || !intval($factoryId)){
        return errorMsg('参数错误');
    }
    $modelUserFactoryRole = new \app\factory\model\UserFactoryRole();
    $roleList = $modelUserFactoryRole->getRole($userId,$factoryId);
    $modelRoleNode = new \app\factory\model\RoleNode();
    $list = $modelRoleNode->getList(array_column($roleList,'id'));
    return $list;
}