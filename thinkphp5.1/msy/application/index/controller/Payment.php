<?php
namespace app\index\controller;

class Payment extends \common\controller\Base {

    /**
     * @return mixed // 确定支付页
     */
    public function pay()
    {
        if(request()->isPost()){
            $postData = input('post.');
            $systemId = $postData['system_id'];
            $sn = $postData['sn'];
            $info = $this->getPayInfo($systemId,$sn);
            if(empty($info) OR !$info['actually_amount']){
                $this->error('订单不存在或金额不能为0 !');
            }
            $attach = [
                'system_id' =>$systemId,
            ];
            $attach = json_encode($attach);
            $jump_url =config('custom.system_id')[$systemId]['jump_url'][$info['type']];
            $return_url = config('custom.return_url');
            $payInfo = [
                'sn'=>$info['sn'],
                'product'=>$info['id'],
                'actually_amount'=>$info['actually_amount'],
                'success_url' =>urlencode($return_url.'?pay_status=success&jump_url='.$jump_url),
                'fail_url' => $return_url.'?pay_status=fail&jump_url='.$jump_url,
                'notify_url'=>config('wx_config.notify_url'),
                'attach'=>$attach,
            ];

            switch($info['pay_code']){
                case 1 : // 微信支付
                    $payInfo['notify_url'] = config('wx_config.notify_url');
                    $wxPay = new \common\component\payment\weixin\weixinpay;
                    $msg   = $wxPay->wxPay($payInfo);
                    break;
                case 2 : // 支付宝
                    $payInfo['notify_url'] = $this->host."/index.php/index/CallBack/aliBack/type/order";
                    $model = new \common\component\payment\alipay\alipay;
                    $model->aliPay($payInfo);
                    break;
                case 3 : // 银联
                    $payInfo['notify_url'] = $this->host."/index.php/index/CallBack/unionBack/type/order";
                    $model = new \common\component\payment\unionpay\unionpay;
                    $model->unionPay($payInfo);
                    break;
            }
            if(isset($msg)){
                $this -> error($msg);
            }
        }else{
            $systemId = input('system_id',0,'int');
            $this->assign('system_id',$systemId);
            $sn = input('sn','','string');
            $info = $this->getPayInfo($systemId,$sn);
            if(empty($info) OR !$info['actually_amount']){
                $this->error('订单不存在或金额不能为0 !');
            }

            $this->assign('info', $info);
            //判断为微信支付，并且为微信浏览器
            if($info['pay_code'] ==1){
                if (!isPhoneSide()) {//pc端微信扫码支付
                    $this ->assign('browser_type',1);
                }elseif(strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') == false ){
                    //手机端非微信浏览器
                    return $this->fetch();
                }else{//微信浏览器(手机端)
                    $this ->assign('browser_type',3);
                    $payOpenId = session('open_id');
                    if(!$payOpenId){
                        $tools = new \common\component\payment\weixin\Jssdk(config('wx_config.appid'), config('wx_config.appsecret'));
                        $payOpenId  = $tools->getOpenid();
                        session('open_id',$payOpenId);
                    }
                }
                $this->assign('isWxBrowser',1);
                //自定义参数，微信支付回调原样返回
                $attach = [
                    'system_id' =>$systemId,
                ];
                $attach = json_encode($attach);
                $jump_url =config('custom.system_id')[$systemId]['jump_url'][$info['type']];
                $return_url = config('wx_config.return_url');
                $payInfo = [
                    'sn'=>$info['sn'],
                    'product'=>$info['id'],
                    'actually_amount'=>$info['actually_amount'],
                    'success_url' => $return_url.'?pay_status=success&jump_url='.$jump_url,
                    'fail_url' => $return_url.'?pay_status=fail&jump_url='.$jump_url,
                    'notify_url'=>config('wx_config.notify_url'),
                    'attach'=>$attach,
                    'open_id'=>$payOpenId,
                ];
                $wxPay = new \common\component\payment\weixin\weixinpay;
                $jsApiParameters   = $wxPay::wxPay($payInfo);
                $this -> assign('jsApiParameters',$jsApiParameters);
                $response = [
                    'success_url' => $jump_url,
                    'fail_url' => $return_url.'?pay_status=fail&jump_url='.$jump_url,
                ];
                $this->assign('payInfo',json_encode($response));
            }
            return $this->fetch();
        }
    }

