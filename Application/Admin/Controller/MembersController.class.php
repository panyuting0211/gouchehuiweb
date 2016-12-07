<?php
namespace Admin\Controller;

use Think\Controller;
use Boris\ExportInspector;

/**
 * Class MembersController 会员管理控制器
 * @package Admin\Controller
 */
class MembersController extends CommonController
{
    /*************************************************************普通会员操作*************************************************************/
    /**
     *普通会员展示
     */
    public function member()
    {
        $this->display();
    }

    /**
     * 会员详情
     */
    public function memberdetails()
    {
        $user = M('user_general');
        $data['id'] = I('id');
        $info = $user->where($data)->find();
        if (!empty($info['head_url'])) {
            $info['head_url'] = OSS . str_replace('type', 'small', $info['head_url']);
        } else {
            $info['head_url'] = '/Public/Home/images/headimg.png';
        }
        $this->assign('info', $info);
        $this->display();
    }

    /**
     * ajax异步删除普通会员
     */
    public function user_general_delete_ajax()
    {
        $id = I('id');
        if (!empty($id)) {
            $data['isdelete'] = 1;
            $data['updatetime'] = date('Y-m-d H:i:s', time());
            $data['updateuser'] = session('admin_name');
            $res = M('user_general')->where(array('id' => $id))->save($data);
            $this->ajaxReturn($res);
        }
    }

    /***********************************************************4s店会员操作**************************************************************************/
    /**
     *4s店会员展示
     */
    public function shopUser()
    {
        $page = I('p');
        if (empty($page)) {
            $page = 1;
        }
        $this->assign('page', $page);
        $this->display();
    }

    /**
     *添加4S店会员
     */
    public function shopUserAdd()
    {

        $province = M('city')->where(array('isdelete' => 0))->distinct(true)->field('province_id')->select();//查询所有省份
        foreach ($province as $key => $value) {
            $province[$key]['province_name'] = M('province')->where(array('isdelete' => 0, 'id' => $value['province_id']))->getField('province_name');
        }
        $this->assign('province', $province);

        $brand = M('brand')->where(array('isdelete' => 0))->order('alif')->select();//查询所有品牌
        foreach ($brand as $key => $value) {
            $brand[$key]['brand_name'] = $value['alif'] . $value['brand_name'];
        }
        $this->assign('brand', $brand);
        $this->display();
    }

    /**
     *添加4S店会员表单处理
     */
    public function shopAddHandle()
    {
        //判断是否选择公司名称
        if (I('name_4s') == '') {
            $this->error('请选择公司名称');
        }
        //判断是否输入地址
        if (I('city_id') == "") {
            $this->error('请选择城市');
        }
        //判断是否输入地址
        if (I('addr') == "") {
            $this->error('请输入地址');
        }
        //判断是否输入联系人
        if (I('contacts') == "") {
            $this->error('请填写联系人');
        }
        //判断是否输入联系电话
        if (I('tel') == "") {
            $this->error('请输入联系电话');
        }
        //判断是否选择主营品牌
        if (I('brand_4s') == "") {
            $this->error('请选择主营品牌');
        }
        //判断账号是否是6-22位
        if (!preg_match("/^([a-zA-Z0-9]|[._]){5,22}$/", I('username'))) {
            $this->error('请输入6-22字母或数字的账号');
        }
        //判断用户是否存在
        if (M('user_4s')->where(array('user_name' => I('username'), 'isdelete' => 0))->find()) {
            $this->error('用户名已存在，请重新输入');
        }
        //判断木木是否小于六位数
        if (strlen(I('password')) < 6) {
            $this->error('密码长度不能小于6位,请检查您的输入!');
        }

        $member = M("user_4s");
        $data['name_4s'] = I('name_4s');
        $data['city_id'] = I('city_id');
        $data['addr'] = I('addr');
        $data['contacts'] = I('contacts');
        $data['tel'] = I('tel');
        $data['brand_4s'] = I('brand_4s');
        $data['id'] = md5(microtime());
        $data['user_name'] = I('username');
        $data['password'] = MD5(I('password'));
        $data['status'] = I('status');
        $data['role'] = 2;
        $data['createuser'] = session('admin_name');
        $data['logintime'] = time();
        $data['loginip'] = $_SERVER['HTTP_X_REAL_IP'];
        $data['my_num'] = randomkeys();
        if (!empty($_POST)) {
            $member->create($data);
            $info = $member->add();
            if ($info) {
                $this->success('添加成功', U('Members/shopUser'));
            } else {
                $this->error('添加失败');
            }
        }
    }

