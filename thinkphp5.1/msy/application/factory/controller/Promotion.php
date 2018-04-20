<?php
namespace app\factory\controller;

class Promotion extends FactoryBase
{
    public function index()
    {
        return $this->fetch();
    }

    /**
     * @return array|mixed
     *商品编辑
     */
    public function edit()
    {
        
        return $this->fetch();
    }




}