    /**
     * @param $systemId
     * @return array|\PDOStatement|string|\think\Model|null
     * 获取支付订单信息
     */
    public function getPayInfo($systemId,$sn){
        $model = new \app\index\model\Pay();
        $model ->setConnection(config('custom.system_id')[$systemId]['db']);
        $config = [
            'where' => [
                ['status', '=', 0],
                ['sn', '=', $sn],
            ],'field' => [
                'id','user_id', 'sn', 'actually_amount','pay_code','pay_status','type'
            ],
        ];
        return  $model->getInfo($config);
    }
    /**
     * @param $systemId
     * @return array|\PDOStatement|string|\think\Model|null
     * 获取订单信息
     */
    public function getOrderInfo($systemId,$sn){
        $model = new \app\index\model\Order();
        $model ->setConnection(config('custom.system_id')[$systemId]['db']);
        $config = [
            'where' => [
                ['status', '=', 0],
                ['sn', '=', $sn],
            ],'field' => [
                'id', 'sn', 'amount','actually_amount','pay_code',
                'user_id',
            ],
        ];
        return  $model->getInfo($config);
    }
    /**获取订单信息
     * @param $systemId 平台系统id 根据此参数连接数据库
     * @param $sn 订单sn
     * @return array|\PDOStatement|string|\think\Model|null
     */
    public function getWalletDetailInfo($systemId,$sn){
        $model = new \app\index\model\WalletDetail();
        $model ->setConnection(config('custom.system_id')[$systemId]['db']);
        $config = [
            'where' => [
                ['status', '=', 0],
                ['sn', '=', $sn],
            ],'field' => [
                'id', 'sn', 'amount','pay_code','type','actually_amount',
                'user_id',
            ],
        ];
        return  $model->getInfo($config);
    }

   //支付完跳转的页面
    public function payComplete(){
        $jump_url = input('jump_url');
        $this->assign('jump_url',$jump_url);
        return $this->fetch();
    }
    //取消支付完跳转的页面
    public function payCancel(){
        return $this->fetch();
    }
    //支付失败完跳转的页面
    public function payFail(){
        return $this->fetch();
    }

    // 微信支付回调处理
    /*
     * 回调处理，修改信息，通知，记录日志
     * wxPayNotifyCallBack
     * */
    public function wxPayNotifyCallBack(){
//        $xml = file_get_contents('php://input');
//        file_put_contents('a.txt',$xml);
        $wxPay = new \common\component\payment\weixin\weixinpay;
        $data  = $wxPay->wxNotify();

        if($data){
            $attach = json_decode($data['attach'],true);
            $systemId = $attach['system_id'];
            $payInfo = $this->getPayInfo($systemId,$data['out_trade_no']);
            if(empty($payInfo)){
                return $this->writeLog("数据库没有此订单",$payInfo);
            }
            //此订单回调已处理过
            if($payInfo['pay_status']>=2){
                echo 'SUCCESS';
                die;
            }
            if($data['total_fee']/100!=$payInfo['actually_amount']){
                return $this->writeLog("订单支付回调的金额和订单的金额不符",$payInfo);
            }
            $payModel = new \app\index\model\Pay();
            $payModel ->setConnection(config('custom.system_id')[$systemId]['db']);
            $data1 = [
                'pay_status'=>2,                              // 订单状态
                'payment_time'=>time(),
                'pay_sn'=>$data['transaction_id'],                      // 支付单号 退款用
            ];
            $condition = [
                ['status', '=', 0],
                ['sn', '=', $data['out_trade_no']],
                ['pay_status', '=', 1],
            ];
            $payModel ->startTrans();
            $result = $payModel->isUpdate(true)->save($data1,$condition);
            if($result === false){
                $payModel ->rollback();
                $payInfo['mysql_error'] = $payModel->getError();
                return $this->writeLog("支付订单更新失败",$payInfo);
            }
            //组装 回调的数据
            $info = [
                'sn' => $data['out_trade_no'],
                'user_id' => $payInfo['user_id'],
                'pay_code' => $payInfo['pay_code'],
                'type' => $payInfo['type'],
                'pay_sn' => $data['transaction_id'],
                'actually_amount' => $data['total_fee']/100,
            ];
            if($payInfo['type'] == 1){
                $this->setOrderPayStatus($info,$systemId);
            }elseif($payInfo['type'] == 2){
                $this->setRechargePayStatus($info,$systemId);
            }elseif($payInfo['type'] == 3){ //hss 加盟店家
                $this->setFranchisePayStatus($info,$systemId);
            }elseif($payInfo['type'] == 4|| $payInfo['type'] == 5){ //hss加盟城市合伙人
                $this->setCityPartnerPayStatus($info,$systemId);
            }
        }
    }
    /**
     * 订单支付单回调处理
     * @param $info 回调信息
     */
    public function setOrderPayStatus($info,$systemId){
        $modelOrder = new \app\index\model\Order();
        $modelOrder ->setConnection(config('custom.system_id')[$systemId]['db']);
        $condition = [
            'where' => [
                ['status', '=', 0],
                ['sn', '=', $info['sn']],
                ['order_status', '=', 1],
            ],'field' => [
                'id', 'user_id','sn', 'amount','pay_code','actually_amount','type','type_id',
                'user_id','order_status'
            ],
        ];
        $orderInfo = $modelOrder->getInfo($condition);
        if(empty($orderInfo)){
            return $this->writeLog("数据库没有此订单",$info);
        }

        //此订单回调已处理过
        if($orderInfo['order_status']>=2){
            $modelOrder->commit();
            echo 'SUCCESS';
            die;
        }
        if($orderInfo['actually_amount']!=$info['actually_amount']){
            return $this->writeLog("订单支付回调的金额和订单的金额不符",$info);
        }
        $data = [
            'order_status'=>2,                              // 订单状态
            'payment_time'=>time(),
            'pay_sn'=>$info['pay_sn'],                      // 支付单号 退款用
            'pay_code'=>$info['pay_code'],                  // 支付单号 退款用
        ];
        $result = $modelOrder -> allowField(true) -> save($data,$condition['where']);
        if(!$result){
            $modelOrder->rollback();
            $info['mysql_error'] = $modelOrder->getError();
            return $this->writeLog("订单支付更新失败",$info);
        }

        // 会员升级 // 每个平台都有自己的支付后业务 后期修改
       if($orderInfo['type']==2) {
            $modelPromotion = new \app\index\model\Promotion();
            $modelPromotion ->setConnection(config('custom.system_id')[$systemId]['db']);
            $condition = [
                'where' => [
                    ['status', '=', 0],
                    ['id', '=', $orderInfo['type_id']],
                ], 'field' => [
                    'upgrade_member_level'
                ]
            ];
            $promotion = $modelPromotion->getInfo($condition);

            if($promotion && $promotion['upgrade_member_level']){
                $upgrade_member_level = (int)$promotion['upgrade_member_level'];

                if ($upgrade_member_level) {
                    $where = [
                        'where' => [
                            ['user_id', '=', $orderInfo['user_id']],
                            ['type', '<', $upgrade_member_level],
                        ]
                    ];
                    $data = [
                        'type' => $upgrade_member_level,
                        'update_time' => time(),
                    ];

                    $memberModel = new \app\index\model\Member();
                    $memberModel ->setConnection(config('custom.system_id')[$systemId]['db']);

                    $result = $memberModel->allowField(true)->save($data,$where);

                    if (!$result) {
                        $modelOrder->rollback();
                        $info['mysql_error'] = $modelOrder->getError();
                        return $this->writeLog("会员等级更新失败",$memberModel->getLastSql());
                    }
                }
            }
        }

        $modelOrder->commit();
        echo 'SUCCESS';
    }


