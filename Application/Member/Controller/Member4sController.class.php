<?php
namespace Member\Controller;

use Think\Controller;

/**
 * Class UsercenterController前台会员中心
 * @package Member\Controller
 */
class Member4sController extends Controller
{
    /**
     *空控制器
     */
/*    public function _empty()
    {
        $this->redirect('fs_member_accinfo');
    }*/

    /**
     *初始化页面
     */
    public function _initialize()
    {
        $info = D('User4s')->user_find(array('id' => session('user_id')));
        if (empty($info)) {
            $this->error('请先登录4s店账号!', U('Public/login'));
        }
    }

    /**
     *进入该控制器默认跳转的页面
     */
    public function index()
    {
        $this->redirect('fs_member_accinfo');
    }

    /**
     *4s店用户--------账号资料
     */
    public function fs_member_accinfo()
    {
        $user_info = D('User4s')->where(array('id' => session('user_id')))->find();
        $user_info['head_url'] = str_replace('type', 'small', $user_info['head_url']);
        $user_info['brand_name'] = M("brand")->where(array('id' => $user_info['brand_4s']))->getField('brand_name');
        if (!empty($_FILES)) {
            $y = upload_local();
            if ($y) {
                $_POST['head_url'] = str_replace("big", 'type', $y['headimg']['savepath'] . $y['headimg']['savename']);
            }
        }
        if (!empty($_POST)) {
            $user = D('User4s');
            $user->create();
            $z = $user->user_save();
            if ($z !== false) {
                $this->success('保存成功！');
            } else {
                $this->error('保存失败！');
            }
        }
        $this->assign('user_info', $user_info);
        $this->display();
    }
    /************************************************积分相关******************************************************************/
    /**
     *积分相关
     */
    public function fs_scorerela()
    {
        $this->display();
    }

    /**
     *积分详情
     */
    public function fs_score_details()
    {
        $role = D('User4s')->where(array('id' => session('user_id')))->getField('role');
        $this->assign('role', $role);
        $total_jifen = D('User4s')->where(array('id' => session('user_id')))->getField('total_jifen');
        $this->assign('total_jifen', $total_jifen);

        $count = M('score')->where(array('user_id' => session('user_id')))->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出
// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $info = M('score')->where(array('user_id' => session('user_id')))->limit($Page->firstRow . ',' . $Page->listRows)->order('createtime desc')->select();

        $this->assign('pagelist', $show);
        $this->assign('info', $info);
        $this->display();
    }

    /**
     *4s店用户---积分兑换商品展示页面
     */
    public function fs_score_exchange()
    {
        $score = D('User4s')->where(array('id' => session('user_id')))->getField('total_jifen');
        $this->assign('score', $score);
        $this->display();
    }

    /**
     *4s店用户-----验证手机号验证码是否正确
     */
    public function fs_codecheck()
    {
        $code = I('code');
        if ($_SESSION['lost_time'] < time()) {
            unset($_SESSION['lost_time']);
            unset($_SESSION['code']);
            $res['status'] = 2;
            $res['msg'] = '手机验证码失效,请重新发送！';
        } else {
            if ($_SESSION['tel'] == I('telphone')) {
                if ($code == session('code')) {
                    $res['status'] = 1;
                    $res['next'] = '/index.php/Member4s/fs_score_exchange';

                } else {
                    $res['status'] = 2;
                    $res['msg'] = '验证码输入错误,请重新输入';
                }
            } else {
                $res['status'] = 2;
                $res['msg'] = '请输入获得验证码的手机号';
            }
        }

        $this->ajaxReturn($res);

    }

    /**
     *4s店用户---积分兑换扣除积分
     */
    public function fs_score_goods()
    {
        $score = D('ScoreExchange');
        if (!$score->create()) {
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            $this->error($score->getError());
        } else {
            // 验证通过 可以进行其他数据操作
            $info = $score->score_goods_add();
            if ($info) {
                D('User4s')->where(array('id' => session('user_id')))->setDec('total_jifen', I('score_value'));
                $data['id'] = md5(microtime());
                $data['user_id'] = session('user_id');
                $data['score'] = '-' . I('score_value');
                $data['info'] = '兑换' . I('goods_name') . '，消耗' . I('score_value') . '积分';
                M('score')->add($data);

                $scorecount = M('ScoreExchange')->where(array('user_id' => session('user_id'), 'isdelete' => 0))->count();
                if ($scorecount == 1) {
                    $res['status'] = 1;
                    $res['next'] = '/index.php/Member4s/fs_address';
                } else {
                    $datafind['user_id'] = session('user_id');
                    $datafind['status'] = 1;
                    $info = D('ReceiptAddress')->address_find($datafind);
                    if ($info) {
                        $res['status'] = 1;
                        $res['next'] = '/index.php/Member4s/fs_score_details';
                    } else {
                        $res['status'] = 1;
                        $res['next'] = '/index.php/Member4s/fs_address';
                    }
                }

            } else {
                $res['status'] = 2;
                $res['msg'] = '商品兑换失败';
            }
        }

        $this->ajaxReturn($res);
    }

    /**
     *4s店用户---积分兑换扣除积分--添加地址
     */
    public function fs_score_address_add()
    {
        $score = D('ScoreExchange');
        if (!$score->create()) {
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            $this->error($score->getError());
        } else {
            // 验证通过 可以进行其他数据操作
            $_POST['role'] = 2;
            $info = $score->score_goods_add();
            if ($info) {
                //兑换成功，扣除积分
                D('User4s')->where(array('id' => session('user_id')))->setDec('total_jifen', I('score_value'));
                $data['id'] = md5(microtime());
                $data['user_id'] = session('user_id');
                $data['score'] = '-' . I('score_value');
                $data['info'] = '兑换' . I('goods_name') . '，消耗' . I('score_value') . '积分';
                $z1 = M('score')->add($data);

                //兑换成功，新增地址
                $address = D('ReceiptAddress');
                $_POST['role'] = 2;
                $_POST['status'] = 1;
                $z2 = $address->address_add();

                if ($z1 and $z2) {
                    $res['status'] = 1;
                    $res['msg'] = '成功';
                } else {
                    $res['status'] = 2;
                    $res['msg'] = '失败';
                }

            } else {
                $res['status'] = 2;
                $res['msg'] = '商品兑换失败';
            }
        }

        $this->ajaxReturn($res);
    }

    /**
     *4s店用户---积分兑换扣除积分--修改地址
     */
    public function fs_score_address_save()
    {
        $score = D('ScoreExchange');
        if (!$score->create()) {
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            $this->error($score->getError());
        } else {
            // 验证通过 可以进行其他数据操作
            $info = $score->score_goods_add();
            if ($info) {
                //兑换成功，扣除积分
                D('User4s')->where(array('id' => session('user_id')))->setDec('total_jifen', I('score_value'));
                $data['id'] = md5(microtime());
                $data['user_id'] = session('user_id');
                $data['score'] = '-' . I('score_value');
                $data['info'] = '兑换' . I('goods_name') . '，消耗' . I('score_value') . '积分';
                $z1 = M('score')->add($data);

                //兑换成功，修改地址
                $address = D('ReceiptAddress');
                $_POST['id'] = I('address_id');
                $_POST['status'] = 1;
                $z2 = $address->address_save();

                if ($z1 and $z2) {
                    $res['status'] = 1;
                    $res['msg'] = '成功';
                } else {
                    $res['status'] = 2;
                    $res['msg'] = '失败';
                }

            } else {
                $res['status'] = 2;
                $res['msg'] = '商品兑换失败';
            }
        }

        $this->ajaxReturn($res);
    }

    /**
     *添加地址页面
     */
    public function fs_address()
    {
        $info = M('receipt_address')->where(array('user_id' => session('user_id'), 'status' => 1))->find();
        $this->assign('info', $info);
        $this->display();
    }

    /**
     *添加地址功能
     */
    public function fs_address_add()
    {
        $address = D('ReceiptAddress');
        if (!$address->create()) {
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            $data['status'] = 2;
            $data['msg'] = $address->getError();
        } else {
            // 验证通过 可以进行其他数据操作
            $_POST['role'] = 2;
            $_POST['status'] = 1;
            $_POST['province_id'] = I('province_id');
            $_POST['city_id'] = I('city_id');
            $_POST['quarter_id'] = I('quarter_id');
            $info = $address->address_add();
            if ($info) {
                $data['status'] = 1;
                $data['next'] = '/index.php/Member4s/fs_score_details';
            } else {
                $data['status'] = 2;
                $data['msg'] = '地址添加失败！';
            }
        }

        $this->ajaxReturn($data);
    }

