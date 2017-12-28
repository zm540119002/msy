<?php
namespace Purchase\Controller;
use web\all\Controller\AuthUserController;
class CommentController extends AuthUserController {
    //
    public function index(){
        $this->display();
    }
}