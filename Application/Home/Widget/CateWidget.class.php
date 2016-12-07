<?php
namespace Home\Widget;

use Think\Controller;

class CateWidget extends Controller
{

    public function header()
    {
        $citys = session('city');
        $provinces = session('province');
        $conc = session('conc');

        if (empty($citys) and empty($provinces)) {
            if (isset($conc) and $conc == 'quanguo') {
                $this->assign('con', $conc);
            } else {
                $getIp = $_SERVER['HTTP_X_REAL_IP'];
                $Index = A('Index');
                $areasina = $Index->sinaIP($getIp);
                if ($areasina['province'] == '江苏') {
                    $city_info = M('city')->where(array('isdelete' => 0, 'city_name' => array('like', '%' . $areasina['city'] . '%')))->getField('id');
                    if ($city_info) {
                        session('city', $city_info);
                    } else {
                        $nanjing = M('city')->where(array('isdelete' => 0, 'city_name' => '南京市'))->getField('id');
                        session('city', $nanjing);
                    }
                } else {
                    $city_info = M('city')->where(array('isdelete' => 0, 'city_name' => array('like', '%' . $areasina['city'] . '%')))->getField('id');
                    if ($city_info) {
                        session('city', $city_info);
                    } else {
                        $province_info = M('province')->where(array('isdelete' => 0, 'province_name' => array('like', '%' . $areasina['province'] . '%')))->getField('id');
                        if ($province_info) {
                            session('province', $province_info);
                        } else {
                            $jiangsu = M('province')->where(array('isdelete' => 0, 'province_name' => '江苏省'))->getField('id');
                            session('province', $jiangsu);
                        }

                    }
                }
            }
        }
        //获得省份城市
        $province = M('province')->where(array('isdelete' => 0, 'status' => 1))->select();
        foreach ($province as $key => $value) {
            $province[$key]['city'] = M('city')->where(array('isdelete' => 0, 'province_id' => $value['id'], 'status' => 1))->select();
        }
        //判断是否选择城市了 
        if (isset($_SESSION['province']) and $_SESSION['province'] != '') {
            $this->province_name = M('province')->where(array('isdelete' => 0, 'id' => $_SESSION['province']))->getField('province_name');
        }
        if (isset($_SESSION['city']) and $_SESSION['city'] != '') {
            $this->city_name = M('city')->where(array('isdelete' => 0, 'id' => $_SESSION['city']))->getField('city_name');
        }

        //获得登录信息
        if (isset($_SESSION['user_id']) and $_SESSION['user_id'] != '') {
            $userinfo = D('UserGeneral')->user_find(array('id' => $_SESSION['user_id']));
            if (empty($userinfo['nick'])) {
                $userinfo['nick'] = '购车惠会员';
            }
            if (empty($userinfo['head_url'])) {
                $userinfo['head_url'] = '/Public/Home/images/headimg.png';
            } else {
                $userinfo['head_url'] = OSS . str_replace('type', 'small', $userinfo['head_url']);
            }
            $userinfo['msg'] = M("user_message")->where(array('user_id' => $userinfo['id'], 'isdelete' => 0))->count();
            $this->assign('userinfo', $userinfo);
        } else {
            $this->assign('userinfo', null);
        }
        if (isset($_GET['select']) and $_GET['select'] != '') {
            $this->assign('select', I('select'));
        }
        //关键字搜索排序前4
        $this->search_res = M('search_key')->limit('4')->order('search_count desc')->select();
        $this->assign('province', $province);
        $this->display('Cate:header');
    }

    public function footer()
    {
        $this->display('Cate:footer');
    }

    public function carlife_banner()
    {
        $this->display('Cate:carlife_banner');
    }

    /**
     * @Description:订单头部
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/10/25 13:45
     * @Version 2.0
     */
    public function header_order(){

        //获得登录信息
        if(isset($_SESSION['user_id']) and $_SESSION['user_id']!=''){
            $userinfo=D('UserGeneral')->user_find(array('id'=>$_SESSION['user_id']));
            if (empty($userinfo['nick']))
            {
                $userinfo['nick']='购车惠会员';
            }
            if(empty($userinfo['head_url']))
            {
                $userinfo['head_url']='/Public/Home/images/headimg.png';
            }else
            {
                $userinfo['head_url']=OSS.str_replace('type','small',$userinfo['head_url']);
            }
            $userinfo['msg'] = M("user_message")->where(array('user_id'=>$userinfo['id'],'isdelete'=>0))->count();
            $this->assign('userinfo',$userinfo);
        }else{
            $this->assign('userinfo',null);
        }

        $this->display('Cate:header_order');
    }
}