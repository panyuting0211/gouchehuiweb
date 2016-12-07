<?php
namespace Home\Controller;

use Think\Controller;

/**
 * Class IndexController前台首页控制器
 * @package Home\Controller
 */
class IndexController extends CommonController
{

    /**
     * @var string
     */
    private $losttime = '3600*4';

    /**
     *首页
     */
    public function index()
    {
        $citys = session('city');
        $provinces = session('province');
        $this->callBackUrl = urlencode('http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
        $con = I('con');
        //判断是否选择城市了
        if (isset($con) and $con == 'quanguo') {
            unset($_SESSION['city']);
            unset($_SESSION['province']);
            session('conc', $con);
            $this->assign('con', $con);
        } else {
            if (isset($_GET['province']) and I('province')) {
                $this->province_name = M('province')->where(array('isdelete' => 0, 'id' => I('province')))->getField('province_name');
                unset($_SESSION['city']);
                $data['area_name'] = I('province');//加入低价车查询条件
                $special_data['province_id'] = I('province');//加入特价车查询条件
                $data_index['province_id'] = I('province');//计入不需要询价的条件筛选
                session('province', I('province'));
            } else {
                if (isset($_GET['city']) and I('city') != '') {
                    $this->city_name = M('city')->where(array('isdelete' => 0, 'id' => I('city')))->getField('city_name');
                    unset($_SESSION['province']);
                    $data['area_name'] = I('city');//加入低价车查询条件
                    $special_data['city_id'] = I('city');//加入特价车查询条件
                    $data_index['city_id'] = I('city');//计入不需要询价的条件筛选
                    session('city', I('city'));
                } else {
                    if (isset($_SESSION['conc']) and $_SESSION['conc'] != '') {
                        unset($_SESSION['city']);
                        unset($_SESSION['province']);
                        $this->assign('con', $_SESSION['conc']);
                    } else {
                        if (isset($_SESSION['province']) and $_SESSION['province'] != '') {

                            $this->province_name = M('province')->where(array('isdelete' => 0, 'id' => $_SESSION['province']))->getField('province_name');
                            unset($_SESSION['city']);
                            $data['area_name'] = $_SESSION['province'];//加入低价车查询条件
                            $special_data['province_id'] = $_SESSION['province'];//加入特价车查询条件
                            $data_index['province_id'] = $_SESSION['province'];//计入不需要询价的条件筛选
                        } else {

                            if (isset($_SESSION['city']) and $_SESSION['city'] != '') {

                                $this->city_name = M('city')->where(array('isdelete' => 0, 'id' => $_SESSION['city']))->getField('city_name');
                                unset($_SESSION['province']);
                                $data['area_name'] = $_SESSION['city'];//加入低价车查询条件
                                $special_data['city_id'] = $_SESSION['city'];//加入特价车查询条件
                                $data_index['city_id'] = $_SESSION['city'];//计入不需要询价的条件筛选
                            } else {
                                //根据IP地址定位
                                $getIp = $_SERVER['HTTP_X_REAL_IP'];
                                $areasina = $this->sinaIP($getIp);
                                if ($areasina['province'] == '江苏') {
                                    $city_info = M('city')->where(array('isdelete' => 0, 'city_name' => array('like', '%' . $areasina['city'] . '%')))->getField('id');
                                    $this->city_name = M('city')->where(array('isdelete' => 0, 'id' => $city_info))->getField('city_name');
                                    if ($city_info) {
                                        session('city', $city_info);
                                        $data['area_name'] = $_SESSION['city'];//加入低价车查询条件
                                        $special_data['city_id'] = $_SESSION['city'];//加入特价车查询条件
                                        $data_index['city_id'] = $_SESSION['city'];//计入不需要询价的条件筛选
                                    } else {
                                        $nanjing = M('city')->where(array('isdelete' => 0, 'city_name' => '南京市'))->getField('id');
                                        session('city', $nanjing);
                                        $data['area_name'] = $_SESSION['city'];//加入低价车查询条件
                                        $special_data['city_id'] = $_SESSION['city'];//加入特价车查询条件
                                        $data_index['city_id'] = $_SESSION['city'];//计入不需要询价的条件筛选
                                    }
                                } else {
                                    $city_info = M('city')->where(array('isdelete' => 0, 'city_name' => array('like', '%' . $areasina['city'] . '%')))->getField('id');
                                    $this->city_name = M('city')->where(array('isdelete' => 0, 'id' => $_SESSION['city']))->getField('city_name');
                                    if ($city_info) {
                                        session('city', $city_info);
                                        $data['area_name'] = $_SESSION['city'];//加入低价车查询条件
                                        $special_data['city_id'] = $_SESSION['city'];//加入特价车查询条件
                                        $data_index['city_id'] = $_SESSION['city'];//计入不需要询价的条件筛选
                                    } else {
                                        $province_info = M('province')->where(array('isdelete' => 0, 'province_name' => array('like', '%' . $areasina['province'] . '%')))->getField('id');
                                        $this->province_name = M('province')->where(array('isdelete' => 0, 'id' => I('province')))->getField('province_name');
                                        if ($province_info) {
                                            session('province', $province_info);
                                            $data['area_name'] = $_SESSION['province'];//加入低价车查询条件
                                            $special_data['province_id'] = $_SESSION['province'];//加入特价车查询条件
                                            $data_index['province_id'] = $_SESSION['province'];//计入不需要询价的条件筛选
                                        } else {
                                            $jiangsu = M('province')->where(array('isdelete' => 0, 'province_name' => '江苏省'))->getField('id');
                                            session('province', $jiangsu);
                                            $data['area_name'] = $_SESSION['province'];//加入低价车查询条件
                                            $special_data['province_id'] = $_SESSION['province'];//加入特价车查询条件
                                            $data_index['province_id'] = $_SESSION['province'];//计入不需要询价的条件筛选
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        //获得登录信息
        if (isset($_SESSION['user_id']) and $_SESSION['user_id'] != '') {
            $userinfo = D('UserGeneral')->user_find(array('id' => $_SESSION['user_id']));
            $userinfo['msg'] = M("user_message")->where(array('user_id' => $userinfo['id'], 'isdelete' => 0))->count();
        }

        $this->assign('userinfo', $userinfo);

        //获得省份城市
        $province=S('province');
        if ($province=='')
        {
            $province = M('province')->where(array('isdelete' => 0, 'status' => 1))->select();
            foreach ($province as $key => $value) {
                $province[$key]['city'] = M('city')->where(array('isdelete' => 0, 'province_id' => $value['id'], 'status' => 1))->select();
            }
            S('province',$province,$this->losttime);
        }

        $this->assign('province', $province);

        //关键字搜索排序前4
        $search_res=S('search_res');
        if ($search_res=='')
        {
            $search_res = M('search_key')->limit('4')->order('search_count desc')->select();
            S('search_res',$search_res,$this->losttime);
        }
        $this->assign('search_res',$search_res);
        //热门品牌
   /*     $brand = M('view_car_price')->where(array('isdelete' => 0, 'is_xunjia' => 1))->order('brand_access_quantity')->group('brand_id')->limit('3')->select();
        $this->assign('brand', $brand);*/

        //报价表里所有的品牌(A-L)
        $brand_allAL = S('brand_allAL');
        if ($brand_allAL == '') {
            $brand_allAL = M('view_car_price')->where(array('isdelete' => 0, 'is_xunjia' => 1, 'brand_alif' => array('between', array('A', 'L'))))->order('brand_alif')->group('brand_alif')->select();
            //这段代码需要优化
            foreach ($brand_allAL as $k => $v) {
                $brand_allAL[$k]['list'] = M('view_car_price')->where(array('isdelete' => 0, 'is_xunjia' => 1, 'brand_alif' => $v['brand_alif']))->group('brand_id')->select();
            }

            S('brand_allAL', $brand_allAL, $this->losttime);
        }
        $this->assign('brand_allAL', $brand_allAL);

        //报价表里所有的品牌(M-Z)
        $brand_allMZ = S('brand_allMZ');
        if ($brand_allMZ == '') {
            $brand_allMZ = M('view_car_price')->where(array('isdelete' => 0, 'is_xunjia' => 1, 'brand_alif' => array('between', array('M', 'Z'))))->order('brand_alif')->group('brand_alif')->select();
            foreach ($brand_allMZ as $k => $v) {
                $brand_allMZ[$k]['list'] = M('view_car_price')->where(array('isdelete' => 0, 'is_xunjia' => 1, 'brand_alif' => $v['brand_alif']))->group('brand_id')->select();
            }
            S('brand_allMZ', $brand_allMZ, $this->losttime);
        }
        $this->assign('brand_allMZ', $brand_allMZ);

        //热门车型car_type
        $car_type=S('car_type');
        if ($car_type=='')
        {
            $car_type = M('car_type')->where(array('isdelete' => 0))->select();
            foreach ($car_type as $key => $value) {
                $car_type[$key]['logo'] = str_replace("type", 'big', $value['logo']);
            }
            S('car_type',$car_type,$this->losttime);
        }
        $this->assign('car_type', $car_type);

        //首页banner广告图
        $banner=S('banner');
        if ($banner=='')
        {
            $banner = M('ad')->where(array('type' => 0, 'isdelete' => 0, 'status' => 1))->order('alif asc,createtime desc')->select();
            foreach ($banner as $key => $value) {
                $banner[$key]['imgurl'] = str_replace("type", 'big', $value['imgurl']);
            }
            S('banner',$banner,$this->losttime);
        }
        $this->assign('banner', $banner);
        //获得首页低价车对比4个
        $dijiache_8 = S('dijiache_8');
        if ($dijiache_8 == '') {
            $dijiache_8 = M('view_index_car_price')->where(array('status' => 1, 'isbaojia' => 1, 'isdelete' => 0,'car_status'=>1))->order('createtime desc')->limit('4')->select();
            foreach ($dijiache_8 as $key => $value) {
                /*$data_index_4['is_xunjia'] = 2;*///不需要询底价的
                $data_index_4['isdelete'] = 0;//是否删除
                $data_index_4['car_status'] = 1;
                $data_index_4['car_id'] = $value['car_id'];
                $data_index_4['interior_color_id'] = $value['interior_color_id'];
                $data_index_4['exterior_color_id'] = $value['exterior_color_id'];
                $find = M('view_car_price')->where($data_index_4)->order('low_price asc')->find();
                if ($find) {
                    $dijiache_8[$key]['index_id'] = $find['id'];
                    $dijiache_8[$key]['car_name'] = $find['car_name'];
                    $dijiache_8[$key]['exterior_color_name'] = $find['exterior_color_name'];
                    $dijiache_8[$key]['discount'] = number_format($find['discount'] / 10000, 2);
                    $dijiache_8[$key]['low_price'] = number_format($find['low_price'] / 10000, 2);
                    $dijiache_8[$key]['auth_price'] = number_format($find['auth_price'] / 10000, 2);
                    $dijiache_8[$key]['imgurl'] = str_replace("type", 'big', $value['imgurl']);
                } else {
                    unset($dijiache_8[$key]);
                }
            }
            S('dijiache_8', $dijiache_8, $this->losttime);
        }
        $this->assign('dijiache_8', $dijiache_8);

        //不需要询价的8个
        $dijiache_6 = S('dijiache_6');
        if ($dijiache_6 == '') {
            $data_index['isbaojia'] = 1;
            $data_index['isdelete'] = 0;
            $data_index['is_xunjia'] = 2;
            $data_index['status'] = 2;
            $dijiache_6 = M('view_index_price')->where($data_index)->order('createtime desc')->group('model_id')->limit('8')->select();
            foreach ($dijiache_6 as $key => $value) {
                $data_index_8['is_xunjia'] = 2;//不需要询底价的
                $data_index_8['isdelete'] = 0;//是否删除
                $data_index_8['car_status'] = 1;
                $data_index_8['car_id'] = $value['car_id'];
                $data_index_8['interior_color_id'] = $value['interior_color_id'];
                $data_index_8['exterior_color_id'] = $value['exterior_color_id'];
                $find = M('view_car_price')->where($data_index_8)->order('low_price asc')->find();
                if ($find) {
                    $dijiache_6[$key]['index_id'] = $find['id'];
                    $dijiache_6[$key]['car_model_imageurl'] = str_replace("type", 'big', $find['car_model_imageurl']);
                    $dijiache_6[$key]['discount'] = number_format($find['discount'] / 10000, 2);
                    $dijiache_6[$key]['low_price'] = number_format($find['low_price'] / 10000, 2);
                    $min = M('car')->where(array('car_model_id' => $find['car_model_id'], 'isdelete' => 0, 'status' => 1))->min('auth_price');//该车型下最便宜的车型
                    $dijiache_6[$key]['min'] = number_format($min / 10000, 2);
                    $max = M('car')->where(array('car_model_id' => $find['car_model_id'], 'isdelete' => 0, 'status' => 1))->max('auth_price');//
                    $dijiache_6[$key]['max'] = number_format($max / 10000, 2);
                    $string = strstr($find['logo'], 'type');
                    if (!empty($string)) {
                        $dijiache_6[$key]['logo'] = OSS . str_replace('type', 'small', $find['logo']);
                    } else {
                        $dijiache_6[$key]['logo'] = OSS . $find['logo'];
                    }
                } else {
                    unset($dijiache_6[$key]);
                }
            }
            S('dijiache_6', $dijiache_6, $this->losttime);
        }
        $this->assign('dijiache_6', $dijiache_6);

        //热门车型
        $suv = S('suv');
        if ($suv == '') {
            $suv = D('view_car_price')->where(array('isdelete' => 0, 'is_xunjia' => 1, 'car_status' => 1, 'car_type_id' => '0ff0c24fbf3d11e5ba4114dda95160ab'))->group('car_model_id')->order('model_access_quantity desc')->limit(0, 9)->select();
            foreach ($suv as $key => $value) {
                $suv[$key]['car_model_imageurl'] = OSS . str_replace('type', 'small', $value['car_model_imageurl']);
                $suv[$key]['attention_count'] = M('user_attention_car_model')->where(array('car_model_id' => $value['car_model_id'], 'isdelete' => 0))->count();
                $suv[$key]['url'] = '/index.php/car/product_details?id=' . $value['id'];
            }
            S('suv', $suv, $this->losttime);
        }
        $this->assign('suv', $suv);
        $compact = S('compact');
        if ($compact == '') {
            $compact = D('view_car_price')->where(array('isdelete' => 0, 'is_xunjia' => 1, 'car_status' => 1, 'car_type_id' => '0ff0c9d4bf3d11e5ba4114dda95160ab'))->group('car_model_id')->order('model_access_quantity desc')->limit(0, 8)->select();
            foreach ($compact as $key => $value) {
                $compact[$key]['car_model_imageurl'] = OSS . str_replace('type', 'small', $value['car_model_imageurl']);
                $compact[$key]['attention_count'] = M('user_attention_car_model')->where(array('car_model_id' => $value['car_model_id'], 'isdelete' => 0))->count();
                $compact[$key]['url'] = '/index.php/car/product_details?id=' . $value['id'];
            }
            S('compact', $compact, $this->losttime);
        }
        $this->assign('compact', $compact);
        $small = S('small');
        if ($small == '') {
            $small = D('view_car_price')->where(array('isdelete' => 0, 'is_xunjia' => 1, 'car_status' => 1, 'car_type_id' => '0ff0c763bf3d11e5ba4114dda95160ab'))->group('car_model_id')->order('model_access_quantity desc')->limit(0, 9)->select();
            foreach ($small as $key => $value) {
                $small[$key]['car_model_imageurl'] = OSS . str_replace('type', 'small', $value['car_model_imageurl']);
                $small[$key]['attention_count'] = M('user_attention_car_model')->where(array('car_model_id' => $value['car_model_id'], 'isdelete' => 0))->count();
                $small[$key]['url'] = '/index.php/car/product_details?id=' . $value['id'];
            }
            S('small', $small, $this->losttime);
        }
        $this->assign('small', $small);
        $center = S('center');
        if ($center == '') {
            $center = D('view_car_price')->where(array('isdelete' => 0, 'is_xunjia' => 1, 'car_status' => 1, 'car_type_id' => '0ff0c392bf3d11e5ba4114dda95160ab'))->group('car_model_id')->order('model_access_quantity desc')->limit(0, 9)->select();
            foreach ($center as $key => $value) {
                $center[$key]['car_model_imageurl'] = OSS . str_replace('type', 'small', $value['car_model_imageurl']);
                $center[$key]['attention_count'] = M('user_attention_car_model')->where(array('car_model_id' => $value['car_model_id'], 'isdelete' => 0))->count();
                $center[$key]['url'] = '/index.php/car/product_details?id=' . $value['id'];
            }
            S('center', $center, $this->losttime);
        }
        $this->assign('center', $center);
        $mpv = S('mpv');
        if ($mpv == '') {
            $mpv = D('view_car_price')->where(array('isdelete' => 0, 'is_xunjia' => 1, 'car_status' => 1, 'car_type_id' => '0ff0bce7bf3d11e5ba4114dda95160ab'))->group('car_model_id')->order('model_access_quantity desc')->limit(0, 9)->select();
            foreach ($mpv as $key => $value) {
                $mpv[$key]['car_model_imageurl'] = OSS . str_replace('type', 'small', $value['car_model_imageurl']);
                $mpv[$key]['attention_count'] = M('user_attention_car_model')->where(array('car_model_id' => $value['car_model_id'], 'isdelete' => 0))->count();
                $mpv[$key]['url'] = '/index.php/car/product_details?id=' . $value['id'];
            }
            S('mpv', $mpv, $this->losttime);
        }
        $this->assign('mpv', $mpv);
        $sale = S('sale');
        if ($sale == '') {
            $sale[0] = D('view_car_price')->where(array('isdelete' => 0, 'is_xunjia' => 1, 'car_status' => 1, 'car_model_id' => '222'))->find();
            $sale[1] = D('view_car_price')->where(array('isdelete' => 0, 'is_xunjia' => 1, 'car_status' => 1, 'car_model_id' => '1263'))->find();
            foreach ($sale as $key => $value) {
                $sale[$key]['car_model_imageurl'] = OSS . str_replace('type', 'center', $value['car_model_imageurl']);
                $sale[$key]['attention_count'] = M('user_attention_car_model')->where(array('car_model_id' => $value['car_model_id'], 'isdelete' => 0))->count();
                $sale[$key]['url'] = '/index.php/car/product_details?id=' . $value['id'];
            }
            S('sale', $sale, $this->losttime);
        }
        $this->assign('sale', $sale);

        //特价车
        $special_price_car = S('special_price_car');
        if ($special_price_car == '') {
            $special['status'] = array('between', array('2', '3'));
            $special['isdelete'] = 0;
            $special_price_car = M('view_special_price_car')->where($special)->order('createtime desc')->limit('2')->select();
            foreach ($special_price_car as $k => $v) {
                $special_price_car[$k]['special_price'] = number_format($v['special_price'] / 10000, 2);
                $special_price_car[$k]['price'] = number_format($v['price'] / 10000, 2);
                $special_price_car[$k]['discount'] = number_format(($v['price'] - $v['special_price']) / 10000, 2);
                $special_price_car[$k]['car_image'] = OSS . str_replace('type', 'big', $v['car_image']);
                $special_price_car[$k]['start_date'] = date("Y-m-d", strtotime($v['start_date']));
                $special_price_car[$k]['end_date'] = date("Y-m-d", strtotime($v['end_date']));
                $special_price_car[$k]['url'] = '/index.php/car/special_price_car_details?id=' . $v['id'];
            }
            S('special_price_car', $special_price_car, $this->losttime);
        }
        $this->assign('special_price_car', $special_price_car);

        //车主秀
        $bbs_info = S('bbs_info');
        if ($bbs_info == '') {
            $bbs = D('view_car_bbs');
            $bbs_info = $bbs->where(array('isdelete' => 0, 'place_index' => 2, 'check' => 2, 'pid' => 2))->order('createtime desc')->limit(0, 8)->select();
            foreach ($bbs_info as $key => $value) {
                $bbs_info[$key]['image'] = OSS . str_replace('type', 'center', $value['image']);
            }
            S('bbs_info', $bbs_info, $this->losttime);
        }
        $this->assign('bbs_info', $bbs_info);

        //新车秀
        $bbs_user_info5=S('bbs_user_info5');
        if ($bbs_user_info5=='')
        {
            $bbs_user_info5 = D('view_car_bbs')->where(array('isdelete' => 0, 'check' => 2, 'pid' => 2,'type_id'=>5))->order('createtime desc')->limit(0,10)->select();

            S('bbs_user_info5',$bbs_user_info5,$this->losttime);
        }
        $this->assign('bbs_user_info5',$bbs_user_info5);

        //装饰秀
        $bbs_user_info6=S('bbs_user_info6');
        if ($bbs_user_info6=='')
        {
            $bbs_user_info6 = D('view_car_bbs')->where(array('isdelete' => 0, 'check' => 2, 'pid' => 2,'type_id'=>6))->order('createtime desc')->limit(0,10)->select();

            S('bbs_user_info6',$bbs_user_info6,$this->losttime);
        }
        $this->assign('bbs_user_info6',$bbs_user_info6);

        //自驾游
        $bbs_user_info7=S('bbs_user_info7');
        if ($bbs_user_info7=='')
        {
            $bbs_user_info7 = D('view_car_bbs')->where(array('isdelete' => 0, 'check' => 2, 'pid' => 2,'type_id'=>7))->order('createtime desc')->limit(0,10)->select();

            S('bbs_user_info7',$bbs_user_info7,$this->losttime);
        }
        $this->assign('bbs_user_info7',$bbs_user_info7);

        //该装秀
        $bbs_user_info9=S('bbs_user_info9');
        if ($bbs_user_info9=='')
        {
            $bbs_user_info9 = D('view_car_bbs')->where(array('isdelete' => 0, 'check' => 2, 'pid' => 2,'type_id'=>9))->order('createtime desc')->limit(0,10)->select();

            S('bbs_user_info9',$bbs_user_info9,$this->losttime);
        }
        $this->assign('bbs_user_info9',$bbs_user_info9);

        //车美容
        $bbs_act_info3=S('bbs_act_info3');
        if ($bbs_act_info3=='')
        {
            $bbs_act_info3 = D('view_car_bbs')->where(array('isdelete' => 0, 'display' => 2, 'pid' => 1,'type_id'=>3))->order('createtime desc')->limit(0,10)->select();
            S('bbs_act_info3',$bbs_act_info3,$this->losttime);

        }
        $this->assign('bbs_act_info3',$bbs_act_info3);

        //车装饰
        $bbs_act_info4=S('bbs_act_info4');
        if ($bbs_act_info4=='')
        {
            $bbs_act_info4 = D('view_car_bbs')->where(array('isdelete' => 0, 'display' => 2, 'pid' => 1,'type_id'=>4))->order('createtime desc')->limit(0,10)->select();
            S('bbs_act_info4',$bbs_act_info4,$this->losttime);

        }
        $this->assign('bbs_act_info4',$bbs_act_info4);

        //车维修
        $bbs_act_info8=S('bbs_act_info8');
        if ($bbs_act_info8=='')
        {
            $bbs_act_info8 = D('view_car_bbs')->where(array('isdelete' => 0, 'display' => 2, 'pid' => 1,'type_id'=>8))->order('createtime desc')->limit(0,10)->select();
            S('bbs_act_info8',$bbs_act_info8,$this->losttime);

        }
        $this->assign('bbs_act_info8',$bbs_act_info8);





        //商城数据
        /* 内饰 */
        $nsGoodsList = S('nsGoodsList');
        if ($nsGoodsList == '') {
            $nsDbArr = array(
                'step' => 'get_shop_goods',
                'type' => 'best',
                'c_id' => 704
            );
            $nsGoodsList = unserialize(request_post("http://shop.gouchehui.com/index.php", $nsDbArr));
            S('nsGoodsList', $nsGoodsList, $this->losttime);
        }
        $this->assign('nsGoodsList', $nsGoodsList);

        /* 外饰 */
        $wsGoodsList = S('wsGoodsList');
        if ($wsGoodsList == '') {
            $wsDbArr = array(
                'step' => 'get_shop_goods',
                'type' => 'best',
                'c_id' => 720
            );
            $wsGoodsList = unserialize(request_post("http://shop.gouchehui.com/index.php", $wsDbArr));
            S('wsGoodsList', $wsGoodsList, $this->losttime);
        }
        $this->assign('wsGoodsList', $wsGoodsList);

        /* 电子子分类 */
        $dzGoodsList = S('dzGoodsList');
        if ($dzGoodsList == '') {
            $dzDbArr = array(
                'step' => 'get_shop_goods',
                'type' => 'best',
                'c_id' => 745
            );
            $dzGoodsList = unserialize(request_post("http://shop.gouchehui.com/index.php", $dzDbArr));
            S('dzGoodsList', $dzGoodsList, $this->losttime);
        }
        $this->assign('dzGoodsList', $dzGoodsList);

        /* 改装 */
       $gzGoodsList = S('gzGoodsList');
        if ($gzGoodsList == '') {
            $gzDbArr = array(
                'step' => 'get_shop_goods',
                'type' => 'best',
                'c_id' => 733
            );
            $gzGoodsList = unserialize(request_post("http://shop.gouchehui.com/index.php", $gzDbArr));
            S('gzGoodsList', $gzGoodsList, $this->losttime);
        }
        $this->assign('gzGoodsList', $gzGoodsList);

        /* 养护 */
        $yhGoodsList = S('yhGoodsList');
        if ($yhGoodsList == '') {
            $yhDbArr = array(
                'step' => 'get_shop_goods',
                'type' => 'best',
                'c_id' => 752
            );
            $yhGoodsList = unserialize(request_post("http://shop.gouchehui.com/index.php", $yhDbArr));
            S('yhGoodsList', $yhGoodsList, $this->losttime);
        }
        $this->assign('yhGoodsList', $yhGoodsList);
        /* 美容 */
        $mrGoodsList = S('mrGoodsList');
        if ($mrGoodsList == '') {
            $mrDbArr = array(
                'step' => 'get_shop_goods',
                'type' => 'best',
                'c_id' => 764
            );
            $mrGoodsList = unserialize(request_post("http://shop.gouchehui.com/index.php", $mrDbArr));
            S('mrGoodsList', $mrGoodsList, $this->losttime);
        }
        $this->assign('mrGoodsList', $mrGoodsList);

        /* 美容 */
        $nwGoodsList = S('nwGoodsList');
        if ($nwGoodsList == '') {
            $mrDbArr = array(
                'step' => 'get_shop_goods',
                'type' => 'new',
                'c_id' => 764
            );
            $nwGoodsList = unserialize(request_post("http://shop.gouchehui.com/index.php", $mrDbArr));
            S('nwGoodsList', $nwGoodsList, $this->losttime);
        }
        $this->assign('nwGoodsList', $nwGoodsList);


        $this->display();

    }

    /**
     * 新浪IP地址定位
     * @param $clientIP
     * @return mixed
     */
    public function sinaIP($clientIP)
    {
        $sinaIP = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=' . $clientIP;
        $IPinfo = json_decode(file_get_contents($sinaIP));
        $data['country'] = $IPinfo->country;
        $data['province'] = $IPinfo->province;
        $data['city'] = $IPinfo->city;
        return $data;
    }

    /**
     * 新浪IP地址定位
     * @param $clientIP
     * @return mixed
     */
    public function getNsShopGoods()
    {

    }

}