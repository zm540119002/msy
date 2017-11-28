<?php
namespace SellerCompany\Controller;

class TaskManageController extends BaseAuthCompanyController {
    //任务管理首页
    public function index(){
        if(IS_POST){
        }else{
            $count = array();
            //我的消息
            $where = array(
                'm.company_read' => 0,
                'm.to_id' => $this->company['id'],
                'm.to_type' => 1,
            );
            $messageListCount = $this->countMessage($where);
            $count['messageListCount'] = $messageListCount;

            //违规下架任务单-统计
            $where = array(
                'online' => 0,
                'user_id' => $this->user['id'],
                'company_id' => $this->company['id'],
            );
            $taskOrderOffLineCount = $this->countTaskOrder($where);
            $count['taskOrderOffLineCount'] = $taskOrderOffLineCount;
            //上架的任务单
            $model = M('task_order');
            $where = array(
                'status' => 0,
                'online' => 1,
                'user_id' => $this->user['id'],
                'company_id' => $this->company['id'],
            );
            $field = ' count(*) statistics,sign_status ';
            $group = ' sign_status ';
            $taskOrderCounts = $model->where($where)->field($field)->group($group)->select();
            if(!empty($taskOrderCounts)){
                foreach ($taskOrderCounts as $val){
                    if($val['sign_status'] == 0){//未签约任务单统计
                        $count['taskOrderUnsignedCount'] = $val['statistics'];
                    }elseif($val['sign_status'] ==1){//已签约任务单统计
                        $count['taskOrderSignedCount'] = $val['statistics'];
                    }elseif($val['sign_status'] ==2){//已完成任务单统计
                        $count['taskOrderCompletedCount'] = $val['statistics'];
                    }elseif($val['sign_status'] ==3){//已取消任务单统计
                        $count['taskOrderCanceledCount'] = $val['statistics'];
                    }
                }
            }
            $this->assign('count',$count);

            $this->display();
        }
    }

    //任务单列表
    public function taskOrderList(){
        if(IS_POST){
            $model = M('task_order t');
            $where = array(
                't.status' => 0,
                't.online' => 1,
                't.user_id' => $this->user['id'],
                't.company_id' => $this->company['id'],
            );
            if(isset($_POST['signStatus']) && is_numeric($_POST['signStatus'])){
                $where['t.sign_status'] = intval($_POST['signStatus']);
            }
            if(isset($_POST['online']) && is_numeric($_POST['online'])){
                $where['t.online'] = intval($_POST['online']);
            }

            $field = array(
                't.id',
            );

            $taskOrderList = $model->where($where)->field($field)->select();
            if($taskOrderList === false){
                $this->ajaxReturn(errorMsg($model->getError()));
            }
            foreach ($taskOrderList as &$val){
                $val = $this->getTaskOrderInfoById($val['id']);
            }
            $this->assign('taskOrderList',$taskOrderList);

            $this->display('Public:taskOrderList');
        }else{
            if(isset($_GET['signStatus']) && is_numeric($_GET['signStatus'])){
                $signStatus = intval($_GET['signStatus']);
                $this->assign('signStatus',$signStatus);
            }

            if(isset($_GET['online']) && is_numeric($_GET['online'])){
                $online = intval($_GET['online']);
                $this->assign('online',$online);
            }

            $this->display();
        }
    }

    //任务单详情
    public function taskOrderDetail(){
        if(IS_POST){
        }else{
            $taskOrderId = I('get.taskOrderId',0,'int');
            //任务单信息
            $taskOrder = $this->getTaskOrderInfoById($taskOrderId);
            $this->assign('taskOrder',$taskOrder);

            $this->display();
        }
    }

    //删除任务单
    public function delTaskOrder(){
        $this->_delTaskOrder();
    }

