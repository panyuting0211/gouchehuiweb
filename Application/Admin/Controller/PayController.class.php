<?php
/**
 * 支付模块
 *  */
namespace Admin\Controller;

use Think\Controller;
use Boris\ExportInspector;

/**
 * Class PayController 订单管理控制器
 * @package Admin\Controller
 */
class PayController extends CommonController
{

    /*******************************************************询价记录*********************************************************************************/
    /**
     *询价记录列表页
     */
    public function orderlist()
    {
        //获得开始和结束时间
        $time = date('Y-m-d', time());
        $endtime = strtotime($time . '23:59:59');
        $starttime = $endtime - 3600 * 24 * 31 + 1; // 默认查询一个月内下订单信息
        $this->assign('endtime', $endtime);
        $this->assign('starttime', $starttime);
        $this->display();
    }

    /**
     *询价订单详情
     */
    public function query_details()
    {
        //根据ID 找到这条订单
        if (I('id')) {
            $info = M('pay')->where(array('id' => I('id')))->find();
            $info['expire'] = $info['updatetime'] + 2 * 24 * 60 * 60;
            $z = M('pay_area_low_price')->where(array('pay_id' => $info['id']))->select();
            foreach ($z as $k => $v) {
                $z[$k]['name_4s'] = M('user_4s')->where(array('id' => $v['id_4s']))->getField('name_4s');
            }
            $info['low_info'] = $z;
        }
        $this->assign('info', $info);
        $this->display();
    }

