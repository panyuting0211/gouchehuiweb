<?php
namespace Admin\Controller;

use Think\Controller;

/**
 * Class AjaxController ajax数据加载控制器
 * @package Admin\Controller
 */
class AjaxController extends CommonController
{
    /**
     *管理员管理后台ajax展示
     */
    public function managerListajax2()
    {
        $pages = I('p');
        $records = 15;
        $count = M('manager')->count();
        $Page = new \Think\PageAjax($count, $records);
        $info = M('manager')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($info as $k => $v) {
            $info[$k][num] = ($k + 1) + ($pages - 1) * $records;
            $rid = M('role_manager')->where(array('user_id' => $v['id']))->getField('role_id');
            $info[$k]['role_name'] = M('role')->where(array('id' => $rid))->getField('name');
        }
        $info2[] = $Page->show();
        $info2[] = $info;
        $info2['count'] = $count;
        $this->ajaxReturn($info2);
    }

    /**
     *角色管理异步展示
     */
    public function roleListajax2()
    {
        //$this->list = M('role')->select();
        $pages = I('p');
        $records = 10;
        $count = M('role')->count();
        $Page = new \Think\PageAjax($count, $records);
        $info = M('role')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($info as $v => $k) {
            $info[$v][num] = ($v + 1) + ($pages - 1) * $records;
        }
        $info2[] = $Page->show();
        $info2[] = $info;
        $info2['count'] = $count;
        $this->ajaxReturn($info2);
    }

    /**
     * @param $arr
     * @param int $pid
     * @return array
     * 无限极分类
     */
    public function Tree($arr, $pid = 0)
    {
        static $tree = array();
        foreach ($arr as $k => $v) {
            if ($v['pid'] == $pid) {
                $tree[] = $v;
                unset($arr[$k]);
                self::Tree($arr, $v['id']);
            }
        }
        return $tree;
    }

