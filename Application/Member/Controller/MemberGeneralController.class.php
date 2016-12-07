<?php
namespace Member\Controller;

use Think\Controller;

/**
 * Class MemberGeneralController前台会员中心
 * @package Member\Controller
 */
class MemberGeneralController extends Controller
{
    /**
     *普通用户初始化---将cookie转化为session
     */
    public function _initialize()
    {
        if (isset($_COOKIE['token']) and !empty($_COOKIE['token'])) {
            $user_id = authcode($_COOKIE['token'], 'DECODE', 'gouchehui');
            $user_name = M('user_general')->where(array('id' => $user_id))->getField('user_name');
            session('user_id', $user_id);
            session('user_name', $user_name);
        } else {
            unset($_SESSION['user_id']);
            $this->error('请先登录普通用户账号!', U('Public/login'));

        }

    }

    /**
     *进入该控制器默认跳转的页面
     */
    public function index()
    {
        $this->redirect('n_uc_index');
    }

    public function n_uc_index()
    {
        $this->display();
    }

    /**
     *功能：消息统计接口
     *作者：任红灯
     *时间：20165181045
     */
    public function orderMesCount()
    {
        $order_mes_count = M('pay')->where(array('user_id' => session('user_id'), 'isread' => 1, 'isdelete' => 0))->count();
        $info = $order_mes_count;
        $this->ajaxReturn($info);
    }

    /**
     *普通用户------账号资料
     */
    public function member_accinfo()
    {
        $info = D('UserGeneral')->user_find(array('id' => session('user_id')));
        if (!empty($info['head_url'])) {
            $info['head_url'] = OSS . str_replace('type', 'small', $info['head_url']);
        } else {
            $info['head_url'] = '/Public/Home/images/headimg.png';
        }
        $pay_query_count = M('pay')->where(array('user_id' => session('user_id'), 'pay_obj' => 1, 'isread' => 1, 'isdelete' => 0))->count();
        $info['pay_query_count'] = $pay_query_count;
        $pay_order_count = M('pay')->where(array('user_id' => session('user_id'), 'pay_obj' => 2, 'isread' => 1, 'isdelete' => 0))->count();
        $info['pay_order_count'] = $pay_order_count;
        $pay_count = M('pay')->where(array('user_id' => session('user_id'), 'isread' => 1, 'isdelete' => 0))->count();
        $info['pay_count'] = $pay_count;
        $sys_count = M('user_message')->where(array('user_id' => session('user_id'), 'isread' => 1, 'isdelete' => 0))->count();
        $info['sys_count'] = $sys_count;
        $info['messagecount'] = $pay_count + $sys_count;
        $info['attention'] = M('user_attention_car_model')->where(array('user_id' => session('user_id'), 'isdelete' => 0))->count();

        if ($info['total_jifen']>=0)
        {
            if($info['total_jifen']<=200)
            {
                $info['grade'] = 1;
                $info['denominator'] = 200;
            }elseif ($info['total_jifen']<=500)
            {
                $info['grade'] = 2;
                $info['denominator'] = 500;
            }elseif ($info['total_jifen']<=800)
            {
                $info['grade'] = 3;
                $info['denominator'] = 800;
            }elseif ($info['total_jifen'] <= 1000)
            {
                $info['grade'] = 4;
                $info['denominator'] = 1000;
            }elseif ($info['total_jifen'] <= 2000)
            {
                $info['grade'] = 5;
                $info['denominator'] = 2000;
            }elseif ($info['total_jifen'] <= 5000)
            {
                $info['grade'] = 6;
                $info['denominator'] = 5000;
            }elseif ($info['total_jifen'] <= 8000)
            {
                $info['grade'] = 7;
                $info['denominator'] = 8000;
            }elseif ($info['total_jifen'] <= 10000)
            {
                $info['grade'] = 8;
                $info['denominator'] = 10000;
            }elseif ($info['total_jifen'] <= 15000)
            {
                $info['grade'] = 9;
                $info['denominator'] = 15000;
            }else
            {
                $info['grade'] = 10;
                $info['denominator'] = $info['total_jifen'];
            }
        }else
        {
            $info['grade'] = 1;
            $info['denominator'] = 200;
        }
        $coin = D('Coin')->coin_find(array('user_id' => session('user_id'), 'info' => '打卡增加5车币'));
        if (date("Y-m-d", strtotime($coin['createtime'])) == date("Y-m-d", time())) {
            //今天已签到
            $info['signstatus'] = 1;
        } else {
            //今天未签到
            $info['signstatus'] = 2;
        }
        $accinfo = D('Coin')->coin_find(array('user_id' => session('user_id'), 'info' => '首次完善资料加50车币'));
        if ($accinfo) {
            //已经完善了资料
            $info['acccoin'] = 1;
        } else {
            //尚未完善资料
            $info['acccoin'] = 2;
        }

        $this->ajaxReturn($info);
    }