    /**
     *修改地址功能
     */
    public function fs_address_save()
    {
        $address = D('ReceiptAddress');
        if (!$address->create()) {
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            $this->error($address->getError());
        } else {
            // 验证通过 可以进行其他数据操作
            $_POST['status'] = 1;
            $info = $address->address_save();

            if ($info) {
                /*$this->success('地址修改成功！');*/
                $data['status'] = 1;
                $data['msg'] = '地址修改成功！';
            } else {
                $data['status'] = 2;
                $data['msg'] = '地址修改失败！';
                /* $this->error('地址修改失败！');*/
            }

            $this->ajaxReturn($data);
        }

    }

    /**
     *ajax获得用户的所有地址
     */
    public function get_user_addr()
    {
        if (IS_POST) {
            $addrs = M('receipt_address')->where(array('user_id' => I('user_id'), 'role' => 2, 'isdelete' => 0))->find();
            if ($addrs) {
                //获取成功
                $data['status'] = 1;
                $data['list'] = $addrs;
            } else {
                //获取失败
                $data['status'] = 0;
            }
            $this->ajaxReturn($data);
        } else {
            $this->redirect('Public/login');
        }
    }

    /************************************************底价车******************************************************************/

    /**
     *4s店用户-------报价管理----已报价
     */
    public function fs_quotation()
    {
        /** @var 主营品牌id $brand_id */
        $brand_id = D('User4s')->where(array('id' => session('user_id')))->getField('brand_4s');
        $this->assign('brand_id', $brand_id);
        /** @var 主营品牌名称 $brand_name */
        $brand_name = M("brand")->where(array('id' => $brand_id))->getField('brand_name');
        $this->assign('brand_name', $brand_name);

        /** @var 主营品牌下的车型 $car_model */
        $car_model = M("car_model")->where(array('brand_id' => $brand_id, 'isdelete' => 0, 'status' => 1))->group('car_model_name')->order('car_model_name')->select();
        /*4S店副营品牌*/
        $brandList = M('view_brand_user')->where(array('user_4s_id' => session('user_id'), 'isdelete' => 0))->select();
        if ($brandList) {
            $this->assign('brandList', $brandList);
        } else {
            $this->assign('brandList', '');
        }
        $this->assign('car_model', $car_model);
        $this->display();
    }

    /**
     *4s店用户-------报价管理----未报价
     */
    public function fs_unquotation()
    {
        /** @var 主营品牌id $brand_id */
        $brand_id = D('User4s')->where(array('id' => session('user_id')))->getField('brand_4s');
        $this->assign('brand_id', $brand_id);
        /** @var 主营品牌名称 $brand_name */
        $brand_name = M("brand")->where(array('id' => $brand_id))->getField('brand_name');
        $this->assign('brand_name', $brand_name);

        /** @var 主营品牌下的车型 $car_model */
        $car_model = M("car_model")->where(array('brand_id' => $brand_id, 'isdelete' => 0))->group('car_model_name')->order('car_model_name')->select();
        $this->assign('car_model', $car_model);

        /*4S店副营品牌*/
        $brandList = M('view_brand_user')->where(array('user_4s_id' => session('user_id'), 'isdelete' => 0))->select();
        if ($brandList) {
            $this->assign('brandList', $brandList);
        } else {
            $this->assign('brandList', '');
        }
        $this->display();
    }

    /**
     *4s店用户-------报价管理----已报价异步提交
     */
    public function FS_quotation_ajax()
    {

        $msg = I("car_model_id");
        if (!empty($msg)) {
            $data_filter['car_model_id'] = $msg;
        }/*else{
            $car_model_hate=M('car_model_prefer')->where(array('user_id'=>session('user_id'),'isdelete'=>0))->field('car_model_id')->select();
            if(!empty($car_model_hate)){
                foreach ($car_model_hate as $key => $value) {
                    $car_model_hate2[]=$value['car_model_id'];
                }
                $data_filter['car_model_id']=array('not in',$car_model_hate2);
            }

        }*/

        $msg1 = I("car_id");
        if (!empty($msg1)) {
            $data_filter['car_id'] = $msg1;
        }
        /*else{
            $car_hate=M('car_prefer')->where(array('user_id'=>session('user_id'),'isdelete'=>0))->field('car_id')->select();
            if(!empty($car_hate)){
                foreach ($car_hate as $key => $value) {
                    $car_hate2[]=$value['car_id'];
                }
                $data_filter['car_id']=array('not in',$car_hate2);
            }

        }*/

        $msg2 = I("exterior_color_id");
        if (!empty($msg2)) {
            $data_filter['exterior_color_id'] = $msg2;
        }
        /*else{
            $exterior_hate=M('exterior_color_prefer')->where(array('user_id'=>session('user_id'),'isdelete'=>0))->field('exterior_color_id')->select();
            if(!empty($exterior_hate)){
                foreach ($exterior_hate as $key => $value) {
                    $exterior_hate2[]=$value['exterior_color_id'];
                }
                $data_filter['exterior_color_id']=array('not in',$exterior_hate2);
            }

        }*/

        $msg3 = I("interior_color_id");
        if (!empty($msg3)) {
            $data_filter['interior_color_id'] = $msg3;
        }
        /*else{
            $interior_hate=M('interior_color_prefer')->where(array('user_id'=>session('user_id'),'isdelete'=>0))->field('interior_color_id')->select();
            if(!empty($interior_hate)){
                foreach ($interior_hate as $key => $value) {
                    $interior_hate2[]=$value['interior_color_id'];
                }
                $data_filter['interior_color_id']=array('not in',$interior_hate2);
            }

        }*/
        $records = I('pagesize');
        $data_filter['isdelete'] = 0;
        if (isset($_POST['brand_id']) and $_POST['brand_id'] != "") {
            $array = D('view_car_price')->where($data_filter)->where(array('user_id' => session('user_id'), 'brand_id' => I('brand_id')))->order('createtime desc')->select();
        } else {
            $array = D('view_car_price')->where($data_filter)->where(array('user_id' => session('user_id')))->order('createtime desc')->select();
        }
        $count = count($array);
        $Page = new \Think\PageAjax($count, $records);
        $pagelist = $Page->show();
        $info2[] = $pagelist;
        $info = array_slice($array, $Page->firstRow, $Page->listRows);
        foreach ($info as $key => $value) {
            $info[$key]['sales_area_name'] = M('sales_area')->where(array('car_price_id' => $value['id'], 'type' => 1, 'isdelete' => 0))->field('sales_area_name')->select();
            if (!$info[$key]['sales_area_name']) {
                $city_id = D('User4s')->where(array('id' => session('user_id')))->getField('city_id');
                $aa[0]['sales_area_name'] = M('city')->where(array('id' => $city_id))->getField('city_name');
                $info[$key]['sales_area_name'] = $aa;
            }
        }
        $info2[] = $info;
        $this->ajaxReturn($info2);
    }

    /**
     *4s店用户-------报价管理----未报价异步提交
     */
    public function FS_unquotation_ajax()
    {
        $msg = I("car_model_id");
        if (!empty($msg)) {
            $data_filter['car_model_id'] = $msg;
        } else {
            $car_model_hate = M('car_model_prefer')->where(array('user_id' => session('user_id'), 'isdelete' => 0))->field('car_model_id')->select();
            if (!empty($car_model_hate)) {
                foreach ($car_model_hate as $key => $value) {
                    $car_model_hate2[] = $value['car_model_id'];
                }
                $data_filter['car_model_id'] = array('not in', $car_model_hate2);
            }

        }

        $msg1 = I("car_id");
        if (!empty($msg1)) {
            $data_filter['car_id'] = $msg1;
        } else {
            $car_hate = M('car_prefer')->where(array('user_id' => session('user_id'), 'isdelete' => 0))->field('car_id')->select();
            if (!empty($car_hate)) {
                foreach ($car_hate as $key => $value) {
                    $car_hate2[] = $value['car_id'];
                }
                $data_filter['car_id'] = array('not in', $car_hate2);
            }

        }

        $msg2 = I("exterior_color_id");
        if (!empty($msg2)) {
            $data_filter['exterior_color_id'] = $msg2;
        } else {
            $exterior_hate = M('exterior_color_prefer')->where(array('user_id' => session('user_id'), 'isdelete' => 0))->field('exterior_color_id')->select();
            if (!empty($exterior_hate)) {
                foreach ($exterior_hate as $key => $value) {
                    $exterior_hate2[] = $value['exterior_color_id'];
                }
                $data_filter['exterior_color_id'] = array('not in', $exterior_hate2);
            }

        }

        $msg3 = I("interior_color_id");
        if (!empty($msg3)) {
            $data_filter['interior_color_id'] = $msg3;
        } else {
            $interior_hate = M('interior_color_prefer')->where(array('user_id' => session('user_id'), 'isdelete' => 0))->field('interior_color_id')->select();
            if (!empty($interior_hate)) {
                foreach ($interior_hate as $key => $value) {
                    $interior_hate2[] = $value['interior_color_id'];
                }
                $data_filter['interior_color_id'] = array('not in', $interior_hate2);
            }

        }
        $records = I('pagesize');
        $data_filter['isdelete'] = 0;
        /** @var 主营品牌id $brand_id */
        if (isset($_POST['brand_id']) and $_POST['brand_id'] != '') {
            $data_filter['brand_id'] = I('brand_id');
        } else {
            $brand_id = D('User4s')->where(array('id' => session('user_id')))->getField('brand_4s');
            $data_filter['brand_id'] = $brand_id;
        }
        /** @var 车库中所有信息 $all_car_info */
        $array = D("view_car_color")->where($data_filter)->select();
        /** @var 已报价信息 $quotes_info */
        $quotes_info = M('view_car_price')->where(array('user_id' => session('user_id'), 'isdelete' => 0))->select();
        /** 排除车库内已经被报价了的信息 */
        foreach ($array as $k1 => $v1) {
            foreach ($quotes_info as $kk => $vv) {
                if (($vv['exterior_color_id'] == $v1['exterior_color_id']) && ($vv['interior_color_id'] == $v1['interior_color_id'])) {
                    unset($array[$k1]);
                }
            }
        }
        $count = count($array);
        $Page = new \Think\PageAjax($count, $records);
        $pagelist = $Page->show();
        $info2[] = $pagelist;
        $info = array_slice($array, $Page->firstRow, $Page->listRows);
        foreach ($info as $key => $value) {
            $city_id = D('User4s')->where(array('id' => session('user_id')))->getField('city_id');
            $city_name[0]['sales_area_name'] = M('city')->where(array('id' => $city_id))->getField('city_name');
            $info[$key]['sales_area_name'] = $city_name;
        }

        $info2[] = $info;
        $this->ajaxReturn($info2);
    }

