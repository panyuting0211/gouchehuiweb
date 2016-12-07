<?php
namespace Admin\Controller;

use Think\Controller;
use Boris\ExportInspector;

/**
 * Class AdversController 广告管理控制器
 * @package Admin\Controller
 */
class TicketController extends CommonController
{
    /**
     *优惠券配置
     */
    public function ticket()
    {
        $this->display();
    }

    /**
     *添加优惠券配置
     */
    public function ticketadd()
    {
        $ticket = M('ticket');
        if (!empty($_POST)) {
            $ticket->create();
            if ($ticket->add()) {
                $this->success('添加成功！');
            } else {
                $this->error('添加失败！');
            }

        }
        $this->display();
    }

    /**
     *激活优惠券
     */
    public function ticketrelease()
    {
        $data['id'] = I('id');
        $data['release_status'] = 2;
        $data['updatetime'] = date('Y-m-d H:i:s', time());
        $data['updateuser'] = session('admin_name');
        if (M('ticket')->save($data)) {
            $tick = M('ticket')->where(array('id' => I('id')))->find();
            if ($tick['amount'] > 0) {
                for ($x = 0; $x < $tick['amount']; $x++) {
                    $datatick[$x]['ticket_id'] = $tick['id'];
                    $datatick[$x]['ticket_number'] = $tick['ticket_prefix'] . date('Ymd', time()) . sprintf("%0" . strlen(strval($tick['amount'])) . "s", strval($x));
                    $datatick[$x]['ticket_name'] = $tick['ticket_name'];
                    $datatick[$x]['money'] = $tick['money'];
                    $datatick[$x]['validity'] = $tick['validity'];
                    $datatick[$x]['remark'] = $tick['remark'];
                }
                M('ticket_pool')->addAll($datatick);
            } elseif ($tick['amount'] == 0) {
                $db = M('');
                $info = $db->query("call ticket_pool(" . $tick['id'] . ")");
            }
            $info['status'] = 1;
            $info['msg'] = '激活成功!';
        } else {
            $info['status'] = 2;
            $info['msg'] = '激活失败!';
        }
        $this->ajaxReturn($info);
    }

    /**
     *删除优惠券
     */
    public function ticketdelete()
    {
        $data['id'] = I('id');
        $data['isdelete'] = 1;
        $data['updatetime'] = date('Y-m-d H:i:s', time());
        $data['updateuser'] = session('admin_name');
        if (M('ticket')->save($data)) {
            $info['status'] = 1;
            $info['msg'] = '删除成功!';
        } else {
            $info['status'] = 2;
            $info['msg'] = '删除失败!';
        }
        $this->ajaxReturn($info);
    }

    /**
     *优惠券使用情况
     */
    public function ticket_user()
    {
        $this->display();
    }

    /**
     *优惠券使用情况
     */
    public function ticket_user_details()
    {
        $id = I('id');
        $array1 = M('ticket_user')->where(array('id' => $id))->find();
        $array2 = M('user_general')->where(array('id' => $array1['user_id']))->find();
        $array3 = M('pay')->where(array('id' => $array1['pay_id']))->find();
        $this->assign('array1', $array1);
        $this->assign('array2', $array2);
        $this->assign('array3', $array3);
        $this->display();
    }


}
