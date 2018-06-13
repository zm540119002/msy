<?php
//获取用户-工厂-角色-权限节点ID
function getUserStoreRoleNode($userId,$storeId){
    if(!intval($userId) || !intval($storeId)){
        return errorMsg('参数错误');
    }
    $modelUserStoreRole = new \app\store\model\UserStoreRole();
    $roleList = $modelUserStoreRole->getRole($userId,$storeId);
    $modelRoleNode = new \app\store\model\RoleNode();
    $list = $modelRoleNode->getList(array_column($roleList,'id'));
    return $list;
}