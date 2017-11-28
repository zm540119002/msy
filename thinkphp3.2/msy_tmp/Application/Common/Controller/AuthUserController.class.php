<?php
namespace Common\Controller;

use Common\Lib\AuthUser;
use Common\Cache\CompanyCache;
use Common\Cache\SellerCache;

/**登录控制器基类
 */
class AuthUserController extends BaseController{
    protected $user = null;
    protected $loginUrl = 'UserCenter/User/login';//登录URL

    public function __construct(){
        parent::__construct();

        //判断是否登录
        $this->user = AuthUser::check();
        if (!$this->user) {
            $this->error(C('ERROR_LOGIN_REMIND'),U($this->loginUrl));
        }
    }

    //新增消息
    protected function addMessage(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        unset($_POST['id']);

        $model = M('seller_message');

        $rules = array(
            array('name','require','消息标题必须！'),
        );
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

        $this->ajaxReturn(successMsg('新增成功',$returnData));
    }

    /**消息-查询
     */
    protected function selectMessage($where = [],$filed = [],$join = []){
        $model = M('seller_message m');
        $_where = array(
            'm.status' => 0,
        );
        $_filed = array(
            'm.id','m.name','m.from_id','m.from_type','m.to_id','m.to_type','m.company_read','m.seller_read',
            'm.create_time','m.type','m.status','m.audit_status','m.task_order_id',
        );
        return $model->where(array_merge($_where,$where))->field(array_merge($_filed,$filed))->join($join)->select();
    }

    /**消息-统计
     */
    protected function countMessage($where = []){
        $model = M('seller_message m');
        $_where = array(
            'm.status' => 0,
        );
        return $model->where(array_merge($_where,$where))->count();
    }

    /**新增公司（机构）
     */
    protected function addCompany($rules){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        unset($_POST['id']);

        $model = M('company');
        $_POST['user_id'] = $this->user['id'];
        $_POST['create_time'] = time();

        $res = $model->validate($rules)->create();
        if(!$res){
            $this->error($model->getError());
        }

        $id = $model->add();
        if($id === false){
            $this->error($model->getError());
        }

        $returnArray = array(
            'id' => $id,
        );
        CompanyCache::remove($this->user['id']);
        $this->ajaxReturn(successMsg('新增成功',$returnArray));
    }

    /**修改公司（机构）
     */
    protected function saveCompany($rules){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        unset($_POST['id']);

        $companyId = I('post.companyId',0,'int');
        if(!$companyId){
            $this->ajaxReturn(errorMsg('缺少参数companyId！'));
        }

        $model = M('company');
        $_POST['update_time'] = time();

        $res = $model->validate($rules)->create();
        if(!$res){
            $this->error($model->getError());
        }

        $where = array(
            'id' => $companyId,
            'user_id' => $this->user['id'],
        );
        $res = $model->where($where)->save();
        if($res === false){
            $this->error($model->getError());
        }

        $returnArray = array(
            'id' => $companyId,
        );
        CompanyCache::remove($this->user['id']);
        $this->ajaxReturn(successMsg('修改成功',$returnArray));
    }

    /**查询分公司
     */
    protected function selectCompany($where=[],$field=[],$join=[]){
        $model = M('company c');
        $_where = array(
            'c.type' => 1,
            'c.status' => 0,
        );
        $_field = array(
            'c.id','c.name','c.type',
        );
        $_join = array(
        );
        $companyList = $model
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return empty($companyList) ? [] : $companyList;
    }

    /**机构认证成功
     */
    protected function companyAuthSuccess($companyId){
        $model = M('company');
        $where = array(
            'id' => $companyId,
            'type' => 0,
            'user_id' => $this->user['id'],
        );
        $data = array(
            'auth_status' => 2,
        );
        $res = $model->where($where)->setField($data);
        if(false === $res){
            $this->ajaxReturn(successMsg($model->getError()));
        }

        CompanyCache::remove($this->user['id']);
        $this->ajaxReturn(successMsg('机构认证成功'));
    }

    /**卖手认证成功
     */
    protected function sellerAuthSuccess($sellerId){
        $model = M('seller');
        $where = array(
            'id' => $sellerId,
            'status' => 0,
            'user_id' => $this->user['id'],
        );
        $data = array(
            'auth_status' => 2,
        );
        $res = $model->where($where)->setField($data);
        if(false === $res){
            $this->ajaxReturn(successMsg($model->getError()));
        }

        $this->ajaxReturn(successMsg('卖手认证成功'));
    }