    /**
     *4s店用户-------添加报价
     */
    public function FS_Quotation_add()
    {
        $car_price = M("car_price");
        $data['id'] = md5(microtime());
        $data['user_id'] = session('user_id');
        $data['exterior_color_id'] = I('exterior_color_id');
        $data['interior_color_id'] = I('interior_color_id');
        $data['car_id'] = I('car_id');
        $data['price'] = I('price');
        $data['discount'] = I('discount');
        $data['stock'] = I('stock');
        $data['onway'] = I('onway');
        $data['low_price'] = I('low_price');
        $data['createuser'] = session('user_name');
        $data['createip'] = $_SERVER['HTTP_X_REAL_IP'];
        //品牌分组，避免组内品牌重复
        $brand_id = D("view_car")->where(array('id' => $data['car_id']))->getField('brand_id');
        $count_brand = D("view_car_price")->where(array('brand_id' => $brand_id))->count();
        $data['show_index'] = $count_brand + 1;
        //参照首页公开底价设置规则
        $is_xunjia = M("index_car_price")->where(array('car_id' => $data['car_id'], 'exterior_color_id' => $data['exterior_color_id'], 'interior_color_id' => $data['interior_color_id']))->find();
        if (!empty($is_xunjia)) {
            if ($is_xunjia['isbaojia'] == 0) {
                M('index_car_price')->where(array('id' => $is_xunjia['id']))->setField(array('isbaojia' => 1));
            }
            $data['is_xunjia'] = 2;
        } else {
            $data['is_xunjia'] = 1;
        }
        $low_price = M('car_price')->where(array('exterior_color_id' => I('exterior_color_id'), 'interior_color_id' => I('interior_color_id')))->order('low_price asc')->getField('low_price');
        if (empty($low_price)) {
            $data['month_status'] = 1;
            $data['quarter_status'] = 1;
            $user_car_info = M('user_car')->where(array('user_id' => $data['user_id'], 'car_id' => $data['car_id'], 'isdelete' => 0))->find();
            if (!empty($user_car_info)) {
                M('user_car')->where(array('id' => $user_car_info['id'], 'isdelete' => 0))->setInc('month_car_price_num', 1);
                M('user_car')->where(array('id' => $user_car_info['id'], 'isdelete' => 0))->setInc('quarter_car_price_num', 1);
            } else {
                $data_user_car['id'] = md5(microtime());
                $data_user_car['user_id'] = $data['user_id'];
                $data_user_car['car_id'] = $data['car_id'];
                $data_user_car['month_status'] = 1;
                $data_user_car['quarter_status'] = 1;
                $data_user_car['month_car_price_num'] = 1;
                $data_user_car['quarter_car_price_num'] = 1;
                M('user_car')->add($data_user_car);
                D('User4s')->where(array('id' => $data['user_id'], 'isdelete' => 0))->setInc('month_car_num', 1);
                D('User4s')->where(array('id' => $data['user_id'], 'isdelete' => 0))->setInc('quarter_car_num', 1);
            }
        } elseif ($data['low_price'] <= $low_price) {
            $data['month_status'] = 1;
            $data['quarter_status'] = 1;

            $user_car_info = M('user_car')->where(array('user_id' => $data['user_id'], 'car_id' => $data['car_id'], 'isdelete' => 0))->find();
            if (!empty($user_car_info)) {

                M('user_car')->where(array('id' => $user_car_info['id'], 'isdelete' => 0))->setInc('month_car_price_num', 1);
                M('user_car')->where(array('id' => $user_car_info['id'], 'isdelete' => 0))->setInc('quarter_car_price_num', 1);

            } else {
                $data_user_car['id'] = md5(microtime());
                $data_user_car['user_id'] = $data['user_id'];
                $data_user_car['car_id'] = $data['car_id'];
                $data_user_car['month_status'] = 1;
                $data_user_car['quarter_status'] = 1;
                $data_user_car['month_car_price_num'] = 1;
                $data_user_car['quarter_car_price_num'] = 1;
                M('user_car')->add($data_user_car);
                D('User4s')->where(array('id' => $data['user_id'], 'isdelete' => 0))->setInc('month_car_num', 1);
                D('User4s')->where(array('id' => $data['user_id'], 'isdelete' => 0))->setInc('quarter_car_num', 1);
            }

        }
        $z = $car_price->add($data);
        $area = I('cities');
        if (!empty($area)) {
            foreach ($area as $k => $v) {
                $datasales['id'] = md5(microtime());
                $datasales['sales_area_name'] = $v['name'];
                $datasales['sales_area_level'] = $v['lvl'];
                $datasales['car_price_id'] = $data['id'];
                $datasales['type'] = 1;
                $datasales['isdelete'] = 0;
                M("sales_area")->add($datasales);
            }
        } else {
            $datasales['id'] = md5(microtime());
            $city_id = D('User4s')->where(array('id' => session('user_id')))->getField('city_id');
            $datasales['sales_area_name'] = M('city')->where(array('id' => $city_id))->getField('city_name');
            $datasales['sales_area_level'] = 3;
            $datasales['car_price_id'] = $data['id'];
            $datasales['type'] = 1;
            $datasales['isdelete'] = 0;
            M("sales_area")->add($datasales);
        }

        if ($z) {
            $datar['status'] = 0;
        } else {
            $datar['status'] = 1;
            $datar['info'] = '添加报价失败！';
        }
        $this->ajaxReturn($datar);

    }

