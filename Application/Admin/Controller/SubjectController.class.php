<?php
namespace Admin\Controller;

use Think\Controller;

/**
 * Class AdversController 广告管理控制器
 * @package Admin\Controller
 */
class SubjectController extends CommonController
{
    /**
     *活动列表
     */
    public function activity()
    {
        $this->display();
    }

    /**
     *添加活动
     */
    public function activity_add()
    {
        if (!empty($_POST)) {
            $activity = M('activities');
            $_POST['starttime'] = strtotime($_POST['starttime'] . '00:00:00');
            $_POST['endtime'] = strtotime($_POST['endtime'] . '00:00:00');
            $activity->create($_POST);
            if ($activity->add()) {
                $this->success('添加成功', U('activity'));
            } else {
                $this->error('添加失败');
            }
        }
        $this->display();
    }

    /**
     *删除活动
     */
    public function activity_delete()
    {
        $data['isdelete'] = 1;
        $data['updateuser'] = session('admin_name');
        $data['updatetime'] = date('Y-m-d H:i:s', time());
        $data['id'] = I('id');
        if (M('activities')->save($data)) {
            $data['status'] = 1;
            $data['msg'] = '删除成功！';
        } else {
            $data['status'] = 2;
            $data['msg'] = '删除失败！';
        }
        $this->ajaxReturn($data);
    }

    /**
     *开启活动
     */
    public function activity_open()
    {
        $data['status'] = I('status');
        $data['updateuser'] = session('admin_name');
        $data['updatetime'] = date('Y-m-d H:i:s', time());
        $data['id'] = I('id');
        if (M('activities')->save($data)) {
            $data['status'] = 1;
            $data['msg'] = '操作成功！';
        } else {
            $data['status'] = 2;
            $data['msg'] = '操作失败！';
        }
        $this->ajaxReturn($data);
    }

    /**
     *活动车款
     */
    public function activity_car()
    {
        $this->act = M('activities')->where(array('isdelete' => 0))->select();
        $this->display();
    }

    /**
     *添加活动车款
     */
    public function activity_car_add()
    {
        $this->act = M('activities')->where(array('isdelete' => 0, 'status' => 2))->select();
        $this->brand = D('view_car_price')->where(array('isdelete' => 0, 'brand_name' => array('neq', '')))->field('brand_id,brand_name')->distinct('true')->select();
        if (!empty($_POST)) {
            if (!empty($_POST['exterior_color_id'])) {
                $data['exterior_color_id'] = I('exterior_color_id');
            }
            if (!empty($_POST['interior_color_id'])) {
                $data['interior_color_id'] = I('interior_color_id');
            }
            /* $data['is_xunjia']=2;*/
            $data['isdelete'] = 0;
            $info = D('view_car_price')->where($data)->order('low_price asc')->find();
            if ($info) {
                $dataadd['activity_id'] = I('activity_id');
                $dataadd['activity_number'] = M('activities')->where(array('id' => I('activity_id')))->getField('activity_number');
                $dataadd['buyer_count'] = I('buyer_count');
                $dataadd['type'] = I('type');
                $dataadd['car_id'] = I('car_id');
                $dataadd['car_price_id'] = $info['id'];
                $dataadd['group_id'] = I('group_id');
                $dataadd['group_name'] = I('group_name');
                $dataadd['sort'] = I('sort');
                $dataadd['createuser'] = session('admin_name');
                if (M('car_activities')->add($dataadd)) {
                    $this->success('添加成功！');
                } else {
                    $this->error('添加失败！');
                }

            } else {
                $this->error('该车款报价不存在，请重选车款');
            }

        }
        $this->display();
    }

