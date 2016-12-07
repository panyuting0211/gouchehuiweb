<?php
namespace Member\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
       if($_COOKIE['token'] == ''){
       		$this->redirect('Public/login');
       }else{
       		$this->redirect('MemberGeneral/index');
       }
    }

    /**
     *获取用户的信息
     */
    public function getUserInfo(){
        //判断是否有数据提交
        if(IS_POST){
            //判断是否是本用户提交
            if($_SESSION['user_id'] != $_POST['uid']){
                $data['status'] = 1;
                $data['info'] = "请先登录自己的账号!";
                $this->ajaxReturn($data);
            }else{
                $list = D('UserGeneral')->user_find(array('id'=>$_POST['uid']));
                if($list){
                    $data['status'] = 0;
                    $data['data'] = $list;
                }else{
                    $data['status'] = 1;
                    $data['info'] ="用户获取失败,请稍后重试";
                }
                $this->ajaxReturn($data);
            }
        }else{
            $this->redirect('MemberGeneral/index');
        }
    }

    /**
     *和商城对接的积分接口
     */
    public function shopPoints(){
        //判断是否有数据提交
        if(IS_POST){
            //判断是否是本用户提交
            if($_SESSION['user_id'] != $_POST['uid']){
                $data['status'] = 1;
                $data['info'] = "请先登录自己的账号!";
                $this->ajaxReturn($data);
            }else{
                //扣除用户抵消的积分
                $del = M('user_general')->where(array('id'=>$_POST['uid']))->setDec('total_jifen',$_POST['delpoint']);
                if($del){
                    //用户积分抵扣成功
                    //添加扣除积分记录
                    $del_record = D('Score')->score_add(array('score'=>$_POST['delpoint'],'info'=>$_POST['goods_name'].'积分抵扣商品的订单号'.$_POST['order_num']));
                    if($del_record){
                        //添加记录成功
                        //购买商品加积分
                        $add = M('user_general')->where(array('id'=>$_POST['uid']))->setInc('total_jifen',$_POST['addpoint']);
                        if($add){
                            //积分添加成功
                            //添加记录
                            $add_record = D('Score')->score_add(array('score'=>$_POST['addpoint'],'info'=>$_POST['goods_name'].'购买成功加'.$_POST['addpoint'].'积分'));
                            if($add_record){
                                $ret['status'] = 0;
                            }else{
                                //购买商品加积分记录失败
                                $ret['status'] = 1;
                                $ret['info'] = "购买商品加积分记录失败";
                            }
                        }else{
                            //购买商品加积分失败
                            $ret['status'] = 1;
                            $ret['info'] = "购买商品加积分失败";
                        }
                    }else{
                        //添加记录失败
                        $ret['status'] = 1;
                        $ret['info'] = "添加记录失败";
                    }
                }else{
                    //用户积分抵扣失败
                    $ret['status'] = 1;
                    $ret['info'] = "用户积分抵扣失败";
                }
                /*返回*/
                $this->ajaxReturn($ret);
            }
        }else{
            $this->redirect('MemberGeneral/index');
        }
    }
}