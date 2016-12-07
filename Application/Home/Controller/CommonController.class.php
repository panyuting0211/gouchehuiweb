<?php
namespace Home\Controller;
use Think\Controller;

/**
 * Class UsercenterController前台会员中心
 * @package Member\Controller
 */
class CommonController extends Controller
{
    public function _initialize()
    {
        if(isset($_COOKIE['token']) and !empty($_COOKIE['token']))
        {
            $user_id=authcode($_COOKIE['token'],'DECODE','gouchehui');
            session('user_id',$user_id);
            $user_name=M('user_general')->where(array('id'=>$user_id))->getField('user_name');
            session('user_name',$user_name);
        }else{
            unset($_SESSION['user_id']);
        }

    }
}