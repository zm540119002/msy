<?php
namespace Myms\Controller;
use web\all\Controller\AuthUserController;
class IntroduceClientController extends AuthUserController {
    public function introduceClientIncome(){
        $this -> display();
    }

    public function introduceClientCash(){
        $this -> display();
    }
}