    /**
     *编辑4S店会员
     */
    public function editShopUser()
    {

        $this->province = M('province')->where(array('isdelete' => 0))->select();//查询所有省份
        $this->page = I('p');
        $brand = M('brand')->where(array('isdelete' => 0))->order('alif')->select();//查询所有品牌
        foreach ($brand as $key => $value) {
            $brand[$key]['brand_name'] = $value['alif'] . $value['brand_name'];
        }
        $this->assign('brand', $brand);

        //根据ID查到用户信息
        $member = M("user_4s");
        $info = $member->where(array('id' => I('id')))->find();
        $city = M('city')->where(array('id' => $info['city_id']))->find();
        $province = M('province')->where(array('id' => $city['province_id']))->find();
        $this->city_all = M('city')->where(array('province_id' => $province['id']))->select();
        $info['city_name'] = $city['city_name'];
        $info['province_id'] = $province['id'];

        //查找改4S店副营品牌
        $brand4S = M('view_brand_user')->where(array('user_4s_id' => I('id'), 'isdelete' => 0))->select();
        if ($brand4S) {
            $this->assign('brand4S', $brand4S);
        }

        $this->assign('info', $info);
        $this->display('editshopuser');
    }

    /**
     *编辑4S表单处理
     */
    public function editShopUserHandle()
    {
        //判断是否选择公司名称
        if (I('name_4s') == '') {
            $this->error('请选择公司名称');
        }
        //判断是否输入地址
        if (I('city_id') == "") {
            $this->error('请选择城市');
        }
        //判断是否输入地址
        if (I('addr') == "") {
            $this->error('请输入地址');
        }
        //判断是否输入联系人
        if (I('contacts') == "") {
            $this->error('请填写联系人');
        }
        //判断是否输入联系电话
        if (I('tel') == "") {
            $this->error('请输入联系电话');
        }
        //判断是否选择主营品牌
        if (I('brand_4s') == "") {
            $this->error('请选择主营品牌');
        }
        //判断是否输入密码
        if (strlen(I('password')) < 6) {
            $this->error('密码长度为不小于6位,请检查您的输入!');
        }

        $member = M("user_4s");
        $id = I('id');
        $password = I('password');
        $res = $member->where(array('id' => $id))->find();
        if ($res['password'] != $password) {
            $data['password'] = md5($password);
        }
        $data['name_4s'] = I('name_4s');
        $data['city_id'] = I('city_id');
        $data['addr'] = I('addr');
        $data['contacts'] = I('contacts');
        $data['tel'] = I('tel');
        $data['brand_4s'] = I('brand_4s');
        $data['status'] = I('status');
        $data['updatetime'] = date("Y-m-d H:i:s", time());
        $data['updateuser'] = session('admin_name');
        $page = I('page');
        $zz = $member->where(array('id' => $id))->save($data);
        if ($zz) {
            $this->success('修改成功', U('Members/shopUser', array('page' => $page)));
        } else {
            $this->error('修改失败');
        }
    }

    /**
     * ajax异步删除4s会员
     */
    public function user_4s_delete_ajax()
    {
        $id = I('id');
        if (!empty($id)) {
            $data['isdelete'] = 1;
            $data['updatetime'] = date("Y-m-d H:i:s", time());
            $data['updateuser'] = session('admin_name');
            $res = M('user_4s')->where(array('id' => $id))->save($data);
            $this->ajaxReturn($res);
        }
    }

