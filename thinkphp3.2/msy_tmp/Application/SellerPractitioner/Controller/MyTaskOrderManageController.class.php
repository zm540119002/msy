<?php
namespace SellerPractitioner\Controller;

class MyTaskOrderManageController extends  BaseAuthSellerController {
    //我的接单管理-首页
    public function index(){
        if(IS_POST){
        }else{
            $count = array();
            //我的消息-统计
            $where = array(
                'm.seller_read' => 0,
                'm.to_id' => $this->seller['id'],
                'm.to_type' => 2,
            );
            $messageListCount = $this->countMessage($where);
            $count['messageListCount'] = $messageListCount;

            //我的接单-统计
            $model = M('task_order_seller_enlist_signing ts');
            $where = array(
                'ts.seller_id' => $this->seller['id'],
                'ts.status' => 0,
            );
            $field = array(
                't.sign_status','count(t.sign_status) statistics',
            );
            $join = array(
                'left join task_order t on t.id = ts.task_order_id '
            );
            $group = ' t.sign_status ';
            $taskOrderCounts = $model->where($where)->field($field)->join($join)->group($group)->select();
            if(!empty($taskOrderCounts)){
                foreach ($taskOrderCounts as $val){
                    if($val['sign_status'] == 0){//未签约任务单-统计
                        $count['taskOrderUnsignedCount'] = $val['statistics'];
                    }elseif($val['sign_status'] ==1){//已签约任务单-统计
                        $count['taskOrderSignedCount'] = $val['statistics'];
                    }elseif($val['sign_status'] ==2){//已完成任务单-统计
                        $count['taskOrderCompletedCount'] = $val['statistics'];
                    }elseif($val['sign_status'] ==3){//已取消任务单-统计
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
            $taskOrderList = $this->getTaskOrderListBySellerId($this->seller['id'],intval($_POST['signStatus']));
            $this->assign('taskOrderList',$taskOrderList);

            $this->display('Public:taskOrderList');
        }else{
            if( isset($_GET['signStatus']) && $_GET['signStatus'] ){
                $this->assign('signStatus',intval($_GET['signStatus']));
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
                'seller_read' => 0,
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
            $this->assign('sellerId',$this->seller['id']);

            $this->display();
        }
    }

    //放弃签约
    public function giveUpSign(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        $taskOrderId = I('post.taskOrderId',0,'int');
        $model = M('task_order_seller_enlist_signing');
        $where = array(
            'task_order_id' => $taskOrderId,
            'seller_id' => $this->seller['id'],
        );
        $res = $model->where($where)->setField('status',1);
        if(!$res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }
        $this->ajaxReturn(successMsg('成功'));
    }

    //同意签约
    public function agreeSign(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        $taskOrderId = I('post.taskOrderId',0,'int');
        $model = M('task_order_seller_enlist_signing');
        $where = array(
            'task_order_id' => $taskOrderId,
            'seller_id' => $this->seller['id'],
        );
        $res = $model->where($where)->setField('sign_status',2);
        if(!$res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }
        $this->ajaxReturn(successMsg('成功'));
    }

    //我的消息
    public function myMessage(){
        if(IS_POST){
        }else{
            //我的消息
            $where = array(
                'm.seller_read' => 0,
                'm.to_id' => $this->seller['id'],
                'm.to_type' => 2,
            );
            $messageList = $this->selectMessage($where);
            $this->assign('messageList',$messageList);
            $this->display();
        }
    }

    //消息阅读
    public function readSellerMessage(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        $id = I('post.messageId',0,'int');
        $model = M('seller_message');
        $where = array(
            'id' => $id,
            'from_id' => $this->user['id'],
        );
        $res = $model->where($where)->setField('seller_read',1);
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

    //查看我的评分评论
    public function viewMyComments(){
        if(IS_POST){
        }else{
            $sellerId = $this->seller['id'];

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
    public function terminateCooperationApply()
    {
        if (IS_POST) {
            if (!isset($_POST['terminateCooperationApplyId']) || !intval($_POST['terminateCooperationApplyId'])) {
                $_POST['from_id'] = $this->company['id'];
                $this->_addterminateCooperationApply();
            } else {
                $this->_saveterminateCooperationApply();
            }
        } else {
            $taskOrderId = I('get.taskOrderId', 0, 'int');
            $taskOrder = $this->getTaskOrderInfoById($taskOrderId);
            $this->assign('taskOrder', $taskOrder);

            $model = M('terminate_cooperation_apply tca');
            $where = array(
                'tca.task_order_id' => $taskOrderId,
                'tca.type' => 0,
                'tca.status' => 0,
            );
            $field = ' tca.id,tca.reason,tca.compensate_scheme ';
            $terminateCooperationApply = $model->where($where)->field($field)->find();
            $this->assign('terminateCooperationApply', $terminateCooperationApply);

            $this->display();
        }
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
}