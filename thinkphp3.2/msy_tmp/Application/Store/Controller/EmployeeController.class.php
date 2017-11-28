<?php
namespace Store\Controller;
use Think\Controller;
use Common\Controller\BaseStoreController;
use Think\Model;

class EmployeeController extends BaseStoreController {
    /**
     * 门店员工管理
     */
    public function manageAccount(){
      if(IS_POST){
          if(isset($_POST['employee_id'])  && $_POST['employee_id']){
              $this -> saveEmployee();
          }else{
              $this -> addEmployee();
          }

      }else{
        $where = array(
            'uid' => session('uid'),
        );
        //获取机构类型的状态
        $scaleType = M('company') -> where($where) ->getField('shop_type');
        $this -> scaleType = intval($scaleType);
        //查找分公司信息
        $companyList = D('company') -> getCompany();
        $this -> companyList = $companyList;
          //查找门店信息
        $shopList = D('shop') -> getShopList();
        $this -> shopList = $shopList;

        $scale = C('SCALE');
        $scale = $scale[$scaleType];
        $position = C('POSITION');
        foreach ($scale as &$item) {
          foreach ($item['position'] as $key => $val) {
            $item['position'][$key] = $position[$key];
          }
        }
        $this -> scale = $scale;

        //查找全部与员工信息
          $empoyeeList = D('Employee') -> getEmployeeList();
          $this -> empoyeeList = $empoyeeList;

        $this -> display();

      }

  }

    /**
     * 增加员工档案
     */
    public function addEmployee(){
        if(IS_POST){
            $employee = D('Employee');
            if(!$employee -> create()){
                //验证失败,输出错误信息
                //getError()方法返回验证失败的信息
                $errorInfo = $employee ->getError();
                show(0,$errorInfo);
            }else{
                $salt = create_random_str(5);
                $data = I('post.');
                $data['salt'] = $salt;
                $data['creat_time'] = time();
                $data['password'] = md5($salt . I('post.password','','string'));
                $organization_id =  D('Company') -> getCompanyID();
                $data['organization_id'] = $organization_id;
                $employee_id = $employee -> add($data);
                if(!$employee_id){
                    show(0,'增加员工账号不成功');
                }else{
                    show(1,'增加员工账号成功',$employee_id);
                }
            }
        }
    }

    /**
     * 修改员工档案
     */
    public function saveEmployee(){
       if(IS_POST){
           $employee = D('Employee');
           if(!$employee -> create()){
               //验证失败,输出错误信息
               //getError()方法返回验证失败的信息
               $errorInfo = $employee ->getError();
               show(0,$errorInfo);
           }else{
               $where = array('id' => I('post.employee_id',0,'number_int'));
               $salt = create_random_str(5);
               $data = I('post.');
               $data['salt'] = $salt;
               $data['update_time'] = time();
               $data['password'] = md5($salt . I('post.password','','string'));
               $employee_id = $employee -> where($where) -> save($data);
               if(!$employee_id){
                   show(0,'增加员工账号不成功');
               }else{
                   show(1,'增加员工账号成功',$employee_id);
               }

           }
       }
    }

    /**
     * 删除员工档案
     */
    public function delEmployee(){
        if(IS_POST){
            $employee_id = I('post.employee_id',0,'number_int');
            if(!$employee_id){
                show(0,'员工ID参数不正确');
            }

            $where = array('id' => $employee_id);
            $data = array(
                'status' => intval('1'),//代表删除
                'update_time' => time(),
            );
            $shop_id = M('Employee') -> where($where) -> save($data);

            if(!$shop_id){
                show(0,'删除门店不成功');
            }
        }
        
    }

    /**
     * 查找员工档案
     */
    public function searchEmployeeAccount(){
        $where = array(
            'uid' => session('uid'),
        );
        //获取机构类型的状态
        $scaleType = M('company') -> where($where) ->getField('shop_type');
        $this -> scaleType = intval($scaleType);
        //查找全部与员工信息
        $employeeList = D('Employee') -> getEmployeeList();
        $this -> employeeList = $employeeList;
        $this -> display();
    }

}