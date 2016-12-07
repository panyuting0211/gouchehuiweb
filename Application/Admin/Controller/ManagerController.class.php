<?php
namespace Admin\Controller;

use Think\Controller;

/**
 * Class ManagerController 用户管理控制器
 * @package Admin\Controller
 */

/**********************************************************角色管理*********************************************************/
class ManagerController extends CommonController
{
    /**
     *添加角色
     */
    public function addRole()
    {

        $this->display();
    }

    /**
     *添加角色处理
     */
    public function addRoleHandle()
    {

        if (I()) {
            $data['name'] = I('name');
            $data['remark'] = I('remark');
            $data['status'] = I('status');
            if (M('role')->add($data)) {
                $this->success('添加角色成功！', U('Manager/rolelist'));
            } else {
                $this->error('添加角色失败！');
            }
        }
    }

    /**
     *角色管理
     */
    public function roleList()
    {
        $this->display();
    }

    /**
     * ajax异步删除角色
     */
    public function member_delete_ajax()
    {
        $id = I('id');
        if (!empty($id)) {
            $res = M('role')->where(array('id' => $id))->delete();
            $this->ajaxReturn($res);
        }
    }

    /**
     *角色管理编辑
     */
    public function editRole()
    {

        $this->info = M('role')->where(array('id' => I('rid')))->find();
        $this->display();
    }

    /**
     *角色编辑处理
     */
    public function editRoleHandle()
    {

        if (I('rid', '', int)) {
            $data['name'] = I('name');
            $data['status'] = I('status');
            $data['remark'] = I('remark');
            if (M('role')->where(array('id' => I('rid')))->save($data)) {
                $this->success('修改成功', U('Manager/rolelist'));
            } else {
                $this->error('修改失败');
            }
        }

    }

    public function access()
    {
        $node = M('node')->order('sort')->select();
        foreach ($node as $key => $value) {
            $info = M('access')->where(array('role_id' => I('rid', '', 'int'), 'node_id' => $value['id']))->select();
            if ($info) {
                //有权限
                $node[$key]['access'] = 1;
            } else {
                //无权限
                $node[$key]['access'] = 0;
            }

        }
        $this->name = M('role')->getFieldById(I('rid', '', 'int'), 'name');
        $this->role_id = I('rid', '', 'int');
        $this->assign('node', $node);

        $this->display();
    }


    public function saveAccess()
    {
        $model = M('access');
        $role_id = I('role_id');
        $model->where(array('role_id' => $role_id))->delete();

        $array0[0]['role_id'] = $role_id;
        $array0[0]['node_id'] = 9;
        $array0[0]['level'] = 1;
        foreach ($_POST['node_id_2'] as $key => $value) {
            $array1[$key]['role_id'] = $role_id;
            $array1[$key]['node_id'] = $value;
            $array1[$key]['level'] = 2;
        }

        foreach ($_POST['node_id_3'] as $key => $value) {
            $array2[$key]['role_id'] = $role_id;
            $array2[$key]['node_id'] = $value;
            $array2[$key]['level'] = 3;
        }
        $array = array_merge($array0, $array1, $array2);
        $info = $model->addAll($array);
        if ($info) {
            $this->success('添加成功', U('Manager/rolelist'));
        } else {
            $this->error('添加失败');
        }
    }


    /****************************************************权限管理*************************************************************/

    /**
     *添加权限
     */
    public function addNode()
    {

        $arr['level'] = array('neq', '3');
        $this->node = M('node')->where($arr)->select();
        $this->display('addnode');
    }

    /**
     *添加权限表单处理
     */
    public function addNodeHandle()
    {

        $node = M('node');
        $node->create();
        $node->status = 1;
        $node->display = I('display');
        if ($node->add()) {
            $this->success('添加成功', U('Manager/nodelist'));
        } else {
            $this->error('添加失败');
        }
    }

    /**
     *权限管理
     */
    public function nodelist()
    {

        $node = M('node')->order('sort')->select();
        $count = M('node')->count();
        $this->node = A('Ajax')->Tree($node);
        $this->assign('count', $count);
        $this->display();
    }

    /**
     *删除权限
     */
    public function delNode()
    {
        if (I('id', '', int)) {
            if (M('node')->where(array('id' => I('id')))->delete()) {
                $this->success('删除成功', U('Manager/nodelist'));
            } else {
                $this->error('删除失败');
            }
        }
    }

    /**
     *编辑节点
     */
    public function editNode()
    {

        $this->info = M('node')->where(array('id' => I('id')))->find();
        $arr['level'] = array('neq', '3');
        $this->node = M('node')->where($arr)->select();
        $this->display();
    }

