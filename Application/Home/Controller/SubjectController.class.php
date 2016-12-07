<?php
namespace Home\Controller;

use Think\Controller;

/**
 * Class OrderController
 * @package Home\Controller
 */
class SubjectController extends CommonController
{
    /**
     * 别克专题页
     * @return [type] [description]
     */
    public function bieke()
    {
        $this->display();
    }

    /**
     * 口号征集页
     * @return [type] [description]
     */
    public function slogan()
    {
        $this->display();
    }

    /**
     * 818活动页
     * @return [type] [description]
     */
    public function hd818()
    {
        $this->display();
    }

    /**
     * 中秋活动页
     * @return [type] [description]
     */
    public function zqzt()
    {
        $this->display();
    }

    public function yr11()
    {
        $this->display();
    }

    /**
     * @Description:双十一活动
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/10/26 13:42
     * @Version 2.0
     */
    public function double_eleven()
    {
        $this->display();
    }

    /**
     * 获取品牌接口
     * @return [type] [description]
     */
    public function brand()
    {
        $info = M('brand')->where(array('isdelete' => 0))->order('alif')->select();
        foreach ($info as $key => $value) {
            $data[$key]['name'] = $value['brand_name'];
            $data[$key]['value'] = $value['id'];
        }
        $this->ajaxReturn($data);
    }

    /**
     * 获取车型接口
     * @return [type] [description]
     */
    public function car_model()
    {
        $brand_id = I('brand');
        $info = M('car_model')->where(array('brand_id' => $brand_id, 'isdelete' => 0))->select();
        foreach ($info as $key => $value) {
            $data[$key]['name'] = $value['car_model_name'];
            $data[$key]['value'] = $value['id'];
        }
        $this->ajaxReturn($data);
    }

    /**
     * 获取车款接口
     * @return [type] [description]
     */
    public function car()
    {
        $car_model_id = I('model');
        $info = M('car')->where(array('car_model_id' => $car_model_id,'status'=>1, 'isdelete' => 0))->order('car_name desc')->select();
        foreach ($info as $key => $value) {
            $data[$key]['name'] = $value['car_name'];
            $data[$key]['value'] = $value['id'];
        }
        $this->ajaxReturn($data);
    }

    /**
     * 获取车款接口
     * @return [type] [description]
     */
    public function car_query()
    {
        $car_model_id = I('model');
        $info = D('view_car_price')->where(array('car_model_id' => $car_model_id, 'isdelete' => 0))->distinct(true)->field('car_name,car_id')->order('car_name desc')->select();
        foreach ($info as $key => $value) {
            $data[$key]['name'] = $value['car_name'];
            $data[$key]['value'] = $value['car_id'];
        }
        $this->ajaxReturn($data);
    }

    /**
     * 获取外观和内饰颜色的接口
     * @return [type] [description]
     */
    public function color()
    {
        $car_id = I('car_id');
        $exterior_info = D('view_car_price')->where(array('car_id' => $car_id, 'isdelete' => 0))->distinct(true)->field('exterior_color_id,exterior_color_name,exterior_color_value')->select();
        foreach ($exterior_info as $key => $value) {
            $data_ex[$key]['exterior_color_id'] = $value['exterior_color_id'];
            $data_ex[$key]['exterior_color_name'] = $value['exterior_color_name'];
            $data_ex[$key]['exterior_color_value'] = $value['exterior_color_value'];
        }
        $interior_info = D('view_car_price')->where(array('car_id' => $car_id, 'isdelete' => 0))->distinct(true)->field('interior_color_id,interior_color_name,interior_color_value')->select();
        foreach ($interior_info as $key => $value) {
            $data_in[$key]['interior_color_id'] = $value['interior_color_id'];
            $data_in[$key]['interior_color_name'] = $value['interior_color_name'];
            $data_in[$key]['interior_color_value'] = $value['interior_color_value'];
        }
        $info['exterior_color'] = $data_ex;
        $info['interior_color'] = $data_in;
        $this->ajaxReturn($info);
    }