    /**
     * 充值支付单回调处理
     * @param $info 回调信息
     */
    public function setRechargePayStatus($info,$systemId){
        $modelWalletDetail= new \app\index\model\WalletDetail();
        $modelWalletDetail ->setConnection(config('custom.system_id')[$systemId]['db']);
        $modelWallet = new \app\index\model\Wallet();
        $modelWallet ->setConnection(config('custom.system_id')[$systemId]['db']);
        $condition = [
            'where' => [
                ['status', '=', 0],
                ['sn', '=', $info['sn']],
                ['recharge_status', '=', 1],
            ],'field' => [
                'id', 'sn', 'amount','pay_code','type','actually_amount','recharge_status',
                'user_id',
            ],
        ];
        $walletDetailInfo = $modelWalletDetail->getInfo($condition);
        if(empty($walletDetailInfo)){
            return $this->writeLog("数据库没有此订单",$info);
        }
        //此订单回调已处理过
        if($walletDetailInfo['recharge_status']>=2){
            $modelWalletDetail->commit();
            echo 'SUCCESS';
            die;
        }
        if($walletDetailInfo['amount']!=$info['actually_amount']){
            return $this->writeLog("订单支付回调的金额和订单的金额不符",$info);
        }
        $data = [
            'recharge_status'=>2,                           // 订单状态
            'payment_time'=>time(),
            'pay_sn'=>$info['pay_sn'],                      // 支付单号 退款用
            'pay_code'=>$info['pay_code'],                      // 支付方式
        ];
//        $condition = [
//            ['status', '=', 0],
//            ['sn', '=', $info['sn']],
//            ['recharge_status', '=', 1],
//        ];
        $result = $modelWalletDetail -> allowField(true) -> save($data,$condition['where']);
        if($result === false){
            $modelWalletDetail ->rollback();
            $info['mysql_error'] = $modelWalletDetail->getError();
            return $this->writeLog('充值订单支付更新失败',$info);
        }
        //充值到钱包表
        $where = [
            ['user_id', '=', $walletDetailInfo['user_id']],
        ];
        $result = $modelWallet->where($where)->setInc('amount', $walletDetailInfo['amount']);

        if($result === false){
            $modelWallet->rollback();
            $info['mysql_error'] = $modelWallet->getError();
            //返回状态给微信服务器
            return $this->writeLog('充值订单支付更新失败',$info);
        }
        $modelWallet->commit();//提交事务
        echo 'SUCCESS';
    }