    /**
     *编辑节点处理
     */
    public function editNodeHandle()
    {
        if (I('id', '', int)) {
            $data['name'] = I('name');
            $data['title'] = I('title');
            $data['display'] = I('display');
            $data['level'] = I('level');
            $data['pid'] = I('pid');
            $data['remark'] = I('remark');
            $data['sort'] = I('sort');
            if (M('node')->where(array('id' => I('id')))->save($data)) {
                $this->success('修改成功', U('Manager/nodelist'));
            } else {
                $this->error('修改失败');
            }
        }

    }


    /********************************************************管理员管理***********************************************************/

    /**
     *管理员管理
     */
    public function managerList()
    {
        $this->display();
    }

    /**
     *管理员编辑
     */
    public function editManager()
    {
        $this->group = M('role')->select();
        $this->find = M('role_manager')->where(array('user_id' => I('id')))->find();
        $this->info = M('manager')->where(array('id' => I('id')))->find();
        $this->display();
    }

    /**
     *管理员编辑处理
     */
    public function editManagerHandle()
    {
        //判断传值的数据和原本的数据是否相同
        $id = I('id');
        $pwd = I('password');
        $repwd = I('repassword');
        $status = I('status');
        $name = I('name');
        $tel = I('tel');
        $role_id = I('role_id');

        if (strlen($tel) != 11) {
            $this->error('您输入是手机号不正确');
        }
        if ($pwd != $repwd) {//判断2此输入的密码
            $this->error('你两次密码输入不一致！');
        }//判断输入信息是否为空
        if (empty($pwd) || empty($name) || empty($role_id) || empty($tel)) {
            $this->error('输入信息不能为空！');
        }

        $info = M('manager')->where(array('id' => I('id')))->find();
        $info['role_id'] = M('role_manager')->where(array('user_id' => $info['id']))->getField('role_id');
        if ($info['pwd'] != $pwd) {
            $data['pwd'] = md5($pwd);
            $data['status'] = $status;
            $data['tel'] = $tel;
            $data['name'] = $name;
            M('manager')->where(array('id' => $id))->save($data) or $this->error('修改失败');
        } elseif ($info['status'] != $status || $info['name'] != $name) {//判断信息是否需要修改
            $data['pwd'] = $pwd;
            $data['status'] = $status;
            $data['name'] = $name;
            $data['tel'] = $tel;
            M('manager')->where(array('id' => $id))->save($data) or $this->error('修改失败2！');
        }

        if ($info['role_id'] != $role_id) {//判断角色权限信息是否需要修改
            M('role_manager')->where(array('user_id' => $id))->save(array('role_id' => $role_id)) or $this->error('修改失败3！');
        }
        $this->success('修改成功！');

    }

    /**
     *添加用户
     */
    public function addManager()
    {

        $this->group = M('role')->select();
        $this->display();
    }

    /**
     *添加用户表单处理
     */
    public function addManagerHandle()
    {
        if (!preg_match("/^([a-zA-Z0-9]|[._]){5,22}$/", I('admin_name'))) {
            $this->error('请输入6-22字母或数字');
        }
        if (M('manager')->where(array('admin_name' => I('admin_name')))->find()) {
            $this->error('用户名已存在，请重新输入');
        }
        if (strlen(I('password')) < 6) {
            $this->error('密码长度为不小于6位,请检查您的输入!');
        }
        if (I('password') != I('repassword')) {
            $this->error('两次输入的不相等,请检查您的输入!');
        }
        if (I('name') == '') {
            $this->error('请输入姓名！');
        }
        if (I('status') == '') {
            $this->error('请确定账号状态！');
        }
        if (strlen(I('tel')) != 11) {
            $this->error('您输入是手机号不正确');
        }
        // if(! preg_match ("/^[0-9a-zA-Z]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/i",I('email'))){
        // 	$this->error ( '请输入正确的邮箱!' );
        // }
        $data['admin_name'] = I('admin_name');
        $data['pwd'] = md5(I('password'));
        $data['name'] = I('name');
        $data['tel'] = I('tel');
        // $data['email'] = I('email');
        $data['status'] = I('status');
        $data['createtime'] = time();
        $data['last_login_time'] = time();
        $data['last_login_ip'] = get_client_ip();
        // $data['status'] = 1;
        $uid = M('manager')->add($data);
        if ($uid) {
            $arr['role_id'] = I('role_id');
            $arr['user_id'] = $uid;
            if (M('role_manager')->add($arr)) {
                $this->success('添加成功', U('Manager/managerlist'));
            } else {
                $this->error('添加失败');
            }
        } else {
            $this->error('系统出错,请稍后重试');
        }
    }

    /**
     * ajax异步删除管理员
     */
    public function manager_delete_ajax()
    {
        $id = I('id');
        if (!empty($id)) {
            $res = M('manager')->where(array('id' => $id))->delete();
            $this->ajaxReturn($res);
        }
    }


}