    /**
     *4s店用户-------批量添加报价
     */
    public function FS_Quotation_add_all()
    {
        //判断是否有登录
        if ($_SESSION['user_id']) {
            $user_info = D('User4s')->where(array('id' => $_SESSION['user_id'], 'isdelete' => 0))->find();
            //判断是否有数据提交
            if ($_POST['car_list']) {
                /*批量报价里的 报价 优惠 最低价*/
                $data['price'] = $_POST['price'];
                $data['discount'] = $_POST['discount'];
                $data['low_price'] = $_POST['low_price'];
                //循环开始
                $sql_price = "insert into gch_car_price (id,user_id,exterior_color_id,interior_color_id,car_id,stock,onway,price,discount,low_price,createuser,createip,is_xunjia,month_status,quarter_status) values";
                $sql_area = "insert into gch_sales_area (id,sales_area_name,sales_area_level,car_price_id,type,isdelete) values";
                foreach ($_POST['car_list'] as $key => $value) {
                    $data['id'] = md5(microtime());
                    $data['user_id'] = session('user_id');
                    $data['exterior_color_id'] = $value['exterior_color_id'];
                    $data['interior_color_id'] = $value['interior_color_id'];
                    $data['car_id'] = $value['car_id'];
                    $data['stock'] = $value['stock'];
                    $data['onway'] = $value['onway'];
                    $data['createuser'] = session('user_name');
                    $data['createip'] = $_SERVER['HTTP_X_REAL_IP'];
                    //品牌分组，避免组内品牌重复
                    $brand_id = D("view_car")->where(array('id' => $data['car_id']))->getField('brand_id');
                    $count_brand = D("view_car_price")->where(array('brand_id' => $brand_id))->count();
                    $data['show_index'] = $count_brand + 1;
                    //参照首页公开底价设置规则
                    $is_xunjia = M("index_car_price")->where(array('car_id' => $data['car_id'], 'exterior_color_id' => $data['exterior_color_id'], 'interior_color_id' => $data['interior_color_id']))->find();
                    if (!empty($is_xunjia)) {
                        if ($is_xunjia['isbaojia'] == 0) {
                            M('index_car_price')->where(array('id' => $is_xunjia['id']))->setField(array('isbaojia' => 1));
                        }
                        $data['is_xunjia'] = 2;
                    } else {
                        $data['is_xunjia'] = 1;
                    }
                    //判断是否为最低价
                    $low_price = M('view_car_price')->where(array('car_id' => $data['car_id'], 'exterior_color_id' => $data['exterior_color_id'], 'interior_color_id' => $data['interior_color_id'], 'city_id' => $user_info['city_id']))->order('low_price asc')->getField('low_price');
                    if (empty($low_price) or $data['low_price'] <= $low_price) {
                        $data['month_status'] = 1;
                        $data['quarter_status'] = 1;
                        $user_car_info = M('user_car')->where(array('user_id' => $data['user_id'], 'car_id' => $data['car_id'], 'isdelete' => 0))->find();
                        //判断如果这个车款之前有加过积分下次就不会在加
                        if (!empty($user_car_info)) {
                            M('user_car')->where(array('id' => $user_car_info['id'], 'isdelete' => 0))->setInc('month_car_price_num', 1);
                            M('user_car')->where(array('id' => $user_car_info['id'], 'isdelete' => 0))->setInc('quarter_car_price_num', 1);
                        } else {
                            $data_user_car['id'] = md5(microtime());
                            $data_user_car['user_id'] = $_SESSION['user_id'];
                            $data_user_car['car_id'] = $data['car_id'];
                            $data_user_car['month_status'] = 1;
                            $data_user_car['quarter_status'] = 1;
                            $data_user_car['month_car_price_num'] = 1;
                            $data_user_car['quarter_car_price_num'] = 1;
                            M('user_car')->add($data_user_car);
                            D('User4s')->where(array('id' => $_SESSION['user_id'], 'isdelete' => 0))->setInc('month_car_num', 1);
                            D('User4s')->where(array('id' => $_SESSION['user_id'], 'isdelete' => 0))->setInc('quarter_car_num', 1);
                        }
                    } else {
                        $data['month_status'] = 0;
                        $data['quarter_status'] = 0;
                    }
                    //插入·car_price·数据库
                    $sql_price .= "('" . $data['id'] . "','" . $data['user_id'] . "','" . $data['exterior_color_id'] . "','" . $data['interior_color_id'] . "','" . $data['car_id'] . "','" . $data['stock'] . "','" . $data['onway'] . "','" . $data['price'] . "','" . $data['discount'] . "','" . $data['low_price'] . "','" . $data['createuser'] . "','" . $data['createip'] . "','" . $data['is_xunjia'] . "','" . $data['month_status'] . "','" . $data['quarter_status'] . "'),";
                    //批量添加区域
                    $area = I('cities');
                    if (!empty($area)) {
                        foreach ($area as $k => $v) {
                            $sql_area .= "(REPLACE (uuid(), \"-\", \"\"),'" . $v['name'] . "','" . $v['lvl'] . "','" . $data['id'] . "','1','0'),";
                        }
                    } else {
                        $city_name = M('city')->where(array('id' => $user_info['city_id']))->getField('city_name');
                        $sql_area .= "(REPLACE (uuid(), \"-\", \"\"),'" . $city_name . "','3','" . $data['id'] . "','1','0'),";
                    }
                }
                $sql_price = substr($sql_price, 0, strlen($sql_price) - 1);
                $add = M()->execute($sql_price);
                //循环结束
                if ($add) {
                    $sql_area = substr($sql_area, 0, strlen($sql_area) - 1);
                    $add_area = M()->execute($sql_area);
                    //添加区域失败
                    if (!$add_area) {
                        $datar['status'] = 1;
                        $datar['info'] = '添加区域失败！';
                    }
                    $datar['status'] = 0;
                    $datar['info'] = '添加报价成功！';
                } else {
                    $datar['status'] = 1;
                    $datar['info'] = '添加报价失败！';
                }
                $this->ajaxReturn($datar);
            } else {
                $this->redirect('Index/index');
            }
        } else {
            $this->redirect('Index/index');
        }

    }

    /**
     *4s店用户-------批量修改区域
     */
    public function FS_Area_save_all()
    {
        //判断用户是否登录
        if ($_SESSION['user_id']) {
            $user_info = D('User4s')->where(array('id' => $_SESSION['user_id'], 'isdelete' => 0))->find();
            //判断是否有数据提交
            if ($_POST['id_list']) {
                foreach ($_POST['id_list'] as $key => $value) {
                    M('sales_area')->where(array('car_price_id' => $value, 'type' => 1))->save(array('isdelete' => 1));
                    $area = I('cities');
                    $sql_area = "insert into gch_sales_area (id,sales_area_name,sales_area_level,car_price_id,type,isdelete) values";
                    if (!empty($area)) {
                        foreach ($area as $k => $v) {
                            $sql_area .= "(REPLACE (uuid(), \"-\", \"\"),'" . $v['name'] . "','" . $v['lvl'] . "','" . $value . "','1','0'),";
                        }
                    } else {
                        $city_name = M('city')->where(array('id' => $user_info['city_id']))->getField('city_name');
                        $sql_area .= "(REPLACE (uuid(), \"-\", \"\"),'" . $city_name . "','3','" . $value . "','1','0'),";
                    }
                    $sql_area = substr($sql_area, 0, strlen($sql_area) - 1);
                    $add_area = M()->execute($sql_area);
                }

                if ($add_area) {
                    $datar['status'] = 0;
                    $datar['info'] = '修改区域成功！';
                } else {
                    $datar['status'] = 1;
                    $datar['info'] = '修改区域失败！';
                }
                $this->ajaxReturn($datar);
            } else {
                $this->redirect('Index/index');
            }
        } else {
            $this->redirect('Public/login');
        }
    }

    /**
     *4s店用户-------修改报价
     */
    public function FS_Quotation_save()
    {
        $car_price = M("car_price");
        $sales_area = M("sales_area");
        $data['exterior_color_id'] = I('exterior_color_id');
        $data['interior_color_id'] = I('interior_color_id');
        $data['car_id'] = I('car_id');
        $car_price_info = $car_price->where(array('id' => I('id'), 'isdelete' => 0))->find();
        $car_price_id = $car_price_info['id'];
        $datae['price'] = I('price');
        $datae['discount'] = I('discount');
        $datae['stock'] = I('stock');
        $datae['onway'] = I('onway');
        $datae['low_price'] = I('low_price');
        $datae['updatetime'] = date(('Y-m-d H:i:s'), time());
        $datae['updateuser'] = session('user_name');
        $datae['updateip'] = $_SERVER['HTTP_X_REAL_IP'];
        //判断要修改的低价车的车款是否曾经为最低价 panyuting
        $find_low_price = M('car_price')->where(array('id' => $car_price_info['id'], 'month_status' => 1))->find();
        if ($find_low_price) {
            if ($car_price_info['updatetime'] == '0000-00-00 00:00:00') {
                $n = time() - strtotime($car_price_info['createtime']);
            } else {
                $n = time() - strtotime($car_price_info['updatetime']);
            }
            //小于24小时上调价格则删除积分标记
            if ($n < 24 * 3600) {
                $datae['month_status'] = 0;
                $datae['quarter_status'] = 0;
                $low_car_price = M('user_car')->where(array('user_id' => session('user_id'), 'car_id' => $data['car_id'], 'isdelete' => 0))->find();
                if ($low_car_price['month_car_price_num'] == 1) {
                    if ($low_car_price['quarter_car_price_num'] == 1) {
                        $de['isdelete'] = 1;
                        M('user_car')->where(array('user_id' => session('user_id'), 'car_id' => $data['car_id'], 'isdelete' => 0))->save($de);
                        D('User4s')->where(array('id' => session('user_id'), 'isdelete' => 0))->setDec('month_car_num', 1);
                        D('User4s')->where(array('id' => session('user_id'), 'isdelete' => 0))->setDec('quarter_car_num', 1);
                    } elseif ($low_car_price['quarter_car_price_num'] > 1) {
                        //月份底价数为0
                        M('user_car')->where(array('user_id' => session('user_id'), 'car_id' => $data['car_id'], 'isdelete' => 0))->setDec('month_car_price_num', 1);
                        D('User4s')->where(array('id' => session('user_id'), 'isdelete' => 0))->setDec('month_car_num', 1);
                    }
                } elseif ($low_car_price['month_car_price_num'] > 1) {
                    M('user_car')->where(array('user_id' => session('user_id'), 'car_id' => $data['car_id'], 'isdelete' => 0))->setDec('month_car_price_num', 1);
                    M('user_car')->where(array('user_id' => session('user_id'), 'car_id' => $data['car_id'], 'isdelete' => 0))->setDec('quarter_car_price_num', 1);
                }
            }
        }
        $area = I('cities');
        /** @var 修改报价表中的数据 $z */
        $z = $car_price->where(array('id' => $car_price_id))->save($datae);

        /** @var 删除原来的销售区域 $info */
        $info = $sales_area->where(array('car_price_id' => $car_price_id))->select();
        $datadelete['isdelete'] = 1;
        foreach ($info as $k => $v) {
            $sales_area->where(array('id' => $v['id']))->save($datadelete);
        }
        foreach ($area as $k => $v) {
            $datasales['id'] = md5(microtime());
            $datasales['sales_area_name'] = $v['name'];
            $datasales['sales_area_level'] = $v['lvl'];
            $datasales['car_price_id'] = $car_price_id;
            $datasales['type'] = 1;
            $datasales['isdelete'] = 0;
            $sales_area->add($datasales);
        }
        if ($z !== false) {
            $datar['status'] = 0;
        } else {
            $datar['status'] = 1;
            $datar['info'] = '修改报价失败！';
        }
        $this->ajaxReturn($datar);
    }