    //卖手招募管理
    public function sellerRecruitManage(){
        if(IS_POST){
        }else{
            $taskOrderId = I('get.taskOrderId',0,'int');
            //任务单信息
            $taskOrder = $this->getTaskOrderInfoById($taskOrderId);

            //任务单-卖手
            $model = M('task_order_seller_enlist_signing ts');
            $where = array(
                'ts.task_order_id' => $taskOrder['id'],
                'ts.status' => 0,
            );
            $field = array(
                'ts.id','ts.task_order_id',
                's.id','s.name','s.nickname','s.sex','s.type','s.avatar','s.grade',
            );
            $join = array(
                ' left join seller s on ts.seller_id = s.id ',
            );

            //已应征的卖手
            $where['ts.sign_status'] = 0;
            $taskOrder['enlistCount'] = $model->where($where)->count();
            $enList = $model->where($where)->field($field)->join($join)->select();
            $taskOrder['enList'] = $enList;

            //已同意签约的卖手
            $where['ts.sign_status'] = 2;
            $taskOrder['signingCount'] = $model->where($where)->count();
            $signingList = $model->where($where)->field($field)->join($join)->select();
            $taskOrder['signingList'] = $signingList;

            $this->assign('taskOrder',$taskOrder);

            $this->display();
        }
    }

    //卖手详情
    public function sellerDetail(){
        if(IS_POST){
        }else{
            $sellerId = I('get.sellerId',0,'int');

            $where = array(
                's.auth_status' => 2,
                's.id' => $sellerId,
            );
            $field = array(
                's.intro','s.goods_skills'
            );
            //卖手信息
            $seller = $this->selectSeller($where,$field);
            $seller = $seller[0];
            $seller['grade'] = $this->getSellerAvgScoreById($sellerId);
            $this->assign('seller',$seller);

            $taskOrderId = I('get.taskOrderId',0,'int');
            $this->assign('taskOrderId',$taskOrderId);

            $type = I('get.type','','string');
            $this->assign('type',$type);

            $this->display();
        }
    }

    //发出签约邀请
    public function sendSignedInvite(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        $sellerId = I('post.sellerId',0,'int');
        $taskOrderId = I('post.taskOrderId',0,'int');

        $model = M('task_order_seller_enlist_signing');
        $model->startTrans();//开启事务
        $where = array(
            'task_order_id' => $taskOrderId,
            'seller_id' => $sellerId,
            'status' => 0,
        );
        $res = $model->where($where)->setField('sign_status',1);
        if($res === false){
            $model->rollback();//回滚事务
            $this->ajaxReturn(errorMsg($model->getError()));
        }
        //更新任务表邀请次数
        $model = M('task_order');
        $where = array(
            'id' => $taskOrderId,
        );
        $res = $model->where($where)->setInc('invite');
        if($res === false){
            $model->rollback();//回滚事务
            $this->ajaxReturn(errorMsg($model->getError()));
        }
        $model->commit();//提交事务
        $this->ajaxReturn(successMsg('邀请成功！'));
    }

    //签约
    public function signedWithSeller(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        $sellerId = I('post.sellerId',0,'int');
        $taskOrderId = I('post.taskOrderId',0,'int');

        $model = M('task_order');
        $where = array(
            'id' => $taskOrderId,
        );
        $signStatus = $model->where($where)->getField('sign_status');
        if($signStatus == 1){
            $this->ajaxReturn(errorMsg('已签约的任务单不能再签约！'));
        }

        $model = M('task_order_seller_enlist_signing');
        $model->startTrans();//开启事务
        $where = array(
            'task_order_id' => $taskOrderId,
            'seller_id' => $sellerId,
            'status' => 0,
        );
        $res = $model->where($where)->setField('sign_status',3);
        if($res === false){
            $model->rollback();//回滚事务
            $this->ajaxReturn(errorMsg($model->getError()));
        }
        //更新任务表签约状态
        $model = M('task_order');
        $where = array(
            'id' => $taskOrderId,
        );
        $res = $model->where($where)->setField('sign_status',1);
        if($res === false){
            $model->rollback();//回滚事务
            $this->ajaxReturn(errorMsg($model->getError()));
        }
        $model->commit();//提交事务
        $this->ajaxReturn(successMsg('签约成功！'));
    }

