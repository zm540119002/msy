<?php
namespace app\factory\controller;

class Organize extends FactoryBase
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     */
    public function  test()
    {
        return $this->fetch();
    }
}