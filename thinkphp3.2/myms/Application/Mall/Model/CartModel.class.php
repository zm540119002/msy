<?php
namespace Mall\Model;
use Think\Model;
use web\all\Lib\AuthUser;
class CartModel extends Model {
    protected $tableName = 'cart';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG_MALL';

    protected $_validate = array();

    //新增
    public function addCart($rules=array()){
        unset($_POST['id']);
        $this->_validate = array_merge($this->_validate,$rules);

        $res = $this->create();
        if(!$res){
            return errorMsg($this->getError());
        }
        $id = $this->add();
        if($id === false){
            return errorMsg($this->getError());
        }
        $returnArray = array(
            'id' => $id,
        );
        return successMsg('新增成功',$returnArray);
    }

    //修改
    public function saveCart($where=array(),$rules=array()){
        unset($_POST['id']);
        $this->_validate = array_merge($this->_validate,$rules);

        $_where = array(
            'status' => 0,
        );
        $id = I('post.cartId',0,'int');
        if($id){
            $_where['id'] = $id;
        }
        $_where = array_merge($_where,$where);

        $res = $this->create();
        if(!$res){
            return errorMsg($this->getError());
        }
        $res = $this->where($_where)->save();
        if($res === false){
            return errorMsg($this->getError());
        }
        $returnArray = array(
            'id' => $id,
        );
        return successMsg('修改成功',$returnArray);
    }

    //标记删除
    public function delCart($where=array()){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);
        $_where = array(
            'status' => 0,
        );
        $id = I('post.cartId',0,'int');
        if($id){
            $_where['id'] = $id;
        }
        $_where = array_merge($_where,$where);