    //任务单处理工作台
    public function taskOrderWorkbench(){
        if(IS_POST){
            $this->addMessage();
        }else{
            $taskOrderId = I('get.taskOrderId',0,'int');
            //任务单信息
            $taskOrder = $this->getTaskOrderInfoById($taskOrderId);

            //业务往来记录-统计
            $where = array(
                'task_order_id' => $taskOrder['id'],
                'company_read' => 0,
                'type' => 1,
            );
            $taskOrder['businessRecordCount'] = $this->countMessage($where);

            //店销日记-统计
            $where['type'] = 2;
            $taskOrder['sellDiaryCount'] = $this->countMessage($where);

            //业绩报告-统计
            $where['type'] = 3;
            $taskOrder['performanceReportCount'] = $this->countMessage($where);
            $this->assign('taskOrder',$taskOrder);

            //收藏卖手
            $where = array(
                'sf.company_id' => $this->company['id'],
            );
            $sellerFavorites = $this->selectSellerFavorites($where);
            $this->assign('sellerFavorites',$sellerFavorites);
//            print_r($sellerFavorites);exit;

            $this->display();
        }
    }

    //卖手收藏
    public function sellerFavorites(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        if(!isset($_POST['seller_id']) || !intval($_POST['seller_id'])){
            $this->ajaxReturn(errorMsg('参数seller_id错误'));
        }

        if($this->_sellerFavoritesExist($_POST['seller_id'],$this->company['id'])){//存在
            $this->_sellerFavorites();
        }else{//不存在
            $this->_addSellerFavorites();
        }
    }

    //卖手收藏
    private function _sellerFavorites(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }

        $model = M('seller_favorites');

        if(!isset($_POST['seller_id']) || !intval($_POST['seller_id'])){
            $this->error('参数seller_id错误');
        }
        $id = I('post.seller_id',0,'int');
        $where = array(
            'seller_id' => $id,
            'company_id' => $this->company['id'],
        );
        $res = $model->where($where)->setField('status',0);
        if($res === false){
            $this->error($model->getError());
        }