    /**
     *普通用户---账号资料修改
     */
    public function member_accinfo_save()
    {
        $user = D("UserGeneral"); // 实例化User对象
        $info = $user->user_find(array('id' => session('user_id')));
        $_POST['id'] = session('user_id');
        if (!$user->create()) {
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            $data['status'] = 1;
            $data['msg'] = $user->getError();
        } else {
            // 验证通过 可以进行其他数据操作
            $tel = I('tel');
            if ($info['tel'] != $tel) {
                if (session('tel') == $tel) {
                    $code = I('code');
                    if (cookie('code') == $code) {
                        $_POST['user_name'] = $_POST['tel'];
                        $z = $user->user_save();
                        if ($z) {
                            //首次全部完善资料加50车币
                            $infoacc = $user->user_find(array('id' => session('user_id')));
                            $acc = D('Coin')->coin_find(array('user_id' => session('user_id'), 'info' => '首次完善资料加50车币'));
                            if (empty($acc) and !empty($infoacc['name']) and !empty($infoacc['sex']) and !empty($infoacc['birthday']) and !empty($infoacc['province']) and !empty($infoacc['city']) and !empty($infoacc['postcode'])) {
                                D('Coin')->coin_accinfo();
                            }

                            $data['status'] = 2;
                            $data['msg'] = '账号资料修改成功！';
                        } else {
                            $data['status'] = 1;
                            $data['msg'] = '账号资料修改失败！';
                        }
                    } else {
                        $data['status'] = 1;
                        $data['msg'] = '验证码错误！';
                    }
                } else {
                    $data['status'] = 1;
                    $data['msg'] = '请输入发送短信的手机号！';
                }
            } else {
                $z = $user->user_save();
                if ($z) {
                    //首次全部完善资料加50车币
                    $infoacc = $user->user_find(array('id' => session('user_id')));
                    $acc = D('Coin')->coin_find(array('user_id' => session('user_id'), 'info' => '首次完善资料加50车币'));
                    if (empty($acc) and !empty($infoacc['name']) and !empty($infoacc['sex']) and !empty($infoacc['birthday']) and !empty($infoacc['province']) and !empty($infoacc['city']) and !empty($infoacc['postcode'])) {
                        D('Coin')->coin_accinfo();
                    }

                    $data['status'] = 2;
                    $data['msg'] = '账号资料修改成功！';
                } else {
                    $data['status'] = 1;
                    $data['msg'] = '账号资料修改失败！';
                }

            }

        }

        $this->ajaxReturn($data);
    }

    /**
     *普通用户----修改密码
     */
    public function modifyPass()
    {
        $user = D('UserGeneral');
        $_POST['id'] = session('user_id');
        if (!$user->create()) {
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            $data['status'] = 1;
            $data['msg'] = $user->getError();
        } else {
            // 验证通过 可以进行其他数据操作
            $password = md5($_POST['password']);
            if (!$user->user_find(array('id' => session('user_id'), 'password' => $password))) {
                $data['status'] = 1;
                $data['msg'] = '原密码错误';
                $this->ajaxReturn($data);
            }else
            {
                $_POST['password'] = md5($_POST['newpassword']);
                if ($user->user_save()) {
                    $data['status'] = 2;
                    $data['msg'] = '密码修改成功';
                } else {
                    $data['status'] = 1;
                    $data['msg'] = '密码修改失败';
                }
            }


        }
        $this->ajaxReturn($data);

    }

    /**
     *地址列表页
     */
    public function receipt_address_list()
    {

        $list = D('ReceiptAddress')->address_select(array('user_id' => $_SESSION['user_id']));
        if ($list) {
            $data['status'] = 1;
            $data['list'] = $list;
        } else {
            $data['status'] = 0;
            $data['msg'] = "暂无地址";
        }

        $this->ajaxReturn($data);

    }

    /**
     *添加收货地址
     */
    public function receipt_address_add()
    {
        if (!empty($_POST)) {
            $_POST['role'] = 1;
            $address = D("ReceiptAddress"); // 实例化User对象
            if (!$address->create($_POST)) { // 指定新增数据
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $data['status'] = 1;
                $data['msg'] = $address->getError();
            } else {
                //判断是否大于3个
                $count = count($address->address_select(array('user_id' => $_SESSION['user_id'])));

                if ($count > 3) {
                    $data['status'] = 1;
                    $data['msg'] = '地址最多只能添加3个！';
                } else {
                    // 验证通过 可以进行其他数据操作
                    if ($address->address_add()) {
                        $data['status'] = 2;
                        $data['msg'] = '地址添加成功！';
                    } else {
                        $data['status'] = 1;
                        $data['msg'] = '地址添加失败！';
                    }
                }

            }
            $this->ajaxReturn($data);
        }

    }

    /**
     *修改收货地址
     */
    public function receipt_address_save()
    {
        if (!empty($_POST)) {
            $address = D("ReceiptAddress"); // 实例化User对象
            if (!$address->create($_POST)) { // 指定新增数据
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $data['status'] = 1;
                $data['msg'] = $address->getError();
            } else {
                // 验证通过 可以进行其他数据操作
                if ($address->address_save()) {
                    $data['status'] = 2;
                    $data['msg'] = '地址修改成功！';
                } else {
                    $data['status'] = 1;
                    $data['msg'] = '地址修改失败！';
                }
            }
            $this->ajaxReturn($data);
        }
    }