    //新增卖手
    protected function addSeller(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        unset($_POST['id']);

        $model = M('seller');
        $rules = array(
            array('name','require','卖手姓名必须！'),
            array('mobile_phone','require','手机号码必须！'),
            array('mobile_phone','','手机号码已经存在！',0,'unique',1),
        );
        $_POST['user_id'] = $this->user['id'];
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
        $this->ajaxReturn(successMsg('新增成功',$returnData));
    }

    //修改卖手
    protected function saveSeller(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        unset($_POST['id']);

        if(!isset($_POST['seller_id']) || !intval($_POST['seller_id'])){
            $this->ajaxReturn(errorMsg('缺少卖手ID参数！'));
        }

        $model = M('seller');
        $rules = array(
            array('name','require','门店名称必须！'),
            array('mobile_phone','require','手机号码必须！'),
        );
        $_POST['update_time'] = time();
        $res = $model->validate($rules)->create();
        if(!$res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }
        $id = I('post.seller_id',0,'int');
        $where = array(
            'id' => $id,
            'user_id' => $this->user['id'],
            'status' => 0,
        );
        $res = $model->where($where)->save();
        if(false === $res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        //删除卖手缓存
        SellerCache::remove($this->user['id']);

        $returnData = array(
            'id'=>$id,
        );

        $this->ajaxReturn(successMsg('修改成功',$returnData));
    }

    /**查询卖手信息
     */
    protected function selectSeller($where=[],$field=[],$join=[]){
        $model = M('seller s');
        $_where = array(
            's.status' => 0,
        );
        $_field = array(
            's.id','s.name','s.nickname','s.sex','s.type','s.avatar',
            's.figure_url_0','s.figure_url_1','s.figure_url_2','s.figure_url_3',
            's.figure_url_4','s.figure_url_5','s.figure_url_6','s.figure_url_7',
        );
        $_join = array(
        );
        $sellerList = $model
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return empty($sellerList) ? [] : $sellerList;
    }

    /**获取卖手平均分
     */
    protected function getSellerAvgScoreById($sellerId){
        $model = M('seller_comments sc');
        $where = array(
            'sc.seller_id' => $sellerId ? $sellerId : $this->seller['id'],
        );
        $sellerScoreAvg = $model->where($where)->avg('sc.score');
        return round($sellerScoreAvg,1);
    }

    /**验证机构对卖手的评论是否唯一
     */
    protected function sellerCommentsUnique($taskOrderId,$sellerId){
        $model = M('seller_comments');
        $where = array(
            'task_order_id' => $taskOrderId,
            'seller_id' => $sellerId,
        );
        $count = $model->where($where)->count();
        return $count ? false : true;
    }

    /**查询卖手信息
     */
    protected function selectSellerComments($where=[],$field=[],$join=[]){
        $model = M('seller_comments sc');
        $_where = array(
        );
        $_field = array(
            'sc.id','sc.name','sc.content','sc.seller_id','sc.company_id','sc.create_time','sc.score',
        );
        $_join = array(
        );
        $sellerCommentsList = $model
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return empty($sellerCommentsList) ? [] : $sellerCommentsList;
    }

    /**卖手评分评论统计
     */
    protected function sellerCommentsCount($where=[]){
        $model = M('seller_comments sc');
        return $model->where($where)->count();
    }

    /**卖手评分比例
     */
    protected function sellerScoreList($sellerId,$sellerScoreCount){
        $model = M('seller_comments sc');
        $where = array(
            'sc.seller_id' => $sellerId,
        );
        $field = ' sc.score,count(sc.score) number ';
        $group = ' sc.score ';
        $sellerScoreList = $model->where($where)->field($field)->group($group)->select();
        foreach ($sellerScoreList as &$val){
            $val['percent'] = round($val['number']/$sellerScoreCount*100,2);
        }
        $scoreList = array(1,2,3,4,5);
        $existScore = array_column($sellerScoreList,'score');
        foreach ($scoreList as $v){
            if(!in_array($v,$existScore)){
                $temp[] = array(
                    'score' => $v,
                    'number' => 0,
                    'percent' => 0,
                );
            }
        }
        $sellerScoreList = array_merge_recursive($sellerScoreList,$temp);
        sortArrByField($sellerScoreList,'score');
        return $sellerScoreList;
    }
}