    /**
     *询价失败订单列表
     */
    public function fail_orderlist()
    {
        $data1['card_place'] = array('eq', '');
        $fail_id = M('pay_area_low_price')->where($data1)->distinct(true)->field('pay_id')->select();
        foreach ($fail_id as $key => $value) {
            $fail_pay_id[] = $value['pay_id'];
        }
        if (!empty($fail_pay_id)) {
            $data['id'] = array('in', $fail_pay_id);
        } else {
            $data['createtime'] = 10000000;//条件都不满足，故意查询不到。
        }

        $data['isdelete'] = 0;
        $data['pay_obj'] = '1';
        $count = M('pay')->where($data)->count();

        $page = new \Think\Page($count, 15);//实例化分页类传入总记录数和每页显示的记录数

        $list = M('pay')->where($data)->order('createtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();

        foreach ($list as $key => $value) {
            $list[$key]['user_name'] = M('user_general')->where(array('id' => $value['user_id']))->getField('user_name');
            $list[$key]['expiretime'] = date("Y-m-d H:i:s", strtotime($value['createtime']) + 2 * 24 * 60 * 60);
            $area_price[$key] = M('pay_area_low_price')->where(array('pay_id' => $value['id']))->order('pay_low_price')->field('pay_id,area_name,pay_low_price')->select();
            $data3['pay_id'] = $value['id'];
            $data3['card_place'] = array('eq', '');
            $fail_city[$key] = M('pay_area_low_price')->where($data3)->field('area_name')->select();
        }
        // var_dump($fail_city);exit();
        $pagelist = $page->show();//分页显示输出
        /* 等有车型的时候foreach出汽车名称 */
        $this->assign('list', $list);
        $this->assign('pagelist', $pagelist);
        $this->assign('area_price', $area_price);
        $this->assign('fail_city', $fail_city);
        $this->display();
    }

    /**
     *ajax异步删除订单
     */
    public function ajax_delete()
    {
        $id = I('id');
        if (!empty($id)) {
            $data['updatetime'] = time();
            $data['updateuser'] = session('admin_name');
            $data['isdelete'] = 1;
            $z = M('pay')->where(array('id' => $id))->save($data);
            if ($z) {
                $datar['status'] = 1;
                $datar['msg'] = '操作成功！';
            } else {
                $datar['status'] = 2;
                $datar['msg'] = '操作失败！';
            }
        }
        $this->ajaxReturn($datar);
    }

    /**
     *客服跟踪订单状态
     */
    public function cus_action()
    {
        $pay = M('pay');
        $data['id'] = I('id');
//        $data['updatetime']=time();
//        $data['updateuser']=session('admin_name');
        $data['cus_name'] = I('cus_name');
        $data['cus_character'] = I('cus_character');
        $data['status_track'] = 2;
        $pay->create($data);
        $pay->save($data);

        $datac['id'] = md5(microtime());
        $datac['pay_id'] = I('id');
        $datac['cus_name'] = I('cus_name');
        $datac['cus_remark'] = I('cus_remark');
        $datac['cus_character'] = I('cus_character');
        $datac['createuser']=session('admin_name');
        $info = M('pay_cus')->add($datac);
        if ($info) {
            $info1['status'] = 1;
            $info1['msg'] = '操作成功！';
        } else {
            $info1['status'] = 2;
            $info1['msg'] = '操作失败！';
        }
        $this->ajaxReturn($info1);

    }

    /**
     *客服跟踪订单状态-新增
     */
    public function cus_action_add()
    {
        $datac['id'] = md5(microtime());
        $datac['pay_id'] = I('id');
        $datac['cus_name'] = I('cus_name');
        $datac['cus_remark'] = I('cus_remark');
        $datac['cus_character'] = I('cus_character');
        $datac['createuser']=session('admin_name');
        $info = M('pay_cus')->add($datac);
        if ($info) {
            $info1['status'] = 1;
            $info1['msg'] = '操作成功！';
        } else {
            $info1['status'] = 2;
            $info1['msg'] = '操作失败！';
        }
        $this->ajaxReturn($info1);

    }
    /*****************************************************************订车订单***********************************************************************/

    /**
     *订车订单列表
     */
    public function depositList()
    {
        //获得开始和结束时间
        $time = date('Y-m-d');
        $endtime = strtotime($time . '23:59:59');
        $starttime = $endtime - 3600 * 24 * 31 + 1; // 默认查询一个月内下订单信息
        $buy_time_end = strtotime($time . '23:59:59') + 3600 * 24 * 15 + 1;;
        $buy_time_begin = $buy_time_end - 3600 * 24 * 46 + 1; // 默认查询一个月内下订单信息
        $this->assign('endtime', !empty($endtime) ? $endtime : null);
        $this->assign('starttime', !empty($starttime) ? $starttime : null);
        $this->assign('buy_time_begin', !empty($buy_time_begin) ? $buy_time_begin : null);
        $this->assign('buy_time_end', !empty($buy_time_end) ? $buy_time_end : null);
        $this->display();
    }

    /**
     *订单详情
     */
    public function orderDetails()
    {
        //根据ID 找到这条订单
        if (I('id')) {
            $info = M('pay')->where(array('id' => I('id')))->find();
            $data['min_price'] = array('ELT', $info['low_price']);
            $data['max_price'] = array('EGT', $info['low_price']);
            $info['description'] = M("buy_car_package")->where($data)->getField('description');
            $info['id_imgurl1'] = str_replace("type", 'center', $info['id_imgurl1']);
            $info['id_imgurl2'] = str_replace("type", 'center', $info['id_imgurl2']);
            $info['exterior_img'] = OSS . str_replace("type", 'center', $info['exterior_img']);
            $info['updateuser_3'] = M('manager')->where(array('admin_name' => $info['updateuser_3']))->getField('name');
            $info['updateuser_4'] = M('manager')->where(array('admin_name' => $info['updateuser_4']))->getField('name');
            $info['updateuser_5'] = M('manager')->where(array('admin_name' => $info['updateuser_5']))->getField('name');

        }

        $this->assign('info', $info);
        $this->display();
    }

    /**
     *改变订单状态
     */
    public function changestatus()
    {
        $info = M('pay')->where(array('id' => I('id')))->find();
        //对不同的status进行不同的操作
        $status = I('status');
        if ($status == 6) {
            //把状态设置成出库中
            if ($info['status'] != 4) {
                //设置出库中的同时判断是否资料通过审核
                $data['status'] = 0;
                $data['info'] = "上传的资料未通过审核！";
                $this->ajaxReturn($data);
            } else {
                $z = M('pay')->where(array('id' => I('id')))->save(array('status' => 6, 'updatetime_3' => time(), 'updateuser_3' => $_SESSION['admin_name']));
                if ($z) {
                    $data['status'] = 1;
                } else {
                    $data['status'] = 0;
                    $data['info'] = "状态修改失败！";
                }
                $this->ajaxReturn($data);
            }
        } elseif ($status == 7) {
            //把状态设置成提车中
            if ($info['status'] != 6) {
                //设置出库中的同时判断是否资料通过审核
                $data['status'] = 0;
                $data['info'] = "请先将订单设置成出库中";
                $this->ajaxReturn($data);
            } else {
                $z = M('pay')->where(array('id' => I('id')))->save(array('status' => 7, 'updatetime_4' => time(), 'updateuser_4' => $_SESSION['admin_name']));
                if ($z) {
                    $data['status'] = 1;
                } else {
                    $data['status'] = 0;
                    $data['info'] = "状态修改失败！";
                }
                $this->ajaxReturn($data);
            }
        } elseif ($status == 8) {
            //把状态设置成订单完成
            if ($info['status'] != 7) {
                //设置出库中的同时判断订单状态是否为提车中
                $data['status'] = 0;
                $data['info'] = "请先将订单设置成提车中";
                $this->ajaxReturn($data);
            } else {
                $z = M('pay')->where(array('id' => I('id')))->save(array('status' => 8, 'updatetime_5' => time(), 'updateuser_5' => $_SESSION['admin_name']));
                if ($z) {
                    //给从微信专属二维码的人加500积分并增加一条购车记录
                    $user_info = M('user_general')->where(array('id' => $info['user_id']))->find();
                    $find = M('wx_reg_log')->where(array('fromopenid' => $user_info['wx_open_id']))->find();
                    if ($find) {
                        $data_log['fromopenid'] = $find['fromopenid'];
                        $data_log['myopenid'] = $find['myopenid'];
                        $data_log['activity_name'] = "shaidan_buy";
                        $data_log['order_id'] = I('id');
                        //判断之前这个车是否有记录
                        $find_log = M('wx_buy_log')->where($data_log)->find();
                        if ($find_log == '') {
                            $myuser = M('user_general')->where(array('wx_open_id' => $find['myopenid']))->find();
                            $data_log['createtime'] = time();
                            $m = M('user_general');
                            $m2 = M('wx_buy_log');
                            $m3 = M('score');
                            $data_score = array('id' => md5(microtime()), 'user_id' => $myuser['id'], 'score' => 500, 'flag' => 6, 'role' => 2, 'info' => "好友在购车惠成功购车奖励500积分", 'from_id' => $user_info['id']);
                            $m->startTrans();//事务开始
                            //给用户加积分
                            $res = $m->where(array('wx_open_id' => $find['myopenid']))->save(array('total_jifen' => $myuser['total_jifen'] + 500, 'buy_car_num' => $myuser['buy_car_num'] + 1));
                            //添加购买的记录
                            $res2 = $m2->add($data_log);
                            //添加增加积分的记录
                            $res3 = $m3->add($data_score);
                            if ($res && $res2 && $res3) {
                                $m->commit();//成功则提交
                            } else {
                                $m->rollback();//不成功，则回滚
                            }
                        }
                    }
                    $data['status'] = 1;
                } else {
                    $data['status'] = 0;
                    $data['info'] = "状态修改失败！";
                }
                $this->ajaxReturn($data);
            }
        } elseif ($status == 4 or $status == 5) {
            //判断审核是否通过
            if ($info['status'] != 3) {
                //设置出库中的同时判断订单状态是否为审核中
                $data['status'] = 0;
                $data['info'] = "请先上传资料";
                $this->ajaxReturn($data);
            } else {
                $z = M('pay')->where(array('id' => I('id')))->save(array('status' => $status, 'updatetime_3' => time(), 'updateuser_3' => $_SESSION['admin_name']));
                if ($z) {
                    $data['status'] = 1;
                } else {
                    $data['status'] = 0;
                    $data['info'] = "审核资料失败！";
                }
                $this->ajaxReturn($data);
            }
        } else {
            $this->error('请按规定操作,如按规定操作不行,请联系管理员');
        }
    }

    /**
     *订车订单分配给其他4s店
     */
    public function distribute()
    {
        $this->city_info = M('city')->where(array('isdelete' => 0, 'status' => 1))->field('id,city_name')->select();
        $pay_id = I('id');
        //获取订单信息
        $pay_info = M('pay')->where(array('id' => $pay_id))->find();
        $pay_info['name_4s'] = M('user_4s')->where(array('id' => $pay_info['id_4s']))->getField('name_4s');
        $pay_info['user_name'] = M('user_4s')->where(array('id' => $pay_info['id_4s']))->getField('user_name');
        $this->assign('pay_info', $pay_info);

        //获取分配信息
        $fenpei_info = M('pay_to_user')->where(array('pay_id' => I('id'), 'isdelete' => 0))->select();
        foreach ($fenpei_info as $k => $v) {
            $fenpei_info[$k]['name_4s'] = M('user_4s')->where(array('id' => $v['user_id']))->getField('name_4s');
            $fenpei_info[$k]['user_name'] = M('user_4s')->where(array('id' => $v['user_id']))->getField('user_name');
            $city_id = M('user_4s')->where(array('id' => $v['user_id']))->getField('city_id');
            $fenpei_info[$k]['city_name'] = M('city')->where(array('id' => $city_id))->getField('city_name');
        }
        $this->assign('fenpei_info', $fenpei_info);
        $pay_id = I('pay_id');
        $user_id = I('accounte');
        if (!empty($pay_id) && !empty($user_id)) {
            $dis['id_4s'] = $user_id;
            $dis['is_distribute'] = 1;
            M('pay')->where(array('id' => $pay_id, 'isdelete' => 0))->save($dis);
            $data['id'] = md5(microtime());
            $data['pay_id'] = $pay_id;
            $data['user_id'] = $user_id;
            $zz = M('pay_to_user')->add($data);
            if ($zz) {
                $this->success('分配成功！');
            } else {
                $this->error('分配失败！');
            }
        }
        $this->display();
    }

    /*****************************************************************商城订单***********************************************************************/
    /**
     * @Description:商城订单列表
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/10/31 11:20
     * @Version 2.0
     */
    public function shoplist()
    {
        $time = date('Y-m-d');
        $endtime = strtotime($time . '23:59:59');
        $starttime = $endtime - 3600 * 24 * 31 + 1; // 默认查询一个月内下订单信息
        $this->assign('endtime', !empty($endtime) ? $endtime : null);
        $this->assign('starttime', !empty($starttime) ? $starttime : null);
        $this->display();
    }

    /**
     * @Description:商城订单详情
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/10/31 11:19
     * @Version 2.0
     */
    public function shopdetails()
    {
        $order_info = M('order_info', 'ecs_', 'DB_CONFIG1');
        $order_goods = M('order_goods', 'ecs_', 'DB_CONFIG1');
        $info = $order_info->where(array('order_id' => I('id')))->find();
        $info['add_time'] = date('Y-m-d H:i:s', $info['add_time']);
        $info['money'] = $info['subtotal'] - $info['integral_money'] + 20;
        $info['province_name'] = M('data_province')->where(array('ProvinceID' => $info['province']))->getField('ProvinceName');
        $info['city_name'] = M('data_city')->where(array('CityID' => $info['city']))->getField('CityName');
        $info['district_name'] = M('data_district')->where(array('DistrictID' => $info['district']))->getField('DistrictName');
        if ($info['shipping_id'] == -1) {
            $info['shipping_id'] = "(空)";
        }
        $goods = $order_goods->where(array('order_id' => $info['order_id']))->select();
        $this->assign('info', $info);
        $this->assign('goods', $goods);
        $this->display();

    }

    /*****************************************************************积分兑换*************************************************************************/

    /**
     *积分兑换显示
     */
    public function credits_exchange()
    {
        $time = date('Y-m-d');
        $endtime = strtotime($time . '23:59:59');
        $starttime = $endtime - 3600 * 24 * 31 + 1; // 默认查询一个月内下订单信息
        $this->assign('endtime', !empty($endtime) ? $endtime : null);
        $this->assign('starttime', !empty($starttime) ? $starttime : null);
        $this->display();
    }

    /**
     *修改兑换状态
     */
    public function exchange()
    {
        $id = I('id');
        $flag = I('flag');
        if ($flag == 1) {
            $data['admin_name'] = session('admin_name');
            $data['goods_name'] = M('score_exchange')->where(array('id' => $id))->getField('goods_name');
        } elseif ($flag == 2) {
            $z = M('score_exchange')->where(array('id' => $id))->setField(array('status' => 2, 'cus_name' => I('cus_name'), 'updatetime' => date('Y-m-d H:i:s', time()), 'updateuser' => session('admin_name')));
            if ($z) {
                $data['status'] = 1;
                $data['msg'] = '兑换成功';
            } else {
                $data['status'] = 2;
                $dta['msg'] = '兑换失败';
            }
        } elseif ($flag == 3) {
            $z = M('score_exchange')->where(array('id' => $id))->setField(array('status' => 1, 'updatetime' => date('Y-m-d H:i:s', time()), 'updateuser' => session('admin_name')));
            if ($z) {
                $data['status'] = 1;
                $data['msg'] = '取消兑换成功';
            } else {
                $data['status'] = 2;
                $dta['msg'] = '取消兑换失败';
            }
        }


        $this->ajaxReturn($data);
    }

    /*****************************************************************品牌维护*************************************************************************/
    public function brand_manager()
    {
        $this->display();
    }

    /**
     *品牌维护--添加品牌
     */
    public function brand_manager_add()
    {
        foreach ($_POST['brand_id'] as $key => $value) {
            $data[$key]['id'] = md5(microtime());
            $data[$key]['manager'] = I('manager');
            $data[$key]['tel'] = I('tel');
            $data[$key]['brand_id'] = $value;
            $data[$key]['brand_name'] = M('brand')->where(array('id' => $value))->getField('brand_name');
            $data[$key]['createuser'] = session('admin_name');
        }
        $info = M('brand_manager')->addAll($data);
        if ($info) {
            $this->success('添加成功！');
        } else {
            $this->error('添加失败！');
        }


    }

    /**
     *品牌维护--修改品牌
     */
    public function brand_manager_save()
    {
        $_POST['updatetime'] = date('Y-m-d H:i:s', time());
        $_POST['updateuser'] = session('admin_name');
        foreach ($_POST['brand_id'] as $key => $value) {

            $_POST['brand_name'] = M('brand')->where(array('id' => $value))->getField('brand_name');

        }
        /* $_POST['brand_name']=M('brand')->where(array('id'=>$_POST['brand_id']))->getField('brand_name');*/
        M('brand_manager')->create();
        $info = M('brand_manager')->where(array('id' => I('id')))->save();
        if ($info) {
            $data['status'] = 1;
            $data['msg'] = '修改成功！';
        } else {
            $data['status'] = 2;
            $data['msg'] = '修改失败！';
        }
        $this->ajaxReturn($data);
    }

    /**
     *品牌维护--删除品牌
     */
    public function brand_manager_delete()
    {
        $data['updatetime'] = date('Y-m-d H:i:s', time());
        $data['updateuser'] = session('admin_name');
        $data['isdelete'] = 1;
        $info = M('brand_manager')->where(array('id' => I('id')))->save($data);
        if ($info) {
            $data['status'] = 1;
            $data['msg'] = '删除成功！';
        } else {
            $data['status'] = 2;
            $data['msg'] = '删除失败！';
        }
        $this->ajaxReturn($data);
    }
    /*****************************************************************优惠券*************************************************************************/
    /**
     * 优惠券
     */
    public function ticket()
    {
        $this->display();
    }

    /**
     * 优惠券添加
     */
    public function ticketadd()
    {
        $this->display();
    }

    /**************************************************************活动订单****************************************************************************/
    /**
     * @Description:活动订单列表也
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/10/21 14:30
     * @Version 2.0
     */
    public function activity_order()
    {
        $time = date('Y-m-d');
        $endtime = strtotime($time . '23:59:59');
        $starttime = $endtime - 3600 * 24 * 31 + 1; // 默认查询一个月内下订单信息
        $this->assign('endtime', !empty($endtime) ? $endtime : null);
        $this->assign('starttime', !empty($starttime) ? $starttime : null);
        $this->display();
    }

    /**
     * @Description:客服操作兑换订单（第一次）
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/10/25 10:36
     * @Version 2.0
     */
    public function cus_action_dh()
    {
        $pay = M('pay');
        $data['id'] = I('id');
        $data['status'] = 8;
        $pay->create($data);
        $pay->save($data);

        $datac['id'] = md5(microtime());
        $datac['pay_id'] = I('id');
        $datac['cus_name'] = I('cus_name');
        $datac['cus_remark'] = I('cus_remark');
        $datac['type'] = 2;
        $datac['createuser']=session('admin_name');
        $info = M('pay_cus')->add($datac);
        if ($info) {
            $info1['status'] = 1;
            $info1['msg'] = '操作成功！';
        } else {
            $info1['status'] = 2;
            $info1['msg'] = '操作失败！';
        }
        $this->ajaxReturn($info1);

    }

    /**
     * @Description:客服操作兑换订单（新增）
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/10/25 10:37
     * @Version 2.0
     */
    public function cus_action_dh_add()
    {
        $datac['id'] = md5(microtime());
        $datac['pay_id'] = I('id');
        $datac['cus_name'] = I('cus_name');
        $datac['cus_remark'] = I('cus_remark');
        $datac['createuser']=session('admin_name');
        $datac['type'] = 2;
        $info = M('pay_cus')->add($datac);
        if ($info) {
            $info1['status'] = 1;
            $info1['msg'] = '操作成功！';
        } else {
            $info1['status'] = 2;
            $info1['msg'] = '操作失败！';
        }
        $this->ajaxReturn($info1);

    }

    /**************************************************************丽车坊（镇江）****************************************************************************/

    /**
     * @Description:丽车坊订单
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/10/31 11:44
     * @Version 2.0
     */
    public function liche_order()
    {
        $time = date('Y-m-d');
        $endtime = strtotime($time . '23:59:59');
        $starttime = $endtime - 3600 * 24 * 31 + 1; // 默认查询一个月内下订单信息
        $this->assign('endtime', !empty($endtime) ? $endtime : null);
        $this->assign('starttime', !empty($starttime) ? $starttime : null);
        $this->display();
    }

}



