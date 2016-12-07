<?php
namespace Admin\Controller;

use Think\Controller;
use Boris\ExportInspector;

/**
 * Class MessageController 互动管理控制器
 * @package Admin\Controller
 */
class MessageController extends CommonController
{

    /****************************************问题咨询*************************************************/
    /**
     *问题咨询列表展示
     */
    public function Messagelist()
    {
        //获得所有品牌
        $brand_info = M('car_question')->field('car_brand_id')->distinct(true)->select();
        foreach ($brand_info as $key => $value) {
            $brand_info[$key]['brand_name'] = M('brand')->where(array('id' => $value['car_brand_id']))->getField('brand_name');
        }
        $this->assign('brand', $brand_info);
        $this->display();
    }

    /**
     * ajax异步删除留言
     */
    public function message_delete_ajax()
    {
        $id = I('id');
        if (!empty($id)) {
            $data['id'] = $id;
            $data['updatetime'] = date('Y-m-d H:i:s', time());
            $data['updateuser'] = session('admin_name');
            $data['isdelete'] = 1;
            $info = M('car_question')->save($data);
            $this->ajaxReturn($info);
        }

    }

    /**
     * 查看详情
     */
    public function MessageDet()
    {
        //获取指定id
        $id = $_GET['id'];
        $det = M("car_question");
        $info = $det->where(array('id' => $id))->select();
        //根据问题中的汽车ID获得品牌，车型名称
        foreach ($info as $k => $v) {
            $model = M('car_model')->where(array('id' => $v['car_model_id']))->find();
            $info[$k]['model_name'] = $model['car_model_name'];
            $info[$k]['brand_name'] = M('brand')->where(array('id' => $model['brand_id']))->getField('brand_name');
        }
        $this->assign('info', $info[0]);

        $this->display();
    }

    /**
     * 留言详情编辑保存
     */
    public function editMessage()
    {
        $car_question = M('car_question');
        $car_question->create();
        $car_question->updatetime = date('Y-m-d H:i:s', time());
        $car_question->updateuser = session('admin_name');
        $zz = $car_question->save();
        if ($zz) {
            $this->success('保存成功!');
        } else {
            $this->error('保存失败！');
        }
    }

    /**************************************在线反馈*****************************************************/
    /**
     * 在线反馈列表
     */
    public function online_feedback()
    {
        $question = M("online_feedback");
        $count = $question->count();
        $page = new \Think\Page($count, 15);//实例化分页类传入总记录数和每页显示的记录数
        $pagelist = $page->show();//分页显示输出
        $info = $question->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign('pagelist', $pagelist);
        $this->assign('info', $info);
        $this->assign('count', $count);
        $this->display();

    }

    /**
     * 在线反馈删除
     */
    public function DelSelectcon()
    {
        $question = M("online_feedback");
        $id = I('id');
        $ids = implode(',', $id);//批量获取id
        $id = is_array($id) ? $ids : $id;//判断是否为数组
        $map['id'] = array('in', $id);
        $data['updatetime'] = date('Y-m-d H:i:s', time());
        $data['updateuser'] = session('admin_name');
        $data['isdelete'] = 1;
        if ($question->where($map)->save($data)) {
            $this->success('删除成功', U('Message/online_feedback'));
        } else {
            $this->error('删除失败');
        }
    }

    /****************************************App互动*****************************************************/