        $returnArray = array(
            'id' => $id,
        );
        $this->ajaxReturn(successMsg('已收藏',$returnArray));
    }

    //验证是否已收藏
    private function _sellerFavoritesExist($sellerId,$companyId){
        $model = M('seller_favorites');
        $where = array(
            'seller_id' => $sellerId,
            'company_id' => $companyId,
        );
        $res = $model->where($where)->find() ;
        return empty($res) ? false : true;
    }
    //新增卖手收藏
    private function _addSellerFavorites(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        unset($_POST['id']);
        if(!isset($_POST['seller_id']) || !intval($_POST['seller_id'])){
            $this->ajaxReturn(errorMsg('参数seller_id错误'));
        }

        $model = M('seller_favorites');

        $_POST['company_id'] = $this->company['id'];
        $_POST['create_time'] = time();

        $res = $model->create();
        if(!$res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        $id = $model->add();
        if(!$id){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        $returnData = array(
            'id'=>$id,
        );

        $this->ajaxReturn(successMsg('已收藏',$returnData));
    }

    //我的消息
    public function myMessage(){
        if(IS_POST){
        }else{
            //我的消息
            $where = array(
                'm.company_read' => 0,
                'm.to_id' => $this->company['id'],
                'm.to_type' => 1,
            );
            $messageList = $this->selectMessage($where);
            $this->assign('messageList',$messageList);
            $this->display();
        }
    }

    //消息阅读
    public function readCompanyMessage(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        $id = I('post.messageId',0,'int');
        $model = M('seller_message');
        $where = array(
            'id' => $id,
            'from_id' => $this->user['id'],
        );
        $res = $model->where($where)->setField('company_read',1);
        if($res===false){
            $this->ajaxReturn(errorMsg($model->getError()));
        }
        $this->ajaxReturn(successMsg('成功'));
    }

    //消息列表
    public function messageList(){
        if(IS_POST){
        }else{
            if(isset($_GET['messageType']) && intval($_GET['messageType'])){
                $messageType = I('get.messageType',0,'int');
            }
            $this->assign('messageType',$messageType);

            $where = array(
                'm.type' => $messageType,
            );
            if(isset($_GET['messageId']) && intval($_GET['messageId'])){
                $where['m.id'] = I('get.messageId',0,'int');
            }
            if(isset($_GET['taskOrderId']) && intval($_GET['taskOrderId'])){
                $where['m.task_order_id'] = I('get.taskOrderId',0,'int');
            }

            $messageList = $this->selectMessage($where);
            $this->assign('messageList',$messageList);

            $this->display();
        }
    }

    //消息详情查看
    public function viewMessage(){
        if(IS_POST){
        }else{
            //默认显示业务往来记录
            $messageType = 1;
            if(isset($_GET['messageType']) && intval($_GET['messageType'])){
                $messageType = I('get.messageType',0,'int');
            }
            $this->assign('messageType',$messageType);

            $where = array(
                'm.type' => $messageType,
            );
            if(isset($_GET['messageId']) && intval($_GET['messageId'])){
                $where['m.id'] = I('get.messageId',0,'int');
            }
            if(isset($_GET['taskOrderId']) && intval($_GET['taskOrderId'])){
                $where['m.task_order_id'] = I('get.taskOrderId',0,'int');
            }
            $field = array(
                'm.content',
            );
            $fromType = I('get.fromType',0,'int');
            $join = array(
                'left join task_order t on t.id = m.task_order_id ',
            );
            if($fromType == 1){
                $field[] = 'c.name source_name';
                $join[] = ' left join company c on c.id = t.company_id ';
            }elseif($fromType == 2){
                $field[] = 's.name source_name';
                $join[] = '  left join seller s on s.id = t.seller_id  ';
            }
            $message = $this->selectMessage($where,$field,$join);
            $this->assign('message',$message[0]);

            $this->display();
        }
    }

    //审核店销业绩报告
    public function auditPerformanceReport(){
        if(IS_POST){
        }else{
            $messageId = I('get.messageId',0,'int');
            $where = array(
                'm.id' => $messageId,
                'm.type' => 3,
            );
            $field = array(
                'm.content',
            );
            $fromType = I('get.fromType',0,'int');
            $join = array(
                'left join task_order t on t.id = m.task_order_id ',
            );
            if($fromType == 1){
                $field[] = 'c.name source_name';
                $join[] = ' left join company c on c.id = t.company_id ';
            }elseif($fromType == 2){
                $field[] = 's.name source_name';
                $join[] = '  left join seller s on s.id = t.seller_id  ';
            }
            $message = $this->selectMessage($where,$field,$join);
            $this->assign('message',$message[0]);

            $this->display();
        }
    }

    //通过业绩报告审核
    public function passPerformanceAudit(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        if(!isset($_POST['messageId']) || !$_POST['messageId']){
            $this->ajaxReturn(errorMsg('缺少参数messageId'));
        }
        $messageId = I('post.messageId',0,'int');
        $model = M('seller_message');
        $where = array(
            'id' => $messageId,
            'status' => 0,
        );
        $res = $model->where($where)->setField('audit_status',2);
        if($res === false){
            $this->ajaxReturn(errorMsg($model->getError()));
        }
        $this->ajaxReturn(successMsg('审核通过'));
    }

    //业绩报告-修改建议
    public function modifyAdvice(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        if(!isset($_POST['messageId']) || !$_POST['messageId']){
            $this->ajaxReturn(errorMsg('缺少参数messageId'));
        }
        $messageId = I('post.messageId',0,'int');
        $modifyAdvice = I('post.modifyAdvice','','string');

        $model = M('seller_message');
        $where = array(
            'id' => $messageId,
            'status' => 0,
        );
        $_data = array(
            'audit_status' => 1,
            'modify_advice' => $modifyAdvice,
        );
        $res = $model->where($where)->setField($_data);
        if($res === false){
            $this->ajaxReturn(errorMsg($model->getError()));
        }
        $this->ajaxReturn(successMsg('建议成功'));
    }

    //对卖手服务的评分评论
    public function sellerServiceComments(){
        if(IS_POST){
            if(isset($_POST['sellerCommentsId']) && $_POST['sellerCommentsId']){
                $this->_saveSellerComments();
            }else{
                $this->_addSellerComments();
            }
        }else{
            $taskOrderId = I('get.taskOrderId',0,'int');
            $this->assign('taskOrderId',$taskOrderId);

            $sellerId = I('get.sellerId',0,'int');

            //卖手信息
            $where = array(
                's.auth_status' => 2,
                's.id' => $sellerId,
            );
            $field = array(
                's.intro','s.goods_skills',
            );
            $seller = $this->selectSeller($where,$field);
            $this->assign('seller',$seller[0]);

            //卖手评分平均值
            $sellerScoreAvg = $this->getSellerAvgScoreById($sellerId);
            $this->assign('sellerScoreAvg',$sellerScoreAvg);

            //本任务单对该卖手的评论
            $where = array(
                'sc.seller_id' => $sellerId,
            );
            $mySellerComments = $this->selectSellerComments($where);
            $this->assign('mySellerComments',$mySellerComments[0]);

            //卖手评分统计
            $where = array(
                'sc.seller_id' => $sellerId,
                'sc.score' => array('neq',0),
            );
            $sellerScoreCount = $this->sellerCommentsCount($where);
            $this->assign('sellerScoreCount',$sellerScoreCount);

            //卖手评分比例
            $sellerScoreList = $this->sellerScoreList($sellerId,$sellerScoreCount);
            $this->assign('sellerScoreList',$sellerScoreList);

            //所有店家评论
            $where = array(
                'sc.seller_id' => $sellerId,
                'sc.content' => array('neq',''),
            );
            $sellerCommentsCount = $this->sellerCommentsCount($where);
            $this->assign('sellerCommentsCount',$sellerCommentsCount);

            $field = ' sc.score,sc.name,sc.create_time,sc.content,u.nickname user_name ';
            $join = array(
                'left join user u on sc.user_id = u.id '
            );
            $sellerCommentsList = $this->selectSellerComments($where,$field,$join);
            $this->assign('sellerCommentsList',$sellerCommentsList);

            $this->display();
        }
    }

    //单方面终止合作协议申请
    public function terminateCooperationApply(){
        if(IS_POST){
            if(!isset($_POST['terminateCooperationApplyId']) || !intval($_POST['terminateCooperationApplyId'])){
                $_POST['from_id'] = $this->company['id'];
                $this->_addterminateCooperationApply();
            }else{
                $this->_saveterminateCooperationApply();
            }
        }else{
            $taskOrderId = I('get.taskOrderId',0,'int');
            $taskOrder = $this->getTaskOrderInfoById($taskOrderId);
            $this->assign('taskOrder',$taskOrder);

            $model = M('terminate_cooperation_apply tca');
            $where = array(
                'tca.task_order_id' => $taskOrderId,
                'tca.type' => 0,
                'tca.status' => 0,
            );
            $field = ' tca.id,tca.reason,tca.compensate_scheme ';
            $terminateCooperationApply = $model->where($where)->field($field)->find();
            $this->assign('terminateCooperationApply',$terminateCooperationApply);

            $this->display();
        }
    }

    //投诉举报
    public function complainInform(){
        if(IS_POST){
            $newRelativePath = C('COMPLAIN_INFORM');
            if( isset($_POST['evidence_url']) && $_POST['evidence_url'] ){
                $_POST['evidence_url'] = $this->moveImgFromTemp($newRelativePath,basename($_POST['evidence_url']));
            }
            if(!isset($_POST['complainInformId']) || !intval($_POST['complainInformId'])){
                $this->_addComplainInform();
            }else{
                $this->_saveComplainInform();
            }
        }else{
            $taskOrderId = I('get.taskOrderId',0,'int');
            $this->assign('taskOrderId',$taskOrderId);

            //卖手信息
            $sellerId = I('get.sellerId',0,'int');
            $where = array(
                's.auth_status' => 2,
                's.id' => $sellerId,
            );
            $field = array(
                's.intro','s.goods_skills',
            );
            $seller = $this->selectSeller($where,$field);
            $this->assign('seller',$seller[0]);

            //卖手平均分
            $avgScore = $this->getSellerAvgScoreById($sellerId);
            $this->assign('avgScore',$avgScore);

            //任务单举报卖手
            $model = M('complain_inform ci');
            $where = array(
                'ci.type' => 0,
                'ci.status' => 0,
                'ci.task_order_id' => $taskOrderId,
                'ci.to_id' => $sellerId,
            );
            $field = ' ci.id,ci.reason,ci.evidence_url ';
            $complainInform = $model->where($where)->field($field)->find();
            $this->assign('complainInform',$complainInform);

            $this->display();
        }
    }

    //新增投诉举报
    private function _addComplainInform(){
        unset($_POST['id']);
        $taskOrderId = I('post.taskOrderId',0,'int');
        $model = M('complain_inform');
        $_POST['task_order_id'] = $taskOrderId;
        $_POST['from_id'] = $this->company['id'];
        $_POST['create_time'] = time();
        $res = $model->create();
        if(!$res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        $id = $model->add();
        if(!$id){
            $this->ajaxReturn(errorMsg($model->getError()));
        }
        $returnData = array(
            'id'=>$id,
        );

        $this->ajaxReturn(successMsg('成功',$returnData));
    }

    //修改投诉举报
    private function _saveComplainInform(){
        $complainInformId = I('post.complainInformId',0,'int');
        $taskOrderId = I('post.taskOrderId',0,'int');

        $model = M('complain_inform');

        unset($_POST['id']);
        $res = $model->create();
        if(!$res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        $_POST['update_time'] = time();
        $where = array(
            'id' => $complainInformId,
            'task_order_id' => $taskOrderId,
        );
        $res = $model->where($where)->save();
        if($res === false){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        $returnData = array(
            'id' => $complainInformId,
        );

        $this->ajaxReturn(successMsg('成功',$returnData));
    }

    //新增终止合作协议申请
    private function _addterminateCooperationApply(){
        unset($_POST['id']);
        $model = M('terminate_cooperation_apply');
        $_POST['create_time'] = time();
        $_POST['task_order_id'] = I('post.taskOrderId',0,'int');
        $res = $model->create();
        if(!$res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        $id = $model->add();
        if(!$id){
            $this->ajaxReturn(errorMsg($model->getError()));
        }
        $returnData = array(
            'id'=>$id,
        );

        $this->ajaxReturn(successMsg('成功',$returnData));
    }

    //修改终止合作协议申请
    private function _saveterminateCooperationApply(){
        $terminateCooperationApplyId = I('post.terminateCooperationApplyId',0,'int');
        $taskOrderId = I('post.taskOrderId',0,'int');

        $model = M('terminate_cooperation_apply');

        unset($_POST['id']);
        $res = $model->create();
        if(!$res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        $_POST['update_time'] = time();
        $where = array(
            'id' => $terminateCooperationApplyId,
            'task_order_id' => $taskOrderId,
        );
        $res = $model->where($where)->save();
        if($res === false){
            $this->ajaxReturn(errorMsg($model->getError()));
        }
        $returnData = array(
            'id' => $terminateCooperationApplyId,
        );

        $this->ajaxReturn(successMsg('修改成功',$returnData));
    }

    //新增评分评论
    private function _addSellerComments(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        unset($_POST['id']);
        $taskOrderId = I('post.taskOrderId',0,'int');
        if(!$taskOrderId){
            $this->ajaxReturn(errorMsg('参数taskOrderId错误！'));
        }
        $sellerId = I('post.seller_id',0,'int');
        if(!$sellerId){
            $this->ajaxReturn(errorMsg('参数seller_id错误！'));
        }
        if(!$this->sellerCommentsUnique($taskOrderId,$sellerId)){
            $this->ajaxReturn(errorMsg('本任务单店家已对此卖手评论过了！'));
        }

        $model = M('seller_comments');

        $rules = array(
        );
        $_POST['user_id'] = $this->user['id'];
        $_POST['task_order_id'] = $taskOrderId;
        $_POST['company_id'] = $this->company['id'];
        $_POST['create_time'] = time();

        $res = $model->validate($rules)->create();
        if(!$res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        $id = $model->add();
        if(!$id){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        $returnData = array(
            'id'=>$id,
        );

        $this->ajaxReturn(successMsg('评论成功',$returnData));
    }

    //修改评分评论
    private function _saveSellerComments(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        unset($_POST['id']);

        $sellerCommentsId = I('post.sellerCommentsId',0,'int');
        if(!$sellerCommentsId){
            $this->ajaxReturn(errorMsg('参数sellerCommentsId错误！'));
        }

        $model = M('seller_comments');

        $rules = array(
        );

        $_POST['update_time'] = time();

        $res = $model->validate($rules)->create();
        if(!$res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        $_POST['id'] = $sellerCommentsId;
        $_POST['company_id'] = $this->company['id'];
        $where = array(
            'id' => $sellerCommentsId,
            'seller_id' => $_POST['seller_id'],
            'task_order_id' => $_POST['taskOrderId'],
            'company_id' => $this->company['id'],
        );
        $res = $model->where($where)->save();
        if($res === false){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        $returnData = array(
            'id' => $sellerCommentsId,
        );

        $this->ajaxReturn(successMsg('修改成功',$returnData));
    }
}