    /**
     *获取价格
     */
    public function getprice()
    {
        $data['exterior_color_id'] = I('exterior_color_id');
        $data['interior_color_id'] = I('interior_color_id');
        $data['isdelete'] = 0;
        $info = D('view_car_price')->where($data)->order('low_price asc')->find();
        $this->ajaxReturn($info);
    }

    /**
     * 根据车款和外观颜色改变汽车图片
     * @param {string} $car_id 参数一的说明
     * @param {string} $ex_id 参数二的说明
     * @return array
     */
    public function get_car_pic()
    {
        if (!empty($_POST)) {
            $ex_id = I('exterior_color_id');
            //$ex_id = I('exterior_color_id');
            $color_image = M("car_exterior_color_image")->where(array('isdelete' => 0, 'exterior_color_id' => $ex_id))->find();
            $data['ex_img'] = 'http://yichenghui.oss-cn-shanghai.aliyuncs.com/gouchehui/web' . str_replace('type', 'big', $color_image['imgurl']);
            $this->ajaxReturn($data);
        }
    }

    /**
     * 普通用户添加计划购车
     * @return [type] [description]
     */
    public function carplan_add()
    {
        if (isset($_SESSION['user_id']) and $_SESSION['user_id'] != '') {
            $carplan = D('CarPlan'); // 实例化User对象
            /* $_POST['from_plan']='活动页面-别克专场';*/
            if (!$carplan->create($_POST)) { // 指定新增数据
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $data['status'] = 1;
                $data['msg'] = $carplan->getError();
            } else {
                $_POST['user_id'] = session('user_id');
                $info = $carplan->car_find($_POST);
                if (empty($info)) {
                    if ($_POST['buy_time'] == 0) {
                        $data['status'] = 1;
                        $data['msg'] = '请选择购车时间';
                        $this->ajaxReturn($data);
                    }

                    $count = $carplan->where(array('user_id' => session('user_id'), 'isdelete' => 0))->count();
                    if ($count < 3) {
                        // 验证通过 可以进行其他数据操作
                        if ($carplan->car_add()) {
                            $data['status'] = 2;
                            $data['msg'] = '添加计划购车成功';
                        } else {
                            $data['status'] = 1;
                            $data['msg'] = '添加计划购车失败';
                        }
                    } else {
                        $data['status'] = 1;
                        $data['msg'] = '计划购车不能超过3辆！';
                    }

                } else {
                    $data['status'] = 1;
                    $data['msg'] = '该车款已经在计划中！';
                }
            }
        } else {
            $data['status'] = 1;
            $data['msg'] = '请先登录！';
        }
        $this->ajaxReturn($data);
    }

    /**
     * 活动点击详情
     * @return [type] [description]
     */
    public function activesLog()
    {
        if (IS_POST) {
            //判断用户是否登录
            if ($_SESSION['user_id'] != '') {
                $data['user_id'] = $_SESSION['user_id'];
            }
            if (I('cpid') == '') {
                $ret['status'] = 0;
                $ret['info'] = "车款不能为空";
                $this->ajaxReturn($ret);
            }
            $data['cpid'] = I('cpid');
            $data['ip'] = get_client_ip();
            $find = M('actives_log')->where($data)->find();
            //判断相同的车款内外饰颜色这个IP是否点击过
            if ($find) {
                $save['update_time'] = time();
                $save['click_num'] = $find['click_num'] + 1;
                $save['ip_click'] = $find['ip_click'] + 1;
                $res = M('actives_log')->where($data)->save($save);
                if ($res) {
                    $ret['status'] = 1;
                    $ret['info'] = "活动记录编辑成功";
                } else {
                    $ret['status'] = 0;
                    $ret['info'] = "活动记录编辑失败";
                }
            } else {
                //如果之前没有添加过就增加
                $data['id'] = md5(microtime());
                $data['create_time'] = time();
                $data['click_num'] = 1;
                $data['ip_click'] = 1;
                $add = M('actives_log')->add($data);
                if ($add) {
                    $ret['status'] = 1;
                    $ret['info'] = "活动记录添加成功";
                } else {
                    $ret['status'] = 0;
                    $ret['info'] = "活动记录添加失败";
                }
            }
            $this->ajaxReturn($ret);
        } else {
            $this->error('请提交正确的数据');
        }
    }

