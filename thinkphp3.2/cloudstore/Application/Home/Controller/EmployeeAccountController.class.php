<?php
namespace Home\Controller;

//店家档案
class EmployeeAccountController extends BaseAuthCompanyController {

    //管理美容机构员工的登录账号
    public function manageAccount(){
        if(IS_POST){
            //过滤employeeId
            unset($_POST['id']);
            if(isset($_POST['employeeId']) && intval($_POST['employeeId'])){
                $_POST['id'] = I('post.employeeId',0,'number_int');
                $this->_saveEmployee();
            }else{
                $this->_addEmployee();
            }
        }else{
            $this->scaleType = $this->company['scale'];

            //机构下所有分公司
            $where = array(
                'c.father_id' => $this->company['id'],
            );
            $this->companyList = $this->selectCompany($where);

            //机构下所有门店
            $where = array(
                's.user_id' => $this->user['id'],
            );
            $this->shopList = $this->selectShop($where);

            //机构规模
            $scale = C('SCALE');
            $scale = $scale[$this->scaleType];
            $position = C('POSITION');
            foreach ($scale as &$item) {
                foreach ($item['position'] as $key => $val) {
                    $item['position'][$key] = $position[$key];
                }
            }
            $this->scale = $scale;

            //机构下所有员工
            $where = array(
                'e.user_id' => $this->user['id'],
            );
            $field = array(
                'c.name company_name','s.name shop_name',
            );
            $join = array(
                ' left join company c on e.company_id = c.id ',
                ' left join shop s on e.shop_id = s.id ',
            );
            $employeeList = $this->selectEmployee($where,$field,$join);
            foreach ($employeeList as $key => $item) {
                foreach ($position as $val) {
                    if($val['id'] == $item['position_id']){
                        $employeeList[$key]['position_name'] = $val['name'];
                    }
                }
            }
            $this->employeeList = $employeeList;

            $this->display();
        }
    }

    //预览员工账号
    public function searchEmployeeAccount(){
        if(IS_POST){
        }else{
            $this->scaleType = $this->company['scale'];

            $where = array(
                'e.user_id' => $this->user['id'],
            );
            $join = array(
                ' left join company c on e.company_id = c.id ',
                ' left join shop s on e.shop_id = s.id ',
            );

            $field = array(
                'c.name company_name','c.name shop_name',
            );
            $employeeList = $this->selectEmployee($where,$field,$join);
            $position = C('POSITION');
            foreach ($employeeList as $key => $item) {
                foreach ($position as $val) {
                    if($val['id'] == $item['position_id']){
                        $employeeList[$key]['position_name'] = $val['name'];
                    }
                }
            }
            $this->employeeList = $employeeList;

            $this->display();
        }
    }

    //删除员工
    public function delEmployee(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg('请用POST访问'));
        }
        if(!isset($_POST['employeeId']) || !$_POST['employeeId']){
            $this->ajaxReturn(errorMsg('缺少员工ID参数！'));
        }

        $id = I('post.employeeId',0,'number_int');
        $where = array(
            'id' => $id,
            'user_id' => $this->user['id'],
        );
        $model = M('employee');
        $res = $model->where($where)->setField('status',2);
        if($res === false){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        $this->ajaxReturn(successMsg('删除成功'));
    }

    //新增员工
    private function _addEmployee(){
        $model = M('employee');
        $rules = array(
            array('name','require','请填姓名！'),
            array('mobile_phone','isMobile','手机号不正确',0,'function'),
            array('mobile_phone','','此手机号已被注册，请更换手机号码！',0,'unique',1),
            array('passwd','require','请填写密码！'),
            array('position_id','require','请设置员工岗位职务！'),
        );
        $_POST['salt'] = create_random_str(10,0);//盐值
        $_POST['passwd'] = md5($_POST['salt'] . $_POST['passwd']);//加密
        $_POST['user_id'] = $this->user['id'];
        $_POST['create_time'] = time();
        $res = $model->validate($rules)->create();
        if(!$res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        $employeeId = $model->add();
        if(!$employeeId){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        $this->ajaxReturn(successMsg('新增成功',array('id'=>$employeeId)));
    }

    //修改员工
    private function _saveEmployee(){
        $model = M('employee');
        $rules = array(
            array('name','require','请填姓名！'),
            array('mobile_phone','isMobile','手机号不正确',0,'function'),
            array('mobile_phone','','此手机号已被注册，请更换手机号码！',0,'unique',1),
            array('passwd','require','请填写密码！'),
            array('position_id','require','请设置员工岗位职务！'),
        );
        $_POST['salt'] = create_random_str(10,0);//盐值
        $_POST['update_time'] = time();
        $res = $model->validate($rules)->create();
        if(!$res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        $res = $model->save();
        if(false === $res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        $this->ajaxReturn(successMsg('修改成功'));
    }
}