    /**
     *品牌维护ajax异步显示
     */
    public function brand_manager()
    {
        $keyword = I('keyword');
        /*$page=I('page');*/
        $pagenum = I('pageNum');
        if (!empty($keyword)) {
            $map['manager'] = array('like', '%' . $keyword . '%');
            $map['brand_name'] = array('like', '%' . $keyword . '%');
            $map['_logic'] = 'or';
            $data['_complex'] = $map;
        }
        $data['isdelete'] = 0;
        $count = M('brand_manager')->where($data)->count();
        $Page = new \Think\PageAjax($count, $pagenum);
        $list = M('brand_manager')->where($data)->order('createtime desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $info['count'] = $count;
        $info['pagelist'] = $Page->show();
        $info['list'] = $list;
        $this->ajaxReturn($info);
    }

    /**
     *普通会员ajax异步展示
     */
    public function member_ajax()
    {
        $keyvalue = I('keyvalue');
        $where = '';
        if (!empty($keyvalue)) {
            $where['tel'] = array('like', '%' . $keyvalue . '%');
            $where['email'] = array('like', '%' . $keyvalue . '%');
            $where['nick'] = array('like', '%' . $keyvalue . '%');
            $where['user_name'] = array('like', '%' . $keyvalue . '%');
            $where['_logic'] = 'or';
            $data['_complex'] = $where;
        }
        $data['role'] = 1;
        $data['isdelete'] = 0;
        $count = M('user_general')->where($data)->count();
        $Page = new \Think\PageAjax($count, 15);
        $info = M('user_general')->where($data)->order('createtime desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($info as $key => $value) {
            if (!empty($value['logintime'])) {
                $info[$key]['logintime'] = date('Y-m-d H:i:s', $value['logintime']);
            }
        }

        $info2[] = $Page->show();
        $info2[] = $info;
        $info2['count'] = $count;
        $this->ajaxReturn($info2);
    }

    /**
     *4s店会员ajax异步展示
     */
    public function shopUser_ajax()
    {
        $companyname = I('keyvalue');
        $data = '';
        if (!empty($companyname)) {
            $where['user_name'] = array('like', '%' . $companyname . '%');
            $where['name_4s'] = array('like', '%' . $companyname . '%');
            $where['_logic'] = 'or';
            $data['_complex'] = $where;
        }
        $data['role'] = array('eq', 2);
        $data['isdelete'] = 0;

        $count = M('user_4s')->where($data)->count();
        $Page = new \Think\PageAjax($count, 12);
        $info = M('user_4s')->where($data)->order('createtime desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $info2[] = $Page->show();
        $info2[] = $info;
        $info2['count'] = $count;
        $this->ajaxReturn($info2);
    }

    /**
     *新车经销商列表-ajax
     */
    public function Dealer_ajax()
    {
        $companyname = I('keyvalue');
        if (!empty($companyname)) {
            $data['user_name'] = array('like', '%' . $companyname . '%');
        }
        $data['role'] = array('eq', 3);
        $data['isdelete'] = 0;
        $count = M('user_dealer')->where($data)->count();
        $Page = new \Think\PageAjax($count, 12);
        $info = M('user_dealer')->where($data)->order('createtime desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $info2[] = $Page->show();
        $info2[] = $info;
        $info2['count'] = $count;
        $this->ajaxReturn($info2);
    }

    /**
     *获得所属省份下所有的城市
     */
    public function city()
    {
        if ($_POST) {
            $res = M('city')->where(array('province_id' => I('province_id')))->select();
            $this->ajaxReturn($res);
        }
    }

    /**
     * 问题咨询列表ajax
     */
    public function messagelist_ajax()
    {
        $brand = I('brand ');
        $model = I('model');
        $question_type = I('question_type');
        $reply_status = I('reply_status');
        //根据品牌筛选
        if (!empty($brand)) {
            $data['car_brand_id'] = $brand;
        }
        //根据车型筛选
        if (!empty($model)) {
            $data['car_model_id'] = $model;
        }
        //根据问题类型筛选
        if (!empty($question_type)) {
            $data['question_type'] = $question_type;
        }
        //根据回复状态筛选
        if (!empty($reply_status)) {
            $data['reply_status'] = $reply_status;
        }
        $data['isdelete'] = 0;
        $count = M("car_question")->where($data)->count();
        $Page = new \Think\PageAjax($count, 15); //实例化分页
        $info = M("car_question")->where($data)->order('createtime desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        //根据问题中的汽车ID获得品牌，车型名称
        foreach ($info as $k => $v) {
            $model = M('car_model')->where(array('id' => $v['car_model_id']))->find();
            $info[$k]['model_name'] = $model['car_model_name'];
            $info[$k]['brand_name'] = M('brand')->where(array('id' => $v['car_brand_id']))->getField('brand_name');
            $info[$k]['user_name'] = M('user_general')->where(array('id' => $v['createuser']))->getField('user_name');
        }
        $info2[] = $Page->show();
        $info2[] = $info;
        $info2['count'] = $count;
        $this->ajaxReturn($info2);
    }

    /**
     *ajax联动获取车型
     */
    public function get_model()
    {
        if ($_POST) {
            $model = M('car_question')->where(array('car_brand_id' => I('brand_id')))->distinct(true)->order('car_model_id asc')->field('car_model_id')->select();
            foreach ($model as $key => $value) {
                $model[$key]['car_model_name'] = M('car_model')->where(array('id' => $value['car_model_id']))->getField('car_model_name');
            }
            $this->ajaxReturn($model);
        }

    }

    /**
     *app留言异步展示
     */
    public function appMessage_ajax()
    {
        $time = date('Y-m-d');
        $starttime = I('begin_time');
        $endtime = I('end_time');
        //查询是否有条件筛选如果没有就选择一个月内的数据
        if ($endtime == '') {
            $endtime = strtotime($time . '23:59:59');
        } else {
            $len = strlen($endtime);
            if ($len > 10) {
                $endtime = strtotime($endtime);
            } else {
                $endtime = strtotime($endtime . '23:59:59');
            }
        }
        if ($starttime == '') {
            $starttime = $endtime - 3600 * 24 * 31 + 1; // 默认查询一个月内下订单信息
            $starttime = date('Y-m-d  H:i:s', $starttime);
        }
        $endtime = date('Y-m-d H:i:s', $endtime);
        if (I('username')) {
            $username = I('username');
            $data['user_id'] = M('user')->where(array('user_name' => $username))->getField('id');
        }

        $data['createtime'] = array('between', array($starttime, $endtime));
        $data['isdelete'] = 0;
        $count = M('interaction')->where($data)->count();//得到总数
        $page = new \Think\PageAjax($count, 5);
        $model = M('interaction')->where($data)->limit($page->firstRow . ',' . $page->listRows)->order('createtime desc')->select();
        foreach ($model as $k => $v) {
            $model[$k]['username'] = M('user_general')->where(array('id' => $v['user_id']))->getField('user_name');
            $imgurl = M('interaction_image')->where(array('interaction_id' => $v['id']))->select();
            foreach ($imgurl as $kk => $vv) {
                $imgurl[$kk]['imgurl'] = str_replace('type', 'small', $vv['imgurl']);
            }
            $model[$k]['imgurl'] = $imgurl;
            $model[$k]['count'] = M('interaction_result')->where(array('interaction_id' => $v['id']))->count();
        }
        $info2[] = $page->show();
        $info2[] = $model;
        $info2['count'] = $count;
        //dump($info2);
        $this->ajaxReturn($info2);
    }

    /**
     *询价记录列表页分页ajax展示
     */
    public function orderList_ajax()
    {
        $time = date('Y-m-d', time());
        $begin_time = I('begin_time');
        $end_time = I('end_time');
        $time_status = I('time_status');//有效失效状态
        $pay_status = I('pay_status');//支付状态
        $status_track = I('status_track');
        if (!empty($time_status) || !empty($pay_status)) {
            $data['status'] = $time_status >= $pay_status ? $time_status : $pay_status;
            if ($data['status'] == -1) {
                $data['status'] = 0;
            }
        }
        if (!empty($status_track)) {
            $data['status_track'] = $status_track;
        }
        $keyword = I('keyword');
        if (!empty($end_time)) {
            $end_time = strtotime($end_time . '23:59:59');
        } else {
            $end_time = strtotime($time . '23:59:59');
        }
        if (!empty($begin_time)) {
            $begin_time = strtotime($begin_time . '00:00:00');
        } else {
            $begin_time = $end_time - 3600 * 24 * 31 + 1; // 默认查询一个月内下订单信息
        }
        $data['UNIX_TIMESTAMP(createtime)'] = array('between', array($begin_time, $end_time));


        if (!empty($keyword)) {
            // $keyword=str_replace("+"," ",$keyword);
            $map['user_name'] = array('like', '%' . $keyword . '%');
            $user_id1 = M('user_general')->where($map)->field('id')->select();
            foreach ($user_id1 as $key => $value) {
                $user_id[] = $value['id'];
            }
            if (!empty($user_id)) {
                $where['user_id'] = array('in', $user_id);
            }
            $all_info = M('pay')->field('id,carstyle,car_name')->select();
            foreach ($all_info as $key => $value) {
                $all_info[$key]['goods'] = $value['carstyle'] . ' ' . $value['car_name'];
                if (strpos($all_info[$key]['goods'], $keyword) !== false) {
                    $pay_id1[] = $value['id'];
                }
            }
            if (!empty($pay_id1)) {
                $where['id'] = array('in', $pay_id1);
                $where['_logic'] = 'or';
            }

            if (!empty($where)) {
                $data['_complex'] = $where;
            } else {
                $data['createtime'] = 10000000;//设置成这样，代表设置错误查询不到
            }
        }
        //成功询价判断
        /*$data1['card_place'] = array('neq', '');
        $data1['isdelete'] = 0;
        $success_id = M('pay_area_low_price')->where($data1)->distinct(true)->field('pay_id')->select();
        foreach ($success_id as $key => $value) {
            $success_pay_id[] = $value['pay_id'];
        }
        if (!empty($success_pay_id)) {
            $data['id'] = array('in', $success_pay_id);
        } else {
            $data['createtime'] = 10000000;//条件都不满足，故意查询不到。
        }*/
        $data['isdelete'] = 0;
        $data['pay_obj'] = array('in', '1,3');

        $count = M('pay')->where($data)->count();
        $page = new \Think\PageAjax($count, 15);//实例化分页类传入总记录数和每页显示的记录数

        $list = M('pay')->where($data)->order('createtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($list as $key => $value) {
            $list[$key]['user_name'] = M('user_general')->where(array('id' => $value['user_id']))->getField('user_name');
            if (!empty($value['from_activityid'])) {
                $list[$key]['from_activity'] = M('activities')->where(array('id' => $value['from_activityid']))->getField('activity_name');
            } else {
                $list[$key]['from_activity'] = '车款询价';
            }
        }
        $info2[] = $page->show();
        $info2[] = $list;
        $info2['count'] = $count;
        $this->ajaxReturn($info2);
    }

    /**
     *订车订单ajax异步展示
     */
    public function depositList_ajax()
    {
        $begin_time = I('begin_time');
        $end_time = I('end_time');
        $buy_time_begin = I('buy_time_begin');
        $buy_time_end = I('buy_time_end');
        $status = I('status');
        $keyword = I('keyword');
        $time = date('Y-m-d');
        if (empty($end_time)) {
            $end_time = strtotime($time . '23:59:59');
        } else {
            $end_time = strtotime($end_time . '23:59:59');
        }

        if (empty($begin_time)) {
            $begin_time = $end_time - 3600 * 24 * 31 + 1; // 默认查询一个月内下订单信息
        } else {
            $begin_time = strtotime($begin_time . '00:00:00');
        }

        if (empty($buy_time_end)) {
            $buy_time_end = strtotime($time . '23:59:59') + 3600 * 24 * 15 + 1;;
        } else {
            $buy_time_end = strtotime($buy_time_end . '23:59:59');
        }

        if (empty($buy_time_begin)) {
            $buy_time_begin = $buy_time_end - 3600 * 24 * 46 + 1; // 默认查询一个月内下订单信息
        } else {
            $buy_time_begin = strtotime($buy_time_begin . '00:00:00');
        }

        if (!empty($status)) {
            $data['status'] = I('status');
            if ($data['status'] == -1) {
                $data['status'] = 0;
            }
        }
        if (!empty($keyword)) {
            $map['carstyle'] = array('like', '%' . $keyword . '%');
            $map['out_trade_no'] = array('like', '%' . $keyword . '%');
            $map['_logic'] = 'or';
            $data['_complex'] = $map;
        }
        $data['UNIX_TIMESTAMP(createtime)'] = array('between', array($begin_time, $end_time));
        $data['UNIX_TIMESTAMP(buy_time)'] = array('between', array($buy_time_begin, $buy_time_end));


        $data['isdelete'] = 0;
        $data['pay_obj'] = '2';
        $count = M('pay')->where($data)->count();
        $page = new \Think\PageAjax($count, 15);//实例化分页类传入总记录数和每页显示的记录数
        $list = M('pay')->where($data)->order('createtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($list as $key => $value) {
            $list[$key]['user_name'] = M('user_general')->where(array('id' => $value['user_id']))->getField('user_name');
        }
        $pagelist = $page->show();//分页显示输出

        $info[] = $pagelist;
        $info[] = $list;
        $info['count'] = $count;
        $this->ajaxReturn($info);
    }

    /**
     *商城订单展示
     */
    public function shoplist_ajax()
    {
        $begin_time = I('begin_time');
        $end_time = I('end_time');
        $time = date('Y-m-d');
        $pay_status = I('pay_status');
        $order_status = I('order_status');
        $shipping_status = I('shipping_status');
        $keyword = I('keyword');
        if (empty($end_time)) {
            $end_time = strtotime($time . '23:59:59');
        } else {
            $end_time = strtotime($end_time . '23:59:59');
        }
        if (empty($begin_time)) {
            $begin_time = $end_time - 3600 * 24 * 31 + 1; // 默认查询一个月内下订单信息
        } else {
            $begin_time = strtotime($begin_time . '00:00:00');
        }
        if (!empty($order_status)) {
            $data['order_status'] = I('order_status');
            if ($data['order_status'] == -1) {
                $data['order_status'] = 0;
            }
        }
        if (!empty($pay_status)) {
            $data['pay_status'] = I('pay_status');
            if ($data['pay_status'] == -1) {
                $data['pay_status'] = 0;
            }
        }
        if (!empty($shipping_status)) {
            $data['shipping_status'] = I('shipping_status');
            if ($data['shipping_status'] == -1) {
                $data['shipping_status'] = 0;
            }
        }
        if (!empty($keyword)) {
            $map['order_sn'] = array('like', '%' . $keyword . '%');
            $map['_logic'] = 'or';
            $data['_complex'] = $map;
        }
        $data['add_time'] = array('between', array($begin_time, $end_time));
        $order_info = M('order_info', 'ecs_', 'DB_CONFIG1');
        $count = $order_info->where($data)->count();
        $page = new \Think\PageAjax($count, 15);//实例化分页类传入总记录数和每页显示的记录数
        $list = $order_info->where($data)->order('add_time desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($list as $k => $v) {
            $list[$k]['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
            $list[$k]['money'] = number_format($v['subtotal'], 2);
        }
        $pagelist = $page->show();//分页显示输出

        $info[] = $pagelist;
        $info[] = $list;
        $info['count'] = $count;
        $this->ajaxReturn($info);
    }

    /**
     *分配订单页面ajax联动
     */
    public function distribute_ajax()
    {
        $user_id = I('user_id');
        $city_id = I('city_id');
        $pay_id = I('pay_id');
        if (!empty($city_id)) {
            $id_4s = M('pay')->where(array('id' => $pay_id))->getField('id_4s');
            $brand_id = M('user_4s')->where(array('id' => $id_4s))->getField('brand_4s');
//            $info = M('view_car_price')->where(array('city_id' => $city_id,'brand_id'=>$brand_id,'isdelete' => 0))->distinct(true)->field('user_id,name_4s')->select();
            $info = M('user_4s')->where(array('city_id' => $city_id, 'brand_4s' => $brand_id, 'isdelete' => 0))->distinct(true)->field('id,name_4s,user_name')->select();
//            foreach ($info as $key => $value) {
//                $info[$key]['user_name'] = M('user_4s')->where(array('id' => $value['user_id']))->getField('user_name');
//            }
        }
        if (!empty($user_id)) {
            $info = M('user_4s')->where(array('id' => $user_id))->field('id,user_name,name_4s,brand_4s')->select();
        }
        $this->ajaxReturn($info);
    }

    /**
     *积分兑换ajax异步显示
     */
    public function credits_exchange_ajax()
    {
        $keyword = I('keyword');
        $status = I('status');
        $begin_time = I('begin_time');
        $end_time = I('end_time');
        $time = date('Y-m-d');
        if (!empty($keyword)) {
            $map['user_name'] = array('like', '%' . $keyword . '%');
            $map['telphone'] = array('like', '%' . $keyword . '%');
            $map['receiver'] = array('like', '%' . $keyword . '%');
            $map['out_trade_no'] = array('like', '%' . $keyword . '%');
            $map['_logic'] = 'or';
            $data['_complex'] = $map;
        }
        if (empty($end_time)) {
            $end_time = strtotime($time . '23:59:59');
        } else {
            $end_time = strtotime($end_time . '23:59:59');
        }

        if (empty($begin_time)) {
            $begin_time = $end_time - 3600 * 24 * 31 + 1; // 默认查询一个月内下订单信息
        } else {
            $begin_time = strtotime($begin_time . '00:00:00');
        }
        if (!empty($status)) {
            $data['status'] = $status;
        }
        $data['UNIX_TIMESTAMP(createtime)'] = array('between', array($begin_time, $end_time));
        $count = M('view_score_address')->where($data)->count();
        $page = new \Think\PageAjax($count, 15);//实例化分页类传入总记录数和每页显示的记录数
        $list = M('view_score_address')->where($data)->order('createtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();

        $pagelist = $page->show();//分页显示输出
        $info[] = $pagelist;
        $info[] = $list;
        $info['count'] = $count;
        $this->ajaxReturn($info);
    }

    /**
     *积分兑换订单详情
     */
    public function credits_exchange_detail()
    {
        $id = I('id');
        $data = null;
        if (!empty($id)) {
            $data['id'] = $id;
            $info = M('view_score_address')->where($data)->find();
            $this->ajaxReturn($info);
        }
        $this->ajaxReturn(null);
    }

    /**
     *结合xml的ajax完成区域，省份，城市联动的方法。添加城市用到。
     */
    public function ajax_city()
    {
        $area = I('area');
        $province = I('province');
        if (!empty($area)) {
            $area_xml = simplexml_load_file("Public/Admin/xml/Areas.xml");
            foreach ($area_xml->Area as $key => $value) {
                if ($value == $area) {
                    $id = $value['ID'];
                    break;
                }
            }
            $id = intval($id);
            $province_xml = simplexml_load_file("Public/Admin/xml/Provinces.xml");
            foreach ($province_xml->Province as $key => $value) {
                if ($id == $value['PID']) {
                    $province_value[] = strval($value['ProvinceName']);
                }
            }
            $info = $province_value;
        }
        if (!empty($province)) {
            $province_xml = simplexml_load_file("Public/Admin/xml/Provinces.xml");
            foreach ($province_xml->Province as $key => $value) {
                if ($province == $value) {
                    $id = $value['ID'];
                    break;
                }
            }
            $id = intval($id);
            $city_xml = simplexml_load_file("Public/Admin/xml/Cities.xml");
            foreach ($city_xml->City as $key => $value) {
                if ($id == $value['PID']) {
                    $city_value[] = strval($value['CityName']);
                }
            }
            $info = $city_value;
        }
        $this->ajaxReturn($info);
    }

    /**
     *品牌异步展示
     */
    public function allcar_ajax()
    {
        $keyvalue = I('keyvalue');
        if (!empty($keyvalue)) {
            $data['brand_name'] = array('like', '%' . $keyvalue . '%');
        }
        $data['isdelete'] = 0;
        $count = M('brand')->where($data)->count();
        $Page = new \Think\PageAjax($count, 10);
        $info = M('brand')->where($data)->limit($Page->firstRow . ',' . $Page->listRows)->order('createtime DESC')->select();
        foreach ($info as $key => $value) {
            $info[$key]['logo'] = str_replace('type', 'big', $info[$key]['logo']);
        }
        $info2[] = $Page->show();
        $info2[] = $info;
        $info2['count'] = $count;

        $this->ajaxReturn($info2);

    }

    /**
     *车型分类ajax异步展示
     */
    public function cartype_ajax()
    {
        $data['isdelete'] = 0;
        $count = M('car_type')->where($data)->count();
        $Page = new \Think\PageAjax($count, 10);
        $info = M('car_type')->where($data)->limit($Page->firstRow . ',' . $Page->listRows)->order('createtime DESC')->select();
        foreach ($info as $key => $value) {
            $info[$key]['logo'] = str_replace('type', 'big', $info[$key]['logo']);
        }
        $info2[] = $Page->show();
        $info2[] = $info;
        $info2['count'] = $count;
        $this->ajaxReturn($info2);
    }

    /**
     *特价车列表异步展示
     */
    public function carspecial_ajax()
    {
        $time = date('Y-m-d');
        $begin_time = I('begin_time');
        $end_time = I('end_time');
        $brand_id = I('brand_id');
        $status = I('status');
        if (empty($end_time)) {
            $end_time = strtotime($time . '23:59:59');
        } else {
            $end_time = strtotime($end_time . '23:59:59');
        }

        if (empty($begin_time)) {
            $begin_time = $end_time - 3600 * 24 * 31 + 1; // 默认查询一个月内下订单信息
        } else {
            $begin_time = strtotime($begin_time . '00:00:00');
        }
        $data['UNIX_TIMESTAMP(start_date)'] = array('between', array($begin_time, $end_time));
        /*$data['UNIX_TIMESTAMP(end_date)'] = array('between', array($begin_time, $end_time));*/
        if (!empty($brand_id)) {
            $data['brand_id'] = $brand_id;
        }
        if (!empty($status)) {
            $data['status'] = $status;
        }

        $data['isdelete'] = 0;
        $count = M('view_special_price_car')->where($data)->count();
        $Page = new \Think\PageAjax($count, 15);
        $info = M('view_special_price_car')->where($data)->limit($Page->firstRow . ',' . $Page->listRows)->order('createtime DESC')->select();
        foreach ($info as $key => $value) {
            $info[$key]['user_name'] = M('user_4s')->where(array('id' => $value['user_id'], 'role' => '2', 'isdelete' => 0))->getField('user_name');
            $info[$key]['sales_area_name'] = M('sales_area')->where(array('car_special_id' => $value['id'], 'type' => 2, 'isdelete' => 0))->field('sales_area_name')->select();
        }
        $info2[] = $Page->show();
        $info2[] = $info;
        $info2['count'] = $count;
        $this->ajaxReturn($info2);
    }

    /**
     *底价车ajax联动效果
     */
    public function carprice_ajax()
    {
        $city_name = I('city_name');
        $brand_name = I('brand_name');
        $brand_name = substr($brand_name, 1);//将前面的字母截取
        $car_model_name = I('car_model_name');
        $car_name = I('car_name');
        $data['isdelete'] = 0;
        if (!empty($city_name)) {
            $data['city_name'] = $city_name;
            $info = M('view_car_price')->where($data)->distinct(true)->field('name_4s')->select();
        }
        if (!empty($brand_name)) {
            $data['brand_name'] = $brand_name;
            $info = M('view_car_price')->where($data)->distinct(true)->field('car_model_name')->select();
        }
        if (!empty($car_model_name)) {
            $data['car_model_name'] = $car_model_name;
            $info = M('view_car_price')->where($data)->distinct(true)->field('car_name')->select();
        }
        if (!empty($car_name)) {
            $data['car_name'] = $car_name;
            $info = M('view_car_price')->where($data)->distinct(true)->field('exterior_color_name')->select();
            $interior_info = M('view_car_price')->where($data)->distinct(true)->field('interior_color_name')->select();
            if (count($info) >= count($interior_info)) {
                foreach ($info as $key => $value) {
                    if (array_key_exists($key, $interior_info)) {
                        $info[$key]['interior_color_name'] = $interior_info[$key]['interior_color_name'];
                    } else {
                        break;
                    }
                }
            } else {
                foreach ($interior_info as $key => $value) {
                    if (array_key_exists($key, $info)) {
                        $interior_info[$key]['exterior_color_name'] = $info[$key]['exterior_color_name'];
                    } else {
                        break;
                    }
                }
                $info = $interior_info;
            }
        }
        $this->ajaxReturn($info);

    }

    /**
     * @Description:客服操作判别订单列表
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/10/25 10:38
     * @Version 2.0
     */
    public function cus_list()
    {
        $id = I('id');
        $info = M('pay')->where(array('id' => $id))->find();
        $info['pay_cus'] = M('pay_cus')->where(array('pay_id' => $id,'type'=>1, 'isdelete' => 0))->select();
        $this->ajaxReturn($info);
    }

    /**
     * @Description:客服操作订单列表(新)
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/10/25 10:41
     * @Version 2.0
     */
    public function cus_dh_list()
    {
        $id = I('id');
        $info = M('pay')->where(array('id' => $id))->find();
        $info['pay_cus'] = M('pay_cus')->where(array('pay_id' => $id,'type'=>I('type'), 'isdelete' => 0))->select();
        $this->ajaxReturn($info);
    }

    /**
     *客服操作列表——报名用户
     */
    public function cus_list_activity()
    {
        $id = I('id');
        $info = M('user_activity')->where(array('id' => $id))->find();
        $info['pay_cus'] = M('pay_cus')->where(array('user_activity_id' => $id, 'isdelete' => 0))->order('createtime desc')->select();
        $this->ajaxReturn($info);
    }

    /**
     *底价车ajax无刷新分页
     */
    public function carprice_ajax2()
    {
        $city = I('city');
        $name_4s = I('name_4s');
        $brand = I('brand');
        $model = I('model');
        // $model   ='H6';
        $car = I('car');
        $exterior = I('exterior');
        $interior = I('interior');
        if (!empty($city)) {
            $data['city_name'] = $city;
        }
        if (!empty($name_4s)) {
            $data['name_4s'] = $name_4s;
        }
        if (!empty($brand)) {
            $data['brand_name'] = substr($brand, 1);
        }
        if (!empty($model)) {
            $data['car_model_name'] = $model;
        }
        if (!empty($car)) {
            $data['car_name'] = $car;
        }
        if (!empty($exterior)) {
            $data['exterior_color_name'] = $exterior;
        }
        if (!empty($interior)) {
            $data['interior_color_name'] = $interior;
        }
        $data['isdelete'] = 0;
        $count = M('view_car_price')->where($data)->count();
        $Page = new \Think\PageAjax($count, 15);
        $info = M('view_car_price')->where($data)->limit($Page->firstRow . ',' . $Page->listRows)->order('createtime DESC')->select();
        foreach ($info as $key => $value) {
            $info[$key]['user_name'] = M('user_4s')->where(array('id' => $value['user_id'], 'role' => 2, 'isdelete' => 0))->getField('user_name');
            $info[$key]['sales_area_name'] = M('sales_area')->where(array('car_price_id' => $value['id'], 'type' => 1, 'isdelete' => 0))->field('sales_area_name')->select();
        }
        $info2[] = $Page->show();
        $info2[] = $info;
        $info2['count'] = $count;
        $this->ajaxReturn($info2);

    }

    /**
     *ajax联动效果
     */
    public function addModelHandle()
    {
        $car_model = M('car_model');
        $car = M('car');
        $data['isdelete'] = 0;
        $data['brand_id'] = I('brand_id');
        $data['car_model_id'] = I('car_model_id');
        $data['car_id'] = I('car_id');
        $car_id = I('car_id1');
        $index_car_price_id = I('id');
        $index_car_price_id2 = I('id2');
        if (!empty($data['brand_id'])) {
            $info_model = $car_model->where($data)->order('car_model_name asc')->select();
            foreach ($info_model as $k => $v) {
                $info_model[$k]['name'] = $v['car_model_name'];
                $info_model[$k]['value'] = $v['id'];
            }
        }
        if (!empty($data['car_model_id'])) {
            $data['status'] = 1;
            $info_model = $car->where($data)->order('`order` desc')->select();
            foreach ($info_model as $k => $v) {
                $info_model[$k]['name'] = $v['car_name'];
                $info_model[$k]['value'] = $v['id'];
            }
        }
        if (!empty($data['car_id'])) {
            $info_model = M('view_exterior_color')->where($data)->select();
        }
        if (!empty($car_id)) {
            $info_model = M('view_interior_color')->where(array('car_id' => $car_id, 'isdelete' => 0))->select();
        }
        if (!empty($index_car_price_id)) {
            $saveinfo['status'] = 2;
            $saveinfo['id'] = $index_car_price_id;
            $saveinfo['updatetime'] = date('Y-m-d H:i:s', time());
            $saveinfo['updateuser'] = session('admin_name');
            $info_model = M('index_car_price')->save($saveinfo);
        }
        if (!empty($index_car_price_id2)) {
            $saveinfo['status'] = 1;
            $saveinfo['id'] = $index_car_price_id2;
            $saveinfo['updatetime'] = date('Y-m-d H:i:s', time());
            $saveinfo['updateuser'] = session('admin_name');
            $info_model = M('index_car_price')->save($saveinfo);
        }
        $this->ajaxReturn($info_model);
    }

    /**
     *公开底价设置ajax联动刷选效果
     */
    public function addModelHandle2()
    {

        $brand_id = I('brand_id');
        $car_model_id = I('car_model_id');
        $car_id = I('car_id');
        $data['isdelete'] = 0;
        if (!empty($brand_id)) {
            $data['brand_id'] = $brand_id;
            $info = M('index_car_price')->where($data)->distinct(true)->field('model_name,model_id')->select();
        }
        if (!empty($car_model_id)) {
            $data['model_id'] = $car_model_id;
            $info = M('index_car_price')->where($data)->distinct(true)->field('car_id')->select();
            foreach ($info as $k => $v) {
                $info[$k]['car_name'] = M('car')->where(array('id' => $v['car_id']))->getField('car_name');
            }
        }
        if (!empty($car_id)) {
            $data['car_id'] = $car_id;
            //获取外观颜色id
            $info = M('index_car_price')->where($data)->distinct(true)->field('exterior_color_id')->select();
            foreach ($info as $k => $v) {
                $info[$k]['exterior_color_name'] = M('view_exterior_color')->where(array('id' => $v['exterior_color_id']))->getField('color_name');
            }
            //获取内饰颜色id
            $interior_info = M('index_car_price')->where($data)->distinct(true)->field('interior_color_id')->select();
            foreach ($interior_info as $k => $v) {
                $interior_info[$k]['interior_color_name'] = M('view_interior_color')->where(array('id' => $v['interior_color_id']))->getField('color_name');
            }
            if (count($info) >= count($interior_info)) {
                foreach ($info as $key => $value) {
                    if (array_key_exists($key, $interior_info)) {
                        $info[$key]['interior_color_name'] = $interior_info[$key]['interior_color_name'];
                        $info[$key]['interior_color_id'] = $interior_info[$key]['interior_color_id'];
                    } else {
                        break;
                    }
                }
            } else {
                foreach ($interior_info as $key => $value) {
                    if (array_key_exists($key, $info)) {
                        $interior_info[$key]['exterior_color_name'] = $info[$key]['exterior_color_name'];
                        $interior_info[$key]['exterior_color_id'] = $info[$key]['exterior_color_id'];
                    } else {
                        break;
                    }
                }
                $info = $interior_info;
            }
        }
        $this->ajaxReturn($info);

    }

    /**
     *首页公开底价设置ajax异步展示
     */
    public function public_carprice_ajax()
    {
        $brand_id = I('brand');
        $model_id = I('model');
        $car_id = I('car');
        $exterior_color_id = I('exterior');
        $interior_color_id = I('interior');
        $index_status = I('index_status');
        if (!empty($brand_id)) {
            $data['brand_id'] = $brand_id;
        }
        if (!empty($model_id)) {
            $data['model_id'] = $model_id;
        }
        if (!empty($car_id)) {
            $data['car_id'] = $car_id;
        }
        if (!empty($exterior_color_id)) {
            $data['exterior_color_id'] = $exterior_color_id;
        }
        if (!empty($interior_color_id)) {
            $data['interior_color_id'] = $interior_color_id;
        }
        if (!empty($index_status)) {
            $data['status'] = $index_status;
        }
        $data['isdelete'] = 0;
        $data['car_status'] = 1;
        $count = M('view_index_car_price')->where($data)->count();
        $Page = new \Think\PageAjax($count, 15);
        $info = M('view_index_car_price')->where($data)->limit($Page->firstRow . ',' . $Page->listRows)->order('createtime DESC')->select();
        /*   foreach ($info as $key => $value) {
                $info[$key]['car_name'] = M('car')->where(array('id' => $value['car_id'], 'isdelete' => 0))->getField('car_name');
                $info[$key]['exterior_color_name'] = M('view_exterior_color')->where(array('id' => $value['exterior_color_id'], 'isdelete' => 0))->getField('color_name');
                $info[$key]['interior_color_name'] = M('view_interior_color')->where(array('id' => $value['interior_color_id'], 'isdelete' => 0))->getField('color_name');
            }*/
        $info2[] = $Page->show();
        $info2[] = $info;
        $info2['count'] = $count;
        $this->ajaxReturn($info2);


    }

    /**
     *品牌接口
     */
    public function brand()
    {
        $info = M('brand')->where(array('isdelete' => 0))->order('alif')->select();
        foreach ($info as $k => $v) {
            $info[$k]['value'] = $v['id'];
            $info[$k]['name'] = $v['brand_name'];
        }
        $this->ajaxReturn($info);
    }

    /**
     *获取发送短信的手机号
     */
    public function sms_member()
    {
        $info = M('user_general')->distinct('true')->field('tel')->select();
        foreach ($info as $key => $value) {
            if (!empty($value['tel'])) {
                $data1[] = $value['tel'];
            }
        }
        $data['tel'] = $data1;
        $this->ajaxReturn($data);

    }

    /**
     *添加4S维护的副营品牌
     */
    public function addBrand4S()
    {
        if (IS_POST) {
            /*查找是否存在*/
            $data['brand_id'] = I('brandid');
            $data['user_4s_id'] = I('userid');
            $data['isdelete'] = 0;
            $find = M('user_4s_brand')->where($data)->find();
            if ($find) {
                $ret['status'] = 0;
                $ret['msg'] = '已经添加过该品牌';
            } else {
                //如果不存在的话插入数据库
                $data['id'] = md5(microtime());
                $data['create_time'] = time();
                $data['create_user'] = $_SESSION['admin_name'];

                if (M('user_4s_brand')->add($data)) {
                    $ret['status'] = 1;
                    $ret['msg'] = '添加成功';
                } else {
                    $ret['status'] = 0;
                    $ret['msg'] = '添加失败';
                }
            }
            $this->ajaxReturn($ret);
        }
    }

    /**
     *删除4S维护的副营品牌
     */
    public function delUserBrand()
    {
        if (IS_POST) {
            $id = I('id');
            $data['update_time'] = time();
            $data['update_user'] = $_SESSION['admin_name'];
            $data['isdelete'] = 1;
            $del = M('user_4s_brand')->where(array('id' => $id))->save($data);
            if ($del) {
                $ret['status'] = 1;
                $ret['msg'] = '删除成功';
            } else {
                $ret['status'] = 0;
                $ret['msg'] = '删除失败';
            }
            $this->ajaxReturn($ret);
        }
    }

    /**
     *用户喜好
     */
    public function userlove()
    {
        $time = date('Y-m-d');
        $begin_time = I('begin_time');
        $end_time = I('end_time');
        $brand_id = I('brand_id');
        $car_model_id = I('car_model_id');
        $car_id = I('car_id');
        $from_plan = I('from_plan');
        if (empty($end_time)) {
            $end_time = strtotime($time . '23:59:59');
        } else {
            $end_time = strtotime($end_time . '23:59:59');
        }

        if (empty($begin_time)) {
            $begin_time = $end_time - 3600 * 24 * 31 + 1; // 默认查询一个月内下订单信息
        } else {
            $begin_time = strtotime($begin_time . '00:00:00');
        }
        if (!empty($brand_id)) {
            $data['brand_id'] = $brand_id;
        }
        if (!empty($car_model_id)) {
            $data['car_model_id'] = $car_model_id;
        }
        if (!empty($car_id)) {
            $data['car_id'] = $car_id;
        }
        if (!empty($from_plan)) {
            $data['from_plan'] = $from_plan;
        }

        $data['UNIX_TIMESTAMP(createtime)'] = array('between', array($begin_time, $end_time));
        $data['isdelete'] = 0;
        $count = D('view_car_plan')->where($data)->count();
        $page = new \Think\PageAjax($count, 15);//实例化分页类传入总记录数和每页显示的记录数
        $list = D('view_car_plan')->where($data)->order('createtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        $pagelist = $page->show();//分页显示输出
        $info['count'] = $count;
        $info[] = $pagelist;
        $info[] = $list;
        $this->ajaxReturn($info);

    }

    /**
     *取消公开底价设置
     */
    public function delAllIndexPrice()
    {
        if (IS_POST) {
            //判断品牌是否为空
            if (I('brand') != '') {
                $data['brand_id'] = I('brand');
            } else {
                $ret['status'] = 0;
                $ret['msg'] = '请选择品牌';
            }
            //判断车型是否为空
            if (I('model') != '') {
                $data['model_id'] = I('model');
                $date_price['car_model_id'] = I('model');
            } else {
                $ret['status'] = 0;
                $ret['msg'] = '车型不能为空';
                $this->ajaxReturn($ret);
            }
            //判断车款是否为空
            if (I('car') != '') {
                $data['car_id'] = I('car');
                $date_price['car_id'] = I('car');
            }
            if (I('exterior') != '') {
                $data['exterior_color_id'] = I('exterior');
                $date_price['exterior_color_id'] = I('exterior');
            }
            //判断内饰颜色是否为空
            if (I('interior') != '') {
                $data['interior_color_id'] = I('interior');
                $date_price['interior_color_id'] = I('interior');
            }
            $data['isdelete'] = 0;
            $date_price['isdelete'] = 0;

            $sel = M('index_car_price')->where($data)->save(array('isdelete' => 1, 'updatetime' => date('Y-m-d H:i:s', time()), 'updateuser' => $_SESSION['admin_name']));
            if ($sel)
                $update = M('view_car_price')->where($date_price)->save(array('is_xunjia' => 1, 'updatetime' => date('Y-m-d H:i:s', time()), 'updateuser' => $_SESSION['admin_name']));
            if ($update) {
                $ret['status'] = 1;
                $ret['msg'] = '取消公开底价车设置成功';
            } else {
                $ret['status'] = 0;
                $ret['msg'] = '取消公开底价车设置成功但是关联报价表修改成需要询价设置失败！';
            }
        } else {
            $ret['status'] = 0;
            $ret['msg'] = '取消公开底价车设置失败';
        }
        $this->ajaxReturn($ret);
    }

    /**
     *活动报名用户
     */
    public function ajax_user_activity()
    {
        $time = date('Y-m-d');
        $begin_time = I('begin_time');
        $end_time = I('end_time');

        $activity = I('activity');
        if (empty($end_time)) {
            $end_time = strtotime($time . '23:59:59');
        } else {
            $end_time = strtotime($end_time . '23:59:59');
        }

        if (empty($begin_time)) {
            $begin_time = $end_time - 3600 * 24 * 31 + 1; // 默认查询一个月内下订单信息
        } else {
            $begin_time = strtotime($begin_time . '00:00:00');
        }

        if (!empty($activity)) {
            $data['activity_number'] = $activity;
        }

        $data['UNIX_TIMESTAMP(createtime)'] = array('between', array($begin_time, $end_time));
        $data['isdelete'] = 0;
        $count = M('user_activity')->where($data)->count();
        $page = new \Think\PageAjax($count, 15);//实例化分页类传入总记录数和每页显示的记录数
        $list = M('user_activity')->where($data)->order('createtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        $pagelist = $page->show();//分页显示输出
        $info['count'] = $count;
        $info[] = $pagelist;
        $info[] = $list;
        $this->ajaxReturn($info);

    }

    /**
     *车生活-车知识数据获取
     */
    public function carlife_carknowledge()
    {
        $begin_time = I('begin_time');
        $end_time = I('end_time');
        $type = I('type');
        $hot = I('hot');
        $display = I('display');
        $keyword = I('keyword');
        $time = date('Y-m-d');
        if (empty($end_time)) {
            $end_time = strtotime($time . '23:59:59');
        } else {
            $end_time = strtotime($end_time . '23:59:59');
        }

        if (empty($begin_time)) {
            $begin_time = $end_time - 3600 * 24 * 31 + 1; // 默认查询一个月内下订单信息
        } else {
            $begin_time = strtotime($begin_time . '00:00:00');
        }
        if (!empty($type)) {
            $data['type_id'] = $type;
        }
        if (!empty($hot)) {
            $data['place_hot'] = $hot;
        }
        if (!empty($display)) {
            $data['display'] = $display;
        }

        if (!empty($keyword)) {
            $map['createuser'] = array('like', '%' . $keyword . '%');
            $map['title'] = array('like', '%' . $keyword . '%');
            $map['_logic'] = 'or';
            $data['_complex'] = $map;
        }
        $data['pid'] = 1;
        $data['UNIX_TIMESTAMP(createtime)'] = array('between', array($begin_time, $end_time));
        $data['isdelete'] = 0;
        $count = M('view_car_bbs')->where($data)->count();
        $page = new \Think\PageAjax($count, 15);//实例化分页类传入总记录数和每页显示的记录数
        $list = M('view_car_bbs')->where($data)->order('createtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        $pagelist = $page->show();//分页显示输出

        $info[] = $pagelist;
        $info[] = $list;
        $info['count'] = $count;
        $this->ajaxReturn($info);

    }

    /**
     *车生活-车主秀数据获取
     */
    public function carlife_carshow()
    {
        $begin_time = I('begin_time');
        $end_time = I('end_time');
        $type = I('type');
        $place_index = I('place_index');
        $check = I('check');
        $keyword = I('keyword');
        $time = date('Y-m-d');
        if (empty($end_time)) {
            $end_time = strtotime($time . '23:59:59');
        } else {
            $end_time = strtotime($end_time . '23:59:59');
        }

        if (empty($begin_time)) {
            $begin_time = $end_time - 3600 * 24 * 31 + 1; // 默认查询一个月内下订单信息
        } else {
            $begin_time = strtotime($begin_time . '00:00:00');
        }
        if (!empty($type)) {
            $data['type_id'] = $type;
        }
        if (!empty($place_index)) {
            $data['place_index'] = $place_index;
        }
        if (!empty($check)) {
            $data['check'] = $check;
        }

        if (!empty($keyword)) {
            $map['user_name'] = array('like', '%' . $keyword . '%');
            $map['title'] = array('like', '%' . $keyword . '%');
            $map['_logic'] = 'or';
            $data['_complex'] = $map;
        }
        $data['pid'] = 2;
        $data['UNIX_TIMESTAMP(createtime)'] = array('between', array($begin_time, $end_time));
        $data['isdelete'] = 0;
        $count = M('view_car_bbs')->where($data)->count();
        $page = new \Think\PageAjax($count, 15);//实例化分页类传入总记录数和每页显示的记录数
        $list = M('view_car_bbs')->where($data)->order('createtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        $pagelist = $page->show();//分页显示输出

        $info[] = $pagelist;
        $info[] = $list;
        $info['count'] = $count;
        $this->ajaxReturn($info);

    }

    /**
     *优惠券列表
     */
    public function ticket_ajax()
    {
        $keyword = I('keyword');
        if (!empty($keyword)) {
            $map['user_name'] = array('like', '%' . $keyword . '%');
            $map['_logic'] = 'or';
            $data['_complex'] = $map;
        }
        $data['isdelete'] = 0;
        $count = M('ticket')->where($data)->count();
        $page = new \Think\PageAjax($count, 15);//实例化分页类传入总记录数和每页显示的记录数
        $list = M('ticket')->where($data)->order('createtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        $pagelist = $page->show();//分页显示输出

        $info[] = $pagelist;
        $info[] = $list;
        $info['count'] = $count;
        $this->ajaxReturn($info);

    }

    /**
     *优惠券订单列表
     */
    public function ticket_user_ajax()
    {
        $keyword = I('keyword');
        $status = I('status');
        if (!empty($keyword)) {
            $map['user_name'] = array('like', '%' . $keyword . '%');
            $map['_logic'] = 'or';
            $data['_complex'] = $map;
        }
        if (!empty($status)) {
            $data['status'] = $status;
        }
        $data['isdelete'] = 0;
        $count = M('ticket_user')->where($data)->count();
        $page = new \Think\PageAjax($count, 15);//实例化分页类传入总记录数和每页显示的记录数
        $list = M('ticket_user')->where($data)->order('createtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($list as $key => $value) {
            $list[$key]['user_name'] = M('user_general')->where(array('id' => $value['user_id']))->getField('user_name');
        }
        $pagelist = $page->show();//分页显示输出

        $info[] = $pagelist;
        $info[] = $list;
        $info['count'] = $count;
        $this->ajaxReturn($info);

    }

    /**
     * @Description:专题活动--活动列表
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/9/20 13:22
     * @Version 2.0
     */
    public function activity_ajax()
    {
        $keyword = I('keyword');
        $status = I('status');
        if (!empty($keyword)) {
            $map['activity_name'] = array('like', '%' . $keyword . '%');
            $map['activity_number'] = array('like', '%' . $keyword . '%');
            $map['_logic'] = 'or';
            $data['_complex'] = $map;
        }
        if (!empty($status)) {
            $data['status'] = $status;
        }
        $data['isdelete'] = 0;
        $count = M('activities')->where($data)->count();
        $page = new \Think\PageAjax($count, 15);//实例化分页类传入总记录数和每页显示的记录数
        $list = M('activities')->where($data)->order('createtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($list as $key => $value) {
            $list[$key]['starttime'] = date('Y-m-d', $value['starttime']);
            $list[$key]['endtime'] = date('Y-m-d', $value['endtime']);
        }
        $pagelist = $page->show();//分页显示输出
        $info[] = $pagelist;
        $info[] = $list;
        $info['count'] = $count;
        $this->ajaxReturn($info);
    }

    /**
     * @Description:专题活动--活动车款列表
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/9/20 13:22
     * @Version 2.0
     */
    public function activity_car_ajax()
    {
        $activity_id = I('activity_id');
        if (!empty($activity_id)) {
            $data['activity_id'] = $activity_id;
        }

        $data['isdelete'] = 0;
        $count = M('car_activities')->where($data)->count();
        $page = new \Think\PageAjax($count, 15);//实例化分页类传入总记录数和每页显示的记录数
        $list = M('car_activities')->where($data)->order('sort asc')->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($list as $key => $value) {
            $find = D('view_car_price')->where(array('id' => $value['car_price_id']))->find();
            $list[$key]['brand_name'] = $find['brand_name'];
            $list[$key]['car_model_name'] = $find['car_model_name'];
            if ($value['activity_id'] == 26) {
                $list[$key]['car_name'] = "所有";
                $list[$key]['exterior_color_name'] = "所有";
                $list[$key]['interior_color_name'] = "所有";
            } else {
                $list[$key]['car_name'] = $find['car_name'];
                $list[$key]['exterior_color_name'] = $find['exterior_color_name'];
                $list[$key]['interior_color_name'] = $find['interior_color_name'];
            }

            $list[$key]['activity_name'] = M('activities')->where(array('id' => $value['activity_id']))->getField('activity_name');
        }
        $pagelist = $page->show();//分页显示输出
        $info[] = $pagelist;
        $info[] = $list;
        $info['count'] = $count;
        $this->ajaxReturn($info);
    }

    /**
     *专题活动--获取公开底价的车型
     */
    public function baojia_car_model()
    {
        $info = D('view_car_price')->where(array('isdelete' => 0, 'brand_id' => I('brand_id')))->field('car_model_id,car_model_name')->distinct('true')->select();
        $this->ajaxReturn($info);

    }

    /**
     *专题活动--获取公开底价的车款
     */
    public function baojia_car()
    {
        $info = D('view_car_price')->where(array('isdelete' => 0, 'car_model_id' => I('car_model_id')))->field('car_id,car_name')->distinct('true')->select();
        $this->ajaxReturn($info);

    }

    /**
     *专题活动--获取公开底价的颜色
     */
    public function baojia_color()
    {
        $info['exterior'] = D('view_car_price')->where(array('isdelete' => 0, 'car_id' => I('car_id')))->field('exterior_color_id,exterior_color_name')->distinct('true')->select();
        $info['interior'] = D('view_car_price')->where(array('isdelete' => 0, 'car_id' => I('car_id')))->field('interior_color_id,interior_color_name')->distinct('true')->select();

        $this->ajaxReturn($info);

    }

    /**
     *专题活动--获取公开底价的车款
     */
    public function baojia_low_price()
    {
        $data['exterior_color_id'] = I('exterior_color_id');
        $data['interior_color_id'] = I('interior_color_id');
        /* $data['is_xunjia']=2;*/
        $data['isdelete'] = 0;
        $info = D('view_car_price')->where($data)->order('low_price asc')->getField('low_price');
        if ($info) {
            $low_price = number_format($info);
        } else {
            $low_price = '暂无最底价';
        }
        $this->ajaxReturn($low_price);

    }

    /**
     * @Description:分享活动-获得晒单记录
     * @Return:
     * @Author: 孙磊
     * @Date: 2016/10/19 11:20
     * @Version 2.0
     */
    public function share_logs_ajax()
    {
        $user_name = I('user_name');
        $is_buy_car = I('buy_car_num');
        if ($user_name != '') {
            $data['user_name'] = $user_name;
        }
        if ($is_buy_car == 1) {
            $data['buy_car_num'] = array(GT, 0);
        }
        if ($is_buy_car == 2) {
            $data['buy_car_num'] = array(eq, 0);
        }
        $count = M('view_share_log')->where($data)->count();
        $page = new \Think\PageAjax($count, 15);//实例化分页类传入总记录数和每页显示的记录数
        $list = M('view_share_log')->where($data)->limit($page->firstRow . ',' . $page->listRows)->select();
        $pagelist = $page->show();//分页显示输出
        $info[] = $pagelist;
        $info[] = $list;
        $info['count'] = $count;
        $this->ajaxReturn($info);
    }

    /**
     * @Description:活动订单
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/10/28 13:53
     * @Version 2.0
     */
    public function activity_order()
    {
        $keyword=I('keyword');
        $from_order = I('from_order');
        $from_activityid = I('from_activityid');
        $status = I('status');
        $begin_time = I('begin_time');
        $end_time = I('end_time');
        $time = date('Y-m-d');
        if (!empty($keyword)) {
            $map['out_trade_no'] = array('like', '%' . $keyword . '%');
            $map['_logic'] = 'or';
            $data['_complex'] = $map;
        }
        if (!empty($from_order)) {

            $data['from_order'] = $from_order;
        }
        if (!empty($from_activityid)) {
            if($from_activityid==27)
            {
                $data['from_activityid'] = array('in','27,28');
            }
        }
        if (empty($end_time)) {
            $end_time = strtotime($time . '23:59:59');
        } else {
            $end_time = strtotime($end_time . '23:59:59');
        }
        if (empty($begin_time)) {
            $begin_time = $end_time - 3600 * 24 * 31 + 1; // 默认查询一个月内下订单信息
        } else {
            $begin_time = strtotime($begin_time . '00:00:00');
        }
        if (!empty($status)) {
            if($status==2)
            {
                $data['status'] = 0;
            }else{
                $data['status'] = $status;
            }

        }

        $data['pay_obj']=4;
        $data['UNIX_TIMESTAMP(createtime)'] = array('between', array($begin_time, $end_time));
        $count = M('pay')->where($data)->count();
        $page = new \Think\PageAjax($count, 15);//实例化分页类传入总记录数和每页显示的记录数
        $list = M('pay')->where($data)->order('createtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($list as $key=>$value)
        {
            $list[$key]['buyer_tel']=M('user_general')->where(array('id'=>$value['user_id']))->getField('tel');
            $actinfo=M('activities')->where(array('id'=>$value['from_activityid']))->find();
            $list[$key]['activity_name']=$actinfo['activity_name'];
            if ($actinfo['starttime']>time())
            {
                $list[$key]['actstatus']=1;
            }elseif ($actinfo['starttime']<=time() and $actinfo['endtime']>=time())
            {
                $list[$key]['actstatus']=2;
            }elseif ($actinfo['endtime']<time())
            {
                $list[$key]['actstatus']=3;
            }

        }

        $pagelist = $page->show();//分页显示输出
        $info[] = $pagelist;
        $info[] = $list;
        $info['count'] = $count;
        $this->ajaxReturn($info);

    }

    /**
     * @Description:活动订单详情
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/10/28 13:53
     * @Version 2.0
     */
    public function activity_order_detail()
    {
        $info=M('pay')->where(array('id'=>I('id')))->find();
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
     * @Description:丽车坊订单
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/10/31 11:45
     * @Version 2.0
     */
    public function liche_order()
    {
        $keyword=I('keyword');
        $from_order = I('from_order');
        $from_activityid = I('from_activityid');
        $status = I('status');
        $begin_time = I('begin_time');
        $end_time = I('end_time');
        $time = date('Y-m-d');
        if (!empty($keyword)) {
            $map['buyer_tel'] = array('like', '%' . $keyword . '%');
            $map['out_trade_no'] = array('like', '%' . $keyword . '%');
            $map['_logic'] = 'or';
            $data['_complex'] = $map;
        }
        if (!empty($from_order)) {

            $data['from_order'] = $from_order;
        }
        if (!empty($from_activityid)) {

            $data['from_activityid'] = $from_activityid;
        }
        if (empty($end_time)) {
            $end_time = strtotime($time . '23:59:59');
        } else {
            $end_time = strtotime($end_time . '23:59:59');
        }
        if (empty($begin_time)) {
            $begin_time = $end_time - 3600 * 24 * 31 + 1; // 默认查询一个月内下订单信息
        } else {
            $begin_time = strtotime($begin_time . '00:00:00');
        }
        if (!empty($status)) {
            if($status==2)
            {
                $data['status'] = 0;
            }else{
                $data['status'] = $status;
            }
        }

        $data['pay_obj']=4;
        $data['from_activityid']=29;
        $data['UNIX_TIMESTAMP(createtime)'] = array('between', array($begin_time, $end_time));
        $count = M('pay')->where($data)->count();
        $page = new \Think\PageAjax($count, 15);//实例化分页类传入总记录数和每页显示的记录数
        $list = M('pay')->where($data)->order('createtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($list as $key=>$value)
        {
            $list[$key]['buyer_tel']=M('user_general')->where(array('id'=>$value['user_id']))->getField('tel');
            $actinfo=M('activities')->where(array('id'=>$value['from_activityid']))->find();
            $list[$key]['activity_name']=$actinfo['activity_name'];
            if ($actinfo['starttime']>time())
            {
                $list[$key]['actstatus']=1;
            }elseif ($actinfo['starttime']<=time() and $actinfo['endtime']>=time())
            {
                $list[$key]['actstatus']=2;
            }elseif ($actinfo['endtime']<time())
            {
                $list[$key]['actstatus']=3;
            }

        }

        $pagelist = $page->show();//分页显示输出
        $info[] = $pagelist;
        $info[] = $list;
        $info['count'] = $count;
        $this->ajaxReturn($info);

    }

    /**
     * @Description:丽车坊订单详情
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/10/31 13:14
     * @Version 2.0
     */
    public function liche_order_detail()
    {
        $info=M('pay')->where(array('id'=>I('id')))->find();
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
        * @Description:热门品牌列表
        * @Return:
        * @Author: 孙磊
        * @Date: 2016/11/3 15:40
        * @Version 2.0
        */
    public function HotBrandList(){
        if(I('client')){
            $data['client'] = I('client');
        }
        $count = M('hot_brand')->where($data)->count();
        $Page = new \Think\PageAjax($count, 10);
        $info = M('hot_brand')->where($data)->limit($Page->firstRow . ',' . $Page->listRows)->order('id DESC')->select();
        foreach ($info as $key => $value) {
            $brand = M('brand')->where(array('id'=>$value['brand_id'],'isdelete'=>0))->find();
            $info[$key]['logo'] = OSS.str_replace('type', 'big',$brand['logo']);
            $info[$key]['brand_name'] = $brand['brand_name'];
            switch($value['client']){
                case '1':$info[$key]['client'] = "手机客户端";break;
                case '2':$info[$key]['client'] = "app";break;
                case '3':$info[$key]['client'] = "pc";break;
            }
        }
        $info2[] = $Page->show();
        $info2[] = $info;
        $info2['count'] = $count;
        $this->ajaxReturn($info2);
    }

    /**
        * @Description:删除热门品牌
        * @Return:
        * @Author: 孙磊
        * @Date: 2016/11/4 13:27
        * @Version 2.0
        */
    public function DelHotBrand(){
        if(IS_POST){
            $del = M('hot_brand')->where(array('id'=>I('id')))->delete();
            if($del){
                $ret['status'] = 1;
                $ret['msg'] = "删除成功";
            }else{
                $ret['status'] = 0;
                $ret['msg'] = "删除失败";
            }
            $this->ajaxReturn($ret);
        }else{
            $this->ajaxReturn("非法操作");
        }
    }
    /**
        * @Description:设置客户端属性
        * @program:id:表ID ,table:表名称
        * @Author: 孙磊
        * @Date: 2016/11/7 13:35
        * @Version 2.0
        */
    public function setClient(){
        if(IS_POST){
            $res = M($_POST['table'])->where(array('id'=>I('id')))->save(array('client'=>I('client')));
            if($res){
                $this->ajaxReturn($res);
            }
        }else{
            $this->ajaxReturn("修改失败！");
        }
    }
}