    /**
     * 活动列表
     * @return [type] [description]
     */
    public function activesList()
    {
        //判断是否有数据提交和秘钥是否正确
        if (isset($_POST['brand_id']) and $_POST['brand_id'] != '') {

            $arr['brand_id'] = I('brand_id');
        }
        if (isset($_POST['car_model_id']) and $_POST['car_model_id'] != '') {
            $arr['car_model_id'] = I('car_model_id');
        }
        if (isset($_POST['car_id']) and $_POST['car_id'] != '') {
            $arr['car_id'] = I('car_id');
        }
        $count = M('view_actives_log')->where($arr)->field('car_name')->count();
        $Page = new \Think\PageAjax($count, 15);
        $list = M('view_actives_log')->where($arr)->field('user_name,car_name,car_model_name,brand_name,click_num,ip,ip_click,exterior_color_name,interior_color_name,create_time')->limit($Page->firstRow . ',' . $Page->listRows)->order('create_time desc')->select();
        foreach ($list as $key => $value) {
            $list[$key]['create_time'] = date('Y-m-d', $value['create_time']);
        }
        if ($list) {
            $data['status'] = 1;
            $data['list'] = $list;
            $data['ip_count'] = $count;
            $data['page'] = $Page->show();
            $data['count'] = M('view_actives_log')->where($arr)->sum('click_num');
        } else {
            $data['status'] = 0;
            $data['info'] = '暂无数据';
        }
        $this->ajaxReturn($data);
    }

