<?php
namespace Member\Controller;
use Think\Controller;
class EmptyController extends Controller{
    public function _empty()
    {
        $this->redirect('Index/index');
    }

    public function index()
    {
        $this->redirect('Index/index');
    }

}