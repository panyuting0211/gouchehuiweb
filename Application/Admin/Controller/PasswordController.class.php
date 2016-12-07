<?php
namespace Admin\Controller;

use Think\Controller;
use Boris\ExportInspector;

/**
 * Class PasswordController 修改密码管理控制器
 * @package Admin\Controller
 */
class PasswordController extends CommonController
{
    /**
     *修改密码
     */
    public function editPassWord()
    {
        $this->display();
    }

    /**
     *修改密码表单处理
     */
    public function editPassWordHandle()
    {
        if ($_POST) {
            //根据sessionID找到管理员
            $res = M('manager')->where(array('id' => $_SESSION['admin_id']))->find();
            //判断输入密码和数据库的密码是否相同
            if ($res['pwd'] != md5($_POST['oldpassword'])) {
                $this->error('您输入的旧密码有误，请重新输入');
            } else {
                //判断输入的新密码和确认新密码是否相同
                if ($_POST['newpassword'] != $_POST['repassword']) {
                    $this->error('新密码和确认新密码不同，请重新输入');
                } else {
                    //保存密码
                    $data['pwd'] = md5($_POST['newpassword']);
                    $save = M('manager')->where(array('id' => $_SESSION['admin_id']))->save($data);
                    if ($save) {
                        $this->success('修改成功');
                    } else {
                        $this->error('密码修改失败，请稍后重试');
                    }
                }
            }
        }
    }

}