    /**
     * 添加活动报名用户
     * @return [status] [操作状态]
     * @return [msg] [状态信息]
     */
    public function user_activity_add()
    {
        if (empty($_POST['brand_id'])) {
            $data['status'] = 2;
            $data['msg'] = '品牌不能为空！';
        } elseif (empty($_POST['car_model_id'])) {
            $data['status'] = 2;
            $data['msg'] = '车型不能为空！';
        } elseif (empty($_POST['car_id'])) {
            $data['status'] = 2;
            $data['msg'] = '车款不能为空！';
        } else {
            $user = M('user_activity');
            $_POST['id'] = md5(microtime());
            $_POST['createuser'] = '游客';
            $tel = I('tel');
            if ($_POST['flag'] == 1) {
                if ($tel == $_SESSION['tel']) {
                    //判读验证码是否过期
                    $code = I('code');
                    //设置验证码过期时间
                    if ($_SESSION['lost_time'] < time()) {
                        unset($_SESSION['lost_time']);
                        unset($_SESSION['code']);
                        $data['status'] = 2;
                        $data['msg'] = '验证码有效期过期！';
                    } else {
                        if ($code == session('code')) {
                            $info = M('user_activity')->where(array('tel' => $_POST['tel'], 'exterior_color_id' => $_POST['exterior_color_id'], 'interior_color_id' => $_POST['interior_color_id'], 'activity_number' => $_POST['activity_number'], 'activity_price' => $_POST['activity_price'], 'isdelete' => 0))->find();

                            if (empty($info)) {
                                $user->create($_POST);
                                if ($user->add()) {
                                    //写入用户表中
                                    $z = M('user_general')->where(array('user_name' => I('tel'), 'isdelete' => 0))->find();
                                    if (empty($z)) {
                                        $datauser['id'] = md5(microtime());
                                        $datauser['user_name'] = I('tel');
                                        $datauser['tel'] = I('tel');
                                        $datauser['password'] = md5('123456');
                                        $datauser['role'] = 1;
                                        $datauser['status'] = 1;
                                        $datauser['remark'] = I('activity_name');
                                        M('user_general')->add($datauser);
                                        //发送成功短信(添加新用户)
                                        $mobile = $_POST['tel'];
                                        $tpl_id = '17531';//短信模板ID
                                        $tpl_value = '';//变量名和变量值对
                                        $ress = sendMessage($mobile, $tpl_id, $tpl_value);
                                        $reslut = json_decode($ress, true);
                                        $data['error_code'] = $reslut['error_code'];
                                        $data['reason'] = $reslut['reason'];
                                    } else {
                                        //发送成功短信(添加新用户)
                                        $mobile = $_POST['tel'];
                                        $tpl_id = '18977';//短信模板ID
                                        $tpl_value = '';//变量名和变量值对
                                        $ress = sendMessage($mobile, $tpl_id, $tpl_value);
                                        $reslut = json_decode($ress, true);
                                        $data['error_code'] = $reslut['error_code'];
                                        $data['reason'] = $reslut['reason'];
                                    }

                                    //返回信息
                                    $data['status'] = 1;
                                    $data['msg'] = '报名成功！';
                                } else {
                                    $data['status'] = 2;
                                    $data['msg'] = '报名失败！';
                                }

                            } else {
                                $data['status'] = 2;
                                $data['msg'] = '该信息已经存在！';
                            }
                        } else {
                            $data['status'] = 2;
                            $data['msg'] = '验证码错误！';
                        }

                    }
                } else {
                    $data['status'] = 2;
                    $data['msg'] = '请输入发送短信的手机号！';
                }
            } elseif ($_POST['flag'] == 2) {
                if (md5(I('code') . I('tel')) == I('md5code')) {
                    $info = M('user_activity')->where(array('tel' => $_POST['tel'], 'car_id' => $_POST['car_id'], 'activity_number' => $_POST['activity_number'], 'isdelete' => 0))->find();
                    if (empty($info)) {
                        $user->create($_POST);
                        if ($user->add()) {
                            $data['status'] = 1;
                            $data['msg'] = '报名成功！';
                        } else {
                            $data['status'] = 2;
                            $data['msg'] = '报名失败！';
                        }
                    }
                } else {
                    $data['status'] = 2;
                    $data['msg'] = '验证码错误！';
                }
            } elseif ($_POST['flag'] == 3) {
                $_POST['tel'] = M('user_general')->where(array('id' => session('user_id')))->getField('tel');
                $info = M('user_activity')->where(array('tel' => $_POST['tel'], 'exterior_color_id' => $_POST['exterior_color_id'], 'interior_color_id' => $_POST['interior_color_id'], 'activity_number' => $_POST['activity_number'], 'activity_price' => $_POST['activity_price'], 'isdelete' => 0))->find();
                if (empty($info)) {
                    $user->create($_POST);
                    if ($user->add()) {
                        //发优惠券
                        $tick = M('ticket_user')->where(array('isdelete' => 0, 'from_ticket' => '中秋活动', 'user_id' => session('user_id')))->find();
                        if (empty($tick)) {
                            $db = M('');
                            $db->execute("call ticket_user(4,'" . session('user_id') . "','中秋活动',9)");
                            //报名成功短信
                            $mobile = $_POST['tel'];
                            $tpl_id = '18977';//短信模板ID
                            $tpl_value = '';//变量名和变量值对
                            $ress = sendMessage($mobile, $tpl_id, $tpl_value);
                            $reslut = json_decode($ress, true);
                            $data['error_code'] = $reslut['error_code'];
                            $data['reason'] = $reslut['reason'];
                        }
                        //返回信息
                        $data['status'] = 1;
                        $data['msg'] = '报名成功！';
                    } else {
                        $data['status'] = 2;
                        $data['msg'] = '报名失败！';
                    }

                } else {
                    $data['status'] = 2;
                    $data['msg'] = '该信息已经存在！';
                }
            } elseif ($_POST['flag'] == 4) {
                if (md5(I('code') . I('tel')) == I('md5code')) {
                    $info = M('user_activity')->where(array('tel' => $_POST['tel'], 'exterior_color_id' => $_POST['exterior_color_id'], 'interior_color_id' => $_POST['interior_color_id'], 'activity_number' => $_POST['activity_number'], 'activity_price' => $_POST['activity_price'], 'isdelete' => 0))->find();

                    if (empty($info)) {
                        $user->create($_POST);
                        if ($user->add()) {
                            //写入用户表中
                            $z = M('user_general')->where(array('user_name' => I('tel'), 'isdelete' => 0))->find();
                            if (empty($z)) {
                                $datauser['id'] = md5(microtime());
                                $datauser['user_name'] = I('tel');
                                $datauser['tel'] = I('tel');
                                $datauser['password'] = md5('123456');
                                $datauser['role'] = 1;
                                $datauser['status'] = 1;
                                $datauser['remark'] = I('activity_name');
                                M('user_general')->add($datauser);
                                //发优惠券
                                $tick = M('ticket_user')->where(array('isdelete' => 0, 'from_ticket' => '中秋活动', 'user_id' => $datauser['id']))->find();
                                if (empty($tick)) {
                                    $db = M('');
                                    $db->execute("call ticket_user(4,'" . $datauser['id'] . "','中秋活动',9)");
                                    //发送添加新用户短信
                                    $mobile = $_POST['tel'];
                                    $tpl_id = '17531';//短信模板ID
                                    $tpl_value = '';//变量名和变量值对
                                    $ress = sendMessage($mobile, $tpl_id, $tpl_value);
                                    $reslut = json_decode($ress, true);
                                    $data['error_code'] = $reslut['error_code'];
                                    $data['reason'] = $reslut['reason'];
                                }

                            } else {
                                //发优惠券
                                $tick = M('ticket_user')->where(array('isdelete' => 0, 'from_ticket' => '中秋活动', 'user_id' => $z['id']))->find();
                                if (empty($tick)) {
                                    $db = M('');
                                    $db->execute("call ticket_user(4,'" . $z['id'] . "','中秋活动',9)");
                                    //发送成功报名短信
                                    $mobile = $_POST['tel'];
                                    $tpl_id = '18977';//短信模板ID
                                    $tpl_value = '';//变量名和变量值对
                                    $ress = sendMessage($mobile, $tpl_id, $tpl_value);
                                    $reslut = json_decode($ress, true);
                                    $data['error_code'] = $reslut['error_code'];
                                    $data['reason'] = $reslut['reason'];
                                }

                            }

                            //返回信息
                            $data['status'] = 1;
                            $data['msg'] = '报名成功！';
                        } else {
                            $data['status'] = 2;
                            $data['msg'] = '报名失败！';
                        }

                    } else {
                        $data['status'] = 2;
                        $data['msg'] = '该信息已经存在！';
                    }
                } else {
                    $data['status'] = 2;
                    $data['msg'] = '验证码错误！';
                }
            }

            $this->ajaxReturn($data);
        }
    }

