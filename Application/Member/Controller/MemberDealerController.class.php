<?php
namespace Member\Controller;
use Think\Controller;

/**
 * Class UsercenterController前台会员中心
 * @package Member\Controller
 */
class MemberDealerController extends Controller
{
    /**
     *初始化页面
     */
    public function  _initialize()
    {
        $info=D('UserDealer')->user_find(array('id'=>session('user_id')));
        if(empty($info))
        {
            $this->error('请先登录经销商账号!');
        }
    }

    /**
     *进入该控制器默认跳转的页面
     */
    public function index()
    {
        $this->redirect('dr_member_accInfo');
    }
    /**
     *经销商用户--------账号资料
     */
    public function dr_member_accInfo()
    {
        $user_info=D('UserDealer')->where(array('id'=>session('user_id')))->find();
        $user_info['head_url']=str_replace('type','small',$user_info['head_url']);
        if(!empty($_FILES))
        {
            $y = upload_local();
            if($y){
                $_POST['head_url']= str_replace("big",'type',$y['headimg']['savepath'].$y['headimg']['savename']);
            }
        }
        if(!empty($_POST))
        {
            $user=D('UserDealer');
            $user->create();
            $z=$user->user_save();
            if($z !== false)
            {
                $this->success('保存成功！');
            }else{
                $this->error('保存失败！');
            }
        }
        $this->assign('user_info',$user_info);
        $this->display();
    }

    /**
     *经销商用户--------报价查询
     */
    public function dr_quotation()
    {
        $brand_info=D("view_car_price")->group('brand_id')->select();
        $cities=M("city")->select();

        if(!empty($_POST))
        {
            $msg1=I('brand_id');
            if(!empty($msg1))
            {
                $data['brand_id']=$msg1;
                $model_info=D("view_car_price")->where(array('brand_id'=>$msg1))->group('car_model_id')->select();
                $this->assign('model_info',$model_info);
                $this->assign('brand_id',$msg1);
            }
            $msg2=I('car_model_id');
            if(!empty($msg2))
            {
                $data['car_model_id']=$msg2;
                $car_info=D("view_car_price")->where(array('car_model_id'=>$msg2))->group('car_id')->select();
                $this->assign('car_info',$car_info);
                $this->assign('car_model_id',$msg2);
            }
            $msg3=I('car_id');
            if(!empty($msg3))
            {
                $data['car_id']=$msg3;
                $exterior_info=D("view_car_price")->where(array('car_id'=>$msg3))->group('exterior_color_id')->select();
                $interior_info=D("view_car_price")->where(array('car_id'=>$msg3))->group('interior_color_id')->select();
                $this->assign('exterior_info',$exterior_info);
                $this->assign('interior_info',$interior_info);
                $this->assign('car_id',$msg3);
            }
            $msg4=I('exterior_color_id');
            if(!empty($msg4))
            {
                $data['exterior_color_id']=$msg4;
                $this->assign('exterior_color_id',$msg4);
            }
            $msg5=I('interior_color_id');
            if(!empty($msg5))
            {
                $data['interior_color_id']=$msg5;
                $this->assign('interior_color_id',$msg5);
            }

            $city_id=I('city_id');
            $this->assign('city_id',$city_id);
            $province_id=M("city")->where(array('id'=>$city_id))->getField('province_id');

            $city_quotes_info=D("view_car_price")->where($data)->where(array('city_id'=>$city_id))->order('low_price')->limit(0,5)->select();
            $province_quotes_info=D("view_car_price")->where($data)->where(array('province_id'=>$province_id))->order('low_price')->limit(0,5)->select();
            $country_quotes_info=D("view_car_price")->where($data)->order('low_price')->limit(0,5)->select();

            foreach($city_quotes_info as $k => $v)
            {
                $city_area=M('sales_area')->where(array('car_price_id'=>$v['id'],'isdelete'=>0))->select();
                foreach($city_area as $kk => $vv)
                {
                    $city_area_one[]=$vv['sales_area_name'];
                }
                $city_quotes_info[$k]['sales_area']=implode('；',array_unique($city_area_one));

            }

            foreach($province_quotes_info as $k => $v)
            {
                $province_area=M('sales_area')->where(array('car_price_id'=>$v['id'],'isdelete'=>0))->select();
                foreach($province_area as $kk => $vv)
                {
                    $province_area_one[]=$vv['sales_area_name'];
                }
                $province_quotes_info[$k]['sales_area']=implode('；',array_unique($province_area_one));

            }

            foreach($country_quotes_info as $k => $v)
            {
                $country_area=M('sales_area')->where(array('car_price_id'=>$v['id'],'isdelete'=>0))->select();
                foreach($country_area as $kk => $vv)
                {
                    $country_area_one[]=$vv['sales_area_name'];
                }
                $country_quotes_info[$k]['sales_area']=implode('；',array_unique($country_area_one));

            }

            $this->assign('city_quotes_info',$city_quotes_info);
            $this->assign('city_name',$city_quotes_info[0]['city_name']);
            $this->assign('province_quotes_info',$province_quotes_info);
            $this->assign('province_name',$province_quotes_info[0]['province_name']);
            $this->assign('country_quotes_info',$country_quotes_info);
        }
        $this->assign('cities',$cities);
        $this->assign('brand_info',$brand_info);
        $this->display();
    }

    /**
     *经销商用户-------联动------车型
     */
    public function DR_ajax_quotes_model()
    {
        $model_info=D("view_car_price")->where(array('brand_id'=>I('brand_id')))->group('car_model_id')->select();
        $this->ajaxReturn($model_info);

    }

    /**
     *经销商用户-------联动------车款
     */
    public function DR_ajax_quotes_style()
    {
        $car_info=D("view_car_price")->where(array('car_model_id'=>I('car_model_id')))->group('car_id')->select();
        $this->ajaxReturn($car_info);

    }

    /**
     *经销商用户-------联动------外观颜色
     */
    public function DR_ajax_quotes_exterior()
    {
        $exterior_info=D("view_car_price")->where(array('car_id'=>I('car_id')))->group('exterior_color_id')->select();
        $this->ajaxReturn($exterior_info);

    }

    /**
     *经销商用户-------联动------内饰颜色
     */
    public function DR_ajax_quotes_interior()
    {
        $interior_info=D("view_car_price")->where(array('car_id'=>I('car_id')))->group('interior_color_id')->select();
        $this->ajaxReturn($interior_info);

    }


}