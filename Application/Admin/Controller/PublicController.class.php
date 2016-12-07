<?php
namespace Admin\Controller;

use Think\Controller;
use Think\Verify;

/**
 * Class PublicController 公共控制器
 * @package Admin\Controller
 */
class PublicController extends Controller
{
    /**
     *验证session是否存在
     */
    public function base()
    {
        $this->display();

    }

    /**
     *登录
     */
    public function login()
    {
        $this->display();
    }

    /**
     *登录表单处理
     */
    public function login_handle()
    {
        if ($_POST['submit']) {
            $verify = new \Think\Verify();
            if (!$verify->check(I('captcha'))) {
                $this->error("验证码错误");
            }

            $res = M('manager')->where(array('admin_name' => I('admin_name'), 'pwd' => md5(I('pwd'))))->find();
            if ($res) {
                //判断用户是否被禁用
                if ($res['status'] == 0) {
                    $this->error('该账户已被禁用');
                }
                //登录成功获取数据
                $rid = M('role_manager')->where(array('user_id' => $res['id']))->getField('role_id');
                $role_name = M('role')->where(array('id' => $rid))->getField('name');
                session('admin_id', $res['id']);
                session('admin_name', I('admin_name'));
                session(C('USER_AUTH_KEY'), $res['id']);
                session('role_name', $role_name);
                session('last_login_ip', $res['last_login_ip']);
                session('last_login_time', $res['last_login_time']);
                //更新数据
                $data['last_login_ip'] = $_SERVER['HTTP_X_REAL_IP'];
                $data['last_login_time'] = time();
                M('manager')->where(array('id' => $res['id']))->save($data);
                //判断是否是admin账号登录如果是不需要RBAC验证
                if ($_SESSION['admin_name'] == C('RBAC_SUPERADMIN')) {
                    session(C('ADMIN_AUTH_KEY'), true);
                }
                $rbac = new \Org\Util\Rbac();
                $rbac::saveAccessList();
                $this->success('登录成功!', U('Index/index'));
            } else {
                $resu = M('manager')->where(array('admin_name' => I('admin_name')))->find();
                if ($resu) {
                    $this->error('密码错误');
                } else {
                    $this->error('账号不存在');
                }
            }
        }
    }

    /**
     *ajax登录异步判断
     */
    public function ajax_login()
    {
        $user_name = I('user_name');
        $password = I('password');
        $captcha = I('captcha');

        if (empty($user_name)) {
            $this->ajaxReturn('用户名不能为空');
        } elseif (empty($password)) {
            $this->ajaxReturn('密码不能为空');
        } elseif (empty($captcha)) {
            $this->ajaxReturn('验证码不能为空');
        }
        $verify = new \Think\Verify();
        if (!$verify->check($captcha)) {
            $this->ajaxReturn("验证码错误");
        }
        $res = M('manager')->where(array('admin_name' => $user_name, 'pwd' => md5($password)))->find();
        if ($res) {
            //判断用户是否被禁用
            if ($res['status'] == 0) {
                $this->ajaxReturn('该账户已被禁用');
            }
            //登录成功获取数据
            $rid = M('role_manager')->where(array('user_id' => $res['id']))->getField('role_id');
            $role_name = M('role')->where(array('id' => $rid))->getField('name');
            session('admin_id', $res['id']);
            session('admin_name', $user_name);
            session(C('USER_AUTH_KEY'), $res['id']);
            session('role_name', $role_name);
            session('last_login_ip', $res['last_login_ip']);
            session('last_login_time', $res['last_login_time']);
            //更新数据
            $data['last_login_ip'] = get_client_ip();
            $data['last_login_time'] = time();
            M('manager')->where(array('id' => $res['id']))->save($data);
            //判断是否是admin账号登录如果是不需要RBAC验证
            if ($_SESSION['admin_name'] == C('RBAC_SUPERADMIN')) {
                session(C('ADMIN_AUTH_KEY'), true);
            }
            $rbac = new \Org\Util\Rbac();
            $rbac::saveAccessList();
            $this->ajaxReturn(1);
        } else {
            $resu = M('manager')->where(array('admin_name' => $user_name))->find();
            if ($resu) {
                $this->ajaxReturn('密码错误');
            } else {
                $this->ajaxReturn('账号不存在');
            }
        }
    }

    /**
     *退出系统
     */
    public function logout()
    {
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_name']);
        unset($_SESSION['authId']);
        unset($_SESSION['role_name']);
        unset($_SESSION['last_login_ip']);
        unset($_SESSION['last_login_time']);
        unset($_SESSION['superadmin']);
        $this->success('退出成功', U('Public/login'));
    }

    /**
     *生成验证码
     */
    function verifyImg()
    {
        import('ORG.Util.Image');
        ob_end_clean();
        $config = array(
            'imageH' => 78,               // 验证码图片高度
            'imageW' => 450,               // 验证码图片宽度
            'length' => 5,               // 验证码位数
            'fontttf' => '4.ttf',              // 验证码字体，不设置随机获取
            'fontSize' => 43
        );
        $verify = new \Think\Verify($config);
        $verify->entry();
    }

}