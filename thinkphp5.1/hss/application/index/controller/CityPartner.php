<?php
namespace app\index\controller;

/**
 * 城市合伙人控制器
 */
class CityPartner extends \common\controller\UserBase {
    /**
     * 城市合伙人申请条件
     */
    public function city(){
        return $this->fetch();
    }
    /**
     * 合伙人申请
     */
    public function registered(){
/*        $str = '无锡，佛山，合肥，大连，福州，厦门，哈尔滨，
济南，温州，南宁，长春，泉州，石家庄，贵阳，
南昌，金华，常州，南通，嘉兴，太原，徐州，
惠州，珠海，中山，台州，烟台，兰州，绍兴，
海口，扬州';
        $arr = explode('，',$str);

        $model = new \app\index\model\CityArea();
        foreach ($arr as $k => &$v){
            //$v = "'".trim($v).'市'."'";
            //$v = trim($v).'市';
            $v = trim($v);
            //$v = "SELECT province_code,city_code,city_name,level,cpmi_id FROM city_area WHERE city_name LIKE '%{$v}%'";
            $v = "update city_area set level=2,cpmi_id=1 WHERE city_name LIKE '%{$v}%'";
            $v = $model->query($v);
            //p($res);
        }
        p($arr);
        exit;
        $arrs = implode(',',$arr);*/
/*        p($arr);
        exit;*/
        //p($arrs);
        //exit;
/*        $model = new \app\index\model\CityArea();
        $config = [
            'where' => [
                ['city_name','in',$arrs]
            ]
        ];

        $str = "'韶关市','常德市','六安市','汕尾市','西宁市','茂名市','驻马店市','邢台市','南充市','宜春市','大理市','丽江市','延边市','衢州市','黔东南市','景德镇市','开封市','红河市','北海市','黄冈市','东营市','怀化市','阳江市','菏泽市','黔南市','宿州市','日照市','黄石市','周口市','晋中市','许昌市','拉萨市','锦州市','佳木斯市','淮南市','抚州市','营口市','曲晴市','齐齐哈尔市','牡丹江市','河源市','德阳市','邵阳市','孝感市','焦作市','益阳市','张家口市','运城市','大同市','德州市','玉林市','榆林市','平顶山市','盘锦市','渭南市','安阳市','铜仁市','宣城市','永州市','黄山市','西双版纳市','十堰市','宜宾市','丹东市','乐山市','吉安市','宝鸡市','鄂尔多斯市','铜陵市','娄底市','六盘水市','承德市','保山市','毕节市','泸州市','恩施市','安顺市','枣庄市','聊城市','百色市','临汾市','梧州市','毫州市','德宏市','鹰潭市','滨州市','绥化市','眉山市','赤峰市','咸宁市'";
        $sql = "SELECT province_code,city_code,city_name,level,cpmi_id FROM city_area WHERE city_name in ($str)";


        $res = $model->query($sql);

        $res = array_column($res,'city_name');
        foreach($arr as $k => $v){
            if(!in_array($v,$res)){
                //p($v);
            }
        }
        p($res);
        exit;*/

        if (request()->isAjax()) {
        } else {
            // 做到这里
            $sn = addslashes(trim(input('sn/s')));
            if($sn){

                $where = [
                    ['cp.status', '=', 0],
                    ['cp.user_id','=',$this->user['id']],
                    ['cp.apply_status','>',0],
                    ['cp.is_partner','=',0],
                    ['cp.sn','=',$sn],
                ];
            //自己提交的申请
/*            $modelCityPartner = new \app\index\model\CityPartner();
            $condition=[
                'where'=>[
                    ['cp.status', '=', 0],
                    ['cp.user_id','=',$this->user['id']],
                    ['cp.apply_status','>',0],
                    ['cp.is_partner','=',0],
                    ['cp.id','=',$id],
                ], 'field'=>[
                    'cp.id','cp.province','cp.city','cp.company_name','cp.applicant',
                    'cp.mobile','cp.city_level','cp.earnest','cp.amount','cp.apply_status','cp.payment_time'
                    ,'p.sn','p.id as pay_id'
                ]
                ,'join' => [
                    ['pay p','p.sn = cp.earnest_sn','left'],
                ]
            ];
            $selfApplyList = $modelCityPartner -> getList($condition);

            $selfApplyInfo = reset($selfApplyList);*/
                //申请中
/*                $apply = [];
                //已申请
                $applied = [];
                if($selfApplyList){
                    foreach ($selfApplyList as $selfapply){
                        if ($selfapply['apply_status']<6){
                            $apply[] = $selfapply;
                        }else{
                            $applied[] = $selfapply;
                        }
                    }
                }
                $this->assign('apply1',$apply);
                $this->assign('apply',json_encode($apply));
                $this->assign('applied',json_encode($applied));*/

            }else{
                $where = [
                    ['cp.status', '=', 0],
                    ['cp.user_id','=',$this->user['id']],
                    ['cp.apply_status','=',2],
                    ['cp.is_partner','=',0],
                    //['cp.sn','=',$sn],
                ];
            }
            $modelCityPartner = new \app\index\model\CityPartner();
            $condition=[
                 'field'=>[
                    'cp.id','cp.province_code','cp.city_code','cp.company_name','cp.applicant','cp.market_name','cp.mobile','cp.city_level',
                    'cp.earnest','cp.amount','cp.apply_status','cp.update_time','cp.city_level',
                    'ca.province_name','ca.city_name'
                    //,'p.sn pay_sn','p.id as pay_id'
                ],
                'where' => $where,
                'join' => [
                    //['pay p','p.sn = cp.earnest_sn','left'],
                    ['city_area ca','cp.city_code = ca.city_code','left'],
                ],
                'order' => [
                    'update_time' => 'desc'
                ],
            ];
            $info = $modelCityPartner -> getInfo($condition);
            //p($where);
            //p($info);
            //exit;
            // 申请中的记录 apply_status：2:提交资料 3:交席位定金 4:待审核（已交定金） 5审核通过  6 交清尾款
            // 申请中的记录 apply_status：2:已提交资料 3:待审核（已交定金） 4审核通过  5 交清尾款
            // 已授权的城市 is_partner：1
            $condition = [
                'field' => [
                    'cp.city_code','cp.is_partner','cp.apply_status',
                    'ca.city_name',
                ],'where' => [
                  ['cp.user_id','=', $this->user['id']],
                  ['cp.apply_status','in', '2,3,4,5,6'],
                  ['cp.status','=', 0],
                ],'join' => [
                    ['city_area ca','cp.city_area_id=ca.id','left']
                ],
            ];
            $list = $modelCityPartner->getList($condition);

            $apply_count = 0;
            if($list){
                foreach($list as $k => $v){
                    if(!$v['is_partner']){
                        if($v['apply_status']>2){
                            $apply_count++;
                        }
                        unset($list[$k]);
                    }
                }
            }
            $this->assign('info',$info);
            $this->assign('auth_city',$list);
            $this->assign('apply_count',$apply_count);

            $unlockingFooterCart = unlockingFooterCartConfig([10, 0, 9]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);
            return $this->fetch();
        }
    }