    /**
     *4s店用户--------批量修改报价
     */
    public function fs_quotation_all_save()
    {
        $z = 0;
        $car_list = I('car_list');
        $area = I('cities');
        $data['price'] = I('price');
        $data['low_price'] = I('low_price');
        $data['discount'] = I('discount');
        foreach ($car_list as $k => $v) {
            $data['stock'] = $v['stock'];
            $data['onway'] = $v['onway'];
            $data['updatetime'] = date(('Y-m-d H:i:s'), time());
            $data['updateuser'] = session('user_name');
            $data['updateip'] = $_SERVER['HTTP_X_REAL_IP'];
            //判断要修改的低价车的车款是否曾经为最低价 panyuting
            $find_low_price = M('car_price')->where(array('id' => $v['id'], 'month_status' => 1))->find();
            if ($find_low_price) {
                if ($v['updatetime'] == '0000-00-00 00:00:00') {
                    $n = time() - strtotime($v['createtime']);
                } else {
                    $n = time() - strtotime($v['updatetime']);
                }
                //小于24小时上调价格则删除积分标记
                if ($n < 24 * 3600) {
                    $data['month_status'] = 0;
                    $data['quarter_status'] = 0;
                    $low_car_price = M('user_car')->where(array('user_id' => session('user_id'), 'car_id' => $find_low_price['car_id'], 'isdelete' => 0))->find();
                    if ($low_car_price['month_car_price_num'] == 1) {
                        if ($low_car_price['quarter_car_price_num'] == 1) {
                            $de['isdelete'] = 1;
                            M('user_car')->where(array('user_id' => session('user_id'), 'car_id' => $find_low_price['car_id'], 'isdelete' => 0))->save($de);
                            D('User4s')->where(array('id' => session('user_id'), 'isdelete' => 0))->setDec('month_car_num', 1);
                            D('User4s')->where(array('id' => session('user_id'), 'isdelete' => 0))->setDec('quarter_car_num', 1);
                        } elseif ($low_car_price['quarter_car_price_num'] > 1) {
                            //月份底价数为0
                            M('user_car')->where(array('user_id' => session('user_id'), 'car_id' => $find_low_price['car_id'], 'isdelete' => 0))->setDec('month_car_price_num', 1);
                            D('User4s')->where(array('id' => session('user_id'), 'isdelete' => 0))->setDec('month_car_num', 1);
                        }
                    } elseif ($low_car_price['month_car_price_num'] > 1) {
                        M('user_car')->where(array('user_id' => session('user_id'), 'car_id' => $find_low_price['car_id'], 'isdelete' => 0))->setDec('month_car_price_num', 1);
                        M('user_car')->where(array('user_id' => session('user_id'), 'car_id' => $find_low_price['car_id'], 'isdelete' => 0))->setDec('quarter_car_price_num', 1);
                    }
                }
            }
            $z = M('car_price')->where(array('id' => $v['id']))->save($data);
            if ($z) {
                $z = 1;
            } else {
                $z = 0;
            }
            /** @var 删除原来的销售区域 $info */
            $datadelete['isdelete'] = 1;
            M('sales_area')->where(array('car_price_id' => $v['id']))->save($datadelete);
            foreach ($area as $kk => $vv) {
                $datasales['id'] = md5(microtime());
                $datasales['sales_area_name'] = $vv['name'];
                $datasales['sales_area_level'] = $vv['lvl'];
                $datasales['car_price_id'] = $v['id'];
                $datasales['type'] = 1;
                $datasales['isdelete'] = 0;
                M('sales_area')->add($datasales);
            }

        }
        /*  if($z==count($car_list,0))
          {
              $datar['status']=0;
          }else
          {
              $datar['status']=1;
              $datar['info']='批量修改报价失败！';
          }*/
        if ($z == 1) {
            $datar['status'] = 0;
        } else {
            $datar['status'] = 1;
            $datar['info'] = '批量修改报价失败！';
        }
        $this->ajaxReturn($datar);

    }

    /**
     *4s店用户-------批量取消
     */
    public function FS_Quotation_deletes()
    {
        $arr = $_POST;
        foreach ($arr as $k1 => $v1) {
            $dataup['isdelete'] = 1;
            $dataup['updatetime'] = date(('Y-m-d H:i:s'), time());
            $dataup['updateuser'] = session('user_name');
            $dataup['updateip'] = $_SERVER['HTTP_X_REAL_IP'];
            $z = M('car_price')->where(array('id' => $v1))->save($dataup);
            $data = M('car_price')->where(array('id' => $v1))->find();
            // 判断要修改的低价车的车款是否曾经为最低价
            $low_car_price = M('user_car')->where(array('user_id' => session('user_id'), 'car_id' => $data['car_id'], 'isdelete' => 0))->find();
            if ($low_car_price) {
                //判断这款车包括内外饰颜色是不是当时插入数据库里的低价车
                $find_low_price = M('car_price')->where(array('user_id' => session('user_id'), 'car_id' => $data['car_id'], 'isdelete' => 0, 'exterior_color_id' => $data['exterior_color_id'], 'interior_color_id' => $data['interior_color_id'], 'month_status' => 1))->find();
                if ($find_low_price) {
                    if ($data['updatetime'] == '0000-00-00 00:00:00') {
                        $n = time() - strtotime($data['createtime']);

                    } else {
                        $n = time() - strtotime($data['updatetime']);
                    }
                    //小于24小时上调价格则删除积分标记
                    if ($n < 24 * 3600) {


                        if ($low_car_price['month_car_price_num'] == 1) {
                            if ($low_car_price['quarter_car_price_num'] == 1) {
                                $de['isdelete'] = 1;
                                M('user_car')->where(array('user_id' => session('user_id'), 'car_id' => $data['car_id'], 'isdelete' => 0))->save($de);
                                D('User4s')->where(array('id' => session('user_id'), 'isdelete' => 0))->setDec('month_car_num', 1);
                                D('User4s')->where(array('id' => session('user_id'), 'isdelete' => 0))->setDec('quarter_car_num', 1);
                            } elseif ($low_car_price['quarter_car_price_num'] > 1) {
                                //月份底价数为0
                                M('user_car')->where(array('user_id' => session('user_id'), 'car_id' => $data['car_id'], 'isdelete' => 0))->setDec('month_car_price_num', 1);
                                D('User4s')->where(array('id' => session('user_id'), 'isdelete' => 0))->setDec('month_car_num', 1);
                            }
                        } elseif ($low_car_price['month_car_price_num'] > 1) {
                            M('user_car')->where(array('user_id' => session('user_id'), 'car_id' => $data['car_id'], 'isdelete' => 0))->setDec('month_car_price_num', 1);
                            M('user_car')->where(array('user_id' => session('user_id'), 'car_id' => $data['car_id'], 'isdelete' => 0))->setDec('quarter_car_price_num', 1);
                        }
                    }
                }
            }
            //判断要修改的低价车是否曾经为最低价end
        }
        if ($z !== false) {
            $datar['status'] = 1;
            $datar['info'] = '批量取消报价成功！';
        } else {
            $datar['status'] = 0;
            $datar['info'] = '批量取消报价失败！';
        }
        $this->ajaxReturn($datar);
    }

