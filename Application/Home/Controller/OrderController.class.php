<?php
namespace Home\Controller;

use Think\Controller;

/**
 * Class OrderController
 * @package Home\Controller
 */
class OrderController extends CommonController
{

    /**
     *订单模块初始化
     */
    public function _initialize()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->error('您还没有登录', U('Public/login'));
        }
    }

    /**
     *订单信息
     */
    public function order_info()
    {
        $pay_info = M('pay')->where(array('id' => I('pay_id')))->find();
        $brand_id = M('view_car')->where(array('id' => $pay_info['car_id']))->getfield('brand_id');
        $displacement = M('view_car')->where(array('id' => $pay_info['car_id']))->getfield('displacement');
        $pay_info['exterior_img'] = str_replace('type', 'small', $pay_info['exterior_img']);
        if (!empty($pay_info['car_price_id'])) {
            $price_info = D('view_car_price')->where(array('id' => $pay_info['car_price_id']))->find();
            $pay_info['auth_price'] = $price_info['auth_price'];
            $pay_info['discount'] = $price_info['discount'];
        } elseif (!empty($pay_info['car_special_id'])) {
            $special_info = D('view_special_price_car')->where(array('id' => $pay_info['car_special_id']))->find();
            $pay_info['auth_price'] = $special_info['auth_price'];
            $pay_info['discount'] = $special_info['discount'];
        }
        //查找大礼包
        if ($brand_id == 66) {
            $data['brand_id'] = 66;
        }
        $pay_info['displacement'] = $displacement;
        $data['isdelete'] = 0;
        $data['min_price'] = array('ELT', $pay_info['low_price']);
        $data['max_price'] = array('EGT', $pay_info['low_price']);
        $package = M("buy_car_package")->where($data)->find();
        $package['imgurl'] = str_replace('type', 'big', $package['imgurl']);
        $this->assign('package', $package);
        $this->assign('brand_id', $brand_id);
        $this->assign('pay_info', $pay_info);

        $this->display();
    }

    /**
     *完善订单信息-》pay表
     */
    public function order_update()
    {
        if (!empty($_POST)) {
            $info = M('pay')->where(array('id' => I('pay_id')))->find();
            if (I('ticket_number')) {
                $tick = M('ticket_user')->where(array('ticket_number' => I('ticket_number')))->find();
                if ($tick['end_time'] < time()) {
                    $this->error('优惠券已过期！');
                } else {
                    $datatick['status'] = 4;
                    $datatick['remark'] = $info['out_trade_no'] . '正在占用这条优惠券！';
                    $datatick['pay_id'] = $info['id'];
                    M('ticket_user')->where(array('id' => $tick['id']))->save($datatick);

                    $data['ticket_number'] = I('ticket_number');
                    $data['ticket_money'] = $tick['money'];
                    $data['money'] = $info['order_money'] - $tick['money'];
                }
            }
            $data['buyer_name'] = I('buyer_name');
            $data['buyer_tel'] = I('buyer_tel');
            $data['buy_time'] = I('buy_time');
            /*	$data['car_mode']=I('car_mode');*/
            $data['buy_way'] = I('buy_way');
            $data['insurance'] = I('insurance');
            //保险
            $data['compulsory_insurance'] = I('compulsory_insurance');
            $data['cess'] = I('cess');
            $data['licensing_fees'] = I('licensing_fees');
            $data['travel_tax'] = I('travel_tax');
            $data['car_loss_dang'] = I('car_loss_dang');
            $data['liability'] = I('liability');
            $data['car_liability'] = I('car_liability');
            $data['deductible_special'] = I('deductible_special');
            if ($data['buyer_name'] == '' && $data['buyer_tel'] == '' && $data['buy_time'] == '') {
                $this->error('请完善个人资料');
            } else {
                $z = M('pay')->where(array('id' => I('pay_id')))->save($data);
                if ($z !== false) {
                    $this->redirect('order_pay', array('pay_id' => I('pay_id')));
                } else {
                    $this->error('信息提交失败！');
                }
            }

        }

    }

    /**
     *订单支付页面
     */
    public function order_pay()
    {
        $order_info = M("pay")->where(array('id' => I('pay_id')))->find();
        $this->assign('order_info', $order_info);
        $this->display();
    }

    /**
     *订单支付成功页面
     */
    public function order_ok()
    {
        $order_info = M("pay")->where(array('id' => I('pay_id')))->find();
        if ($order_info['status'] != 1) {
            $this->redirect('Index/index');
        } else {
            $order_info['next'] = MEMBER_URL . '/n_uc_myorder_details/pay_id/' . $order_info['id'];
            $this->assign('info', $order_info);
        }
        $this->display();
    }

    /**
     * 生成订单号
     * 可根据自身的业务需求更改
     */
    public function createOrderNo()
    {
        $year_code = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        return $year_code[intval(date('Y')) - 2010] .
        strtoupper(dechex(date('m'))) . date('d') .
        substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
    }

    /********************************************************活动生成订单***************************************************************************/
    /**
     *订单信息---活动
     */
    public function order_info_activity()
    {
        $pay_info = M('pay')->where(array('id' => I('pay_id')))->find();
        $brand_id = M('view_car')->where(array('id' => $pay_info['car_id']))->getfield('brand_id');
        $displacement = M('view_car')->where(array('id' => $pay_info['car_id']))->getfield('displacement');
        $string = strstr($pay_info['exterior_img'], 'type');
        if (!empty($string)) {
            $pay_info['exterior_img'] = str_replace('type', 'small', $pay_info['exterior_img']);
        } else {
            $pay_info['exterior_img'] = $pay_info['exterior_img'];
        }
        $info = D('view_car_price')->where(array('id' => $pay_info['car_price_id']))->find();
        $pay_info['brand_name'] = $info['brand_name'];
        $pay_info['car_model_name'] = $info['car_model_name'];
        $pay_info['displacement'] = $displacement;
        $this->assign('pay_info', $pay_info);
        //查找大礼包
        if ($brand_id == 66) {
            $data['brand_id'] = 66;
        }
        $data['isdelete'] = 0;
        $data['min_price'] = array('ELT', $pay_info['low_price']);
        $data['max_price'] = array('EGT', $pay_info['low_price']);
        $package = M("buy_car_package")->where($data)->find();
        $package['imgurl'] = str_replace('type', 'big', $package['imgurl']);
        $this->assign('package', $package);
//车款
        $car_info = D('view_car_price')->distinct(true)->field('car_name,car_id')->where(array('car_model_id' => $info['car_model_id']))->select();
        $this->assign('car_info', $car_info);
        //外观颜色
        $exterior_info = D('view_car_price')->distinct(true)->field('exterior_color_name,exterior_color_id')->where(array('car_id' => $info['car_id']))->select();
        $this->assign('exterior_info', $exterior_info);
        //内饰颜色
        $interior_info = D('view_car_price')->distinct(true)->field('interior_color_name,interior_color_id')->where(array('car_id' => $info['car_id']))->select();
        $this->assign('interior_info', $interior_info);
        //上牌地
        $area = M('sales_area')->where(array('car_price_id' => $info['id']))->select();
        if (empty($area)) {
            $area[0]['sales_area_name'] = '不限';
            $area[1]['sales_area_name'] = '南京市';
        }
        $this->assign('area', $area);
        $this->assign('brand_id', $brand_id);
        $this->display();
    }

    /**
     *完善订单信息-》pay表---活动
     */
    public function order_update_activity()
    {
        if (!empty($_POST)) {
            $car_price_id = I('car_price_id');
            $car_price_info = M('view_car_price')->where(array('id' => $car_price_id))->find();
            $data['car_name'] = $car_price_info['car_name'];
            $data['car_id'] = $car_price_info['car_id'];
            $data['carstyle'] = $car_price_info['brand_name'] . $car_price_info['car_model_name'] . $car_price_info['car_name'];
            $data['car_price_id'] = $car_price_info['id'];
            $data['exterior_color_id'] = $car_price_info['exterior_color_id'];
            $data['exterior_color_name'] = $car_price_info['exterior_color_name'];
            $data['exterior_color_value'] = $car_price_info['exterior_color_value'];
            $data['interior_color_id'] = $car_price_info['interior_color_id'];
            $data['interior_color_name'] = $car_price_info['interior_color_name'];
            $data['interior_color_value'] = $car_price_info['interior_color_value'];
            $data['exterior_img'] = $car_price_info['exterior_img'];
            $data['low_price_city_name'] = $car_price_info['city_name'];
            $data['id_4s'] = $car_price_info['user_id'];
            $data['low_price'] = $car_price_info['low_price'];
            if ($car_price_info['low_price'] <= 200000) {
                $car_price_info['deposit'] = 2000;
            } elseif ($car_price_info['low_price'] <= 500000) {
                $car_price_info['deposit'] = 5000;
            } else {
                $car_price_info['deposit'] = 10000;
            }
            $data['money'] = $car_price_info['deposit'];

            $data['card_place'] = I('ucc_name');
            $data['buyer_name'] = I('buyer_name');
            $data['buyer_tel'] = I('buyer_tel');
            $data['buy_time'] = I('buy_time');
            $data['buy_way'] = I('buy_way');
            $data['insurance'] = I('insurance');

            $data['compulsory_insurance'] = I('compulsory_insurance');
            $data['cess'] = I('cess');
            $data['licensing_fees'] = I('licensing_fees');
            $data['travel_tax'] = I('travel_tax');
            $data['car_loss_dang'] = I('car_loss_dang');
            $data['liability'] = I('liability');
            $data['car_liability'] = I('car_liability');
            $data['deductible_special'] = I('deductible_special');
            if ($data['buyer_name'] == '' && $data['buyer_tel'] == '' && $data['buy_time'] == '') {
                $this->error('请完善个人资料');
            } else {
                $z = M('pay')->where(array('id' => I('pay_id')))->save($data);
                if ($z !== false) {
                    $this->redirect('order_pay', array('pay_id' => I('pay_id')));
                } else {
                    $this->error('信息提交失败！');
                }
            }

        }

    }

    /**
     *车款颜色--ajax联动
     */
    public function color()
    {
        $car_id = I('car_id');
        if (!empty($car_id)) {
            $color1 = $car_info = D('view_car_price')->distinct(true)->field('exterior_color_name,exterior_color_id')->where(array('car_id' => $car_id))->select();
            foreach ($color1 as $key => $value) {
                $data1[$key]['id'] = $value['exterior_color_id'];
                $data1[$key]['name'] = $value['exterior_color_name'];

            }
        }
        $exterior_color_id = I('exterior_color_id');
        if (!empty($exterior_color_id)) {
            $color_image = M("car_exterior_color_image")->where(array('isdelete' => 0, 'exterior_color_id' => $exterior_color_id))->find();
            $color2 = $car_info = D('view_car_price')->distinct(true)->field('interior_color_name,interior_color_id')->where(array('exterior_color_id' => $exterior_color_id))->select();
            foreach ($color2 as $key => $value) {

                $data2[$key]['id'] = $value['interior_color_id'];
                $data2[$key]['name'] = $value['interior_color_name'];

            }
            $info['ex_img'] = 'http://yichenghui.oss-cn-shanghai.aliyuncs.com/gouchehui/web' . str_replace('type', 'big', $color_image['imgurl']);
        }
        $info['interior'] = $data2;

        $info['exterior'] = $data1;
        $this->ajaxReturn($info);

    }

    /**
     *确定底价车的详情
     */
    public function price_details()
    {
        $info = D('view_car_price')->where(array('exterior_color_id' => I('exterior_color_id'), 'interior_color_id' => I('interior_color_id')))->order('low_price desc')->limit(0, 1)->find();
        if ($info['low_price'] <= 200000) {
            $info['deposit'] = 2000;
        } elseif ($info['low_price'] <= 500000) {
            $info['deposit'] = 5000;
        } else {
            $info['deposit'] = 10000;
        }
        $area = M('sales_area')->where(array('car_price_id' => $info['id']))->select();
        if (empty($area)) {
            $area[0]['sales_area_name'] = '不限';
            $area[1]['sales_area_name'] = '南京市';
        }
        $info['area'] = $area;

        $this->ajaxReturn($info);
    }

    /********************************************************订单页面优惠券***************************************************************************/

    public function ticket()
    {
        $info = M('ticket_user')->where(array('user_id' => session('user_id'), 'status' => 1, 'isdelete' => 0))->select();
        foreach ($info as $key => $value) {
            $info[$key]['starttime'] = date('Y-m-d', strtotime($value['createtime']));
            $info[$key]['endtime'] = date('Y-m-d', $value['end_time']);
        }
        $db['list'] = $info;
        $this->ajaxReturn($db);
    }

    /********************************************************询价成功优惠券***************************************************************************/

    public function order_askfee_ok()
    {
        $pid = I('pid');
        if (!empty($pid)) {
            $info = M('pay')->where(array('id' => $pid))->find();
            $info['order_url'] = U('Car/car_price_pay', array('pid' => $pid));
            $tick = M('ticket_user')->where(array('isdelete' => 0, 'user_id' => $info['user_id']))->order('createtime desc')->find();
            $tick['startdate'] = date('Y-m-d', strtotime($tick['createtime']));
            $tick['enddate'] = date('Y-m-d', time());
            $this->assign('tick', $tick);
            $this->assign('info', $info);
        }
        $this->display();
    }

    /********************************************************一元抢（双十一）***************************************************************************/

    /**
     * @Description:订单支付页面
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/11/1 9:45
     * @Version 2.0
     */
    public function order_active_pay()
    {

        $info=M('pay')->where(array('id'=>I('pay_id')))->find();
        $this->assign('order_info',$info);
        $this->display();
    }

    /**
     * @Description:订单支付成功页面
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/11/1 9:45
     * @Version 2.0
     */
    public function order_active_ok()
    {
        $info=M('pay')->where(array('id'=>I('pay_id')))->find();
        $info['next']='/index.php/Order/order_active_perfectinfo?pay_id='.I('pay_id');
        $info['userorder_url']='/member.php/MemberGeneral/n_uc_active_list';
        $this->assign('info',$info);
        $this->display();
    }

    /**
     * @Description:支付完完善资料的页面
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/11/1 9:45
     * @Version 2.0
     */
    public function order_active_perfectinfo()
    {
        $info=M('pay')->where(array('id'=>I('pay_id')))->find();
        $car_info=M('car')->distinct('true')->field('id,car_name')->where(array('car_model_id'=>$info['car_model_id'],'isdelete'=>0))->order('car_name desc')->select();
        $this->assign('car_info',$car_info);
        $this->assign('info',$info);
        $this->display();
    }

    /********************************************************一元抢（双十一）***************************************************************************/

    /**
     * @Description:订单支付页面
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/11/1 9:45
     * @Version 2.0
     */
    public function order_act_pay()
    {
        $info=M('pay')->where(array('id'=>I('pay_id')))->find();
        $this->assign('order_info',$info);
        $this->display();
    }


} 