    /**
     * @Description:添加报名用户（改版）
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/9/21 9:42
     * @Version 2.0
     */
    public function user_activity_newadd()
    {
        if (empty($_POST['brand_id'])) {
            $data['status'] = 2;
            $data['msg'] = '品牌不能为空！';
        } elseif (empty($_POST['car_model_id'])) {
            $data['status'] = 2;
            $data['msg'] = '车型不能为空！';
        } elseif (empty($_POST['car_id'])) {
            $data['status'] = 2;
            $data['msg'] = '车款不能为空！';
        } else {
            $user = M('user_activity');
            $_POST['id'] = md5(microtime());
            $_POST['createuser'] = '游客';
            $tel = I('tel');
            $array_act = I('activities');//发这张券的活动ID
            $activity_id = I('activity_id');//发券活动的ID(网页端)
            $ticket_id = I('ticket_id');//发的券的ID
            $from_ticket = I('from_ticket');//券的来源（中文）
            if ($_POST['flag'] == 1) {
                if ($tel == $_SESSION['tel']) {
                    //判读验证码是否过期
                    $code = I('code');
                    //设置验证码过期时间
                    if ($_SESSION['lost_time'] < time()) {
                        unset($_SESSION['lost_time']);
                        unset($_SESSION['code']);
                        $data['status'] = 2;
                        $data['msg'] = '验证码有效期过期！';
                    } else {
                        if ($code == session('code')) {
                            $info = M('user_activity')->where(array('tel' => $_POST['tel'], 'exterior_color_id' => $_POST['exterior_color_id'], 'interior_color_id' => $_POST['interior_color_id'], 'activity_number' => $_POST['activity_number'], 'activity_price' => $_POST['activity_price'], 'isdelete' => 0))->find();

                            if (empty($info)) {
                                $user->create($_POST);
                                if ($user->add()) {
                                    //写入用户表中
                                    $z = M('user_general')->where(array('user_name' => I('tel'), 'isdelete' => 0))->find();
                                    if (empty($z)) {
                                        $datauser['id'] = md5(microtime());
                                        $datauser['user_name'] = I('tel');
                                        $datauser['tel'] = I('tel');
                                        $datauser['password'] = md5('123456');
                                        $datauser['role'] = 1;
                                        $datauser['status'] = 1;
                                        $datauser['remark'] = I('activity_name');
                                        M('user_general')->add($datauser);
                                        //发送成功短信(添加新用户)
                                        $mobile = $_POST['tel'];
                                        $tpl_id = '17531';//短信模板ID
                                        $tpl_value = '';//变量名和变量值对
                                        $ress = sendMessage($mobile, $tpl_id, $tpl_value);
                                        $reslut = json_decode($ress, true);
                                        $data['error_code'] = $reslut['error_code'];
                                        $data['reason'] = $reslut['reason'];
                                    } else {
                                        //发送成功短信(添加新用户)
                                        $mobile = $_POST['tel'];
                                        $tpl_id = '18977';//短信模板ID
                                        $tpl_value = '';//变量名和变量值对
                                        $ress = sendMessage($mobile, $tpl_id, $tpl_value);
                                        $reslut = json_decode($ress, true);
                                        $data['error_code'] = $reslut['error_code'];
                                        $data['reason'] = $reslut['reason'];
                                    }

                                    //返回信息
                                    $data['status'] = 1;
                                    $data['msg'] = '报名成功！';
                                } else {
                                    $data['status'] = 2;
                                    $data['msg'] = '报名失败！';
                                }

                            } else {
                                $data['status'] = 2;
                                $data['msg'] = '该信息已经存在！';
                            }
                        } else {
                            $data['status'] = 2;
                            $data['msg'] = '验证码错误！';
                        }

                    }
                } else {
                    $data['status'] = 2;
                    $data['msg'] = '请输入发送短信的手机号！';
                }
            } elseif ($_POST['flag'] == 2) {
                if (md5(I('code') . I('tel')) == I('md5code')) {
                    $info = M('user_activity')->where(array('tel' => $_POST['tel'], 'car_id' => $_POST['car_id'], 'activity_number' => $_POST['activity_number'], 'isdelete' => 0))->find();
                    if (empty($info)) {
                        $user->create($_POST);
                        if ($user->add()) {
                            $data['status'] = 1;
                            $data['msg'] = '报名成功！';
                        } else {
                            $data['status'] = 2;
                            $data['msg'] = '报名失败！';
                        }
                    }
                } else {
                    $data['status'] = 2;
                    $data['msg'] = '验证码错误！';
                }
            } elseif ($_POST['flag'] == 3) {
                $_POST['tel'] = M('user_general')->where(array('id' => session('user_id')))->getField('tel');
                $info = M('user_activity')->where(array('tel' => $_POST['tel'], 'exterior_color_id' => $_POST['exterior_color_id'], 'interior_color_id' => $_POST['interior_color_id'], 'activity_number' => $_POST['activity_number'], 'activity_price' => $_POST['activity_price'], 'isdelete' => 0))->find();
                if (empty($info)) {
                    $user->create($_POST);
                    if ($user->add()) {
                        //发优惠券
                        $tick = M('ticket_user')->where(array('isdelete' => 0, 'activity_id' => array('in', $array_act), 'user_id' => session('user_id')))->find();
                        if (empty($tick)) {
                            $db = M('');
                            $db->execute("call ticket_user(" . $ticket_id . ",'" . session('user_id') . "','" . $from_ticket . "'," . $activity_id . ")");
                            //报名成功短信
                            $mobile = $_POST['tel'];
                            $tpl_id = '18977';//短信模板ID
                            $tpl_value = '';//变量名和变量值对
                            $ress = sendMessage($mobile, $tpl_id, $tpl_value);
                            $reslut = json_decode($ress, true);
                            $data['error_code'] = $reslut['error_code'];
                            $data['reason'] = $reslut['reason'];
                        }
                        //返回信息
                        $data['status'] = 1;
                        $data['msg'] = '报名成功！';
                    } else {
                        $data['status'] = 2;
                        $data['msg'] = '报名失败！';
                    }

                } else {
                    $data['status'] = 2;
                    $data['msg'] = '该信息已经存在！';
                }
            } elseif ($_POST['flag'] == 4) {
                if (md5(I('code') . I('tel')) == I('md5code')) {
                    $info = M('user_activity')->where(array('tel' => $_POST['tel'], 'exterior_color_id' => $_POST['exterior_color_id'], 'interior_color_id' => $_POST['interior_color_id'], 'activity_number' => $_POST['activity_number'], 'activity_price' => $_POST['activity_price'], 'isdelete' => 0))->find();

                    if (empty($info)) {
                        $user->create($_POST);
                        if ($user->add()) {
                            //写入用户表中
                            $z = M('user_general')->where(array('user_name' => I('tel'), 'isdelete' => 0))->find();
                            if (empty($z)) {
                                $datauser['id'] = md5(microtime());
                                $datauser['user_name'] = I('tel');
                                $datauser['tel'] = I('tel');
                                $datauser['password'] = md5('123456');
                                $datauser['role'] = 1;
                                $datauser['status'] = 1;
                                $datauser['remark'] = I('activity_name');
                                M('user_general')->add($datauser);
                                //发优惠券
                                $tick = M('ticket_user')->where(array('isdelete' => 0, 'activity_id' => array('in', $array_act), 'user_id' => $datauser['id']))->find();
                                if (empty($tick)) {
                                    $db = M('');
                                    $db->execute("call ticket_user(" . $ticket_id . ",'" . $datauser['id'] . "','" . $from_ticket . "'," . $activity_id . ")");
                                    //发送添加新用户短信
                                    $mobile = $_POST['tel'];
                                    $tpl_id = '17531';//短信模板ID
                                    $tpl_value = '';//变量名和变量值对
                                    $ress = sendMessage($mobile, $tpl_id, $tpl_value);
                                    $reslut = json_decode($ress, true);
                                    $data['error_code'] = $reslut['error_code'];
                                    $data['reason'] = $reslut['reason'];
                                }

                            } else {
                                //发优惠券
                                $tick = M('ticket_user')->where(array('isdelete' => 0, 'activity_id' => array('in', $array_act), 'user_id' => $z['id']))->find();
                                if (empty($tick)) {
                                    $db = M('');
                                    $db->execute("call ticket_user(" . $ticket_id . ",'" . $z['id'] . "','" . $from_ticket . "'," . $activity_id . ")");
                                    //发送成功报名短信
                                    $mobile = $_POST['tel'];
                                    $tpl_id = '18977';//短信模板ID
                                    $tpl_value = '';//变量名和变量值对
                                    $ress = sendMessage($mobile, $tpl_id, $tpl_value);
                                    $reslut = json_decode($ress, true);
                                    $data['error_code'] = $reslut['error_code'];
                                    $data['reason'] = $reslut['reason'];
                                }

                            }

                            //返回信息
                            $data['status'] = 1;
                            $data['msg'] = '报名成功！';
                        } else {
                            $data['status'] = 2;
                            $data['msg'] = '报名失败！';
                        }

                    } else {
                        $data['status'] = 2;
                        $data['msg'] = '该信息已经存在！';
                    }
                } else {
                    $data['status'] = 2;
                    $data['msg'] = '验证码错误！';
                }
            }

            $this->ajaxReturn($data);
        }

    }