    /**
     *4s店用户------历史报价
     */
    public function fs_quotation_history()
    {
        $car_price_id = I('car_price_id');
        if (!empty($car_price_id)) {
            $car_price_info = D("view_car_price")->where(array('id' => $car_price_id))->find();
            $price_trend = M("price_trend")->where(array('car_price_id' => $car_price_id))->group('discount')->select();
            /*	$price_trend=M("view_car_price")->where(array('user_id'=>$car_price_info['user_id'],'car_id'=>$car_price_info['car_id'],'exterior_color_id'=>$car_price_info['exterior_color_id'],'interior_color_id'=>$car_price_info['interior_color_id'],'isdelete'=>0))->group('discount')->select();*/
            $this->assign('car_price_info', $car_price_info);
            $this->assign('price_trend', $price_trend);

        }
        $this->display();
    }


    /************************************************特价车******************************************************************/
    public function fs_especial()
    {
        $brand_id = M('user_4s')->where(array('id' => session('user_id'), 'isdelete' => 0))->getField('brand_4s');
        $info = M('brand')->where(array('id' => $brand_id, 'isdelete' => 0))->find();
        $this->assign('info', $info);
        $this->display();
    }

    /**-
     *4s店用户-------特价车
     */
    public function fs_special()
    {
        $status = I('status');
        if (!empty($status)) {
            $data['status'] = $status;
        }
        $page = I('page');
        $pagenum = I('pageNum');
        /** @var 特价车信息 $info */
        $records = I('pagesize');
        $count = D('view_special_price_car')->where($data)->where(array('user_id' => session('user_id'), 'isdelete' => 0))->count();
        $Page = new \Think\PageAjax($count, $records);
        $pagelist = $Page->show();
        $list = D('view_special_price_car')->where($data)->where(array('user_id' => session('user_id'), 'isdelete' => 0))->order('createtime desc')->limit($Page->firstRow, $Page->listRows)->select();
        foreach ($list as $key => $value) {
            $list[$key]['area'] = M("sales_area")->where(array('car_special_id' => $value['id'], 'isdelete' => 0))->select();
        }
        $info['count'] = $count;
        $info['list'] = $list;
        $info['pagelist'] = $pagelist;
        $this->ajaxReturn($info);
    }

    /**
     *4s店用户-------添加特价车
     */
    public function fs_add_special()
    {
        if (!empty($_FILES)) {
            $y = upload_local();
            if ($y) {
                $_POST['car_image'] = str_replace("big", 'type', $y['car_image']['savepath'] . $y['car_image']['savename']);
            }
        }

        if (!empty($_POST)) {
            $car_id = I('car_id');
            if (!empty($car_id)) {
                $data['car_id'] = $car_id;
            } else {
                $this->error('请选择车款');
            }
            $exterior_color_id = I('exterior_color_id');
            if (!empty($exterior_color_id)) {
                $data['exterior_color_id'] = $exterior_color_id;
            } else {
                $this->error('请选择外观颜色');
            }
            $interior_color_id = I('interior_color_id');
            if (!empty($interior_color_id)) {
                $data['interior_color_id'] = I('interior_color_id');
            } else {
                $this->error('请选择内饰颜色');
            }
            $start_date = I('start_date');
            if (!empty($start_date)) {
                $data['start_date'] = I('start_date');
            } else {
                $this->error('开始时间不能为空');
            }
            $end_date = I('end_date');
            if (!empty($end_date)) {
                $data['end_date'] = I('end_date');
            } else {
                $this->error('结束时间不能为空');
            }
            $number = I('number');
            if (!empty($number)) {
                $data['number'] = I('number');
            } else {
                $this->error('活动数量不能为空');
            }

            $data['user_id'] = session('user_id');
            $data['car_image'] = $_POST['car_image'];
            $data['special_price'] = $_POST['special_price'];
            $data['price'] = $_POST['price'];
            $data['description'] = $_POST['description'];
            $exist = M("special_price_car")->where($data)->select();
            if ($exist) {
                $this->error('您输入的信息有误，请您重新输入！', U('fs_especial'));
            } else {
                $data['id'] = md5(microtime());
                $data['createuser'] = session('user_name');
                $z = M("special_price_car")->add($data);
                $area = I('cities');
                if (!empty($area)) {
                    foreach ($area as $k => $v) {
                        $datasales['id'] = md5(microtime());
                        $datasales['sales_area_name'] = $v['name'];
                        $datasales['sales_area_level'] = $v['lvl'];
                        $datasales['car_special_id'] = $data['id'];
                        $datasales['type'] = 2;
                        $datasales['isdelete'] = 0;
                        M("sales_area")->add($datasales);
                    }
                } else {
                    $datasales['id'] = md5(microtime());
                    $city_id = D('User4s')->where(array('id' => session('user_id')))->getField('city_id');
                    $datasales['sales_area_name'] = M('city')->where(array('id' => $city_id))->getField('city_name');
                    $datasales['sales_area_level'] = 3;
                    $datasales['car_special_id'] = $data['id'];
                    $datasales['type'] = 2;
                    $datasales['isdelete'] = 0;
                    M("sales_area")->add($datasales);
                }
                if ($z) {
                    $this->success('添加成功', U('fs_especial'));
                } else {
                    $this->error('添加失败', U('fs_especial'));

                }

            }
        }

    }

    /**
     *4s店用户-------删除特价车
     */
    public function fs_delete_especial()
    {
        $id = I('car_special_id');
        if (!empty($id)) {
            $data['updatetime'] = date('Y-m-d H:i:s', time());
            $data['updateuser'] = session('user_name');
            $data['isdelete'] = 1;
            $z = M("special_price_car")->where(array('id' => $id))->save($data);
            if ($z) {
                $data['status'] = 1;
                $data['msg'] = '删除成功';
            } else {
                $data['status'] = 2;
                $data['msg'] = '删除失败';
            }
        }
        $this->ajaxReturn($data);
    }

    /**
     *特价车详情
     */
    public function fs_special_details()
    {
        /** @var 展示单个特价车车款颜色信息 $id */
        $id = I('special_id');
        if (!empty($id)) {
            $special_car_info = D("view_special_price_car")->where(array('id' => $id))->find();
            $special_car_info['car_image'] = OSS . str_replace('type', 'center', $special_car_info['car_image']);

            $area_info = M("sales_area")->where(array('car_special_id' => $id, 'isdelete' => 0))->select();

            foreach ($area_info as $k => $v) {
                $info[$k]['area'] = $v['sales_area_name'];
                $info[$k]['lvl'] = $v['sales_area_level'];

            }
            $special_car_info['cities'] = $info;
            $this->ajaxReturn($special_car_info);

        }
    }

    /**
     *4s店用户-------编辑特价车
     */
    public function fs_especial_editor()
    {
        if (!empty($_FILES)) {
            $y = upload_local();
            if ($y) {
                $_POST['car_image'] = str_replace("big", 'type', $y['car_image']['savepath'] . $y['car_image']['savename']);
            }
        }

        /** @var 修改报价、底价、库存等相关信息 $special_car_id */
        $special_car_id = I('special_car_id');
        if (!empty($special_car_id)) {
            $data['price'] = I('price');
            $data['special_price'] = I('special_price');
            $data['start_date'] = I('start_date');
            $data['end_date'] = I('end_date');
            $data['number'] = I('number');
            $data['description'] = I('description');
            if (!empty($_POST['car_image'])) {
                $data['car_image'] = $_POST['car_image'];
            }

            $data['status'] = 1;
            if ($data['number'] < 0) {
                $this->error('库存数量不能小于0');
            }
            $z = M("special_price_car")->where(array('id' => $special_car_id))->save($data);

            /*$sales_area_name_info=array_filter(explode('|',I('selas_area_name')));
            $sales_area_level_info=array_filter(explode('|',I('selas_area_level')));*/

            $primary_area = M("sales_area")->where(array('car_special_id' => $special_car_id))->select();
            $datadelete['isdelete'] = 1;
            foreach ($primary_area as $k => $v) {
                M("sales_area")->where(array('id' => $v['id']))->save($datadelete);
            }
            $area = I('cities');
            if (!empty($area)) {
                foreach ($area as $k => $v) {
                    $datasales['id'] = md5(microtime());
                    $datasales['sales_area_name'] = $v['name'];
                    $datasales['sales_area_level'] = $v['lvl'];
                    $datasales['car_special_id'] = $special_car_id;
                    $datasales['type'] = 2;
                    $datasales['isdelete'] = 0;
                    M("sales_area")->add($datasales);
                }
            } else {
                $datasales['id'] = md5(microtime());
                $city_id = D('User4s')->where(array('id' => session('user_id')))->getField('city_id');
                $datasales['sales_area_name'] = M('city')->where(array('id' => $city_id))->getField('city_name');
                $datasales['sales_area_level'] = 3;
                $datasales['car_special_id'] = $special_car_id;
                $datasales['type'] = 2;
                $datasales['isdelete'] = 0;
                M("sales_area")->add($datasales);
            }
            /* foreach($sales_area_name_info as $k => $v)
             {
                 $datas['id']=md5(microtime());
                 $datas['car_special_id']=$special_car_id;
                 $datas['sales_area_name']=$v;
                 $datas['sales_area_level']=$sales_area_level_info[$k];
                 $datas['type']=2;
                 $datas['isdelete']=0;
                 M("sales_area")->add($datas);
             }*/
            if ($z !== false) {
                $this->success('操作成功', U("fs_especial"));
            } else {
                $this->error('操作失败', U("fs_especial_editor"));
            }
        }
        $this->display();
    }

