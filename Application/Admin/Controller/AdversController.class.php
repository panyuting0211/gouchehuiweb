<?php
namespace Admin\Controller;

use Think\Controller;
use Boris\ExportInspector;

/**
 * Class AdversController 广告管理控制器
 * @package Admin\Controller
 */
class AdversController extends CommonController
{

    public function edit_name()
    {
        $list = M('node')->select();
        foreach ($list as $key => $value) {
            M('node')->where(array('id' => $value['id']))->save(array('name' => strtolower($value['name'])));
        }
        print_r($list);
    }
    /**********************************************************网站首页***********************************************/

    /**
     *网站首页广告
     */
    public function Adver()
    {
        $advers = M("ad");
        $data['type'] = 0;
        $data['isdelete'] = 0;
        $count = $advers->where($data)->count();
        $page = new \Think\Page($count, 15);//实例化分页类传入总记录数和每页显示的记录数
        $pagelist = $page->show();//分页显示输出
        $info = $advers->where($data)->order('status desc,alif asc,createtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($info as $key => $value) {
            $info[$key]['imgurl'] = str_replace('type', 'big', $info[$key]['imgurl']);
        }
        $this->assign('count', $count);
        $this->assign('info', $info);
        $this->assign('pagelist', $pagelist);
        $this->display('adver');
    }

    /**
     *添加广告页面
     */
    public function adveradd()
    {
        $this->display();
    }

    /**
     *添加网站广告表单处理
     */
    public function adveraddHandle()
    {
        //判断是否有数据提价
        if (!empty($_FILES)) {
            $z = upload_local();
            if ($z) {
                $data['imgurl'] = str_replace('big', 'type', $z['imgurl']['savepath'] . $z['imgurl']['savename']);
            } else {
                $this->error('请选择图片');
            }
        }
        if ($_POST) {
            $data['id'] = md5(microtime());
            $data['ad_name'] = I('ad_name');//广告标题
            $data['status'] = 1;//状态
            $data['createuser'] = $_SESSION['admin_name'];//创建人
            $data['type'] = 0;//网站广告
            $data['adurl'] = I('adurl');
            $data['alif'] = I('alif');
            //插入数据库
            $res = M('ad')->add($data);
            //判断是否添加成功
            if ($res) {
                $this->success('添加成功', U('Advers/Adver'));
            } else {
                $this->error('添加失败');
            }

        }
    }

    /**
     *修改广告信息
     */
    public function edit_ad()
    {
        $id = I('id');
        if (!empty($id)) {
            $info = M('ad')->where(array('id' => $id))->find();
            $info['imgurl'] = str_replace('type', 'small', $info['imgurl']);
            $this->assign('info', $info);
        } else {
            $this->error('非法操控！');
        }

        $this->display();
    }

    /**
     *编辑广告表单处理
     */
    public function edit_ad_haddle()
    {
        $id = I('id');
        if (!empty($_FILES)) {
            $z = upload_local();
            if ($z) {
                $data['imgurl'] = str_replace('big', 'type', $z['imgurl']['savepath'] . $z['imgurl']['savename']);
            }
        }
        if ($_POST) {
            $data['ad_name'] = I('ad_name');//广告标题
            $data['adurl'] = I('adurl');
            $data['alif'] = I('alif');
            $data['updatetime'] = date('Y-m-d H:i:s', time());
            $data['updateuser'] = session('admin_name');
            //插入数据库
            $res = M('ad')->where(array('id' => $id))->save($data);

            //判断是否添加成功
            if ($res) {
                $this->success('添加成功', U('Advers/Adver'));
            } else {
                $this->error('添加失败');
            }

        }
    }

    /**********************************************************APP首页***********************************************/

    /**
     *APP广告列表
     */
    public function AppAd()
    {
        $advers = M("ad");
        $data['type'] = 1;
        $data['isdelete'] = 0;
        $count = $advers->where($data)->count();
        $page = new \Think\Page($count, 15);//实例化分页类传入总记录数和每页显示的记录数
        $pagelist = $page->show();//分页显示输出
        $info = $advers->where($data)->order('alif,createtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($info as $key => $value) {
            $info[$key]['imgurl'] = str_replace('type', 'big', $info[$key]['imgurl']);
        }
        $this->assign('info', $info);
        $this->assign('pagelist', $pagelist);
        $this->assign('count', $count);
        $this->display();
    }

    /**
     *添加APP广告页面
     */
    public function AppAdAdd()
    {
        $this->display();
    }

