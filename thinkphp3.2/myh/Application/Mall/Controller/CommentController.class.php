<?php
namespace Mall\Controller;
use web\all\Controller\AuthUserController;
class CommentController extends AuthUserController {
    //
    public function index(){
        $this->display();
    }
}