    /**
     *修改特价车停售在售状态
     */
    public function fs_changestatus()
    {
        $datac['id'] = I('car_special_id');
        $datac['status'] = I('status');
        $datac['updatetime'] = date('Y-m-d H:i:s', time());
        $datac['updateuser'] = session('user_name');
        $z = M('special_price_car')->save($datac);
        if ($z) {
            $datar['status'] = 1;
            $datar['msg'] = '操作成功！';
        } else {
            $datar['status'] = 2;
            $datar['msg'] = '操作失败！';
        }
        $this->ajaxReturn($datar);
    }

    /************************************************定车订单******************************************************************/

    /**
     *4s店用户-------定车订单
     */
    public function fs_ordercar()
    {
        $out_trade_no = I('out_trade_no');
        if (!empty($out_trade_no)) {
            $data['out_trade_no'] = array('like', '%' . $out_trade_no . '%');
            $this->assign('out_trade_no', $out_trade_no);
        }
        $data['status'] = array('IN','1,3,4,5,6,7,8');;
        $count = M("pay")->where($data)->where(array('id_4s' => session('user_id'), 'pay_obj' => 2))->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 5);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $pagelist = $Page->show();// 分页显示输出

        $info = M("pay")->where($data)->where(array('id_4s' => session('user_id'), 'pay_obj' => 2))->order("createtime desc")->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($info as $k => $v) {
            $car_info = D("view_car")->where(array('id' => $v['car_id']))->find();
            $info[$k]['brand_name'] = $car_info['brand_name'];
            $info[$k]['car_model_name'] = $car_info['car_model_name'];
            $info[$k]['car_name'] = $car_info['car_name'];

        }

