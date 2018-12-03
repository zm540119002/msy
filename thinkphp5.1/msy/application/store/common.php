<?php
//获取用户-工厂-角色-权限节点ID
function getUserFactoryRoleNode($userId,$factoryId){
    if(!intval($userId) || !intval($factoryId)){
        return errorMsg('参数错误');
    }
    $modelUserFactoryRole = new \app\store\model\UserFactoryRole();
    $roleList = $modelUserFactoryRole->getRole($userId,$factoryId);
    $modelRoleNode = new \app\store\model\RoleNode();
    $list = $modelRoleNode->getList(array_column($roleList,'id'));
    return $list;
}