    /**
     * APP互动管理列表
     */
    public function appMessage()
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
        $this->assign('username', !empty($username) ? $username : null);
        $this->assign('endtime', !empty($endtime) ? $endtime : null);
        $this->assign('starttime', !empty($starttime) ? $starttime : null);
        $this->display();
    }

    /**
     * 删除APP互动删除 暂时不用
     */
    public function appDel()
    {
        $model = M("interaction");
        $id = I('id');
        $ids = implode(',', $id);//批量获取id
        $id = is_array($id) ? $ids : $id;//判断是否为数组
        $map['id'] = array('in', $id);

        $data['updatetime'] = date('Y-m-d H:i:s', time());
        $data['updateuser'] = session('admin_name');
        $data['isdelete'] = 1;
        if ($model->where($map)->save($data)) {
            $this->success('删除成功', U('Message/appMessage'));
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 删除APP互动删除
     */
    public function app_delete_ajax()
    {
        $id = I('id');
        if (!empty($id)) {
            $data['id'] = $id;
            $data['updatetime'] = date('Y-m-d H:i:s', time());
            $data['updateuser'] = session('admin_name');
            $data['isdelete'] = 1;
            $info = M("interaction")->save($data);
            $this->ajaxReturn($info);
        }

    }

    /**
     * APP回复列表
     */
    public function appReply()
    {
        $model = M('interaction_result');

        $count = $model->where(array('interaction_id' => I('id'), 'isdelete' => 0))->count();//得到总数
        $page = new \Think\Page($count, 15);//实例化分页类传入总记录数和每页显示的记录数
        $pagelist = $page->show();//分页显示输出

        $info = $model->where(array('interaction_id' => I('id'), 'isdelete' => 0))->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($info as $k => $v) {
            $info[$k]['username'] = M('user')->where(array('id' => $v['from_user']))->getField('user_name');
        }
        $this->assign('pagelist', $pagelist);
        $this->assign('info', $info);
        $this->display();
    }

    /**
     * APP回复删除
     */
    public function appReplyDel()
    {
        $model = M("interaction_result");
        $id = I('id');
        $ids = implode(',', $id);//批量获取id
        $id = is_array($id) ? $ids : $id;//判断是否为数组
        $map['id'] = array('in', $id);

        $data['updatetime'] = date('Y-m-d H:i:s', time());
        $data['updateuser'] = session('admin_name');
        $data['isdelete'] = 1;
        if ($model->where($map)->save($data)) {
            $this->success('删除成功', U('Message/appReply'));
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 活动记录
     */
    public function Interactivity()
    {
        $this->display();
    }

    /****************************************用户喜欢*****************************************************/
    public function userlove()
    {
        //获得开始和结束时间
        $time = date('Y-m-d');
        $endtime = strtotime($time . '23:59:59');
        $starttime = $endtime - 3600 * 24 * 31 + 1; // 默认查询一个月内下订单信息
        $this->assign('endtime', !empty($endtime) ? $endtime : null);
        $this->assign('starttime', !empty($starttime) ? $starttime : null);
        $from_plan = M('car_plan')->distinct('true')->field('from_plan')->select();
        $this->assign('from_plan', $from_plan);
        $this->display();
    }

    /****************************************报名用户*****************************************************/
    public function user_activity()
    {
        //获得开始和结束时间
        $time = date('Y-m-d');
        $endtime = strtotime($time . '23:59:59');
        $starttime = $endtime - 3600 * 24 * 31 + 1; // 默认查询一个月内下订单信息
        $this->assign('endtime', !empty($endtime) ? $endtime : null);
        $this->assign('starttime', !empty($starttime) ? $starttime : null);
        $activity = M('user_activity')->distinct('true')->field('activity_number,activity_name')->select();
        $this->assign('activity', $activity);
        $this->display();
    }

    /**
     *客服操作报名用户状态
     */
    public function cus_action()
    {
        $user = M('user_activity');
        $data['id'] = I('id');
        $data['updatetime'] = time();
        $data['updateuser'] = session('admin_name');
        $data['cus_name'] = I('cus_name');
        $data['cus_character'] = I('cus_character');
        $data['status_track'] = 2;
        $user->create($data);
        $user->save($data);

        $datac['id'] = md5(microtime());
        $datac['user_activity_id'] = I('id');
        $datac['cus_name'] = I('cus_name');
        $datac['cus_remark'] = I('cus_remark');
        $datac['cus_character'] = I('cus_character');
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
     *客服跟踪报名用户状态-新增
     */
    public function cus_action_add()
    {
        $datac['id'] = md5(microtime());
        $datac['user_activity_id'] = I('id');
        $datac['cus_name'] = I('cus_name');
        $datac['cus_remark'] = I('cus_remark');
        $datac['cus_character'] = I('cus_character');
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

}