    /**
     * 提交申请
     * @return array
     * @throws \think\exception\PDOException
     */
    public function submitApplicant()
    {
        if(!request()->isAjax()){
            $this->errorMsg('请求方式错误');
        }


        $postData = input('post.');
        if(!$postData){
            $this->errorMsg('失败');
        }

        $validate = new \app\index\validate\CityPartner();
        $postData['apply_status'] = $postData['step'];
        $validateName = 'step'.$postData['apply_status'];
        if(!$validate->scene($validateName)->check($postData)) {
            $this->errorMsg($validate->getError());
        }

        $modelCityArea = new \app\index_admin\model\CityArea();
        $info = $modelCityArea->getPartner($postData['province'],$postData['city']);
        if(!$info){
            $this->errorMsg('已有合伙人',['status'=>1000]);
        }

        $modelCityPartner = new \app\index\model\CityPartner();
        //$modelCityPartner -> startTrans();
        $where = [];
        //$data = [];
        // 1、城市查询 2、登记资料 3、支付定金 4、支付尾款
        switch ($postData['step']){
            case 1:
                // 查询状态，设置不开放&&已签约
/*                $condition = [
                    'field' => [
                        'ca.city_code','ca.province_name','ca.city_name','ca.city_status','ca.alone_amount','ca.alone_earnest',
                        'cp.company_name','cp.applicant','cp.mobile','cp.user_id'
                    ],
                    'join' => [
                        //['city_partner cp','ca.id = cp.city_area_id','left'],
                        ['city_area ca','ca.id = cp.city_area_id','right'],
                    ],
                    'where' => [
                        ['ca.city_status','=',0],
                        ['ca.province_code','=',$postData['province']],
                        ['ca.city_code','=',$postData['city']],
                        ['cp.is_partner','=',0],
                    ],
                ];
                $res = $modelCityPartner->getInfo($condition);
                if($res){
                    $this->errorMsg('失败',['status'=>1000]);
                }*/

                $data = $info;
                unset($data['user_id']);
                unset($data['city_status']);
                unset($data['alone_amount']);
                unset($data['alone_earnest']);

                break;

            case 2:
                // 添加记录
                $postData['user_id']     = $this->user['id'];
                $postData['province_code']= $postData['province'];
                $postData['city_code']   = $postData['city'];
                $postData['city_level']  = $info['level'];
                $postData['earnest']     = $info['earnest'];
                $postData['amount']      = $info['amount'];
                $postData['apply_status']= 2;
                $postData['city_area_id']= $info['id'];
                $postData['market_name'] = $info['market_name'];
                $postData['create_time'] = time();
                $postData['update_time'] = time();

                if($postData['id']>0){
                    $where = [
                        'id'=>$postData['id'],
                        'user_id'=>$this->user['id'],
                        'status'=>0,
                    ];
                }

                $res = $modelCityPartner->edit($postData,$where);

                if(!$res){
                    $this->errorMsg('失败');
                }

                break;
            case 3:
                $paySn = generateSN(); //内部支付编号
                $postData['earnest_sn']  = $paySn;
                $postData['province_code']= $postData['province'];
                $postData['city_code']   = $postData['city'];
                $postData['city_level']  = $info['level'];
                $postData['earnest']     = $info['earnest'];
                $postData['amount']      = $info['amount'];
                $postData['city_area_id']= $info['id'];
                $postData['market_name'] = $info['market_name'];
                $postData['update_time'] = time();

                //$postData['apply_status']= 3;
                //$postData['sn'] = 1115 . generateSN(14);
                //$postData['create_time'] = time();
                if($postData['id']>0){
                    $where = [
                        'id'=>$postData['id'],
                        'user_id'=>$this->user['id'],
                        'status'=>0,
                        'apply_status'=> 2,
                    ];
                }

                $id  = $modelCityPartner->edit($postData,$where);
                if(false===$id){
                    $modelCityPartner ->rollback();
                    $this->errorMsg('失败');
                }
                //生成支付表数据
                $modelPay = new \app\index\model\Pay();
                $data = [
                    'sn' => $paySn,
                    'actually_amount' => $info['earnest'],
                    'user_id' => $this->user['id'],
                    'pay_code' => $postData['pay_code'],
                    'type' => config('custom.pay_type')['cityPartnerSeatPay']['code'],
                    'create_time' => time(),
                ];

                if(isset($postData['pay_id']) && $postData['pay_id']){
                    $where1 = [
                        'id'=>$postData['pay_id'],
                        'user_id'=>$this->user['id'],
                        'status'=>0,
                    ];
                }
                $payId = $modelPay->edit($data,$where1);
                if(false===$payId){
                    $modelCityPartner ->rollback();
                    return errorMsg('失败');
                };

                $data = ['url'=>config('custom.pay_gateway').$paySn,'id'=>$id];
                break;
            //尾款支付
            case 4:
                $paySn = generateSN(); //内部支付编号
                if($postData['id']){
                    $data = [
                        'balance_sn' => $paySn
                    ];
                    $where = [
                        'id'=>$postData['id'],
                        'user_id'=>$this->user['id'],
                        'status'=>0,
                        'apply_status'=> 4,
                    ];
                }
                $id  = $modelCityPartner->edit($data,$where);
                if(false===$id){
                    $modelCityPartner ->rollback();
                    $this->errorMsg('失败');
                }
                //生成支付表数据
                $modelPay = new \app\index\model\Pay();
                $data = [
                    'sn' => $paySn,
                    'actually_amount' =>config('custom.cityPartner_fee')[1]['amount'],
                    'user_id' => $this->user['id'],
                    'pay_code' => $postData['pay_code'],
                    'type' => config('custom.pay_type')['cityPartnerBalancePay']['code'],
                    'create_time' => time(),
                ];
                $payId = $modelPay->edit($data);
                if(!$payId){
                    $modelCityPartner ->rollback();
                    return errorMsg('失败');
                };
        }

        $this->successMsg('成功',$data);
        //$this->successMsg($result);
        //$modelCityPartner -> commit();
        //$this->successMsg('成功',['url'=>config('custom.pay_gateway').$paySn,'id'=>$id]);
    }

    /**
     * 申请中
     */
    public function applicationList(){
        return $this->fetch();
    }

    public function getList(){

        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }

        $condition = [
            'field' => [
                'cp.id','cp.sn','cp.apply_status','cp.create_time','cp.update_time',
                'ca.city_name',
            ],'where' => [
                ['cp.status','=',0],
                ['cp.apply_status','in','3,4,-1'],
                ['cp.user_id','=',$this->user['id']],
            ],'join' => [
                ['city_area ca','cp.city_area_id=ca.id','left']
            ],
        ];

        $modelCityPartner = new \app\index\model\CityPartner();
        $list = $modelCityPartner->pageQuery($condition);
        //p($condition);
        //exit;
        $this->assign('list',$list);
        return $this->fetch('list_tpl');

    }

}