    /**
     * @Description:别克活动特价车款
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/9/22 9:53
     * @Version 2.0
     */
    public function activity_bieke()
    {
        if (empty($_POST['brand_id'])) {
            $data['status'] = 2;
            $data['msg'] = '品牌不能为空！';
        } elseif (empty($_POST['car_model_id'])) {
            $data['status'] = 2;
            $data['msg'] = '车型不能为空！';
        } elseif (empty($_POST['car_id'])) {
            $data['status'] = 2;
            $data['msg'] = '车款不能为空！';
        } else {
            $user = M('user_activity');
            $_POST['id'] = md5(microtime());
            $_POST['createuser'] = '游客';
            $tel = I('tel');
            if ($_POST['flag'] == 2) {
                if (md5(I('code') . $tel) == I('md5code')) {
                    $info = M('user_activity')->where(array('tel' => $_POST['tel'], 'car_id' => $_POST['car_id'], 'activity_number' => $_POST['activity_number'], 'isdelete' => 0))->find();
                    if (empty($info)) {
                        $user->create($_POST);
                        if ($user->add()) {
                            //写入用户表中
                            $z = M('user_general')->where(array('user_name' => I('tel'), 'isdelete' => 0))->find();
                            if (empty($z)) {
                                $datauser['id'] = md5(microtime());
                                $datauser['user_name'] = I('tel');
                                $datauser['tel'] = I('tel');
                                $datauser['password'] = md5('123456');
                                $datauser['role'] = 1;
                                $datauser['status'] = 1;
                                $datauser['remark'] = I('activity_name');
                                M('user_general')->add($datauser);

                                //发送添加新用户短信
                                $mobile = $_POST['tel'];
                                $tpl_id = '20909';//短信模板ID
                                $tpl_value = '';//变量名和变量值对
                                $ress = sendMessage($mobile, $tpl_id, $tpl_value);
                                $reslut = json_decode($ress, true);
                                $data['error_code'] = $reslut['error_code'];
                                $data['reason'] = $reslut['reason'];

                            }
                            $data['status'] = 1;
                            $data['msg'] = '报名成功！稍后有购车惠客服与您联系！';
                        } else {
                            $data['status'] = 2;
                            $data['msg'] = '报名失败！';
                        }
                    } else {
                        $data['status'] = 2;
                        $data['msg'] = '该信息已存在！';
                    }
                } else {
                    $data['status'] = 2;
                    $data['msg'] = '验证码错误！';
                }
            }
        }

        $this->ajaxReturn($data);
    }

    /**
     * 获取该活动最新报名用户信息
     * @return [type] [description]
     */
    public function user_activity_info()
    {
        $number = I('activity_number');
        $count = M('user_activity')->where(array('activity_number' => $number, 'isdelete' => 0))->count();
        $info = M('user_activity')->where(array('activity_number' => $number, 'isdelete' => 0))->order('createtime', desc)->limit(0, 15)->select();
        foreach ($info as $key => $value) {
            $info[$key]['tel'] = substr_replace($value['tel'], '****', 5, 4);
        }
        $data['info'] = $info;
        $data['count'] = $count + 51;
        $this->ajaxReturn($data);
    }

    /**
        * @Description:丽车坊控制器
        * @Return:
        * @Author: 孙磊
        * @Date: 2016/11/2 14:02
        * @Version 2.0
        */

    public function lichefang(){
        //获得登录信息
        if (isset($_SESSION['user_id']) and $_SESSION['user_id'] != '') {
            $userinfo = D('UserGeneral')->user_find(array('id' => $_SESSION['user_id']));
            $userinfo['msg'] = M("user_message")->where(array('user_id' => $userinfo['id'], 'isdelete' => 0))->count();
        }

        $this->assign('userinfo', $userinfo);
        $this->display();
    }
}