    /**
     *修改活动车款
     */
    public function activity_car_save()
    {
        $this->act = M('activities')->where(array('isdelete' => 0))->select();
        $this->brand = D('view_car_price')->where(array('isdelete' => 0))->field('brand_id,brand_name')->distinct('true')->select();
        $info = M('car_activities')->where(array('id' => I('id')))->find();
        $car_price = D('view_car_price')->where(array('id' => $info['car_price_id']))->find();
        $this->assign('info', $info);
        $this->assign('car_price', $car_price);
        if (!empty($_POST)) {
            $data['exterior_color_id'] = I('exterior_color_id');
            $data['interior_color_id'] = I('interior_color_id');
            /* $data['is_xunjia']=2;*/
            $data['isdelete'] = 0;
            $info = D('view_car_price')->where($data)->order('low_price asc')->find();
            if ($info) {
                $dataadd['id'] = I('id');
                $dataadd['activity_id'] = I('activity_id');
                $dataadd['activity_number'] = M('activities')->where(array('id' => I('activity_id')))->getField('activity_number');
                $dataadd['buyer_count'] = I('buyer_count');
                $dataadd['type'] = I('type');
                $dataadd['car_id'] = I('car_id');
                $dataadd['car_price_id'] = $info['id'];
                $dataadd['group_id'] = I('group_id');
                $dataadd['group_name'] = I('group_name');
                $dataadd['sort'] = I('sort');
                $dataadd['createuser'] = session('admin_name');
                if (M('car_activities')->save($dataadd)) {
                    $this->success('修改成功！', U('activity_car'));
                } else {
                    $this->error('修改失败！');
                }

            } else {
                $this->error('该车款报价不存在，请重选车款');
            }
        }
        $this->display();
    }

    /**
     *删除活动车款
     */
    public function activity_car_delete()
    {
        $data['isdelete'] = 1;
        $data['updateuser'] = session('admin_name');
        $data['updatetime'] = date('Y-m-d H:i:s', time());
        $data['id'] = I('id');
        if (M('car_activities')->save($data)) {
            $data['status'] = 1;
            $data['msg'] = '删除成功！';
        } else {
            $data['status'] = 2;
            $data['msg'] = '删除失败！';
        }
        $this->ajaxReturn($data);
    }

    /**
     * @Description:
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/10/21 13:36
     * @Version 2.0
     */
    public function activity_car_open()
    {
        $data['status'] = I('status');
        $data['updateuser'] = session('admin_name');
        $data['updatetime'] = date('Y-m-d H:i:s', time());
        $data['id'] = I('id');
        if (M('car_activities')->save($data)) {
            $data['status'] = 1;
            $data['msg'] = '操作成功！';
        } else {
            $data['status'] = 2;
            $data['msg'] = '操作失败！';
        }
        $this->ajaxReturn($data);
    }

    /**
     * @Description:分享活动统计报表
     * @Return:
     * @Author: 孙磊
     * @Date: 2016/10/19 9:38
     * @Version 2.0
     */
    public function shareLogsList()
    {
        $this->display('sharelogslist');
    }

    /**
     * @Description:分享活动详情页
     * @Return:
     * @Author: 孙磊
     * @Date: 2016/10/20 9:48
     * @Version 2.0
     */
    public function shareLogsDetail()
    {
        $id = I('id');
        $find = M('share_logs')->where(array('id' => $id))->find();
        if ($find) {
            $find['image_url'] = OSS . str_replace("type", 'small', $find['image_url']);
            $this->assign('info', $find);
            $this->display('sharelogsdetail');
        } else {
            $this->error("请传递正确的数据！", U('shareLogsList'));
        }
    }

    /**
     * @Description:好友扫码详情
     * @Return:
     * @Author: 孙磊
     * @Date: 2016/10/20 15:33
     * @Version 2.0
     */
    public function friendDetail()
    {
        $id = I('id');
        $find = M('share_logs')->where(array('id' => $id))->find();
        if ($find) {
            //已经关注
            $attention = M('wx_scan_log')->where(array('myopenid' => $find['wx_open_id'], 'activity_name' => 'shaidan'))->select();
            if ($attention) {
                $this->assign('attention', $attention);
            }
            //已经注册
            $reg = M('wx_reg_log')->where(array('myopenid' => $find['wx_open_id'], 'activity_name' => 'shaidan_reg'))->select();
            if ($reg) {
                foreach ($reg as $k => $v) {
                    $reg[$k]['tel'] = M('user_general')->where(array('wx_open_id' => $v['fromopenid']))->getField('user_name');
                }
                $this->assign('reg', $reg);
            }
            //已经购车的
            $buy = M('wx_buy_log')->where(array('myopenid' => $find['wx_open_id'], 'activity_name' => 'shaidan_buy'))->select();
            if ($buy) {
                foreach ($buy as $k => $v) {
                    $buy[$k]['tel'] = M('user_general')->where(array('wx_open_id' => $v['fromopenid']))->getField('user_name');
                    $buy[$k]['carstyle'] = M('pay')->where(array('id' => $v['order_id']))->getField('carstyle');
                }
                $this->assign('buy', $buy);
            }
            $this->display('frienddetail');
        } else {
            $this->error("请传递正确的数据！", U('shareLogsList'));
        }
    }
}