        $this->assign('pagelist', $pagelist);
        $this->assign('info', $info);
        $this->display();
    }

    /************************************************用户车型喜好******************************************************************/
    /**
     *增加和删除4S店喜好
     */
    public function fs_ajax_car_prefer_edit()
    {
        if ($_SESSION['user_id']) {
            if ($_POST['brand_id'] != '') {
                $brand_id = I('brand_id');
            } else {
                $brand_id = D('User4s')->where(array('id' => session('user_id')))->getField('brand_4s');
            }

            //对车型进行编辑
            if (I('type') == 'get_car_model') {
                // 默认的都是喜好的车型
                if (I('action') == 'add') {
                    //用户添加喜好的车型
                    $del = M('car_model_prefer')->where(array('user_id' => $_SESSION['user_id'], 'car_model_id' => I('car_model_id')))->save(array('isdelete' => 1));
                    if ($del) {
                        $data['stutas'] = 0;
                    } else {
                        $data['stutas'] = 1;
                        $data['info'] = "修改失败，请稍后重试！!";
                    }
                    $this->ajaxReturn($data);
                } else {
                    //用户删除喜好的车型
                    $find_model = M('car_model_prefer')->where(array('user_id' => $_SESSION['user_id'], 'car_model_id' => I('car_model_id')))->find();
                    if ($find_model) {
                        //如果之前喜好的车型取消了,修改之前的数据
                        if ($find_model['isdelete'] == 0) {
                            $car_model_edit = false;
                        } else {
                            $car_model_edit = M('car_model_prefer')->where(array('user_id' => $_SESSION['user_id'], 'car_model_id' => I('car_model_id')))->save(array('isdelete' => 0));
                        }
                    } else {
                        //如果之前没有添加过，重新添加一条
                        $data_model['id'] = md5(microtime());
                        $data_model['brand_id'] = $brand_id;
                        $data_model['car_model_id'] = I('car_model_id');
                        $data_model['user_id'] = session('user_id');
                        $data_model['car_model_name'] = I('car_model_name');
                        $car_model_add = M('car_model_prefer')->add($data_model);
                    }
                    if ($car_model_edit or $car_model_add) {
                        $data['stutas'] = 0;
                    } else {
                        $data['stutas'] = 1;
                        $data['info'] = "修改失败，请稍后重试！";
                    }
                    $this->ajaxReturn($data);
                }
            } elseif (I('type') == 'get_car') {
                //对车款喜好进行添加
                if (I('action') == 'add') {
                    $del = M('car_prefer')->where(array('user_id' => $_SESSION['user_id'], 'car_model_id' => I('car_model_id'), 'car_id' => I('car_id')))->save(array('isdelete' => 1));
                    if ($del) {
                        $data['stutas'] = 0;
                    } else {
                        $data['stutas'] = 1;
                        $data['info'] = "修改失败，请稍后重试！";
                    }
                    $this->ajaxReturn($data);
                } else {
                    //对车款喜好进行删除
                    $find_car = M('car_prefer')->where(array('user_id' => $_SESSION['user_id'], 'car_model_id' => I('car_model_id'), 'car_id' => I('car_id')))->find();
                    if ($find_car) {
                        if ($find_car['isdelete'] == 0) {
                            $car_edit = false;
                        } else {
                            $car_edit = M('car_prefer')->where(array('user_id' => $_SESSION['user_id'], 'car_model_id' => I('car_model_id'), 'car_id' => I('car_id')))->save(array('isdelete' => 0));
                        }
                    } else {
                        $data_car['id'] = md5(microtime());
                        $data_car['car_id'] = I('car_id');
                        $data_car['car_model_id'] = I('car_model_id');
                        $data_car['user_id'] = $_SESSION['user_id'];
                        $data_car['car_name'] = I('car_name');
                        $car_add = M('car_prefer')->add($data_car);
                    }
                    if ($car_edit or $car_add) {
                        /*//判断车款父级车型之前是否关注过*/
                        $data['stutas'] = 0;

                    } else {
                        $data['stutas'] = 1;
                        $data['info'] = "修改失败，请稍后重试！";
                    }
                    $this->ajaxReturn($data);
                }
            } elseif (I('type') == 'get_exterior_color') {
                if (I('action') == 'add') {
                    //对车款外饰喜好进行添加
                    $del = M('exterior_color_prefer')->where(array('user_id' => $_SESSION['user_id'], 'car_id' => I('car_id'), 'exterior_color_id' => I('exterior_color_id')))->save(array('isdelete' => 1));
                    if ($del) {
                        $data['stutas'] = 0;
                    } else {
                        $data['stutas'] = 1;
                        $data['info'] = "修改失败，请稍后重试！";
                    }
                    $this->ajaxReturn($data);
                } else {
                    //对喜欢的外饰进行删除
                    $find_exterior = M('exterior_color_prefer')->where(array('user_id' => $_SESSION['user_id'], 'car_id' => I('car_id'), 'exterior_color_id' => I('exterior_color_id')))->find();
                    if ($find_exterior) {
                        if ($find_exterior['isdelete'] == 0) {
                            $exterior_edit = false;
                        } else {
                            $exterior_edit = M('exterior_color_prefer')->where(array('user_id' => $_SESSION['user_id'], 'car_id' => I('car_id'), 'exterior_color_id' => I('exterior_color_id')))->save(array('isdelete' => 0));
                        }
                    } else {
                        $data_exterior['id'] = md5(microtime());
                        $data_exterior['car_id'] = I('car_id');
                        $data_exterior['user_id'] = $_SESSION['user_id'];
                        $data_exterior['exterior_color_id'] = I('exterior_color_id');
                        $data_exterior['exterior_color_name'] = I('exterior_color_name');
                        $exterior_add = M('exterior_color_prefer')->add($data_exterior);
                    }
                    if ($exterior_edit or $exterior_add) {
                        //判断外饰颜色父级车款是否设置喜好
                        $data['stutas'] = 0;
                    } else {
                        $data['stutas'] = 1;
                        $data['info'] = "修改失败，请稍后重试";
                    }
                    $this->ajaxReturn($data);
                }

            } elseif (I('type') == 'get_interior_color') {
                if (I('action') == 'add') {
                    //对车款内饰喜好进行添加
                    $del = M('interior_color_prefer')->where(array('user_id' => $_SESSION['user_id'], 'car_id' => I('car_id'), 'interior_color_id' => I('interior_color_id')))->save(array('isdelete' => 1));
                    if ($del) {
                        $data['stutas'] = 0;
                    } else {
                        $data['stutas'] = 1;
                        $data['info'] = "修改失败，请稍后重试！";
                    }
                    $this->ajaxReturn($data);
                } else {
                    //删除喜好的外观颜色
                    $find_interior = M('interior_color_prefer')->where(array('user_id' => $_SESSION['user_id'], 'car_id' => I('car_id'), 'interior_color_id' => I('interior_color_id')))->find();
                    if ($find_interior) {
                        if ($find_interior['isdelete'] == 0) {
                            $interior_edit = false;
                        } else {
                            $interior_edit = M('interior_color_prefer')->where(array('user_id' => $_SESSION['user_id'], 'car_id' => I('car_id'), 'interior_color_id' => I('interior_color_id')))->save(array('isdelete' => 0));
                        }
                    } else {
                        $data_interior['id'] = md5(microtime());
                        $data_interior['car_id'] = I('car_id');
                        $data_interior['user_id'] = $_SESSION['user_id'];
                        $data_interior['interior_color_id'] = I('interior_color_id');
                        $data_interior['interior_color_name'] = I('interior_color_name');
                        $interior_add = M('interior_color_prefer')->add($data_interior);
                    }
                    if ($interior_edit or $interior_add) {
                        $data['stutas'] = 0;
                    } else {
                        $data['stutas'] = 1;
                        $data['info'] = "修改失败，请稍后重试";
                    }
                    $this->ajaxReturn($data);
                }
            } else {
                $data['stutas'] = 2;
                $data['info'] = "请选择要编辑的对象！";
                $this->ajaxReturn($data);
            }
        } else {
            $this->redirect('Public/login');
        }
    }

    /**
     *获得4s店车型喜好
     */
    public function fs_ajax_car_prefer()
    {
        if ($_SESSION['user_id']) {

            //获得4s店的主营品牌ID
            if (isset($_GET['brand_id']) and $_GET['brand_id'] != '') {
                $brand_id = I('brand_id');
            } else {
                $brand_id = D('User4s')->where(array('id' => session('user_id')))->getField('brand_4s');
            }


            if (I('type') == 'get_car') {
                $car = M('car')->where(array('car_model_id' => I('car_model_id'), 'isdelete' => 0, 'status' => 1))->select();
                //选中车型下用户喜好的车款
                $prefer_car = M('car_prefer')->where(array('user_id' => $_SESSION['user_id'], 'car_model_id' => I('car_model_id'), 'isdelete' => 0))->select();
                foreach ($car as $key => $value) {
                    foreach ($prefer_car as $k => $v) {
                        if ($v['car_id'] == $value['id']) {
                            unset($car[$key]);
                        }
                    }
                }
                if ($car or $prefer_car) {
                    $data['stutas'] = 0;
                    $data['list']['unlike_car_list'] = $prefer_car;
                    $data['list']['like_car_list'] = array_merge($car);
                } else {
                    $data['stutas'] = 1;
                    $data['info'] = "获取车款失败,请稍后重试！";
                }
                $this->ajaxReturn($data);
            } elseif (I('type') == 'get_color') {
                //获得该车款下所有的外饰
                $car_exterior = M('exterior_color')->where(array('car_id' => I('car_id'), 'isdelete' => 0))->select();
                $exterior_prefer = M('exterior_color_prefer')->where(array('user_id' => $_SESSION['user_id'], 'car_id' => I('car_id'), 'isdelete' => 0))->select();
                foreach ($car_exterior as $key => $value) {
                    $car_exterior[$key]['color_name'] = M('color')->where(array('id' => $value['color_id']))->getField('color_name');
                    foreach ($exterior_prefer as $k => $v) {
                        if ($v['exterior_color_id'] == $value['id']) {
                            unset($car_exterior[$key]);
                        }
                    }
                }

                //获得该车款下所有的内饰
                $car_interior = M('interior_color')->where(array('car_id' => I('car_id'), 'isdelete' => 0))->select();
                $interior_prefer = M('interior_color_prefer')->where(array('user_id' => $_SESSION['user_id'], 'car_id' => I('car_id'), 'isdelete' => 0))->select();
                foreach ($car_interior as $key => $value) {
                    $car_interior[$key]['color_name'] = M('color')->where(array('id' => $value['color_id']))->getField('color_name');
                    foreach ($interior_prefer as $k => $v) {
                        if ($v['interior_color_id'] == $value['id']) {
                            unset($car_interior[$key]);
                        }
                    }
                }
                //判断外饰是否返回成功
                if ($car_exterior or $exterior_prefer) {
                    $data['stutas'] = 0;
                    $data['list']['unlike_exterior_list'] = $exterior_prefer;
                    $data['list']['like_exterior_list'] = array_merge($car_exterior);
                } else {
                    $data['stutas'] = 1;
                    $data['info'] = "获取外饰颜色失败,请稍后重试！";
                }
                //判断内饰是否返回成功
                if ($car_interior or $interior_prefer) {
                    $data['stutas'] = 0;
                    $data['list']['unlike_interior_list'] = $interior_prefer;
                    $data['list']['like_interior_list'] = array_merge($car_interior);
                } else {
                    $data['stutas'] = 1;
                    $data['info'] = "获取内饰颜色失败,请稍后重试！";
                }
                $this->ajaxReturn($data);
            } elseif (I('type') == 'get_model') {
                //获取所有车型
                $car_model = M("car_model")->where(array('brand_id' => $brand_id, 'isdelete' => 0))->group('car_model_name')->order('car_model_name')->select();
                //获得用户喜好的车型
                $prefer_model = M('car_model_prefer')->where(array('user_id' => $_SESSION['user_id'], 'brand_id' => $brand_id, 'isdelete' => 0))->select();
                foreach ($car_model as $key => $value) {
                    foreach ($prefer_model as $k => $v) {
                        if ($v['car_model_id'] == $value['id']) {
                            unset($car_model[$key]);
                        }
                    }
                }
                if ($prefer_model or $car_model) {
                    $data['status'] = 0;
                    $data['list']['unlike_model_list'] = $prefer_model;
                    $data['list']['like_model_list'] = array_merge($car_model);
                } else {
                    $data['status'] = 1;
                    $data['info'] = "获取车型失败,请稍后重试！";
                }
                $this->ajaxReturn($data);
            } else {
                $data['status'] = -1;
                $data['info'] = "请输入正确的数据";
                $this->ajaxReturn($data);
            }
        } else {
            $this->redirect('Public/login');
        }
    }

    /************************************************接口信息******************************************************************/

    /**
     *4s店用户------车款联动
     */
    public function FS_ajax_style()
    {
        $model_id = I('model_id');
        if (!empty($model_id)) {
            $info = M("car")->where(array('car_model_id' => $model_id, 'isdelete' => 0))->group('car_name')->order('car_name')->select();
            $this->ajaxReturn($info);
        }
    }

    /**
     *4s店用户------颜色联动
     */
    public function FS_ajax_colors()
    {
        $car_id = I('car_id');
        if (!empty($car_id)) {
            $ex_info = D("exterior_color")->where(array('car_id' => $car_id, 'isdelete' => 0))->group('color_id')->select();
            $in_info = M("interior_color")->where(array('car_id' => $car_id, 'isdelete' => 0))->group('color_id')->select();

            foreach ($ex_info as $key => $value) {
                $color_info = M("color")->where(array('id' => $value['color_id'], 'type' => 1))->find();
                $ex_info[$key]['exterior_color_name'] = $color_info['color_name'];
            }
            foreach ($in_info as $key => $value) {
                $color_info = M("color")->where(array('id' => $value['color_id'], 'type' => 2))->find();
                $in_info[$key]['interior_color_name'] = $color_info['color_name'];
            }

            $info["ex_info"] = $ex_info;
            $info["in_info"] = $in_info;
            $this->ajaxReturn($info);
        }
    }

    /**
     *4s店用户------内饰颜色联动
     */
    public function FS_ajax_interior_color()
    {
        $car_id = I('car_id');
        if (!empty($car_id)) {
            $info = M("interior_color")->where(array('car_id' => $car_id, 'isdelete' => 0))->group('color_id')->select();
            foreach ($info as $key => $value) {
                $color_info = M("color")->where(array('id' => $value['color_id'], 'type' => 2))->find();
                $info[$key]['interior_color_name'] = $color_info['color_name'];
            }
            $this->ajaxReturn($info);
        }
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
}