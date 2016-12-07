<?php
namespace Admin\Controller;
use Think\Controller;

/**
 * Class CityController 城市管理
 * @package Admin\Controller
 */
class CityController extends CommonController
{
    /*****************************************************城市站点******************************************************************************/
    /**
     *城市站点列表
     */
    public function citysite()
    {
        $city = M('city');
        $province = M('province');
        $area = M('area');
        $location_c = $city->where(array('isdelete' => 0))->order('createtime desc')->select();
        $count = $city->where(array('isdelete' => 0))->count();
        //根据市查找对应的省份
        foreach ($location_c as $key => $value) {
            $info = $province->where(array('id' => $value['province_id']))->find();

            $area_name = $area->where(array('id' => $info['area_id']))->getField('area_name');

            $location_c[$key]['province_name'] = $info['province_name'];

            $location_c[$key]['area_name'] = $area_name;
        }
        $this->assign('count', $count);
        $this->assign('city', $location_c);
        $this->display();
    }

    /**
     *添加城市
     */
    public function addcity()
    {
        $this->display();
    }

    /**
     *添加城市处理
     */
    public function addcity_handdle()
    {
        if (!I('city')) {
            $this->error('请选择城市！');
        }
        $area_id = M('area')->where(array('area_name' => I('area')))->getField('id');
        $province_id = M('province')->where(array('province_name' => I('province')))->getField('id');
        $city_id = M('city')->where(array('city_name' => I('city')))->getField('id');
        // echo $area_id."--".$province_id."--".$city_id;exit();
        if (!$area_id) {
            $data1['id'] = md5(microtime());
            $data1['area_name'] = I('area');
            $data1['createuser'] = $_SESSION['admin_name'];
            M('area')->add($data1) or $this->error('系统错误，请重试！');

            $data2['id'] = md5(microtime());
            $data2['area_id'] = $data1['id'];
            $data2['createuser'] = $_SESSION['admin_name'];
            $data2['province_name'] = I('province');
            M('province')->add($data2) or $this->error('系统错误，请重试！');

            $data3['id'] = md5(microtime());
            $data3['province_id'] = $data2['id'];
            $data3['city_name'] = I('city');
            $data3['createuser'] = $_SESSION['admin_name'];
            $data3['status'] = I('status');
            if (M('city')->add($data3)) {
                $this->success('添加成功！', 'citysite');
            } else {
                $this->error('添加失败！', U('City/addcity'));
            }

        } elseif (!$province_id) {
            $data2['id'] = md5(microtime());
            $data2['area_id'] = $area_id;
            $data2['province_name'] = I('province');
            $data2['createuser'] = $_SESSION['admin_name'];
            M('province')->add($data2) or $this->error('系统错误，请重试！');

            $data3['id'] = md5(microtime());
            $data3['province_id'] = $data2['id'];
            $data3['city_name'] = I('city');
            $data3['createuser'] = $_SESSION['admin_name'];
            $data3['status'] = I('status');
            if (M('city')->add($data3)) {
                $this->success('添加成功！', 'citysite');
            } else {
                $this->error('添加失败！', U('City/addcity'));
            }
        } elseif (!$city_id) {
            $data3['id'] = md5(microtime());
            $data3['province_id'] = $province_id;
            $data3['createuser'] = $_SESSION['admin_name'];
            $data3['city_name'] = I('city');
            $data3['status'] = I('status');
            if (M('city')->add($data3)) {
                $this->success('添加成功！', 'citysite');
            } else {
                $this->error('添加失败！', U('City/addcity'));
            }
        } else {
            $this->error('城市已存在，请勿重复添加！', U('City/addcity'));
        }
    }

    /**
     *删除城市
     */
    public function deletecity()
    {
        $city = M('city');
        $city_id = I('cityid');
        $data['updatetime'] = date('Y-m-d H:i:s', time());
        $data['updateuser'] = session('admin_name');
        $data['isdelete'] = 1;
        $z = $city->where(array('id' => $city_id))->save($data);
        if ($z) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }

    }

    /**
     *设置城市是否启用
     */
    public function changestatus()
    {
        $city_id = I('id');
        $city_id2 = I('id2');
        //启用城市
        if (!empty($city_id)) {
            $province_id = M('city')->where(array('id' => $city_id))->getField('province_id');
            $z = M('province')->where(array('id' => $province_id, 'isdelete' => 0, 'status' => 0))->getField('id');
            //启动城市的同时也启动省份
            if (!empty($z)) {
                $data1['updatetime'] = date('Y-m-d H:i:s', time());
                $data1['updateuser'] = session('admin_name');
                $data1['status'] = 1;
                $z2 = M('province')->where(array('id' => $province_id, 'isdelete' => 0, 'status' => 0))->save($data1);
            }
            $saveinfo['status'] = 1;
            $saveinfo['id'] = $city_id;
            $saveinfo['updatetime'] = date('Y-m-d H:i:s', time());
            $saveinfo['updateuser'] = session('admin_name');
            $info_model = M('city')->save($saveinfo);
        }
        //禁用城市
        if (!empty($city_id2)) {
            $saveinfo['status'] = 0;
            $saveinfo['id'] = $city_id2;
            $saveinfo['updatetime'] = date('Y-m-d H:i:s', time());
            $saveinfo['updateuser'] = session('admin_name');
            $info_model = M('city')->save($saveinfo);
            //如果城市全部禁用，省份也禁用
            $province_id = M('city')->where(array('id' => $city_id2))->getField('province_id');
            $z = M('city')->where(array('province_id' => $province_id, 'isdelete' => 0, 'status' => 1))->getField('id');
            if (empty($z)) {
                $data2['updatetime'] = date('Y-m-d H:i:s', time());
                $data2['updateuser'] = session('admin_name');
                $data2['status'] = 0;
                $z2 = M('province')->where(array('id' => $province_id, 'isdelete' => 0, 'status' => 1))->save($data2);
            }
        }
        $this->ajaxReturn($info_model);
    }


}