    /**
     * 报价信息
     */
    public function Offer()
    {
        $user_id = I('id');
        $model_id = I('model_id');
        $car_id = I('car_id');
        $exterior_color_name = I('exterior');
        $interior_color_name = I('interior');
        $page = I('p');
        $this->assign('page', $page);
        if (!empty($user_id)) {
            $data['user_id'] = $user_id;
        } else {
            $this->error('参数错误！');
        }
        if (!empty($model_id)) {
            $data['car_model_id'] = $model_id;
        }
        if (!empty($car_id)) {
            $data['car_id'] = $car_id;
        }
        if (!empty($exterior_color_name)) {
            $data['exterior_color_name'] = $exterior_color_name;
        }
        if (!empty($interior_color_name)) {
            $data['interior_color_name'] = $exterior_color_name;
        }
        $car_price = M('view_car_price');
        $count = $car_price->where($data)->count();
        $page = new \Think\Page($count, 15);
        $pagelist = $page->show();
        $model_info = $car_price->where(array('user_id' => $user_id))->field('car_model_id,car_model_name')->distinct(true)->select();
        $car_info = $car_price->where(array('user_id' => $user_id))->field('car_id,car_name')->distinct(true)->select();
        $exterior_info = $car_price->where(array('user_id' => $user_id))->field('exterior_color_name')->distinct(true)->select();
        $interior_info = $car_price->where(array('user_id' => $user_id))->field('interior_color_name')->distinct(true)->select();

        $info = $car_price->where($data)->field('id,car_name,brand_name,car_model_name,exterior_color_name,interior_color_name,price,low_price,discount,stock,onway,city_name')->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign('info', $info);
        $this->assign('pagelist', $pagelist);
        $this->assign('model_info', $model_info);
        $this->assign('car_info', $car_info);
        $this->assign('exterior_info', $exterior_info);
        $this->assign('interior_info', $interior_info);
        $this->assign('user_id', $user_id);
        $this->display();
    }

    /**
     * 历史报价
     */
    public function history_price()
    {
        $car_price_id = I('id');
        $count = M('price_trend')->where(array('car_price_id' => $car_price_id))->count();
        $page = new \Think\Page($count, 15);
        $pagelist = $page->show();
        $this->info = M('price_trend')->where(array('car_price_id' => $car_price_id))->field('id,price,low_price,discount,createtime')->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign('pagelist', $pagelist);
        $this->display();
    }

    /**
     *积分管理
     */
    public function integral()
    {
        $user_id = I('id');
        if (I('score_change')) {
            $score = (int)I('score_change');//修改的分数
        }
        if (I('info')) {
            $info = I('info');             //修改备注
        }
        if (I('user_id')) {
            $userid = I('user_id');           //隐藏表单传递过来的
        }
        $this->total_score = M('user_4s')->where(array('id' => $user_id))->getField('total_jifen');
        $this->user_id = $user_id;
        $this->score_change_info = M('score')->where(array('user_id' => $user_id))->order("createtime desc")->select();
        $this->page = I('p');
        if (isset($score) && $score == 0) {
            $this->error('您输入的数值有误');//如果用户输入的是一个无法转换成int型的字符串，则系统将其转换成0
        }
        if (!empty($score)) {
            if (empty($info) || empty($userid)) {
                $this->error('请输入修改备注');
            } else {
                $data['id'] = md5(microtime());
                $data['createuser'] = $_SESSION['admin_name'];
                $data['user_id'] = I('user_id');
                $data['score'] = $score;
                $total_score2 = (int)I('total_score') + $score;
                $data['info'] = $info;
                M('score')->startTrans();//开启事务
                $res1 = M('score')->add($data);
                $datauser['updatetime'] = date('Y-m-d H:i:s', time());
                $datauser['updateuser'] = session('admin_name');
                $datauser['total_jifen'] = $total_score2;
                $res2 = M('user_4s')->where(array('id' => I('user_id')))->save($datauser);
                if ($res1 && $res2) {
                    M('score')->commit();//成功提交
                    $this->success('修改成功');
                } else {
                    M('score')->rollback();//失败回滚
                    $this->error('修改失败');
                }
            }
        }

        $this->display();
    }

    /***********************************************************经销商会员操作**************************************************************************/
    /**
     *新车经销商列表
     */
    public function Dealer()
    {
        $this->display();
    }

    /**
     *经销商添加页面
     */
    public function Dealeradd()
    {
        $province = M('city')->where(array('isdelete' => 0))->distinct(true)->field('province_id')->select();//查询所有省份
        foreach ($province as $key => $value) {
            $province[$key]['province_name'] = M('province')->where(array('isdelete' => 0, 'id' => $value['province_id']))->getField('province_name');
        }
        $this->assign('province', $province);
        $this->display();
    }