    /**
     *头像修改功能
     */
    public function headimg()
    {
        $user = D('UserGeneral');
        $info = $user->user_find(array('id' => session('user_id')));
        if (!empty($_FILES)) {
            if (!empty($_FILES)) {
                $y = upload_local();
                if ($y) {
                    $_POST['head_url'] = str_replace("big", 'type', $y['cardpic_front']['savepath'] . $y['cardpic_front']['savename']);
                }
            }
            $_POST['id'] = session('user_id');
            if (!$user->create($_POST)) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息\
                $this->error($user->getError());
            } else {
                // 验证通过 可以进行其他数据操作
                if ($user->user_save()) {
                    if (empty($info['head_url']) and !empty($_POST['head_url'])) {
                        D('Coin')->coin_headimg();
                    }
                    $this->success('头像上传成功', 'n_uc_index');
                } else {
                    $this->error('头像上传失败', 'n_uc_base_info_pic');
                }
            }
        } else {
            $this->redirect('index');
        }
    }

    /**
     *普通用户-------我的询价
     */
    public function querylist()
    {
        $info['status'] = array('between', '0,2');
        $info['isdelete'] = 0;
        $info['user_id'] = session('user_id');
        $info['pay_obj'] = array('in', '1,3');
        $count = M('pay')->where($info)->count();
        $Page = new \Think\PageAjax($count, 15);
        $data = M('pay')->where($info)->order('createtime desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($data as $key => $value) {
            $data[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
            $data[$key]['validity'] = date('Y-m-d H:i:s', $value['updatetime'] + 2 * 24 * 3600);
            if ($value['pay_obj'] == 3) {
                $data[$key]['validity'] = '(空)';
            }
            $quotes = M('pay_area_low_price')->where(array('pay_id' => $value['id'], 'isdelete' => 0))->select();
            $data[$key]['car_price_id'] = $quotes[0]['car_price_id'];
            if ($value['status'] == 0) {
                $data[$key]['info'] = '未支付无法显示报价';
            } elseif ($value['status'] == 1) {
                $data[$key]['info'] = $quotes;
                if ($value['pay_obj'] == 3) {
                    $data[$key]['info'] = '一键询价无法显示报价';
                }
            } elseif ($value['status'] == 2) {
                $data[$key]['info'] = '订单已失效无法显示报价';
            }

            //询价车图片
            $data[$key]['imgurl'] = OSS . str_replace("type", 'small', $value['exterior_img']);
        }
        $db['count'] = $count;
        $db['pagelist'] = $Page->show();
        $db['list'] = $data;
        $this->ajaxReturn($db);
    }

    /**
     *普通用户-------我的订车订单
     */
    public function orderlist()
    {
        $info['isdelete'] = 0;
        $info['user_id'] = session('user_id');
        $info['pay_obj'] = 2;
        $count = M('pay')->where($info)->count();
        $Page = new \Think\PageAjax($count, I('pagesize'));
        $data = M('pay')->where($info)->order('createtime desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($data as $key => $value) {
            if (!empty($value['ticket_number'])) {
                $tick = M('ticket_user')->where(array('pay_id' => $value['id']))->find();
                $time = time();
                if ($tick['end_time'] < $time) {
                    $tick_status = 3;
                }
            }
//没有优惠券和优惠券未过期直接支付（$tick_status为空）
            if (!empty($value['buyer_name']) and !empty($value['buyer_tel']) and !empty($value['buy_time']) and !empty($value['buy_way']) and !empty($value['insurance']) and empty($tick_status)) {
                $data[$key]['payurl'] = '/index.php/Order/order_pay?pay_id=' . $value['id'];
            } else {
                $data[$key]['payurl'] = '/index.php/Order/order_info?pay_id=' . $value['id'];
            }
            $data[$key]['paydetail'] = U('n_uc_myorder_details', array('pay_id' => $value['id']));
            $lost_time = strtotime($value['createtime']) + 3600 * 24 * 2;
            if ($value['status'] == 0 and $lost_time < time()) {
                $data[$key]['status'] = 2;
                M('pay')->where(array('id' => $value['id']))->save(array('status' => 2));
            }

        }

        $db['count'] = $count;
        $db['pagelist'] = $Page->show();
        $db['list'] = $data;
        $this->ajaxReturn($db);
    }

    /**
     *商城订单
     */
    public function shoplist()
    {
        $order_info = M('order_info', 'ecs_', 'DB_CONFIG1');
        $order_goods = M('order_goods', 'ecs_', 'DB_CONFIG1');
        $count = $order_info->where(array('user_id' => session('user_id')))->count();
        $page = new \Think\PageAjax($count, 15);//实例化分页类传入总记录数和每页显示的记录数
        $info = $order_info->where(array('user_id' => session('user_id')))->limit($page->firstRow . ',' . $page->listRows)->order('add_time desc')->select();
        foreach ($info as $key => $value) {
            $info[$key]['goods'] = $order_goods->where(array('order_id' => $value['order_id']))->select();
            $info[$key]['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
            $info[$key]['pay_time'] = date('Y-m-d H:i:s', $value['pay_time']);
            $info[$key]['url'] = '/member.php/MemberGeneral/n_uc_shop_order_detail?order_id=' . $value['order_id'];
        }
        $pagelist = $page->show();//分页显示输出
        $data['pagelist'] = $pagelist;
        $data['list'] = $info;
        $data['count'] = $count;
        $this->ajaxReturn($data);

    }

    /**
     *商城订单详情
     */
    public function shopdetails()
    {
        $order_info = M('order_info', 'ecs_', 'DB_CONFIG1');
        $order_goods = M('order_goods', 'ecs_', 'DB_CONFIG1');
        $info = $order_info->where(array('order_id' => I('order_id')))->find();
        $info['add_time'] = date('Y-m-d H:i:s', $info['add_time']);
        $info['province_name'] = M('data_province')->where(array('ProvinceID' => $info['province']))->getField('ProvinceName');
        $info['city_name'] = M('data_city')->where(array('CityID' => $info['city']))->getField('CityName');
        $info['district_name'] = M('data_district')->where(array('DistrictID' => $info['district']))->getField('DistrictName');
        if ($info['shipping_id'] == -1) {
            $info['shipping_id'] = "(空)";
        }
        $info['goods'] = $order_goods->where(array('order_id' => $info['order_id']))->select();
        $this->ajaxReturn($info);

    }

    /**
     *商城订单签收
     */

    public function shopreceipt()
    {
        $order_info = M('order_info', 'ecs_', 'DB_CONFIG1');
        $data['shipping_time'] = time();
        $data['pay_status'] = 2;
        $data['shipping_status'] = 2;
        $data['order_status'] = 1;
        $z = $order_info->where(array('order_id' => I('order_id')))->save($data);
        if ($z) {
            $info['status'] = 1;
            $info['msg'] = '操作成功！';
        } else {
            $info['status'] = 2;
            $info['msg'] = '操作失败！';
        }
        $this->ajaxReturn($info);
    }

    /**
     *商城订单取消
     */
    public function shopcancel()
    {
        $order_info = M('order_info', 'ecs_', 'DB_CONFIG1');
        $data['shipping_time'] = time();
        $data['order_status'] = 2;
        $z = $order_info->where(array('order_id' => I('order_id')))->save($data);
        if ($z) {
            $info['status'] = 1;
            $info['msg'] = '操作成功！';
        } else {
            $info['status'] = 2;
            $info['msg'] = '操作失败！';
        }
        $this->ajaxReturn($info);
    }

    /**
     * @Description:活动订单列表
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/10/26 10:34
     * @Version 2.0
     */
    public function activitylist()
    {
        $pagesize=I('pagesize');
        $count= M('pay')->where(array('isdelete'=>0,'pay_obj'=>4,'user_id'=>session('user_id')))->count();
        $page = new \Think\PageAjax($count, $pagesize);//实例化分页类传入总记录数和每页显示的记录数
        $info=M('pay')->where(array('isdelete'=>0,'pay_obj'=>4,'user_id'=>session('user_id')))->order("createtime desc")->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($info as $key =>$value)
        {
            $actinfo=M('activities')->where(array('id'=>$value['from_activityid']))->find();
            $info[$key]['activity_name']=$actinfo['activity_name'];
            $info[$key]['endtime']=date('Y-m-d H:i:s',$actinfo['endtime']);
            if ($actinfo['starttime']>time())
            {
                $info[$key]['actstatus']=1;/*未开始*/
            }elseif ($actinfo['starttime']<=time() and $actinfo['endtime']>=time())
            {
                $info[$key]['actstatus']=2;/*有效*/
            }elseif ($actinfo['endtime']<time())
            {
                $info[$key]['actstatus']=3;/*失效*/
            }

        }
        $pagelist = $page->show();//分页显示输出
        $data['pagelist'] = $pagelist;
        $data['list'] = $info;
        $data['count'] = $count;
        $this->ajaxReturn($data);

    }

    /**
     * @Description:活动订单详情
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/10/26 10:43
     * @Version 2.0
     */
    public function activitydetails()
    {
        $info=M('pay')->where(array('id'=>I('pay_id')))->find();
        $info['exterior_img']=OSS.str_replace('type','small',$info['exterior_img']);
        $actinfo=M('activities')->where(array('id'=>$info['from_activityid']))->find();
        $info['activity_name']=$actinfo['activity_name'];
        if ($actinfo['starttime']>time())
        {
            $info['actstatus']=1;
        }elseif ($actinfo['starttime']<=time() and $actinfo['endtime']>=time())
        {
            $info['actstatus']=2;
        }elseif ($actinfo['endtime']<time())
        {
            $info['actstatus']=3;
        }
        $this->ajaxReturn($info);

    }

    /**
     * @Description:取消活动订单
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/10/26 10:46
     * @Version 2.0
     */
    public function activitycancel()
    {
        $z=M('pay')->where(array('id'=>I('pay_id')))->save(array('isdelete'=>1));
        if ($z)
        {
            $data['status']=1;
            $data['msg']="操作成功！";
        }else
        {
            $data['status']=2;
            $data['msg']="操作失败！";
        }
        $this->ajaxReturn($data);
    }

    /**
     *普通用户-------车币订单
     */
    public function scorelist()
    {
        $data = D("ScoreExchange")->score_goods_select(array('user_id' => session('user_id'), 'role' => 1));
        $db['list'] = $data;
        $this->ajaxReturn($db);
    }

    /**
     *普通用户-----订车订单详情
     */
    public function order_info()
    {
        $info = D("Pay")->pay_find(array('id' => I('id')));
        $info['exterior_img'] = str_replace('type', 'small', $info['exterior_img']);

        //查找大礼包
        $data['min_price'] = array('ELT', $info['low_price']);
        $data['max_price'] = array('EGT', $info['low_price']);
        $package = M("buy_car_package")->where($data)->find();
        $info['packageimgurl'] = str_replace('type', 'big', $package['imgurl']);
        $info['packageprice'] = $package['price'];
        $this->ajaxReturn($info);
    }

    /**
     *普通用户-----订车订单详情(同步)
     */
    public function n_uc_myorder_details()
    {

        //根据ID 找到这条订单
        if (I('pay_id')) {
            /* M("pay")->where(array('id'=>I('pay_id')))->setField(array('isread'=>2));*/
            $info = M('pay')->where(array('id' => I('pay_id')))->find();
            if ($info['updateuser_3'] != '') {
                $mamager_info = M('manager')->where(array('admin_name' => $info['updateuser_3']))->find();
                $info['name'] = $mamager_info['name'];
                $info['manager_tel'] = $mamager_info['tel'];
            }
            $info['id_imgurl1'] = str_replace("type", 'center', $info['id_imgurl1']);
            $info['id_imgurl2'] = str_replace("type", 'center', $info['id_imgurl2']);
            $info['exterior_img'] = OSS . str_replace('type', 'small', $info['exterior_img']);
            $tick = M('ticket_user')->where(array('ticket_number' => $info['ticket_number']))->find();
            $tick['starttime'] = date('Y-m-d', strtotime($tick['createtime']));
            $tick['endtime'] = date('Y-m-d', $tick['end_time']);
            $diffence = $tick['end_time'] - time();
            if ($diffence <= 7 * 24 * 3600 and $diffence > 0 and $tick['status'] == 1) {
                $tick['status'] = 5;
            }

        }
        $this->assign('info', $info);
        $this->assign('tick', $tick);
        $this->display();
    }

    /**
     *普通用户-----订车订单资料完善(同步)
     */
    public function n_uc_myorder_update()
    {

        if (!empty($_FILES)) {
            $y = upload_local();
            if ($y) {
                $_POST['cardpic_front'] = str_replace("big", 'type', $y['cardpic_front']['savepath'] . $y['cardpic_front']['savename']);
                $_POST['cardpic_back'] = str_replace("big", 'type', $y['cardpic_back']['savepath'] . $y['cardpic_back']['savename']);

                $data['ID_imgurl1'] = $_POST['cardpic_front'];
                $data['ID_imgurl2'] = $_POST['cardpic_back'];
                $data['status'] = 3;
                $z = M('pay')->where(array('id' => I('pay_id')))->save($data);
                if ($z) {
                    $this->success('图片上传成功');
                } else {
                    $this->error('图片保存失败');
                }
            } else {
                $this->error('图片上传失败');
            }
        } else {
            $this->error('请上传图片');
        }
    }

    /**
     *普通用户-----车币订单详情
     */
    public function score_info()
    {
        $info = D("ScoreExchange")->score_goods_find(array('id' => I('id')));
        $address = D("view_receipt_address")->where(array('id' => $info['address_id']))->find();
        $info['province'] = $address['provincename'];
        $info['city'] = $address['cityname'];
        $info['district'] = $address['districtname'];
        $info['address'] = $address['receipt_address'];
        $this->ajaxReturn($info);
    }

    /**
     *普通用户---我的足迹
     */
    public function footprint()
    {
        $info = unserialize(cookie('history'));
        foreach ($info as $key => $value) {
            $info[$key] = D('view_car_price')->where(array('id' => $value))->find();
            $info[$key]['car_model_imageurl'] = OSS . str_replace('type', 'small', $info[$key]['car_model_imageurl']);

        }
        $db['list'] = $info;
        $this->ajaxReturn($db);
    }

    /**
     *普通用户---我的爱车
     */
    public function carlove()
    {
        $info = D('CarLove')->car_select(array('user_id' => session('user_id')));
        foreach ($info as $key => $value) {
            $data = D('view_car')->where(array('id' => $value['car_id']))->find();
            $find = D('view_car_price')->where(array('car_id' => $value['car_id'], 'isdelete' => 0))->find();
            if ($find) {
                $info[$key]['url'] = "http://www.gouchehui.com/index.php/car/product_details/id/" . $find['id'];
            } else {
                $info[$key]['url'] = "http://www.gouchehui.com/index.php/car/product_search";
            }
            $info[$key]['brand_id'] = $data['brand_id'];
            $info[$key]['brand_name'] = $data['brand_name'];
            $info[$key]['car_model_id'] = $data['car_model_id'];
            $info[$key]['car_model_name'] = $data['car_model_name'];
            $info[$key]['car_name'] = $data['car_name'];
            $info[$key]['auth_price'] = $data['auth_price'];
            $info[$key]['province_name'] = M('data_province')->where(array('ProvinceID' => $value['province_id']))->getField('ProvinceName');
            $info[$key]['city_name'] = M('data_city')->where(array('CityID' => $value['city_id']))->getField('CityName');
            $string = strstr($data['car_model_imageurl'], 'type');
            if (!empty($string)) {
                $info[$key]['car_model_imageurl'] = OSS . str_replace('type', 'small', $data['car_model_imageurl']);
            } else {
                $info[$key]['car_model_imageurl'] = OSS . $data['car_model_imageurl'];
            }

        }
        $datar['list'] = $info;
        $this->ajaxReturn($datar);

    }

    /**
     *普通用户---添加我的爱车
     */
    public function carlove_add()
    {
        $carlove = D('CarLove'); // 实例化User对象
        if (!$carlove->create($_POST)) { // 指定新增数据
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            $data['status'] = 1;
            $data['msg'] = $carlove->getError();
        } else {
            $count = $carlove->where(array('user_id' => session('user_id'), 'isdelete' => 0))->count();
            if ($count < 3) {
                // 验证通过 可以进行其他数据操作
                if ($_POST['add'] == 'car')//添加了新车款
                {
                    $data['car_model_id'] = $_POST['car_model_id'];
                    $data['car_name'] = $_POST['car_name'];
                    $meminfo = D('MemberCar')->car_find($data);
                    if (empty($meminfo)) {
                        if (D('MemberCar')->car_add($data)) {
                            $_POST['car_id'] = D('MemberCar')->where($data)->getField('id');
                            $_POST['from'] = 2;
                            if ($carlove->car_add()) {
                                $data['status'] = 2;
                                $data['msg'] = '添加爱车成功';
                            } else {
                                $data['status'] = 1;
                                $data['msg'] = '添加爱车失败';
                            }
                        } else {
                            $data['status'] = 1;
                            $data['msg'] = '新车款添加失败';
                        }

                    } else {
                        $_POST['car_id'] = $meminfo['id'];
                        $_POST['from'] = 2;
                        if ($carlove->car_add()) {
                            $data['status'] = 2;
                            $data['msg'] = '添加爱车成功';
                        } else {
                            $data['status'] = 1;
                            $data['msg'] = '添加爱车失败';
                        }
                    }

                } else {
                    if ($carlove->car_add()) {
                        $data['status'] = 2;
                        $data['msg'] = '添加爱车成功';
                    } else {
                        $data['status'] = 1;
                        $data['msg'] = '添加爱车失败';
                    }
                }

            } else {
                $data['status'] = 1;
                $data['msg'] = '添加爱车不能超过3辆';
            }
        }
        $this->ajaxReturn($data);
    }

    /**
     *普通用户---修改我的爱车
     */
    public function carlove_save()
    {
        $carlove = D('CarLove'); // 实例化User对象
        if (!$carlove->create($_POST)) { // 指定新增数据
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            $data['status'] = 1;
            $data['msg'] = $carlove->getError();
        } else {

            // 验证通过 可以进行其他数据操作
            if ($_POST['add'] == 'car')//添加了新车款
            {
                $data['car_model_id'] = $_POST['car_model_id'];
                $data['car_name'] = $_POST['car_name'];
                $meminfo = D('MemberCar')->car_find($data);
                if (empty($meminfo)) {
                    if (D('MemberCar')->car_add($data)) {
                        $_POST['car_id'] = D('MemberCar')->where($data)->getField('id');
                        $_POST['from'] = 2;
                        if ($carlove->car_save()) {
                            $data['status'] = 2;
                            $data['msg'] = '修改爱车成功';
                        } else {
                            $data['status'] = 1;
                            $data['msg'] = '修改爱车失败';
                        }
                    } else {
                        $data['status'] = 1;
                        $data['msg'] = '新车款添加失败';
                    }

                } else {
                    $_POST['car_id'] = $meminfo['id'];
                    $_POST['from'] = 2;
                    if ($carlove->car_save()) {
                        $data['status'] = 2;
                        $data['msg'] = '修改爱车成功';
                    } else {
                        $data['status'] = 1;
                        $data['msg'] = '修改爱车失败';
                    }
                }

            } else {
                if ($carlove->car_save()) {
                    $data['status'] = 2;
                    $data['msg'] = '修改爱车成功';
                } else {
                    $data['status'] = 1;
                    $data['msg'] = '修改爱车失败';
                }
            }


        }
        $this->ajaxReturn($data);
    }

    /**
     *普通用户------删除我的爱车
     */
    public function carlove_delete()
    {
        $z = D('CarLove')->car_save(array('id' => I('id'), 'isdelete' => 1));
        if ($z) {
            $data['status'] = 2;
            $data['msg'] = '删除爱车成功！';

        } else {
            $data['status'] = 1;
            $data['msg'] = '删除爱车失败！';
        }
        $this->ajaxReturn($data);

    }

    /**
     *普通用户---计划购车
     */
    public function carplan()
    {
        $info = D('CarPlan')->car_select(array('user_id' => session('user_id')));
        foreach ($info as $key => $value) {
            $data = D('view_car')->where(array('id' => $value['car_id']))->find();
            $info[$key]['price_id'] = D('view_car_price')->where(array('car_id' => $data['id'], 'isdelete' => 0))->find();
            $info[$key]['price_id'] = $info[$key]['price_id'] ? $info[$key]['price_id'] : 0;
            $info[$key]['brand_id'] = $data['brand_id'];
            $info[$key]['brand_name'] = $data['brand_name'];
            $info[$key]['car_model_id'] = $data['car_model_id'];
            $info[$key]['car_model_name'] = $data['car_model_name'];
            $info[$key]['car_name'] = $data['car_name'];
            $info[$key]['auth_price'] = $data['auth_price'];
            $info[$key]['province'] = M('data_province')->where(array('ProvinceId' => $value['province_id']))->getField('ProvinceName');
            $info[$key]['city'] = M('data_city')->where(array('CityID' => $value['city_id']))->getField('CityName');
            $string = strstr($data['car_model_imageurl'], 'type');
            if (!empty($string)) {
                $info[$key]['image'] = OSS . str_replace('type', 'small', $data['car_model_imageurl']);
            } else {
                $info[$key]['image'] = OSS . $data['car_model_imageurl'];
            }

        }
        $datar['list'] = $info;
        $this->ajaxReturn($datar);

    }

    /**
     *普通用户---添加计划购车
     */
    public function carplan_add()
    {
        $carplan = D('CarPlan'); // 实例化User对象
        $_POST['from_plan'] = '会员中心-计划购车';
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
        $this->ajaxReturn($data);
    }

    /**
     *普通用户---修改计划购车
     */
    public function carplan_save()
    {
        $carplan = D('CarPlan'); // 实例化User对象
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
                // 验证通过 可以进行其他数据操作
                if ($carplan->car_save()) {
                    $data['status'] = 2;
                    $data['msg'] = '修改计划购车成功';
                } else {
                    $data['status'] = 1;
                    $data['msg'] = '修改计划购车失败';
                }
            } else {
                $data['status'] = 1;
                $data['msg'] = '该车款已经在计划中！';
            }


        }
        $this->ajaxReturn($data);
    }

    /**
     *普通用户------删除计划购车
     */
    public function carplan_delete()
    {
        $z = D('CarPlan')->car_save(array('id' => I('id'), 'isdelete' => 1));
        if ($z) {
            $data['status'] = 2;
            $data['msg'] = '取消计划成功！';

        } else {
            $data['status'] = 1;
            $data['msg'] = '取消计划失败！';
        }
        $this->ajaxReturn($data);

    }

    /**
     *普通用户----关注的车
     */
    public function attention_car()
    {
        $info = D('AttentionCar')->attention_select(array('user_id' => session('user_id')));
        foreach ($info as $key => $value) {
            $data = D('view_car_model')->where(array('id' => $value['car_model_id']))->find();
            $info[$key]['car_brand_id'] = $data['brand_id'];
            $info[$key]['car_brand'] = $data['brand_name'];
            $info[$key]['car_model_id'] = $data['id'];
            $info[$key]['car_model'] = $data['car_model_name'];
            $info[$key]['max_price'] = D('view_car')->where(array('car_model_id' => $value['car_model_id']))->order('auth_price desc')->getField('auth_price');
            $info[$key]['min_price'] = D('view_car')->where(array('car_model_id' => $value['car_model_id']))->order('auth_price asc')->getField('auth_price');
            $string = strstr($data['imgurl'], 'type');
            if (!empty($string)) {
                $info[$key]['image'] = OSS . str_replace('type', 'small', $data['imgurl']);
            } else {
                $info[$key]['image'] = OSS . $data['imgurl'];
            }

        }
        $datar['list'] = $info;
        $this->ajaxReturn($datar);
    }

    /**
     *普通用户----其他用户关注的车
     */
    public function other_attention_car()
    {
        $info = D('AttentionCar')->where(array('isdelete' => 0))->order('createtime desc')->group('car_model_id')->limit(0, 5)->select();
        foreach ($info as $key => $value) {
            $find = D('AttentionCar')->attention_find(array('user_id' => $_SESSION['user_id'], 'car_model_id' => $value['car_model_id']));
            if ($find) {
                $info[$key]['attention'] = 1;
            } else {
                $info[$key]['attention'] = 0;
            }
            $data = D('view_car_model')->where(array('id' => $value['car_model_id']))->find();
            $info[$key]['car_brand_id'] = $data['brand_id'];
            $info[$key]['car_brand'] = $data['brand_name'];
            $info[$key]['car_model_id'] = $data['id'];
            $info[$key]['car_model'] = $data['car_model_name'];
            $info[$key]['max_price'] = D('view_car')->where(array('car_model_id' => $value['car_model_id']))->order('auth_price desc')->getField('auth_price');
            $info[$key]['min_price'] = D('view_car')->where(array('car_model_id' => $value['car_model_id']))->order('auth_price asc')->getField('auth_price');
            $string = strstr($data['imgurl'], 'type');
            if (!empty($string)) {
                $info[$key]['image'] = OSS . str_replace('type', 'small', $data['imgurl']);
            } else {
                $info[$key]['image'] = OSS . $data['imgurl'];
            }
        }
        $datar['list'] = $info;
        $this->ajaxReturn($datar);
    }

    /**
     *添加关注
     */
    public function attention_add()
    {
        $attention = D('AttentionCar'); // 实例化User对象
        if ($_POST['car_model_id'] == '') {
            $data['status'] = 1;
            $data['msg'] = '请提交正确的数据';
            $this->ajaxReturn($data);
        }
        $find = $attention->attention_find(array('car_model_id' => $_POST['car_model_id'], 'user_id' => $_SESSION['user_id']));
        if ($find) {
            $data['status'] = 3;
            $data['msg'] = '该车型已经关注过';
            $this->ajaxReturn($data);

        }
        if (!$attention->create($_POST)) { // 指定新增数据
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            $data['status'] = 1;
            $data['msg'] = $attention->getError();
        } else {
            // 验证通过 可以进行其他数据操作
            if ($attention->attention_add()) {
                $data['status'] = 2;
                $data['msg'] = '关注成功';
            } else {
                $data['status'] = 1;
                $data['msg'] = '关注失败';
            }
        }
        $this->ajaxReturn($data);
    }

    /**
     *普通用户------删除关注
     */
    public function attention_delete()
    {
        $find = D('AttentionCar')->attention_find(array('id' => I('id')));
        if ($find['user_id'] == $_SESSION['user_id']) {
            $z = D('AttentionCar')->attention_save(array('id' => I('id'), 'isdelete' => 1));
            if ($z) {
                /*取消关注后，车型关注度少1*/
                $model_id = D('AttentionCar')->where(array('id' => I('id')))->getField('car_model_id');
                M('car_model')->where(array('id' => $model_id))->setDec('attention_count', 1);
                $data['status'] = 2;
                $data['msg'] = '取消关注成功！';

            } else {
                $data['status'] = 1;
                $data['msg'] = '取消关注失败！';
            }
            $this->ajaxReturn($data);
        } else {
            $this->error("非法操作！", 'MemberGeneral/n_uc_garage_attencar');
        }

    }

    /**
     *普通用户————系统消息
     */
    public function sysmessage()
    {
        $info = M("user_message")->where(array('isdelete' => 0))->order('createtime desc')->select();
        $data['list'] = $info;
        $this->ajaxReturn($info);
    }

    /**
     *普通用户————每天打卡增加5车币
     */
    public function everyday()
    {
        if (isset($_SESSION['user_id']) and !empty($_SESSION['user_id'])) {
            $coin = D('Coin');
            $info = $coin->coin_find(array('user_id' => session('user_id'), 'info' => '打卡增加5车币'));
            if (date("Y-m-d", time()) != date("Y-m-d", strtotime($info['createtime']))) {
                if ($coin->coin_everyday()) {
                    $data['status'] = 1;
                    $data['msg'] = '打卡成功';
                } else {
                    $data['status'] = 2;
                    $data['msg'] = '打卡失败';
                }
            } else {
                $data['status'] = 3;
                $data['msg'] = '今天已经打过卡';
            }

        } else {
            $this->redirect('Public/login');
        }
        $this->ajaxReturn($data);
    }

    /**
     *品牌
     */
    public function brand()
    {
        $info = M('brand')->where(array('isdelete' => 0))->select();
        foreach ($info as $key => $value) {
            $data[$key]['name'] = $value['brand_name'];
            $data[$key]['value'] = $value['id'];
        }
        $this->ajaxReturn($data);
    }

    /**
     *车型
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
     *车款
     */
    public function car()
    {
        $car_model_id = I('model');
        $info = M('car')->where(array('car_model_id' => $car_model_id, 'isdelete' => 0))->select();
        foreach ($info as $key => $value) {
            $data[$key]['name'] = $value['car_name'];
            $data[$key]['value'] = $value['id'];
        }
        $this->ajaxReturn($data);
    }

    /**
     *车款----我的爱车
     */
    public function car_love()
    {
        $car_model_id = I('model');
        $info = M('car')->where(array('car_model_id' => $car_model_id, 'isdelete' => 0))->select();
        foreach ($info as $key => $value) {
            $data[$key]['name'] = $value['car_name'];
            $data[$key]['value'] = $value['id'];
            $data[$key]['from'] = 1;
        }
        $data_mem = M('MemberCar')->where(array('car_model_id' => $car_model_id, 'isdelete' => 0))->select();
        foreach ($data_mem as $key => $value) {
            $data1[$key]['name'] = $value['car_name'];
            $data1[$key]['value'] = $value['id'];
            $data1[$key]['from'] = 2;
        }
        $allinfo = array_merge($data, $data1);
        $this->ajaxReturn($allinfo);
    }

    /**
     *省份
     */
    public function province()
    {
        $info = M('data_province')->select();
        foreach ($info as $key => $value) {
            $data[$key]['name'] = $value['provincename'];
            $data[$key]['value'] = $value['provinceid'];
        }
        $this->ajaxReturn($data);
    }

    /**
     *城市
     */
    public function city()
    {
        $province_id = I('province');
        $info = M('data_city')->where(array('ProvinceID' => $province_id))->select();
        foreach ($info as $key => $value) {
            $data[$key]['name'] = $value['cityname'];
            $data[$key]['value'] = $value['cityid'];
        }
        $this->ajaxReturn($data);
    }

    /**
     *地区
     */
    public function district()
    {
        $city_id = I('city');
        $info = M('data_district')->where(array('CityID' => $city_id))->select();
        foreach ($info as $key => $value) {
            $data[$key]['name'] = $value['districtname'];
            $data[$key]['value'] = $value['districtid'];
        }
        $this->ajaxReturn($data);
    }

    /*********************************************************车生活**********************************************************************************/
    /**
     *发帖的类别
     */
    public function post_type()
    {
        $node = M('car_life')->where(array('isdelete' => 0, 'pid' => 2))->select();
        foreach ($node as $key => $value) {
            $data[$key]['name'] = $value['type_name'];
            $data[$key]['value'] = $value['id'];
        }
        $this->ajaxReturn($data);
    }

    /**
     * 车生活---车主秀发帖
     * 字段：title,$_FILES,type_id,car_id,contents
     * @return [type] [description]
     */
    public function n_uc_post()
    {
        if (!empty($_FILES)) {
            $y = upload_local();
            if ($y) {
                $_POST['image'] = str_replace("big", 'type', $y['image']['savepath'] . $y['image']['savename']);
            }
        }
        if (!empty($_POST)) {
            $bbs = M('car_life_bbs');
            $_POST['id'] = md5(microtime());
            $_POST['user_id'] = session('user_id');
            $_POST['createuser'] = session('user_name');
            $_POST['summary'] = substr(strip_tags($_POST['contents']), 0, 108) . '......';
            $bbs->create($_POST);
            if ($bbs->add()) {
                $this->success('添加成功！');
            } else {
                $this->error('添加失败！');
            }
        }

        $this->display();
    }

    /**
     *车生活---编辑帖子
     */
    public function n_uc_post_edit()
    {
        if (!empty($_FILES)) {
            $y = upload_local();
            if ($y) {
                $_POST['image'] = str_replace("big", 'type', $y['image']['savepath'] . $y['image']['savename']);
            }
        }
        if (!empty($_POST)) {
            $bbs = M('car_life_bbs');
            $_POST['user_id'] = session('user_id');
            $_POST['updateuser'] = session('user_name');
            $_POST['updatetime'] = date('Y-m-d H:i:s', time());
            $_POST['summary'] = substr(strip_tags($_POST['contents']), 0, 108) . '......';
            $bbs->create($_POST);
            if ($bbs->save()) {
                $this->success('操作成功！');
            } else {
                $this->error('操作失败！');
            }
        }
        $info = D('view_car_bbs')->where(array('id' => I('id')))->find();
        $info['image'] = OSS . str_replace('type', 'small', $info['image']);
        $this->assign('info', $info);
        $this->display();
    }

    /**
     *车生活---帖子列表
     */
    public function post_list()
    {
        $bbs = D('view_car_bbs');
        $type_id = I('typeid');
        if (!empty($type_id)) {
            $data['type_id'] = $type_id;
        }
        $data['user_id'] = session('user_id');
        $data['isdelete'] = 0;
        $pagenum = I('pageNum');
        $count = $bbs->where($data)->count();
        $Page = new \Think\PageAjax($count, $pagenum);
        $info = $bbs->where($data)->order('createtime desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($info as $key => $value) {
            $info[$key]['url'] = '/member.php/MemberGeneral/n_uc_post_edit?id=' . $value['id'];
            $info[$key]['image'] = OSS . str_replace('type', 'small', $value['image']);
        }
        $data['count'] = $count;
        $data['pagelist'] = $Page->show();
        $data['list'] = $info;

        $this->ajaxReturn($data);
    }

    /**
     *车生活--删帖
     */
    public function post_delete()
    {
        $bbs = M('car_life_bbs');
        $_POST['isdelete'] = 1;
        $_POST['updatetime'] = date('Y-m-d H:i:s', time());
        $_POST['updateuser'] = session('user_name');
        $bbs->create($_POST);
        if ($bbs->save()) {
            $data['status'] = 1;
            $data['msg'] = '删除成功！';
        } else {
            $data['status'] = 2;
            $data['msg'] = '删除失败！';
        }
        $this->ajaxReturn($data);

    }
    /*********************************************************积分、车币、优惠券**********************************************************************************/

    /**
     * @Description:优惠券列表
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/9/22 15:38
     * @Version 2.0
     */
    public function ticket()
    {
        $info = M('ticket_user')->where(array('user_id' => session('user_id'), 'isdelete' => 0))->select();
        foreach ($info as $key => $value) {
            $tick = M('ticket')->where(array('id' => $value['ticket_id']))->find();
            $info[$key]['starttime'] = date('Y-m-d', strtotime($value['createtime']));
            $info[$key]['endtime'] = date('Y-m-d', $value['end_time']);
            $info[$key]['money'] = $tick['money'];
            $diffence = $value['end_time'] - time();
            if ($diffence <= 7 * 24 * 3600 and $diffence > 0 and $value['status'] == 1) {
                $info[$key]['status'] = 5;
            }

        }
        $db['list'] = $info;
        $this->ajaxReturn($db);
    }

    /**
     * @Description:积分使用明细
     * @Return:info，pagelist
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/9/22 15:39
     * @Version 2.0
     */
    public function scoredetails_list()
    {
        $count = M('score')->where(array('user_id' => session('user_id')))->count();// 查询满足要求的总记录数
        $Page = new \Think\PageAjax($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $info = M('score')->where(array('user_id' => session('user_id')))->limit($Page->firstRow . ',' . $Page->listRows)->order('createtime desc')->select();
        $data['pagelist'] = $show;
        $data['list'] = $info;
        $this->ajaxReturn($data);
    }

    /**
     * @Description:车币使用明细
     * @Return:info,pagelist
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/9/22 15:53
     * @Version 2.0
     */
    public function coindetails_list()
    {
        $count = M('coin')->where(array('user_id' => session('user_id')))->count();// 查询满足要求的总记录数
        $Page = new \Think\PageAjax($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $info = M('coin')->where(array('user_id' => session('user_id')))->limit($Page->firstRow . ',' . $Page->listRows)->order('createtime desc')->select();
        $data['pagelist'] = $show;
        $data['list'] = $info;
        $this->ajaxReturn($data);
    }

}