    /**
     * 添加APP广告表单处理-sun
     */
    public function AppAdAddHandle()
    {
        //判断是否有数据提价
        if (!empty($_FILES)) {
            $z = upload_local();
            if ($z) {
                $data['imgurl'] = str_replace('big', 'type', $z['imgurl']['savepath'] . $z['imgurl']['savename']);
            } else {
                show_bug('文件上传失败！');
            }

        }

        if ($_POST) {
            $data['id'] = md5(microtime());
            $data['ad_name'] = I('ad_name');//广告标题
            $data['status'] = 1;//状态
            $data['createuser'] = $_SESSION['admin_name'];//创建人
            $data['adurl'] = I('adurl');
            $data['type'] = 1;//app广告
            $data['alif'] = I('alif');
            //插入数据库
            $res = M('ad')->add($data);
            //判断是否添加成功
            if ($res) {
                $this->success('添加成功', U('Advers/AppAd'));
            } else {
                $this->error('添加失败');
            }

        }
    }

    /**
     *删除广告
     */
    public function DelSelect()
    {
        $advers = M("ad");
        $id = I('id');
        $ids = implode(',', $id);//批量获取id
        $id = is_array($id) ? $ids : $id;//判断是否为数组
        $map['id'] = array('in', $id);

        $data['updatetime'] = date('Y-m-d H:i:s', time());
        $data['updateuser'] = session('admin_name');
        $data['isdelete'] = 1;
        if ($advers->where($map)->save($data)) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败，请选择需要删除的数据', U('Adver'));
        }
    }

    /**
     *ajax设置首页图片是否展示
     */
    public function ajax_ad()
    {
        if (I('ad_id1')) {
            $saveinfo['status'] = 1;
            $saveinfo['id'] = I('ad_id1');
            $saveinfo['updatetime'] = date('Y-m-d H:i:s', time());
            $saveinfo['updateuser'] = session('admin_name');
            $info = M('ad')->save($saveinfo);
        }
        if (I('ad_id0')) {
            $saveinfo['status'] = 0;
            $saveinfo['id'] = I('ad_id0');
            $saveinfo['updatetime'] = date('Y-m-d H:i:s', time());
            $saveinfo['updateuser'] = session('admin_name');
            $info = M('ad')->save($saveinfo);
        }
        $this->ajaxReturn($info);
    }

    /**********************************************************移动web首页***********************************************/

    /**
     * @Description:移动web站首页
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/11/1 11:28
     * @Version 2.0
     */
    public function mobileweb()
    {
        $advers = M("ad");
        $data['type'] = 2;
        $data['isdelete'] = 0;
        $count = $advers->where($data)->count();
        $page = new \Think\Page($count, 15);//实例化分页类传入总记录数和每页显示的记录数
        $pagelist = $page->show();//分页显示输出
        $info = $advers->where($data)->order('status desc,alif asc,createtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($info as $key => $value) {
            $info[$key]['imgurl'] = str_replace('type', 'big', $info[$key]['imgurl']);
        }
        $this->assign('count', $count);
        $this->assign('info', $info);
        $this->assign('pagelist', $pagelist);

        $this->render();
    }


    /**
     * @Description:添加移动web首页
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/11/1 11:28
     * @Version 2.0
     */
    public function mobilewebadd()
    {
        //判断是否有数据提价
        if (!empty($_FILES)) {
            $z = upload_local();
            if ($z) {
                $data['imgurl'] = str_replace('big', 'type', $z['imgurl']['savepath'] . $z['imgurl']['savename']);
            } else {
                $this->error('请选择图片！');
            }
        }
        if ($_POST) {
            $data['id'] = md5(microtime());
            $data['ad_name'] = I('ad_name');//广告标题
            $data['status'] = 1;//状态
            $data['createuser'] = $_SESSION['admin_name'];//创建人
            $data['type'] = 2;//网站广告
            $data['adurl'] = I('adurl');
            $data['alif'] = I('alif');
            //插入数据库
            $res = M('ad')->add($data);
            //判断是否添加成功
            if ($res) {
                $this->success('添加成功');
            } else {
                $this->error('添加失败');
            }

        }
        $this->render();
    }

    /**********************************************************短信发送***********************************************/

    public function message()
    {
        $this->display();
    }

    /**
     *批量发送短信接口
     */
    public function note()
    {
        $mobile = $_POST['tel'];//接收短信的手机号码
        if (!empty($mobile)) {
            foreach ($mobile as $key => $value) {
                $key = 'a69a5764f670ccd1f473f21e1f4dad9c';//应用APPKEY
                $tpl_id = '22911';//短信模板ID
                $url = 'http://v.juhe.cn/sms/send?mobile=' . $value . '&tpl_id=' . $tpl_id . '&key=' . $key;
                $ress = file_get_contents($url);
                $reslut = json_decode($ress, true);
                /*error_code==0 发送成功*/
                if ($reslut['error_code'] == 0) {
                    $data['status'] = $reslut['error_code'];
                    $data['msg'] = $reslut['reason'];
                } else {
                    $info[] = $value;
                    $data['tel'] = $info;
                    $data['status'] = 1;
                }
            }

        } else {
            $data['status'] = 1;
            $data['msg'] = '手机号不能为空';

        }
        $this->ajaxReturn($data);
    }

}