        $res = $this->where($_where)->setField('status',2);
        if($res === false){
            return errorMsg($this->getError());
        }
        $returnArray = array(
            'id' => $id,
        );
        return successMsg('删除成功',$returnArray);
    }

    //查询
    public function selectCart($where=[],$field=[],$join=[]){
        $_where = array(
            'ct.status' => 0,
        );
        $_field = array(
            'ct.id','ct.user_id','ct.type','ct.status','ct.foreign_id','ct.num','ct.create_time',
        );
        $_join = array(
        );
        $list = $this
            ->alias('ct')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return $list?:[];
    }

    //购物车统计
    public function cartCount($where){
        $_where = array(
            'ct.status' => 0,
        );
        $_field = array(
            'ct.type','count(1) num',
        );
        $list = $this
            ->alias('ct')
            ->where(array_merge($_where,$where))
            ->field($_field)
            ->group('ct.type')
            ->select();
        $cartCount = array();
        foreach ($list as $value){
            $cartCount[$value['type']] = $value['num'];
        }
        return $cartCount?:[];
    }

    //登录后获取购物车列表
    public function getCartList($uid){
        $where = array(
            'user_id' =>$uid
        );
        $cartList = $this -> selectCart($where);
        $delTime = C('DEFAULT_DUE_TIME');
        $this -> delCartByTime($cartList,$uid,$delTime);
        $this->addCartBySession($uid);
        $cartList = $this -> selectCart($where);
        $newCartList =[];
        foreach ($cartList as $k=>$v){
            if($v['type'] == 1){//商品
                $goodsInfo =  D('Goods') -> getGoodsInfoByGoodsId($v['foreign_id']);
            }
            if($v['type'] == 2){//项目
                $goodsInfo =  D('Project')-> getProjectInfoByProjectId($v['foreign_id']);
            }
            $cartInfo['id']           =  $v['id'];
            $cartInfo['foreign_id']   =  $goodsInfo['id'];
            $cartInfo['foreign_name'] =  $goodsInfo['name'];
            $cartInfo['img']          =  $goodsInfo['main_img'];
            $cartInfo['price']        =  $goodsInfo['real_price'];
            $cartInfo['num']          =  $v['num'];
            $cartInfo['type']         =  $v['type'];
            $cartInfo['buy_type']     =  $goodsInfo['buy_type'];
            $newCartList[] = $cartInfo;
        }
        return $newCartList;
    }

    //登录后，session的产品入库
    public function addCartByCookie($cartList,$cookieCart,$uid){
        $allIds = array();
        $sameIds = array();
        $cartIds = '';
        foreach ($cookieCart as $k=>$v){
            $allIds[]= $k;
            foreach ($cartList as $kk => $vv){
                if($v['foreign_id'] == $vv['foreign_id'] && $v['type'] == $vv['type']) {//修改数据库数量
                    $sameIds[] = $k;
                    $goodsNum = $v['num'] + $vv['num'];
                    $_POST = [];
                    $_POST['num'] = $goodsNum;
                    $where = array(
                        'user_id' => $uid,
                        'id' => $vv['id'],
                        'foreign_id' => $vv['foreign_id'],
                    );
                    $res = $this->saveCart($where);
                }
            }
        }
        $addIds = array_diff($allIds,$sameIds);//不同goodsIds
        foreach ($addIds as $k=>$v){//添加到数据库
            foreach ($cookieCart as $kk=>$vv){
                if($v == $kk){
                    $_POST = [];
                    $_POST['user_id'] = $uid;
                    $_POST['foreign_id'] = $vv['foreign_id'];
                    $_POST['num'] = $vv['num'];
                    $_POST['create_time'] = time();
                    $res = $this->addCart();
                }
            }
        }
        cookie('cart',null);
    }

    //获取购物车列表ids
    public function getCartIds($uid){
        $cartIds = D('Cart') -> where( array( 'user_id'=>$uid ) ) -> getField('id',true);
        $cartIds = implode(',',$cartIds);
        return $cartIds;
    }
    //定时更新购物车的信息
    public function delCartByTime($cartList,$uid,$delTime){
        foreach ($cartList as $k=>$v){
            if(time() - intval($v['create_time']) >$delTime ) {
                $where['id'] = $v['id'];
                $where['user_id'] = $uid;
                D('Cart') ->where($where)-> delete();

            }
        }
    }

    //获取购物车的总数和总商品数量
    public function getCartInfo($cartList){
        $total = 0;
        $count = 0;
        foreach ($cartList as $k => $v ){
            $minTotal = $v['num'] * $v['price'];
            $total   += $minTotal;
            $count   += $v['num'];
        }
        $cartInfo['total'] = $total;
        $cartInfo['count'] = $count;
        return $cartInfo;
    }

    //查找客户购物车的某件商品信息
    public function getCartInfoByForeignId($uid,$foreignId,$type){
        $where['user_id']  = $uid;
        $where['foreign_id'] = $foreignId;
        $where['type'] = $type;
        $cartInfo =  D('Cart') -> where($where) -> find();
        return $cartInfo;
    }

    //根据cartIds查找客户购物车的商品信息
    public function getCartListByCartIds($uid,$cartIds){
        $where['user_id']  = $uid;
        $where['id'] = array('in',$cartIds);
        $cartList =  D('Cart') -> where($where) -> select();
        return $cartList;
    }
    //查找24小时内客户添加购物车的商品信息
    public function getCartListByTime($uid){
        $where['user_id']  = $uid;
        $where['create_time'] =array(array('gt',time()-24*60*60),array('lt',time())) ;
        $cartList =  D('Cart') -> where($where) -> select();
        return $cartList;
    }

    //根据cartId删除购物车
    public function delCartByCartIds($uid,$cartIds){
        $where['user_id'] = $uid;
        $where['id']      = array('in',$cartIds);
        $result = D('Cart')->where($where)->delete();
        return $result;
    }


    //查询session上购物车列表
    public function getCartListByCookie(){
        $cartList = [];
        $cart = unserialize(cookie('cart'));
        foreach ($cart as $k=>$v){

            if($v['type'] == 1){//商品
                $goodsInfo =  D('Goods') ->getGoodsInfoByGoodsId($v['foreign_id']);
                $goodsInfo = $goodsInfo[0];
            }
            if($v['type'] == 2){//项目
                $goodsInfo =  D('Project')->getProjectInfoByProjectId($v['foreign_id']);
                $goodsInfo = $goodsInfo[0];
            }
            $cartInfo['foreign_id']   =  $goodsInfo['id'];
            $cartInfo['foreign_name'] =  $goodsInfo['name'];
            $cartInfo['img']          =  $goodsInfo['main_img'];
            $cartInfo['price']        =  $goodsInfo['real_price'];
            $cartInfo['num']          =  $v['num'];
            $cartInfo['type']         =  $v['type'];
            $cartInfo['buy_type']     =  $goodsInfo['buy_type'];
            $cartList[] = $cartInfo;
        }
        return $cartList;
    }

    //获取购物车的总价、商品数和CartId
    public function getAllCartInfo(){
        $this->user = AuthUser::check();
        if(isset($user)  && !empty($user)){
            $cartList = $this -> getCartList($user['id']);
            $cartIds = $this->getCartIds($user['id']);
        }else{
            $cartList = $this -> getCartListByCookie();
        }
        $cartInfo = $this -> getCartInfo($cartList);
        $cartInfo['cartIds'] = $cartIds;
        return $cartInfo;
    }
    //登录或没有登录获取购物车列表
    public function cartList(){
        $this->user = AuthUser::check();
        if(isset($this->user) && !empty($this->user['id'])){
            $cartList = $this ->getCartList($this->user['id']);
        }else{
            //没有登录
            $cartList = $this ->getCartListByCookie();
        }
        return $cartList;

    }
}