    /**
     *添加新车经销商
     */
    public function DealeraddHandle()
    {
        //判断是否填写经销商名称
        if (I('dealer_name') == '') {
            $this->error('请选择公司名称');
        }
        //判断是否输入地址
        if (I('city_id') == "") {
            $this->error('请选择城市');
        }
        //判断是否输入地址
        if (I('addr') == "") {
            $this->error('请输入地址');
        }
        //判断是否输入联系人
        if (I('contacts') == "") {
            $this->error('请填写联系人');
        }
        //判断是否输入联系电话
        if (I('tel') == "") {
            $this->error('请输入联系电话');
        }
        //判断账号是否是6-22位
        if (!preg_match("/^([a-zA-Z0-9]|[._]){5,22}$/", I('username'))) {
            $this->error('请输入6-22字母或数字的账号');
        }
        //判断用户是否存在
        if (M('user_dealer')->where(array('user_name' => I('username')))->find()) {
            $this->error('用户名已存在，请重新输入');
        }
        //判断木木是否小于六位数
        if (strlen(I('password')) < 6) {
            $this->error('密码长度为不小于6位,请检查您的输入!');
        }
        if (I('power') != '') {
            $power = I('power');
            $power_value = 0;
            foreach ($power as $key => $value) {
                $power_value += $value;
            }
        }
        //提交的数据插入数据库
        $member = M("user_dealer");
        $data['dealer_name'] = I('dealer_name');
        $data['city_id'] = I('city_id');
        $data['addr'] = I('addr');
        $data['contacts'] = I('contacts');
        $data['tel'] = I('tel');
        // $data['brand_4s'] =I('brand_4s');
        $data['id'] = md5(microtime());
        $data['user_name'] = I('username');
        $data['password'] = MD5(I('password'));
        $data['status'] = I('status');
        $data['role'] = 3;
        $data['createuser'] = session('admin_name');
        $data['logintime'] = time();
        $data['loginip'] = $_SERVER['HTTP_X_REAL_IP'];
        $data['power'] = $power_value;

        if (!empty($_POST)) {
            $member->create($data);
            $info = $member->add();
            if ($info) {
                $this->success('添加成功', U('Members/Dealer'));
            } else {
                $this->error('添加失败');
            }
        }
    }

    /**
     *修改新车经销商用户信息
     */
    public function Dealerupd()
    {
        //根据ID查到用户信息
        $this->province = M('province')->select();//查询所有省份
        $this->brand = M('brand')->select();//查询所有品牌
        //根据ID查到用户信息
        $member = M("user_dealer");
        $info = $member->where(array('id' => I('id')))->find();
        $city = M('city')->where(array('id' => $info['city_id']))->find();
        $province = M('province')->where(array('id' => $city['province_id']))->find();
        $this->city_all = M('city')->where(array('province_id' => $province['id']))->select();
        $info['city_name'] = $city['city_name'];
        $info['province_id'] = $province['id'];
        $this->assign('info', $info);
        $this->display();

    }

    /**
     * 修改新车经销商信息
     */
    public function DealerupdHandle()
    {
        if (I('dealer_name') == '') {
            $this->error('请选择公司名称');
        }
        if (I('city_id') == "") {
            $this->error('请选择城市');
        }
        if (I('addr') == "") {
            $this->error('请输入地址');
        }
        if (I('contacts') == "") {
            $this->error('请填写联系人');
        }
        if (I('tel') == "") {
            $this->error('请输入联系电话');
        }
        if (!preg_match("/^([a-zA-Z0-9]|[._]){5,22}$/", I('username'))) {
            $this->error('请输入6-22字母或数字的账号');
        }
        if (strlen(I('password')) < 6) {
            $this->error('密码长度为不小于6位,请检查您的输入!');
        }
        if (I('power') != '') {
            $power = I('power');
            $power_value = 0;
            foreach ($power as $key => $value) {
                $power_value += $value;
            }
        }
        $member = M("user_dealer");
        $data['dealer_name'] = I('dealer_name');
        $data['city_id'] = I('city_id');
        $data['addr'] = I('addr');
        $data['contacts'] = I('contacts');
        $data['tel'] = I('tel');
        $data['password'] = MD5(I('password'));
        $data['status'] = I('status');
        $data['power'] = $power_value;
        $data['updatetime'] = date('Y-m-d H:i:s', time());
        $data['updateuser'] = session('admin_name');

        $info = $member->where(array('id' => I('id')))->save($data);
        if ($info) {
            $this->success('修改成功！', U('Members/Dealer'));
        } else {
            $this->error('修改失败！');
        }

    }

    /**
     * ajax异步删除经销商会员
     */
    public function user_dealer_delete_ajax()
    {
        $id = I('id');
        if (!empty($id)) {
            $data['isdelete'] = 1;
            $data['updatetime'] = date("Y-m-d H:i:s", time());
            $data['updateuser'] = session('admin_name');
            $res = M('user_dealer')->where(array('id' => $id))->save($data);
            $this->ajaxReturn($res);
        }
    }

}