    /**
     * hss加盟店申请回调处理
     * @param $info 回调信息
     * @param $systemId 平台id
     */
    public function setFranchisePayStatus($info,$systemId){
        $modelFranchise = new \app\index\model\Franchise();
        $modelFranchise ->setConnection(config('custom.system_id')[$systemId]['db']);
        $data = [
            'apply_status'=>2,                              // 状态
            'payment_time'=>time(),
            'pay_sn'=>$info['pay_sn'],                      // 支付单号 退款用
            'pay_code'=>$info['pay_code'],                      // 支付方式
        ];
        $condition = [
            ['status', '=', 0],
            ['sn', '=', $info['sn']],
            ['user_id', '=', $info['user_id']],
        ];
        $result = $modelFranchise -> allowField(true) -> save($data,$condition);
        if($result === false){
            $modelFranchise ->rollback();
            $info['mysql_error'] = $modelFranchise->getError();
            return $this->writeLog("加盟店表更新失败",$info);
        }
        //增加平台member
        $modelMember = new \app\index\model\Member();
        $modelMember ->setConnection(config('custom.system_id')[$systemId]['db']);
        $config =[
            'where' => [
                ['status', '=', 0],
                ['user_id', '=', $info['user_id']],
            ],'field'=>[
                'id','type'
            ],
        ];
        $data = [
            'type'=>2,                              // hss 加盟店会员
            'user_id'=> $info['user_id'],
            'create_time'=>time(),
        ];
        $memberInfo = $modelMember -> getInfo($config);
        if($memberInfo ){
            $data['id'] = $memberInfo['id'];
        }
        $memberId = $modelMember -> edit($data);
        if(!$memberId){
            $modelFranchise ->rollback();
            $info['mysql_error'] = $modelMember->getError();
            return $this->writeLog("hss 会员表增加失败",$info);
        }

        $modelFranchise ->commit();
        echo 'SUCCESS';
    }
    /**
     * hss城市人申请回调处理
     * @param $info 回调信息
     * @param $systemId 平台id
     */
    public function setCityPartnerPayStatus($info,$systemId){
        $modelCityPartner = new \app\index\model\CityPartner();
        $modelCityPartner ->setConnection(config('custom.system_id')[$systemId]['db']);

        $data = [
            'apply_status'=>3,                              // 状态
            'payment_time'=>time(),
            'pay_sn'=>$info['pay_sn'],                      // 支付单号 退款用
            'pay_code'=>$info['pay_code'],                   // 支付方式
            'update_time' => time(),
        ];
        $condition = [
            ['status', '=', 0],
            ['user_id', '=', $info['user_id']],
        ];

        if($info['type'] == 4){
            //席位支付
            $condition[] = ['earnest_sn','=',$info['sn']];
            $data['apply_status']=3;
        }elseif($info['type'] == 5){
            //增加平台member
            $modelMember = new \app\index\model\Member();
            $modelMember ->setConnection(config('custom.system_id')[$systemId]['db']);
            $config =[
                'where' => [
                    ['status', '=', 0],
                    ['user_id', '=', $info['user_id']],
                ],'field'=>[
                    'id','type'
                ],
            ];
            $data = [
                'type'=>4,                              // hss 城市合伙人
                'user_id'=> $info['user_id'],
                'create_time'=>time(),
            ];
            $memberInfo = $modelMember -> getInfo($config);
            if($memberInfo){
                $data['id'] = $memberInfo['id'];
            }
            $memberId = $modelMember -> edit($data);
            if(!$memberId){
                $modelCityPartner ->rollback();
                $info['mysql_error'] = $modelMember->getError();
                return $this->writeLog("hss 会员表增加失败",$info);
            }

            //尾款支付
            $condition[] = ['balance_sn','=',$info['sn']];
            $data['apply_status']=5;

        }
        unset($data['id']);
        $result = $modelCityPartner  -> allowField(true)-> save($data,$condition);
        if(false === $result){
            $modelCityPartner ->rollback();
            $info['mysql_error'] = $modelCityPartner->getError();
            return $this->writeLog("城市人申请席位定金支付更新失败",$info);
        }
        $modelCityPartner ->commit();
        echo 'SUCCESS';
    }
    /**
     * 支付订单 错误日志记录
     * @param $msg 错误信息
     * @param $info 订单信息
     */
    private function writeLog($msg='',$info=[]){
        \think\facade\Log::init(['path' => './logs/pay/']);
        \think\facade\Log::error($msg,json_encode($info));
        \think\facade\Log::save();
    }
}