<?php
namespace Home\Controller;

use Think\Controller;

/**
 * Class CarController 汽车管理
 * @package Home\Controller
 */
class CarController extends CommonController
{


    private $losttime = '3600';//过期时间

    /**
     *空控制器
     */
    public function _empty()
    {
        $this->redirect('product_search');
    }
    /********************************************************底价车列表页***************************************************************************/

    /**
     *导航进入低价车列表页(不需要询价的低价车列表页)
     */
    public function product()
    {
        //添加城市筛选
        if (isset($_SESSION['province']) and $_SESSION['province'] != '') {
            $data['province_id'] = $_SESSION['province'];
            $data_index['province_id'] = $_SESSION['province'];
        } else {
            if (isset($_SESSION['city']) and $_SESSION['city'] != '') {
                $data['city_id'] = $_SESSION['city'];
                $data_index['city_id'] = $_SESSION['city'];
            }
        }
        $data['isdelete'] = 0;
        $data['is_xunjia'] = 1;
        $data['car_status'] = 1;
        //展示本城市4条最新需要询价的低价车
        $list = M('view_car_price')->field('id,brand_name,car_model_name,car_name,exterior_color_name,car_id,exterior_color_id')->where($data)->order('createtime desc')->limit('4')->select();
        foreach ($list as $key => $value) {
            $imgurl = M('car_exterior_color_image')->where(array('isdelete' => 0, 'car_id' => $value['car_id'], 'exterior_color_id' => $value['exterior_color_id']))->getField('imgurl');
            $list[$key]['imgurl'] = str_replace("type", 'big', $imgurl);
            $list[$key]['url'] = '/index.php/car/product_details?id=' . $value['id'];
        }
        $this->assign('list', $list);
        /*       //不需要询价的低价车展示
               $dijiache  = D('view_index_price')->where(array('isdelete'=>0,'isbaojia'=>1,'status'=>2,'car_status'=>1))->order('createtime desc')->group('model_id')->select();
               foreach ($dijiache as $key => $value) {
                   $data_index['car_id'] = $value['car_id'];
                   $data_index['interior_color_id'] = $value['interior_color_id'];

                   $min= M('car')->where(array('car_model_id'=>$value['model_id'],'isdelete'=>0,'status'=>1))->min('auth_price');//该车型下最便宜的车型
                   $dijiache[$key]['min'] =number_format($min/10000,2);

                   $max= M('car')->where(array('car_model_id'=>$value['model_id'],'isdelete'=>0,'status'=>1))->max('auth_price');//该车型下最贵的车型
                   $dijiache[$key]['max']=number_format($max/10000,2);

                   $dijiache[$key]['youhui'] = M('view_car_price')->where(array('car_model_id'=>$value['model_id'],'isdelete'=>0,'status'=>1))->min('discount');//该车型下最低的优惠车款
                   $data_index['exterior_color_id'] = $value['exterior_color_id'];
                   $data_index['status'] = 1;
                   $find = M('view_car_price')->where($data_index)->order('low_price asc')->find();
                   $dijiache[$key]['imgurl'] = M('car_exterior_color_image')->where(array('car_id'=>$value['car_id'],'isdelete'=>0,'exterior_color_id'=>$value['exterior_color_id']))->getField('imgurl');
                   $dijiache[$key]['imgurl'] = OSS.str_replace("type",'big',$dijiache[$key]['imgurl']);
                   $dijiache[$key]['index_id'] = $find['id'];
                   $dijiache[$key]['price'] =number_format($find['low_price']/10000,2);
                   $dijiache[$key]['auth_price'] =number_format($find['auth_price']/10000,2);
                   $dijiache[$key]['logo']=OSS.str_replace("type",'big',$find['logo']);
               }*/
        //获得所有首字母
        $brand_alif = D('view_index_price')->where(array('isdelete' => 0, 'isbaojia' => 1, 'status' => 2, 'car_status' => 1))->order('brand_alif')->group('brand_alif')->select();
        foreach ($brand_alif as $key => $value) {
            $brand_alif[$key]['list'] = D('index_car_price')->where(array('isdelete' => 0, 'brand_alif' => $value['brand_alif'], 'isbaojia' => 1, 'status' => 2, 'car_status' => 1))->group('brand_id')->select();
        }
//        $this->assign('res', $dijiache);

        $this->assign('brand_alif', $brand_alif);
        $this->display();

    }

    /**
     *ajax获得品牌下面所有的车型(不需要询价)
     */
    public function ajax_car_model()
    {
        if ($_POST) {
            $model['isdelete'] = 0;
            $model['brand_id'] = I('brandid');
            if (isset($_POST['brandid']) and $_POST['brandid'] != '') {
                $model = M('index_car_price')->where($model)->group('model_id')->select();
                if ($model) {
                    $data['status'] = 1;
                    $data['btypes'] = $model;
                    $this->ajaxReturn($data);
                } else {
                    $data['status'] = 0;
                    $this->ajaxReturn($data);
                }
            }
        }
    }

    /**
     *ajax返回所有不需要询价的低价
     */
    public function ajax_price_car()
    {
        if ($_POST) {
            //返回一个品牌下所有的车款
            if (isset($_POST['brandId']) and $_POST['brandId'] != '') {
                $datas['z.brand_id'] = I('brandId');
            }
            //返回一个车型下所有的车款
            if (isset($_POST['brandTypeId']) and $_POST['brandTypeId'] != '') {
                $datas['z.model_id'] = I('brandTypeId');
            }
            //判读是否有价格区间提交
            if (isset($_POST['minprice']) and $_POST['minprice'] != '') {
                $min = $_POST['minprice'] * 10000;
                $max = $_POST['maxprice'] * 10000;
                $datas['z.low_price'] = array(array('EGT', $min), array('ELT', $max));
            }
            if (isset($_POST['discount']) and !empty($_POST['discount'])) {
                $order['z.discount'] = I('discount');
            }
            if (isset($_POST['low_price']) and !empty($_POST['low_price'])) {
                $order['z.low_price'] = I('low_price');
            }

            $datas['z.isdelete'] = 0;
            $datas['z.isbaojia'] = 1;
            $datas['z.car_status'] = 1;
            $pagenum = I('pageNum');
            $count1 = D('view_index_price')->alias('z')->where($datas)->group('car_id')->select();
            $count = count($count1, 2);
            $Page = new \Think\PageAjax($count, $pagenum);
            $res = D('view_index_price')->alias('z')->join("RIGHT join (SELECT car_id,min(low_price) AS low_price FROM gch_view_index_price GROUP BY car_id) AS z1 ON z.car_id=z1.car_id AND z.low_price=z1.low_price")->group('z.car_id')->where($datas)->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            foreach ($res as $k => $v) {
                $res[$k]['discount'] = number_format($v['discount'] / 10000, 2);
                $res[$k]['low_price'] = number_format($v['low_price'] / 10000, 2);
                $res[$k]['auth_price'] = number_format($v['auth_price'] / 10000, 2);
                $res[$k]['exterior_img'] = OSS . str_replace("type", 'big', $v['exterior_img']);
                $res[$k]['url'] = '/index.php/car/details_unquotes?id=' . $v['id'];
                $res[$k]['logo'] = OSS . str_replace("type", 'big', $v['logo']);
            }
            if ($res) {
                $data['status'] = 1;
                $data['list'] = $res;
                $data['count'] = $count;
                $data['pagelist'] = $Page->show();
                $this->ajaxReturn($data);
            } else {
                $data['status'] = 0;
                $data['info'] = "你选择的条件里暂无低价车";
                $this->ajaxReturn($data);
            }
        }
    }

    /********************************************************优惠买车列表页***************************************************************************/

    /**
     *头部搜索，条件筛选,首页更多车型进入的低价车列表页(需要询价的低价车列表页)
     */
    public function product_search()
    {
        //模糊搜索
        $select = I('select');
        if (!empty($select)) {
            //搜索关键字插入数据库
            $search_key = M('search_key')->where(array('search_key' => $select, 'isdelete' => 0))->find();
            if ($search_key) {
                M('search_key')->where(array('id' => $search_key['id']))->setInc('search_count'); // 搜索量+1
            } else {
                $arr['id'] = md5(microtime());
                $arr['search_key'] = $select;
                $arr['search_count'] = 1;
                M('search_key')->add($arr);
            }
            //模糊查询
            $car_list['_string'] = "concat (brand_name,car_model_name,car_name) like '%" . $select . "%'";
            //不需要询价的
            $car_list_un['_string'] = "concat (brand_name,model_name) like '%" . $select . "%'";
            /* $car_list_un['status'] =1;*/
            $car_list_un['isbaojia'] = 1;
            $car_list_un['isdelete'] = 0;
            $car_list_un['car_status'] = 1;
            $car_nu_list = D('view_index_price')->where($car_list_un)->group('model_id')->select();//获得公开底价的查询
            foreach ($car_nu_list as $k => $v) {
                $car_nu_list[$k]['discount'] = number_format(D('view_car_price')->where(array('car_model_id' => $v['model_id'], 'isdelete' => 0, 'status' => 1))->min('discount') / 10000, 2);
                $car_nu_list[$k]['car_model_imageurl'] = OSS . str_replace('type', 'small', $v['car_model_imageurl']);

                $min = M('car')->where(array('car_model_id' => $v['model_id'], 'isdelete' => 0, 'status' => 1))->min('auth_price');//该车型下最便宜的车型
                $car_nu_list[$k]['min'] = number_format($min / 10000, 2);
                $max = M('car')->where(array('car_model_id' => $v['model_id'], 'isdelete' => 0, 'status' => 1))->max('auth_price');//
                $car_nu_list[$k]['max'] = number_format($max / 10000, 2);
                $find = M('view_car_price')->where(array('car_id' => $v['car_id'], 'exterior_color_id' => $v['exterior_color_id'], 'interior_color_id' => $v['interior_color_id'], 'isdelete' => 0))->order('low_price asc')->find();
                $car_nu_list[$k]['low_price'] = number_format($find['low_price'] / 10000, 2);
                $car_nu_list[$k]['url'] = '/index.php/car/product_details?id=' . $find['id'];


            }

        } else {
            if (isset($_GET['select']) and $select == '') {
                $this->redirect('Index/index');
            }
            /*if(isset($_SESSION['province']) and $_SESSION['province'] !=''){
                $car_list['province_id'] = $_SESSION['province'];
            }else{
                if(isset($_SESSION['city']) and $_SESSION['city'] !=''){
                    $car_list['city_id'] = $_SESSION['city'];
                }
            }
            $car_list['show_index'] = 1;后期数据多的时候展开*/
        }

        $this->assign('select', $select);//拿出来，防止没传入到模板中，debug报错。

        //添加城市筛选
        /*  $brand=M('view_car_price')->where(array('isdelete'=>0,'is_xunjia'=>1))->group('brand_id')->select();
        $this->assign('brand',$brand);*/
        $car_types['isdelete'] = 0;
        $car_types['is_xunjia'] = 1;
        $car_types['car_status'] = 1;
        //获得所有报价表里的汽车分类
        $car_type = S('car_type');
        if (empty($car_type)) {
            $car_type = D('view_car_price')->where($car_types)->group('car_type_id')->select();
            S('car_type', $car_type, $this->losttime);
        }
        //获得所有首字母

        $brand_alif = S('brand_alif');
        if (empty($brand_alif)) {
            $brand_alif = D('view_car_price')->where($car_types)->order('brand_alif')->group('brand_alif')->select();
            foreach ($brand_alif as $key => $value) {
                $list['isdelete'] = 0;
                $list['is_xunjia'] = 1;
                $list['car_status'] = 1;
                $list['brand_alif'] = $value['brand_alif'];
                $brand_alif[$key]['list'] = M('view_car_price')->where($list)->group('brand_id')->select();
            }
            S('brand_alif', $brand_alif, $this->losttime);
        }

        //没有条件时候的页面展示
        //添加城市筛选
        if (isset($_SESSION['province']) and $_SESSION['province'] != '') {
            $car_list['province_id'] = $_SESSION['province'];
        } else {
            if (isset($_SESSION['city']) and $_SESSION['city'] != '') {
                $car_list['city_id'] = $_SESSION['city'];
            }
        }
        $car_list['isdelete'] = 0;
        $car_list['is_xunjia'] = 1;
        $car_list['car_status'] = 1;
        $car_list_1 = D('view_car_price')->where($car_list)->order('createtime')->group('car_model_id')->select();
        foreach ($car_list_1 as $key => $value) {
            $car_list_1[$key]['car_model_imageurl'] = OSS . str_replace('type', 'big', $value['car_model_imageurl']);
            $car_list_1[$key]['url'] = '/index.php/car/product_details?id=' . $value['id'];

            $min = M('car')->where(array('car_model_id' => $value['car_model_id'], 'isdelete' => 0, 'status' => 1))->min('auth_price');//该车型下最便宜的车型
            $car_list_1[$key]['min'] = number_format($min / 10000, 2);
            $max = M('car')->where(array('car_model_id' => $value['car_model_id'], 'isdelete' => 0, 'status' => 1))->max('auth_price');//
            $car_list_1[$key]['max'] = number_format($max / 10000, 2);
        }

        $this->assign('car_type', $car_type);
        $this->assign('brand_alif', $brand_alif);
        $this->assign('car_list_1', $car_list_1);
        $this->assign('car_nu_list', $car_nu_list);
        $this->display();
    }

    /**
     * 车型选车--根据品牌找车型
     * @return [type] [description]
     */
    public function ajax_query_model()
    {
        $info = D('view_car_price')->where(array('brand_id' => I('brand_id'), 'isdelete' => 0))->distinct(true)->field('car_model_id,car_model_name')->select();
        $this->ajaxReturn($info);
    }

    /**
     *车型选车---异步加载列表
     */
    public function ajax_price_car_xunjia()
    {
        if ($_POST) {
            $datas['is_xunjia'] = 1;
            $datas['isdelete'] = 0;
            $datas['car_status'] = 1;
            //返回一个品牌下所有的车型
            if (isset($_POST['brandId']) and $_POST['brandId'] != '') {
                $datas['brand_id'] = I('brandId');
            }

            if (isset($_POST['brandTypeId']) and $_POST['brandTypeId'] != '') {
                $datas['car_type_id'] = I('brandTypeId');
            }

            //返回一个分类下所有的车型
            /* if(isset($_POST['car_model_id']) and $_POST['car_model_id']!=''){
                 $datas['car_model_id'] = I('car_model_id');
             }*/

            if (isset($_POST['car_xj_count']) and $_POST['car_xj_count'] != '') {
                $order['car_xj_count'] = I('car_xj_count');
            }

            //判读是否有价格区间提交
            if (isset($_POST['minprice']) and $_POST['minprice'] != '') {
                if ($_POST['minprice'] != 0) {
                    $min = $_POST['minprice'] * 10000;
                } else {
                    $min = 0;
                }
                $max = $_POST['maxprice'] * 10000;
                $datas['auth_price'] = array(array('EGT', $min), array('ELT', $max));
            }

            $pagenum = I('pageNum');
            /* $count =D('view_car_price')->where($datas)->count();*/
            $count1 = D('view_car_price')->where($datas)->group('car_model_id')->select();
            $count = count($count1, 2);
            $Page = new \Think\PageAjax($count, $pagenum);
            $res = D('view_car_price')->where($datas)->order($order)->group('car_model_id')->limit($Page->firstRow . ',' . $Page->listRows)->select();
            foreach ($res as $key => $value) {
                $res[$key]['car_model_imageurl'] = OSS . str_replace("type", 'big', $value['car_model_imageurl']);
                $res[$key]['url'] = '/index.php/car/product_details?id=' . $value['id'];
                $min = M('car')->where(array('car_model_id' => $value['car_model_id'], 'isdelete' => 0, 'status' => 1))->min('auth_price');//该车型下最便宜的车型
                $res[$key]['min'] = number_format($min / 10000, 2);
                $max = M('car')->where(array('car_model_id' => $value['car_model_id'], 'isdelete' => 0, 'status' => 1))->max('auth_price');
                $res[$key]['max'] = number_format($max / 10000, 2);
            }
            if ($res) {
                $data['status'] = 1;
                $data['list'] = $res;
                $data['count'] = $count;
                $data['pagelist'] = $Page->show();
                $this->ajaxReturn($data);
            } else {
                $data['status'] = 0;
                $data['list'] = $res;
                $data['info'] = "你选择的条件里暂无低价车";
                $this->ajaxReturn($data);
            }
        } else {
            $this->redirect('Index/index');
        }
    }

    /********************************************************公开底价车详情页***************************************************************************/
    /**
     *不需要询价的底价车详情页
     */
    public function details_unquotes()
    {
        //从底价车列表页传的id(index_car_price表中的id)
        if (I('id')) {
            $index_car_id = I('id');
        }
        //从当前页面其他车款传来的id(car_price表中的id)
        if (I('car_price_id')) {
            $new_car = D('view_car_price')->where(array('id' => I('car_price_id')))->find();
            $msg = M('index_car_price')->where(array('car_id' => $new_car['car_id'], 'exterior_color_id' => $new_car['exterior_color_id'], 'interior_color_id' => $new_car['interior_color_id'], 'isdelete' => 0))->find();
            if ($msg) {
                $index_car_id = $msg['id'];
            } else {
                $this->redirect('product_details', array('id' => I('car_price_id')));
            }
        }

        $info = M("index_car_price")->where(array('id' => $index_car_id, 'isdelete' => 0))->find();

        if (!empty($info)) {
            $info['auth_price'] = M('car')->where(array('id' => $info['car_id'], 'isdelete' => 0))->getField('auth_price');

            //寻找这个款式的最低价
            if (session('city')) {
                $data_area['city_id'] = session('city');
            } elseif (session('province')) {
                $data_area['province_id'] = session('province');
            }/*where($data_area)->*/
            $car_info = D("view_car_price")->where(array('car_id' => $info['car_id'], 'exterior_color_id' => $info['exterior_color_id'], 'interior_color_id' => $info['interior_color_id'], 'is_xunjia' => 2))->order('low_price asc')->limit(0, 1)->find();
            if ($car_info['low_price'] <= 200000) {
                $car_info['deposit'] = 2000;
            } elseif ($car_info['low_price'] <= 500000) {
                $car_info['deposit'] = 5000;
            } else {
                $car_info['deposit'] = 10000;
            }
            $auth_price = M('car')->where(array('id' => $car_info['car_id']))->getField('auth_price');
            $car_info['auth_price'] = number_format($auth_price / 10000, 2);
            $car_info['low_price'] = $car_info['low_price'];
            $this->assign('info', $info);

            $this->assign('car_info', $car_info);
            //颜色图
            $color_image = M("car_exterior_color_image")->where(array('isdelete' => 0, 'car_id' => $car_info['car_id'], 'exterior_color_id' => $car_info['exterior_color_id']))->find();
            $color_image['imgurl'] = str_replace("type", 'big', $color_image['imgurl']);
            $this->assign('color_image', $color_image);
            //所有的颜色图
            $color_images = M("car_exterior_color_image")->where(array('isdelete' => 0, 'car_id' => $car_info['car_id']))->select();
            foreach ($color_images as $key => $value) {
                $color_images[$key]['imgurl'] = str_replace("type", 'big', $value['imgurl']);
                $exterior_color = M('exterior_color')->where(array('isdelete' => 0, 'special_price_carid' => $value['exterior_color_id']))->find();
                $color_images[$key]['color_name'] = M('color')->where(array('isdelete' => 0, 'id' => $exterior_color['color_id']))->getField('color_name');
            }
            /*show_bug($color_images);*/
            $this->assign('color_images', $color_images);
            //同类车款
            $other_car_name = D('view_car_price')->where(array('brand_id' => $car_info['brand_id'], 'car_model_id' => $car_info['car_model_id'], 'isdelete' => 0, 'car_status' => 1))->group('car_id')->order('createtime desc')->select();
            $this->assign('other_car_name', $other_car_name);
            //该车款下所有的外观颜色
            $exterior_color_info = D("view_exterior_color")->where(array('car_id' => $car_info['car_id'], 'isdelete' => 0))->group('color_id')->select();
            //默认排名第一
            foreach ($exterior_color_info as $k => $v) {
                if ($k != 0 and $v['id'] == $car_info['exterior_color_id']) {
                    $a = $exterior_color_info[0];
                    $exterior_color_info[0] = $exterior_color_info[$k];
                    unset($exterior_color_info[$k]);
                    $exterior_color_info[$k] = $a;

                }
            }

            $this->assign('exterior_color_info', $exterior_color_info);
            //该车款下所有的内饰颜色
            // $interior_color_info= D("view_interior_color")->where(array('car_id'=>$car_info['car_id']))->group('color_id')->select();
            $interior_color_info = M('view_interior_color')->where(array('car_id' => $car_info['car_id'], 'isdelete' => 0))->order('createtime')->select();
            foreach ($interior_color_info as $k => $v) {
                if ($k != 0 and $v['id'] == $car_info['interior_color_id']) {
                    $a = $interior_color_info[0];
                    $interior_color_info[0] = $interior_color_info[$k];
                    unset($interior_color_info[$k]);
                    $interior_color_info[$k] = $a;

                }
            }
            foreach ($interior_color_info as $k => $v) {
                $interior_color_info[$k]['color_value'] = explode(' ', $interior_color_info[$k]['color_value']);
                if (count($interior_color_info[$k]['color_value']) == 1) {
                    $interior_color_info[$k]['color_value'][1] = $interior_color_info[$k]['color_value'][0];
                }
            }
            $this->assign('interior_color_info', $interior_color_info);
            //判断是否已经有关注了
            if (isset($_SESSION['user_id'])) {
                $attention['user_id'] = $_SESSION['user_id'];
                $attention['car_model_id'] = $car_info['car_model_id'];
                $attention['isdelete'] = 0;
                $find = M('user_attention_car_model')->where($attention)->find();
                if ($find) {
                    $this->assign('attention', $find);
                }
            }
            //查找大礼包
            if ($info['brand_id'] == 66) {
                $data['brand_id'] = 66;
            }
            $data['min_price'] = array('ELT', $car_info['low_price']);
            $data['max_price'] = array('EGT', $car_info['low_price']);
            $data['isdelete'] = 0;
            $package = M("buy_car_package")->where($data)->find();
            $package['imgurl'] = str_replace('type', 'big', $package['imgurl']);
            $this->assign('package', $package);

            //汽车外观图type=0
            $color_image_0['type'] = 0;
            $color_image_0['car_id'] = $info['car_id'];
            $color_image_0['exterior_color_id'] = $info['exterior_color_id'];
            $color_image_0['interior_color_id'] = $info['interior_color_id'];
            $color_image_0['isdelete'] = 0;
            $car_color_image_0 = M('car_color_image')->where($color_image_0)->limit('5')->select();
            foreach ($car_color_image_0 as $key => $value) {
                $car_color_image_0[$key]['imgurl'] = str_replace("type", 'big', $value['imgurl']);
            }

            //汽车内饰图type=1
            $color_image_1['type'] = 1;
            $color_image_1['car_id'] = $info['car_id'];
            $color_image_1['exterior_color_id'] = $info['exterior_color_id'];
            $color_image_1['interior_color_id'] = $info['interior_color_id'];
            $color_image_1['isdelete'] = 0;
            $car_color_image_1 = M('car_color_image')->where($color_image_1)->limit('5')->select();
            foreach ($car_color_image_1 as $key => $value) {
                $car_color_image_1[$key]['imgurl'] = str_replace("type", 'big', $value['imgurl']);
            }
            //汽车空间图type=2
            $color_image_2['type'] = 2;
            $color_image_2['car_id'] = $info['car_id'];
            $color_image_2['exterior_color_id'] = $info['exterior_color_id'];
            $color_image_2['interior_color_id'] = $info['interior_color_id'];
            $color_image_2['isdelete'] = 0;
            $car_color_image_2 = M('car_color_image')->where($color_image_2)->limit('5')->select();
            foreach ($car_color_image_2 as $key => $value) {
                $car_color_image_2[$key]['imgurl'] = str_replace("type", 'big', $value['imgurl']);
            }
            //汽车细节图type=3
            $color_image_3['type'] = 3;
            $color_image_3['car_id'] = $info['car_id'];
            $color_image_3['exterior_color_id'] = $info['exterior_color_id'];
            $color_image_3['interior_color_id'] = $info['interior_color_id'];
            $color_image_3['isdelete'] = 0;
            $car_color_image_3 = M('car_color_image')->where($color_image_3)->limit('5')->select();
            foreach ($car_color_image_3 as $key => $value) {
                $car_color_image_3[$key]['imgurl'] = str_replace("type", 'big', $value['imgurl']);
            }

            $this->assign('car_color_image_0', $car_color_image_0);
            $this->assign('car_color_image_1', $car_color_image_1);
            $this->assign('car_color_image_2', $car_color_image_2);
            $this->assign('car_color_image_3', $car_color_image_3);

        } else {
            $this->redirect('product');
        }
        $this->display();
    }

    /**
     *公开底价车----上牌地选择
     */
    public function ajax_unquotes_shangpai()
    {
        $car_price_id = I('car_price_id');
        if (!empty($car_price_id)) {
            $area_info = M('sales_area')->where(array('car_price_id' => $car_price_id, 'isdelete' => 0))->select();
            foreach ($area_info as $k => $v) {
                $uccities['uccities'][] = $v['sales_area_name'];
            }

            $this->ajaxReturn($uccities);
        }
    }

    /**
     *获得汽车最近一周的价格
     */
    public function get_car_trend()
    {
        if (IS_POST) {
            $data['car_id'] = I('car_id');
            $data['exterior_color_id'] = I('exterior_color_id');
            $data['interior_color_id'] = I('interior_color_id');
            $data['price'] = array('neq', 0);
            $data['low_price'] = array('neq', 0);
            $data['isdelete'] = '0';
            //$data['createtime'] = array(array('EGT',date('Y-m-d',time()-3600*24*7)),array('ELT',date('Y-m-d',time())));

            $price = M('price_trend')->where($data)->field('price')->order('createtime')->select();
            $low_price = M('price_trend')->where($data)->field('low_price')->order('createtime')->select();
            $date = M('price_trend')->where($data)->field('createtime')->order('createtime')->select();
            if ($price) {
                $arr = array();
                $arr_price = array();
                $rand = 1.1;
                foreach ($price as $k) {
                    $arr[] = $k['price'] * $rand;
                    $arr_price[] = intval($k['price']);
                }
                $arr_low_price = array();
                foreach ($low_price as $k) {
                    $arr_low_price[] = intval($k['low_price']);
                }
                $arr_date = array();
                foreach ($date as $k) {
                    $arr_date[] = date("m-d", strtotime($k['createtime']));
                }
                $ret['status'] = 1;
                $ret['price'] = $arr_price;
                $ret['low_price'] = $arr_low_price;
                $ret['other_price'] = $arr;
                $ret['date'] = $arr_date;
            } else {
                $ret['status'] = 0;
            }
            $this->ajaxReturn($ret);
        }
    }

    /**
     *ajax根据颜色的选择返回不同的价格
     */
    public function ajax_unquotes_color()
    {
        $z = D('view_car_price')->where(array('car_id' => I('car_id'), 'exterior_color_id' => I('excolor'), 'interior_color_id' => I('incolor'), 'isdelete' => 0))->order('low_price asc')->limit(0, 1)->find();
        if ($z) {
            $info = M("index_car_price")->where(array('car_id' => I('car_id'), 'exterior_color_id' => I('excolor'), 'interior_color_id' => I('incolor'), 'isdelete' => 0))->select();
            if ($info) {
                $data['name_4s'] = $z['city_name'];//城市名称
                $data['status'] = 2;
                $data['newPrice'] = $z['low_price'];
                $data['car_price_id'] = $z['id'];
            } else {
                $data['status'] = 1;
                $data['next'] = U('product_details', array('id' => $z['id']));
            }
        } else {
            $data['status'] = 0;
            $data['info'] = '暂无报价数据';
        }
        $this->ajaxReturn($data);
    }

    /********************************************************不公开底价车详情页***************************************************************************/
    /**
     *底价车详情页
     */
    public function product_details()
    {
        G('begin');
        //判断是否有低价车ID
        if (isset($_GET['id']) and $_GET['id'] != '') {
            $id = I('id');
            $model = M('view_car_price')->where(array('id' => $id))->find();
            $data['isdelete'] = 0;
            $data['is_xunjia'] = 1;
            $data['car_status'] = 1;
            $data['car_id'] = $model['car_id'];
            $ress = M('view_car_price')->where($data)->find();
            if ($ress) {
                $res = M('view_car_price')->where(array('id' => $id))->find();
                $res['auth_price'] = number_format(M('car')->where(array('id' => $res['car_id'], 'isdelete' => 0))->getField('auth_price') / 10000, 2);
                //判断选中的城市或者省份是否有报价
                if (isset($_SESSION['province'])) {
                    $this->name = M('province')->where(array('id' => $_SESSION['province']))->getField('province_name');
                    $data_p['province_id'] = $_SESSION['province'];
                    $data_p['isdelete'] = 0;
                    $data_p['exterior_color_id'] = $res['exterior_color_id'];
                    $data_p['interior_color_id'] = $res['interior_color_id'];
                    $find_pro = M('view_car_price')->where($data_p)->find();
                    if ($find_pro) {
                        $this->assign('find_pro', $find_pro);
                    }
                } else {
                    if (isset($_SESSION['city'])) {
                        $this->name = M('city')->where(array('id' => $_SESSION['city']))->getField('city_name');
                        $data_p['city_id'] = $_SESSION['city'];
                        $data_p['isdelete'] = 0;
                        $data_p['exterior_color_id'] = $res['exterior_color_id'];
                        $data_p['interior_color_id'] = $res['interior_color_id'];
                        $find_city = M('view_car_price')->where($data_p)->find();
                        if ($find_city) {
                            $this->assign('find_city', $find_city);
                        }
                    } else {
                        $this->name = '全国';
                        $data_p['isdelete'] = 0;
                        $data_p['exterior_color_id'] = $res['exterior_color_id'];
                        $data_p['interior_color_id'] = $res['interior_color_id'];
                        $find_con = M('view_car_price')->where($data_p)->find();

                        $this->assign('find_con', $find_con);
                    }
                }
                //判断是否有该车款
                foreach ($res as $key => $value) {
                    $var = explode(" ", $value['interior_color_id']);
                    if (isset($var[1]) and $var[1] != '') {
                        $value['interior_color_id'] = preg_replace("/\s|　/", "", $value['interior_color_id']);
                    }
                }
                //车款名称
                $car_list = M('view_car_price')->where(array('isdelete' => 0, 'car_model_id' => $res['car_model_id'], 'car_status' => 1))->group('car_id')->select();
                //外观颜色
                $exterior_color_list = M('view_exterior_color')->where(array('isdelete' => 0, 'car_id' => $res['car_id']))->group('color_id')->select();
                foreach ($exterior_color_list as $k => $v) {
                    //默认排名第一
                    if ($k != 0 and $v['id'] == $res['exterior_color_id']) {
                        $a = $exterior_color_list[0];
                        $exterior_color_list[0] = $exterior_color_list[$k];
                        unset($exterior_color_list[$k]);
                        $exterior_color_list[$k] = $a;
                        //$exterior_color_list[0] = $exterior_color_list[$k];
                    }
                }
                //内饰颜色
                $interior_color = M('interior_color')->where(array('isdelete' => 0, 'car_id' => $res['car_id']))->group('color_id')->select();
                foreach ($interior_color as $k => $v) {
                    $incolor = M('color')->where(array('isdelete' => 0, 'id' => $v['color_id']))->find();
                    $var = explode(" ", $incolor['color_value']);
                    if (isset($var[1]) and $var[1] != '') {
                        $interior_color[$k]['color_value'] = $var;
                    } else {
                        $interior_color[$k]['color_value'] = $incolor['color_value'];
                    }
                    $interior_color[$k]['color_name'] = $incolor['color_name'];
                    //默认排名第一
                    if ($k != 0 and $v['id'] == $res['interior_color_id']) {
                        $a = $interior_color[0];
                        $interior_color[0] = $interior_color[$k];
                        unset($interior_color[$k]);
                        $interior_color[$k] = $a;
                        //$interior_color[0] = $interior_color[$k];
                    }
                }
                $this->assign('info', $res);
                $this->assign('car_list', $car_list);
                $this->assign('exterior_color_list', $exterior_color_list);
                $this->assign('interior_color', $interior_color);
                //判断是否已经有关注了
                if (isset($_SESSION['user_id'])) {
                    $attention['user_id'] = $_SESSION['user_id'];
                    $attention['car_model_id'] = $res['car_model_id'];
                    $attention['isdelete'] = 0;
                    $find = M('user_attention_car_model')->where($attention)->find();
                    if ($find) {
                        $this->assign('attention', $find);
                    }
                    //判断这个用户之前有没有询价过(如果没询价过有一次免费看底价的机会)
                    $find_price = M('pay')->where(array('user_id' => $_SESSION['user_id'], 'status' => array('neq', '0'), 'pay_obj' => 1, 'isdelete' => 0))->find();

                    //添加浏览记录
                    if ($_COOKIE['history']) {
                        $history = unserialize($_COOKIE['history']);
                        array_unshift($history, $id);
                        /* 去除重复记录 */
                        $history = array_unique($history);
                        //如果记录数量多余2则去除
                        while (count($history) > 2) {
                            array_pop($history); //弹出
                        }
                        cookie('history', serialize($history), array('expire' => time() + 3600 * 24 * 30, 'domain' => 'gouchehui.com'));
                    } else {
                        $history = serialize(array($id));
                        cookie('history', $history, array('expire' => time() + 3600 * 24 * 30, 'domain' => 'gouchehui.com'));
                    }
                    //添加浏览记录结束
                }
                //汽车轮播展示
                $color_image['car_id'] = $res['car_id'];
                $color_image['isdelete'] = 0;
                $car_color_image = M('car_exterior_color_image')->where($color_image)->group('exterior_color_id')->field('car_id,exterior_color_id,imgurl')->select();
                foreach ($car_color_image as $key => $value) {
                    $car_color_image[$key]['imgurl'] = str_replace("type", 'big', $value['imgurl']);
                    $exterior_color = M('exterior_color')->where(array('isdelete' => 0, 'id' => $value['exterior_color_id']))->find();
                    $car_color_image[$key]['color_name'] = M('color')->where(array('isdelete' => 0, 'id' => $exterior_color['color_id']))->getField('color_name');
                }
                //汽车外观图type=0
                $color_image_0['type'] = 0;
                $color_image_0['car_id'] = $res['car_id'];
                $color_image_0['exterior_color_id'] = $res['exterior_color_id'];
                $color_image_0['interior_color_id'] = $res['interior_color_id'];
                $color_image_0['isdelete'] = 0;
                $car_color_image_0 = M('car_color_image')->where($color_image_0)->limit('5')->select();
                foreach ($car_color_image_0 as $key => $value) {
                    $car_color_image_0[$key]['imgurl'] = str_replace("type", 'big', $value['imgurl']);
                }

                //汽车外观图type=1
                $color_image_1['type'] = 1;
                $color_image_1['car_id'] = $res['car_id'];
                $color_image_1['exterior_color_id'] = $res['exterior_color_id'];
                $color_image_1['interior_color_id'] = $res['interior_color_id'];
                $color_image_1['isdelete'] = 0;
                $car_color_image_1 = M('car_color_image')->where($color_image_1)->limit('5')->select();
                foreach ($car_color_image_1 as $key => $value) {
                    $car_color_image_1[$key]['imgurl'] = str_replace("type", 'big', $value['imgurl']);
                }
                //汽车空间图type=2
                $color_image_2['type'] = 2;
                $color_image_2['car_id'] = $res['car_id'];
                $color_image_2['exterior_color_id'] = $res['exterior_color_id'];
                $color_image_2['interior_color_id'] = $res['interior_color_id'];
                $color_image_2['isdelete'] = 0;
                $car_color_image_2 = M('car_color_image')->where($color_image_2)->limit('5')->select();
                foreach ($car_color_image_2 as $key => $value) {
                    $car_color_image_2[$key]['imgurl'] = str_replace("type", 'big', $value['imgurl']);
                }
                //汽车细节图type=3
                $color_image_3['type'] = 3;
                $color_image_3['car_id'] = $res['car_id'];
                $color_image_3['exterior_color_id'] = $res['exterior_color_id'];
                $color_image_3['interior_color_id'] = $res['interior_color_id'];
                $color_image_3['isdelete'] = 0;
                $car_color_image_3 = M('car_color_image')->where($color_image_3)->limit('5')->select();
                foreach ($car_color_image_3 as $key => $value) {
                    $car_color_image_3[$key]['imgurl'] = str_replace("type", 'big', $value['imgurl']);
                }

                //问题咨询
                $question_arr['isdelete'] = 0;
                $question_arr['car_brand_id'] = $res['brand_id'];
                $question_arr['car_model_id'] = $res['car_model_id'];
                $question_arr['status'] = 1;
                $question = M('car_question')->where($question_arr)->select();

                $this->all_count = M('car_question')->where($question_arr)->count();
                $question_type = M('car_question')->where($question_arr)->group('question_type')->select();

                foreach ($question_type as $k => $v) {
                    $question_arr['question_type'] = $v['question_type'];
                    $question_type[$k]['count'] = M('car_question')->where($question_arr)->count();
                }
                //后面维护数据的时候开启
                $x_arr = array("09/12", "09/13", "09/14", "09/15", "09/16", "09/17", "09/20");
                $t = "[";
                foreach ($x_arr as $v) {
                    $t .= "'$v'" . ',';
                }
                $newstr = substr($t, 0, strlen($t) - 1);
                $newstr .= "]";
                $this->assign('newstr', $newstr);
                $this->assign('question', $question);
                $this->assign('question_type', $question_type);
                $this->assign('find_price', !empty($find_price) ? $find_price : null);
                $this->assign('car_color_image', $car_color_image);
                $this->assign('car_color_image_0', $car_color_image_0);
                $this->assign('car_color_image_1', $car_color_image_1);
                $this->assign('car_color_image_2', $car_color_image_2);
                $this->assign('car_color_image_3', $car_color_image_3);

            } else {
                //去不需要询价的详情页
                $data_xunjia['isdelete'] = 0;
                $data_xunjia['is_xunjia'] = 2;
                $data_xunjia['car_id'] = $model['car_id'];
                $res_xunjia = M('view_car_price')->where($data_xunjia)->find();
                if ($res_xunjia) {
                    $msg = M('index_car_price')->where(array('car_id' => $res_xunjia['car_id'], 'exterior_color_id' => $model['exterior_color_id'], 'interior_color_id' => $model['interior_color_id'], 'isdelete' => 0))->find();
                    if ($msg) {
                        $this->redirect('Car/details_unquotes', array('id' => $msg['id']));
                    } else {
                        $this->redirect('product_search');
                    }
                } else {
                    $this->redirect('product_search');
                }
                /* $this->redirect('product_search'); */
            }
        } else {
            $this->redirect('product_search');
        }
        /*G('end');echo G('begin','end').'s';*/
        $this->display();
    }

    /**
     *上牌地城市选择
     */
    public function ajax_shangpai()
    {

        if (isset($_POST['id']) and $_POST['id'] != '') {
            $car_id = M('view_car_price')->where(array('isdelete' => 0, 'id' => $_POST['id']))->getField('car_id');
            $excolor = I('excolor');
            $incolor = I('incolor');
            foreach ($_POST['bpcities'] as $k => $v) {
                //判断是否为全国
                if ($v == '001') {
                    $data_001['isdelete'] = 0;
                    $data_001['car_id'] = $car_id;
                    $data_001['exterior_color_id'] = $excolor;
                    $data_001['interior_color_id'] = $incolor;
                    $res_001 = M('view_car_price')->where($data_001)->order('discount asc')->find();
                    $find = M('sales_area')->where(array('isdelete' => 0, 'car_price_id' => $res_001['id']))->select();
                    if ($find) {
                        $newArr[$k] = $find;
                    }
                } else {
                    //判断是否为省份
                    $province = M('province')->where(array('id' => $v, 'isdelete' => 0))->find();
                    if ($province) {
                        $data_province['isdelete'] = 0;
                        $data_province['car_id'] = $car_id;
                        $data_province['exterior_color_id'] = $excolor;
                        $data_province['interior_color_id'] = $incolor;
                        $data_province['province_id'] = $v;
                        $res_province = M('view_car_price')->where($data_province)->order('discount asc')->find();

                        $find = M('sales_area')->where(array('isdelete' => 0, 'car_price_id' => $res_province['id']))->select();
                        if ($find) {
                            $newArr[$k] = $find;
                        }

                    } else {
                        $data_city['isdelete'] = 0;
                        $data_city['car_id'] = $car_id;
                        $data_city['exterior_color_id'] = $excolor;
                        $data_city['interior_color_id'] = $incolor;
                        $data_city['city_id'] = $v;
                        $city = M('city')->where(array('id' => $v, 'isdelete' => 0))->find();
                        if ($city) {
                            $res_city = M('view_car_price')->where($data_city)->order('discount asc')->find();
                            $find = M('sales_area')->where(array('isdelete' => 0, 'car_price_id' => $res_city['id']))->select();
                            if ($find) {
                                $newArr[$k] = $find;
                            }
                        }
                    }
                }
            }
            $temp_array = $newArr;
            $sel_city = '';
            //用,号分割成一个字符串
            foreach ($temp_array as $key => $value) {
                foreach ($temp_array[$key] as $k => $v) {
                    $sel_city .= $v['sales_area_name'] . ',';
                }
            }
            //去掉字符串中最后的一个,
            $newstr = substr($sel_city, 0, strlen($sel_city) - 1);
            //组合后的字符串用，分割成数组
            $sel_city = array_unique(explode(',', $newstr));
            //如果之前询价的城市一样不需要再次询价
            if ($sel_city['0'] != '') {
                $re['uccities'] = $sel_city;
            } else {
                $re['uccities'] = '';
            }
            $this->ajaxReturn($re);
        } else {
            if ($_SESSION['user_id'] == '') {
                $data['status'] = '-1';
                $data['info'] = '您还没有登录';
            } else {
                $data['status'] = '-2';
                $data['info'] = '系统出错请稍后从试';
            }
            $this->ajaxReturn($data);
        }
    }

    /**
     *ajax询价提醒所询价的城市是否能上牌
     */
    public function ajax_xunjia_tixing()
    {
        if (isset($_POST)) {

            $car_id = M('view_car_price')->where(array('isdelete' => 0, 'id' => $_POST['car']))->getField('car_id');
            $excolor = I('excolor');
            $incolor = I('incolor');
            foreach ($_POST['bpcities'] as $k => $v) {
                if ($v == '001') {
                    /*$_POST['bpcities'][$k]['sales_area_name'] = '全国';*/
                    $data_001['isdelete'] = 0;
                    $data_001['car_id'] = $car_id;
                    $data_001['exterior_color_id'] = $excolor;
                    $data_001['interior_color_id'] = $incolor;
                    $res_001 = M('view_car_price')->where($data_001)->order('discount asc')->find();
                    $find = M('sales_area')->where(array('isdelete' => 0, 'car_price_id' => $res_001['id'], 'sales_area_name' => $_POST['upCardCity'], 'sales_area_level' => 1))->find();
                    if ($find) {
                        $newArr[$k]['cityName'] = "全国";
                        $newArr[$k]['isYes'] = '1';
                    } else {
                        $newArr[$k]['cityName'] = "全国";
                        $newArr[$k]['isYes'] = '0';
                    }
                } else {
                    $province = M('province')->where(array('id' => $v, 'isdelete' => 0))->find();
                    if ($province) {
                        /*$_POST['bpcities'][$k]['sales_area_name'] = $province['province_name'];*/
                        $data_province['isdelete'] = 0;
                        $data_province['car_id'] = $car_id;
                        $data_province['exterior_color_id'] = $excolor;
                        $data_province['interior_color_id'] = $incolor;
                        $data_province['province_id'] = $v;
                        $res_province = M('view_car_price')->where($data_province)->order('low_price asc')->find();

                        $find = M('sales_area')->where(array('isdelete' => 0, 'car_price_id' => $res_province['id'], 'sales_area_name' => $_POST['upCardCity'], 'sales_area_level' => 2))->find();
                        if ($find) {
                            $newArr[$k]['cityName'] = $province['province_name'];
                            $newArr[$k]['isYes'] = '1';
                        } else {
                            $newArr[$k]['cityName'] = $province['province_name'];
                            $newArr[$k]['isYes'] = '0';
                        }

                    } else {
                        $data_city['isdelete'] = 0;
                        $data_city['car_id'] = $car_id;
                        $data_city['exterior_color_id'] = $excolor;
                        $data_city['interior_color_id'] = $incolor;
                        $data_city['city_id'] = $v;
                        $city = M('city')->where(array('id' => $v, 'isdelete' => 0))->find();
                        if ($city) {
                            /*$_POST['bpcities'][$k]['sales_area_name'] = $city['city_name'];*/
                            $res_city = M('view_car_price')->where($data_city)->order('low_price asc')->find();
                            $find = M('sales_area')->where(array('isdelete' => 0, 'car_price_id' => $res_city['id'], 'sales_area_name' => $_POST['upCardCity'], 'sales_area_level' => 3))->find();
                            if ($find) {
                                $newArr[$k]['cityName'] = $city['city_name'];
                                $newArr[$k]['isYes'] = '1';
                            } else {
                                $newArr[$k]['cityName'] = $city['city_name'];
                                $newArr[$k]['isYes'] = '0';
                            }
                        }
                    }
                }
            }
            $data['status'] = 1;
            $data['citiesList'] = $newArr;
            $this->ajaxReturn($data);
        }
    }

    /**
     *ajax询价生成询价订单
     */
    public function ajax_xunjia()
    {
        foreach ($_POST['bpcities'] as $k => $v) {
            if ($v == '001') {
                $new_arr[$k] = '全国';
            } else {
                $province = M('province')->where(array('id' => $v, 'isdelete' => 0))->find();
                if ($province) {
                    $new_arr[$k] = $province['province_name'];
                } else {
                    $city = M('city')->where(array('id' => $v, 'isdelete' => 0))->find();
                    if ($city) {
                        $new_arr[$k] = $city['city_name'];
                    }
                }
            }
        }
        if (isset($_SESSION['user_id']) and $_SESSION['user_id'] != '') {
            $bpcities = $_POST['bpcities'];
            if (isset($_POST['car']) and $_POST['car'] != '') {
                $car_price_list = M('view_car_price')->where(array('isdelete' => 0, 'id' => I('car')))->find();
                $car_id = $car_price_list['car_id'];
                $excolor = I('excolor');
                $incolor = I('incolor');
                $car_low_price = M('view_car_price')->where(array('isdelete' => 0, 'car_id' => $car_id, 'exterior_color_id' => I('excolor'), 'interior_color_id' => $incolor))->order('low_price asc')->find();
                $user_id = $_SESSION['user_id'];
                //判断该搭配的车款两天内是否有询价过
                $data['user_id'] = $user_id;//询价的用户ID
                $data['car_id'] = $car_id;  //询价的汽车ID
                $data['exterior_color_id'] = $excolor;//外观ID
                $data['interior_color_id'] = $incolor;//内饰ID
                $data['pay_obj'] = 1;//支付对象询价
                $data['status'] = 1;//支付状态1表示支付成功
                $find = M('pay')->where($data)->select();
                //判断是否之前对这款车是否询价过
                if ($find) {
                    foreach ($find as $k => $v) {
                        $area['pay_id'] = $v['id'];
                        $area['pay_obj'] = array('in', '1,3');;
                        $area_list = M('pay_area_low_price')->where($area)->select();
                        $new_arrs = $this->array_column($area_list, 'area_name');
                        $c = array_diff($new_arr, $new_arrs);
                        if (count($new_arr) === count($new_arrs)) {
                            if (count($c) == '') {
                                $data_re['info'] = "已经询价过";
                                $data_re['status'] = "2";
                                $data_re['url'] = U('Car/car_price_pay?pid=' . $v['id']);
                                $this->ajaxReturn($data_re);
                            }
                        }

                    }
                }
                $count = count($_POST['bpcities']);
                if ($count !== 0) {
                    //判读询价的城市个数
                    if ($count == 1) {
                        $data_re['price'] = 99.00;
                    } else {
                        if ($count == 2) {
                            $data_re['price'] = 119.00;
                        } else {
                            if ($count == 3) {
                                $data_re['price'] = 139.00;
                            } else {
                                $data_re['price'] = 0.00;
                            }
                        }
                    }
                    //之前没询价的
                    //插入pay
                    $Order = A('Order');
                    $data['user_id'] = $user_id;//询价的用户ID
                    $data['money'] = $data_re['price'];
                    $data['car_id'] = $car_id;  //询价的汽车ID
                    $data['callback'] = U("Pay/payback");
                    $data['exterior_color_id'] = $excolor;//外观ID
                    $data['interior_color_id'] = $incolor;//内饰ID
                    $data['pay_obj'] = '1';//支付对象询价
                    $data['status'] = '0';//支付状态1表示支付成功
                    $data['id'] = md5(microtime());//id
                    $data['url'] = U("Order/order_askfee_ok?pid=" . $data['id']);
                    $data['out_trade_no'] = $Order->createOrderNo();//id
                    $data['pay_ip'] = $_SERVER['HTTP_X_REAL_IP'];
                    $data['updatetime'] = time();//低价车
                    $data['type'] = 1;//低价车
                    $data['carstyle'] = $car_low_price['brand_name'] . ' ' . $car_low_price['car_model_name'] . ' ' . $car_low_price['car_name'];
                    $data['exterior_color_name'] = $car_low_price['exterior_color_name'];
                    $data['exterior_color_id'] = $car_low_price['exterior_color_id'];
                    $data['exterior_color_value'] = $car_low_price['exterior_color_value'];
                    $data['interior_color_name'] = $car_low_price['interior_color_name'];
                    $data['interior_color_id'] = $car_low_price['interior_color_id'];
                    $data['interior_color_value'] = $car_low_price['interior_color_value'];
                    $data['car_name'] = $car_low_price['car_name'];
                    $data['card_place'] = $_POST['upCardCityName'];
                    $data['exterior_img'] = $car_low_price['exterior_img'];
                    $data['from_order'] = 'PC端';
                    $data['from_activityid'] = 20;
                    $add = M('pay')->add($data);
                    if ($add) {
                        //把地区插入pay_area_low_price
                        foreach ($_POST['bpcities'] as $v) {
                            //判断是否选择全国
                            $pay_area['pay_obj'] = 1;
                            $pay_area['id'] = md5(microtime());//id
                            $pay_area['pay_id'] = $data['id'];
                            $pay_area['card_place'] = $_POST['upCardCity'];
                            if ($v == '001') {
                                $pay_area['car_price_id'] = $car_low_price['id'];
                                $pay_area['id_4s'] = $car_low_price['user_id'];
                                $pay_area['pay_low_price'] = $car_low_price['low_price'];
                                $pay_area['area_name'] = "全国";
                                $pay_area['sales_area_level'] = 1;
                                M('pay_area_low_price')->add($pay_area);
                            } else {
                                //省份的最底价
                                $province = M('province')->where(array('id' => $v, 'isdelete' => 0))->find();
                                if ($province) {
                                    $province_low_price = M('view_car_price')->where(array('isdelete' => 0, 'car_id' => $car_id, 'exterior_color_id' => I('excolor'), 'interior_color_id' => $incolor, 'province_id' => $v))->order('low_price asc')->find();
                                    $pay_area['id'] = md5(microtime());//id
                                    $pay_area['pay_id'] = $data['id'];
                                    $pay_area['car_price_id'] = $province_low_price['id'];
                                    $pay_area['id_4s'] = $province_low_price['user_id'];
                                    $pay_area['pay_low_price'] = $province_low_price['low_price'];
                                    $pay_area['area_name'] = $province['province_name'];
                                    $pay_area['sales_area_level'] = 2;
                                    $pay_area['pay_obj'] = 1;
                                    M('pay_area_low_price')->add($pay_area);
                                } else {
                                    //城市的最低价
                                    $city = M('city')->where(array('id' => $v, 'isdelete' => 0))->find();
                                    $city_low_price = M('view_car_price')->where(array('isdelete' => 0, 'car_id' => $car_id, 'exterior_color_id' => I('excolor'), 'interior_color_id' => $incolor, 'city_id' => $v))->order('low_price asc')->find();
                                    $pay_area['id'] = md5(microtime());//id
                                    $pay_area['pay_id'] = $data['id'];
                                    $pay_area['car_price_id'] = $city_low_price['id'];
                                    $pay_area['id_4s'] = $city_low_price['user_id'];
                                    $pay_area['pay_low_price'] = $city_low_price['low_price'];
                                    $pay_area['area_name'] = $city['city_name'];
                                    $pay_area['sales_area_level'] = 3;
                                    $pay_area['pay_obj'] = 1;
                                    M('pay_area_low_price')->add($pay_area);
                                }
                            }
                        }
                        $where['user_id'] = $user_id;
                        $where['status'] = array('in', '1,2');
                        $where['pay_obj'] = 1;
                        $where['isdelete'] = 0;
                        $find = M('pay')->where($where)->find();
                        if ($find) {
                            $data_re['status'] = '1';
                            $data_re['pay_id'] = $data['id'];
                            $this->ajaxReturn($data_re);
                        } else {
                            M('pay')->where(array('id' => $data['id']))->save(array('status' => 1, 'pay_way' => '用户第一次免费询价'));
                            $data_re['status'] = "2";
                            $data_re['url'] = U('Car/car_price_pay?pid=' . $data['id']);
                            //订单支付成功后给客服发送邮箱提醒
                            $user_name = M('user_general')->where(array('id' => $_SESSION['user_id']))->getField('user_name');//用户名
                            $subject = "用户订单信息";//邮件主题
                            $content = $user_name . " ：于" . date('Y-m-d H:i', $data['updatetime']) . '成功支付订单请及时与其联系';//邮件内容
                            sendMail(KEFU_EMAIL, $subject, $content);

                            $this->ajaxReturn($data_re);
                        }
                    }
                } else {
                    $data_re['info'] = "请选择询价城市";
                    $data_re['status'] = "-1";
                    $this->ajaxReturn($data_re);
                }
            }
        }
    }

    /**
     * @Description:一键询价（免费询价）
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/10/11 14:33
     * @Version 2.0
     */
    public function free_xunjia()
    {
        foreach ($_POST['bpcities'] as $k => $v) {
            if ($v == '001') {
                $new_arr[$k] = '全国';
            } else {
                $province = M('province')->where(array('id' => $v, 'isdelete' => 0))->find();
                if ($province) {
                    $new_arr[$k] = $province['province_name'];
                } else {
                    $city = M('city')->where(array('id' => $v, 'isdelete' => 0))->find();
                    if ($city) {
                        $new_arr[$k] = $city['city_name'];
                    }
                }
            }
        }
        $tel = I('buyer_tel');
        if (!empty($tel)) {
            if (md5(I('code') . $tel) == I('md5code')) {
                $userinfo = M('user_general')->where(array('user_name' => $tel, 'isdelete' => 0))->find();
                if (!empty($userinfo)) {
                    $user_id = $userinfo['id'];
                } else {
                    $datauser['id'] = md5(microtime());
                    $datauser['user_name'] = $tel;
                    $datauser['password'] = md5('123456');
                    $datauser['tel'] = $tel;
                    $datauser['role'] = 1;
                    $datauser['status'] = 1;
                    M('user_general')->add($datauser);
                    $user_id = $datauser['id'];

                    //发送成功报名短信
                    $mobile = $tel;
                    $tpl_id = '20909';//短信模板ID
                    $tpl_value = '';//变量名和变量值对
                    $ress = sendMessage($mobile, $tpl_id, $tpl_value);
                    $reslut = json_decode($ress, true);
                    $data_re['error_code'] = $reslut['error_code'];
                    $data_re['reason'] = $reslut['reason'];

                }
                //登陆成功，设置cookie
                $token=authcode($user_id,'ENCODE','gouchehui');
                cookie('token',$token,array('expire'=>3600,'domain'=>'gouchehui.com'));

                if (isset($_POST['car']) and $_POST['car'] != '') {
                    $car_price_list = M('view_car_price')->where(array('isdelete' => 0, 'id' => I('car')))->find();
                    $car_id = $car_price_list['car_id'];
                    $excolor = I('excolor');
                    $incolor = I('incolor');
                    /*$user_id = $_SESSION['user_id'];*/
                    //判断该搭配的车款两天内是否有询价过
                    $data['user_id'] = $user_id;//询价的用户ID
                    $data['car_id'] = $car_id;  //询价的汽车ID
                    $data['exterior_color_id'] = $excolor;//外观ID
                    $data['interior_color_id'] = $incolor;//内饰ID
                    $data['pay_obj'] = array('in', '1,3');//支付对象询价
                    $data['status'] = 1;//支付状态1表示支付成功
                    $find = M('pay')->where($data)->select();
                    //判断是否之前对这款车是否询价过
                    if ($find) {
                        foreach ($find as $k => $v) {
                            $area['pay_id'] = $v['id'];
                            $area['pay_obj'] = array('in', '1,3');
                            $area['isdelete'] = 0;
                            $area_list = M('pay_area_low_price')->where($area)->select();
                            $new_arrs = $this->array_column($area_list, 'area_name');
                            $c = array_diff($new_arr, $new_arrs);
                            if (count($new_arr) === count($new_arrs)) {
                                if (count($c) == '') {
                                    $data_re['status'] = 2;
                                    $data_re['msg'] = "已经询价过";
                                    $data_re['url'] = U('Car/car_price_pay?pid=' . $v['id']);
                                    $this->ajaxReturn($data_re);
                                    exit();
                                }
                            }

                        }
                    }
                    $count = count($_POST['bpcities']);
                    if ($count !== 0) {
                        $car_low_price = M('view_car_price')->where(array('isdelete' => 0, 'car_id' => $car_id, 'exterior_color_id' => I('excolor'), 'interior_color_id' => $incolor))->order('low_price asc')->find();
                        //插入pay
                        $data['id'] = md5(microtime());//id
                        $data['user_id'] = $user_id;//询价的用户ID
                        $data['money'] = 0.00;
                        $data['car_id'] = $car_id;  //询价的汽车ID
                        $data['callback'] = U("Pay/payback");
                        $data['exterior_color_id'] = $excolor;//外观ID
                        $data['interior_color_id'] = $incolor;//内饰ID
                        $data['pay_obj'] = 3;//支付对象询价
                        $data['status'] = 1;//支付状态1表示支付成功
                        $data['url'] = U("Order/order_askfee_ok?pid=" . $data['id']);
                        $data['out_trade_no'] = createOrderNo();
                        $data['pay_ip'] = $_SERVER['HTTP_X_REAL_IP'];
                        $data['updatetime'] = time();//低价车
                        $data['type'] = 1;//低价车
                        $data['carstyle'] = $car_low_price['brand_name'] . ' ' . $car_low_price['car_model_name'] . ' ' . $car_low_price['car_name'];
                        $data['exterior_color_name'] = $car_low_price['exterior_color_name'];
                        $data['exterior_color_id'] = $car_low_price['exterior_color_id'];
                        $data['exterior_color_value'] = $car_low_price['exterior_color_value'];
                        $data['interior_color_name'] = $car_low_price['interior_color_name'];
                        $data['interior_color_id'] = $car_low_price['interior_color_id'];
                        $data['interior_color_value'] = $car_low_price['interior_color_value'];
                        $data['car_name'] = $car_low_price['car_name'];
                        $data['card_place'] = $_POST['upCardCityName'];
                        $data['exterior_img'] = $car_low_price['exterior_img'];
                        $data['from_order'] = 'PC端';
                        $data['from_activityid'] = 21;
                        $data['user_remark'] = I('user_remark');
                        $data['buyer_name'] = I('buyer_name');
                        $data['buyer_tel']=$tel;
                        $add = M('pay')->add($data);
                        if ($add) {
                            //把地区插入pay_area_low_price
                            foreach ($_POST['bpcities'] as $v) {
                                //判断是否选择全国
                                $pay_area['pay_obj'] = 3;
                                $pay_area['id'] = md5(microtime());//id
                                $pay_area['pay_id'] = $data['id'];
                                $pay_area['card_place'] = $_POST['upCardCityName'];
                                if ($v == '001') {
                                    $pay_area['car_price_id'] = $car_low_price['id'];
                                    $pay_area['id_4s'] = $car_low_price['user_id'];
                                    $pay_area['pay_low_price'] = $car_low_price['low_price'];
                                    $pay_area['area_name'] = "全国";
                                    $pay_area['sales_area_level'] = 1;
                                    M('pay_area_low_price')->add($pay_area);
                                } else {
                                    //省份的最底价
                                    $province = M('province')->where(array('id' => $v, 'isdelete' => 0))->find();
                                    if ($province) {
                                        $province_low_price = M('view_car_price')->where(array('isdelete' => 0, 'car_id' => $car_id, 'exterior_color_id' => I('excolor'), 'interior_color_id' => $incolor, 'province_id' => $v))->order('low_price asc')->find();
                                        $pay_area['car_price_id'] = $province_low_price['id'];
                                        $pay_area['id_4s'] = $province_low_price['user_id'];
                                        $pay_area['pay_low_price'] = $province_low_price['low_price'];
                                        $pay_area['area_name'] = $province['province_name'];
                                        $pay_area['sales_area_level'] = 2;
                                        M('pay_area_low_price')->add($pay_area);
                                    } else {
                                        //城市的最低价
                                        $city = M('city')->where(array('id' => $v, 'isdelete' => 0))->find();
                                        $city_low_price = M('view_car_price')->where(array('isdelete' => 0, 'car_id' => $car_id, 'exterior_color_id' => I('excolor'), 'interior_color_id' => $incolor, 'city_id' => $v))->order('low_price asc')->find();
                                        $pay_area['car_price_id'] = $city_low_price['id'];
                                        $pay_area['id_4s'] = $city_low_price['user_id'];
                                        $pay_area['pay_low_price'] = $city_low_price['low_price'];
                                        $pay_area['area_name'] = $city['city_name'];
                                        $pay_area['sales_area_level'] = 3;
                                        M('pay_area_low_price')->add($pay_area);
                                    }
                                }
                            }

                            //订单支付成功后给客服发送邮箱提醒
                                  $user_name = M('user_general')->where(array('id' => $user_id))->getField('user_name');//用户名
                                   $subject = "用户订单信息";//邮件主题
                                   $content = $user_name . " ：于" . date('Y-m-d H:i', $data['updatetime']) . '成功支付订单请及时与其联系';//邮件内容
                                   sendMail(KEFU_EMAIL, $subject, $content);

                            $data_re['status'] = 1;
                            $data_re['url'] = U('Car/car_price_pay?pid=' . $data['id']);
                            $data_re['msg']="询价成功";


                        }
                    } else {
                        $data_re['msg'] = "请选择询价城市";
                        $data_re['status'] = 2;

                    }
                }
            }else
            {
                $data_re['msg'] = "验证码错误";
                $data_re['status'] =2;

            }
        }
        $this->ajaxReturn($data_re);
    }

    /**
     *ajax返回询价价格
     */
    public function ajax_price()
    {

        if (isset($_POST)) {
            $count = count($_POST['cities']);
            if ($count == 1) {
                $data['newPrice'] = '99.00';
            } elseif ($count == 2) {
                $data['newPrice'] = '119.00';
            } else {
                if ($count == 3) {
                    $data['newPrice'] = '139.00';
                } else {
                    $data['newPrice'] = "";
                    $data['info'] = "请选择需要询价的城市";
                }
            }
        }
        $this->ajaxReturn($data);
    }

    /*******************************************************付过询价费展示页***************************************************************************/

    /**
     *底价车付过询价费购车页面
     */
    public function car_price_pay()
    {
        //判断是否有该订单
        if (isset($_GET['pid']) and $_GET['pid'] != '') {
            $pay = M('pay')->where(array('id' => I('pid'), 'isdelete' => '0', 'status' =>1))->find();
            if ($pay['user_id'] == $_SESSION['user_id']) {
                if ($pay) {
                    $sale = M('pay_area_low_price')->where(array('isdelete' => 0, 'pay_id' => I('pid'), 'pay_obj' => array('in','1,3')))->select();
                    $car_info = M('view_car_price')->where(array('isdelete' => 0, 'id' => $sale[0]['car_price_id']))->find();
                    $car_info['Imgurl'] = str_replace("type", 'big', $car_info['exterior_img']);
                    $car_info['interior_color']=explode(" ",$car_info['interior_color_value']);
                    $image0 = M('car_color_image')->where(array('exterior_color_id' => $car_info['exterior_color_id'], 'interior_color_id' => $car_info['interior_color_id'], 'isdelete' => 0, 'type' => 0))->select();
                    foreach ($image0 as $key => $value) {
                        $image0[$key]['imgurl'] = str_replace('type', 'big', $value['imgurl']);
                    }
                    $this->assign('image0', $image0);
                    $image1 = M('car_color_image')->where(array('exterior_color_id' => $car_info['exterior_color_id'], 'interior_color_id' => $car_info['interior_color_id'], 'isdelete' => 0, 'type' => 1))->select();
                    foreach ($image1 as $key => $value) {
                        $image0[$key]['imgurl'] = str_replace('type', 'big', $value['imgurl']);
                    }
                    $this->assign('image1', $image1);
                    $image2 = M('car_color_image')->where(array('exterior_color_id' => $car_info['exterior_color_id'], 'interior_color_id' => $car_info['interior_color_id'], 'isdelete' => 0, 'type' => 2))->select();
                    foreach ($image2 as $key => $value) {
                        $image2[$key]['imgurl'] = str_replace('type', 'big', $value['imgurl']);
                    }
                    $this->assign('image2', $image2);
                    $image3 = M('car_color_image')->where(array('exterior_color_id' => $car_info['exterior_color_id'], 'interior_color_id' => $car_info['interior_color_id'], 'isdelete' => 0, 'type' => 3))->select();
                    foreach ($image3 as $key => $value) {
                        $image3[$key]['imgurl'] = str_replace('type', 'big', $value['imgurl']);
                    }
                    $this->assign('image3', $image3);
                    //判断是否已经有关注了
                    if (isset($_SESSION['user_id'])) {
                        $attention['user_id'] = $_SESSION['user_id'];
                        $attention['car_model_id'] = $car_info['car_model_id'];
                        $attention['isdelete'] = 0;
                        $find = M('user_attention_car_model')->where($attention)->find();
                        if ($find) {
                            $this->assign('attention', $find);
                        }
                    }
                    $this->assign('sale', $sale);
                    $this->assign('pay', $pay);
                    $this->assign('info', $car_info);

                }
            } else {
                $this->redirect('Car/product_search');
            }
        } else {
            $this->redirect('Car/product_search');
        }
        $this->display();
    }

    /**
     *ajax订金
     */
    public function ajax_dingjin()
    {
        if (isset($_POST['id']) and $_POST['id'] != '') {
            $sale = M('pay_area_low_price')->where(array('isdelete' => 0, 'id' => I('id')))->find();
            if ($sale) {
                if ($sale['pay_low_price'] < 200000 and $sale['pay_low_price'] > 0) {
                    $dingjin = '2000';
                } elseif ($sale['price'] < 500000 and $sale['pay_low_price'] >= 200000) {
                    $dingjin = '5000';
                } else {
                    if ($sale['pay_low_price'] >= 500000) {
                        $dingjin = '10000';
                    } else {
                        $dingjin = '0';
                    }
                }
                //获得汽车礼包
                $where['min_price'] = array('ELT', $sale['pay_low_price']);
                $where['max_price'] = array('EGT', $sale['pay_low_price']);
                $data['_complex'] = $where;
                $data['isdelete'] = 0;
                $package = M('buy_car_package')->where($data)->find();

                $re['newInquiryFee'] = $dingjin;
                $re['newPackagePrice'] = $package['price'];
                $re['description'] = $package['description'];
                $re['newUrl'] = str_replace("type", 'big', $package['imgurl']);
                $re['status'] = 1;
                $re['nextUrl'] = U('Order/order_info', array('car_price_id' => $sale['car_price_id']));
            } else {
                $re['status'] = -1;
                $re['info'] = '系统出错请稍后从试';
            }
            $this->ajaxReturn($re);
        }
    }

    /*******************************************************（详情页公用）***************************************************************************/
    /**
     *用户关注
     */
    public function ajax_attention()
    {
        if (isset($_POST['model_id']) and $_POST['model_id'] != '') {
            if (isset($_SESSION['user_id']) and $_SESSION['user_id'] != '') {
                $data['user_id'] = $_SESSION['user_id'];
                $data['car_model_id'] = I('model_id');
                $data['isdelete'] = 0;
                $res = M('user_attention_car_model')->where($data)->find();
                if ($res) {
                    $del = M('user_attention_car_model')->where(array('user_id' => $_SESSION['user_id'], 'car_model_id' => I('model_id')))->save(array('isdelete' => 1));
                    if ($del) {
                        $arr['status'] = 2;//已经关注过了取消关注
                        $arr['info'] = '取消关注成功';
                        $this->ajaxReturn($arr);
                    } else {
                        $arr['status'] = 3;//已经关注过了取消关注
                        $arr['info'] = '取消关注失败，请重试！';
                        $this->ajaxReturn($arr);
                    }
                } else {
                    $datas['id'] = md5(microtime());
                    $datas['user_id'] = $_SESSION['user_id'];
                    $datas['car_model_id'] = I('model_id');
                    if (M('user_attention_car_model')->add($datas)) {
                        M('car_model')->where(array('id' => I('model_id')))->setInc('attention_count', 1);
                        $arr['status'] = 1;//关注成功
                        $arr['info'] = '关注成功';//关注成功
                        $this->ajaxReturn($arr);
                    } else {
                        $arr['status'] = -1;//关注失败
                        $arr['info'] = '关注失败';//关注失败
                        $this->ajaxReturn($arr);
                    }
                }
            } else {
                $arr['status'] = -2;//未登录
                $arr['info'] = '未登录';//未登录
                $this->ajaxReturn($arr);
            }
        } else {
            $this->redirect('Index/index');
        }
    }

    /**
     *普通用户------取消关注
     */
    public function attention_delete()
    {
        $find = D('AttentionCar')->attention_find(array('id' => I('id')));
        if ($find['user_id'] == $_SESSION['user_id']) {
            $z = D('AttentionCar')->attention_save(array('id' => I('id'), 'isdelete' => 1));
            if ($z) {
                /*取消关注后，车型关注度少1*/
                $model_id = D('AttentionCar')->where(array('id' => I('id')))->getField('car_model_id');
                M('car_model')->where(array('id' => $model_id))->setDec('attention_count', 1);
                $data['status'] = 2;
                $data['msg'] = '取消关注成功！';

            } else {
                $data['status'] = 1;
                $data['msg'] = '取消关注失败！';
            }

        } else {
            $data['status'] = 1;
            $data['msg'] = '非法操作！';

        }
        $this->ajaxReturn($data);
    }

    /**
     *底价车城市选择
     */
    public function ajax_city_select()
    {

        if (isset($_POST['id']) and $_POST['id'] != '') {

            if (isset($_SESSION['province']) and $_SESSION['province'] != '') {
                //获得省份名称
                $province_name = M('province')->where(array('isdelete' => 0, 'id' => $_SESSION['province']))->getField('province_name');

            } else {
                if (isset($_SESSION['city']) and $_SESSION['city'] != '') {
                    //获得城市名称
                    $city_name = M('city')->where(array('isdelete' => 0, 'id' => $_SESSION['city']))->getField('city_name');

                }
            }
            //根据ID 外观内饰的ID 检索报价表里是否有城市报价
            $res = M('view_car_price')->where(array('isdelete' => 0, 'id' => I('id')))->find();
            $data['car_id'] = $res['car_id'];
            $data['exterior_color_id'] = I('excolor');
            $data['interior_color_id'] = I('incolor');
            $data['isdelete'] = 0;
            $rr = M('view_car_price')->query("select * from
                (SELECT
                    province_id,
                    province_name,
                    province_id as id,
                    province_name as city_name 
                    FROM
                    gch_view_car_price 
                    where
                    isdelete = 0
                    and exterior_color_id = '{$data['exterior_color_id']}' 
                    and interior_color_id = '{$data['interior_color_id']}'
                    group by
                    province_name  
                    UNION
                    ALL SELECT
                    province_id,
                    province_name,
                    city_id as id,
                    city_name 
                    FROM
                    gch_view_car_price 
                    where
                    isdelete = 0 
                    and exterior_color_id = '{$data['exterior_color_id']}'  
                    and interior_color_id = '{$data['interior_color_id']}'
                    group by
                    city_name 
                    ) as temp 
GROUP BY
province_name,
city_name");
            $data['is_xunjia'] = 1;
            $info = M('view_car_price')->where($data)->group('city_id')->select();
            if ($info) {
                ////根据ID 外观内饰的ID 城市或者省份ID 检索报价表里是否有报价
                if ($province_name != '') {
                    $data['province_id'] = $_SESSION['province'];
                    //判断是否是按照省份检索，该省份是否有报价
                    $find = M('view_car_price')->where($data)->find();
                    if ($find) {
                        $re['curCityPrice']['isBool'] = 1;
                        $re['curCityPrice']['id'] = $_SESSION['province'];
                        $re['curCityPrice']['name'] = $province_name;
                    } else {
                        //如果没有进入城市判断
                        $re['curCityPrice']['isBool'] = 0;
                        $re['curCityPrice']['id'] = $_SESSION['province'];
                        $re['curCityPrice']['name'] = '您选择的颜色搭配' . $province_name . '暂无报价';
                    }
                } else {
                    //判断该城市是否有报价
                    if ($city_name != '') {
                        $data['city_id'] = $_SESSION['city'];
                        //获得城市名称
                        $find = M('view_car_price')->where($data)->find();
                        if ($find) {
                            $re['curCityPrice']['isBool'] = 1;
                            $re['curCityPrice']['id'] = $_SESSION['city'];
                            $re['curCityPrice']['name'] = $city_name . '底价';
                        } else {
                            $re['curCityPrice']['isBool'] = 0;
                            $re['curCityPrice']['id'] = $_SESSION['city'];
                            $re['curCityPrice']['name'] = '您选择的颜色搭配' . $city_name . '暂无报价';
                        }
                    } else {
                        $find = M('view_car_price')->where($data)->find();
                        if ($find) {
                            $re['curCityPrice']['isBool'] = 1;
                            $re['curCityPrice']['name'] = '全国底价';
                        } else {
                            $re['curCityPrice']['isBool'] = 0;
                            $re['curCityPrice']['id'] = '001';
                            $re['curCityPrice']['name'] = '您选择的颜色搭配全国暂无报价';
                        }
                    }
                }
                //将报价的数组赋给一个新的数组
                $re['cities'] = $rr;
            } else {
                $info = M('index_car_price')->where(array('car_id' => $data['car_id'], 'exterior_color_id' => $data['exterior_color_id'], 'interior_color_id' => $data['interior_color_id'], 'isdelete' => 0, 'isbaojia' => 1))->find();
                if ($info) {
                    $re['status'] = 2;
                    $re['next'] = U('details_unquotes', array('id' => $info['id']));
                } else {
                    if (isset($province_name) and $province_name != '') {
                        $re['curCityPrice']['id'] = $_SESSION['province'];
                        $re['curCityPrice']['name'] = '您选择的颜色搭配' . $province_name . '暂无报价';
                    } else {
                        if (isset($_SESSION['city']) and $_SESSION['city'] != '') {
                            $re['curCityPrice']['id'] = $_SESSION['city'];
                            $re['curCityPrice']['name'] = '您选择的颜色搭配' . $city_name . '暂无报价';
                        } else {
                            $re['curCityPrice']['id'] = '001';
                            $re['curCityPrice']['name'] = '您选择的颜色搭配全国暂无报价';
                        }
                    }
                }


                $re['cities'] = '';
                $re['curCityPrice']['isBool'] = 0;
            }
            $this->ajaxReturn($re);
        }
    }

    /**
     *ajax提问问题
     */
    public function ajax_set_comment()
    {
        if ($_SESSION['user_id']) {
            if (I('id') != '' and I('bd') != '') {
                $data['question_type'] = I('id');
                $data['question'] = I('bd');
                $data['createuser'] = $_SESSION['user_id'];
                $data['car_brand_id'] = I('brand_id');
                $data['car_model_id'] = I('model_id');
                $data['status'] = 1;
                $find = M('car_question')->where($data)->find();
                if ($find) {
                    $ret['status'] = 2;
                    $ret['info'] = "请不要重复提交问题";
                }
                //添加到数据库
                $data['id'] = md5(microtime());
                $add = M('car_question')->add($data);
                if ($add) {
                    $ret['status'] = 1;
                    $ret['info'] = "问题咨询成功";
                    $this->ajaxReturn($ret);
                } else {
                    $ret['status'] = -1;
                    $ret['info'] = "问题咨询失败";
                    $this->ajaxReturn($ret);
                }

            } else {
                $ret['status'] = -2;
                $ret['info'] = "请填写数据";
                $this->ajaxReturn($ret);
            }
        } else {
            $ret['status'] = -3;
            $ret['info'] = "请登录";
            $this->ajaxReturn($ret);
        }
    }

    /**
     *异步获取提问的问题
     */
    public function ajax_set_comment_type()
    {
        if (isset($_POST)) {
            if (I('commId') != 0) {
                $data['question_type'] = I('commId');
            } else {
                $ret['data']['type'] = '所有问题';
            }
            $data['car_brand_id'] = I('brand_id');
            $data['car_model_id'] = I('model_id');
            $data['status'] = 1;
            $data['isdelete'] = 0;
            $res = M('car_question')->where($data)->select();
            if ($res) {
                foreach ($res as $k => $v) {
                    $user = M('user_general')->where(array('id' => $v['createuser']))->find();
                    if (!empty($user['head_url'])) {
                        $res[$k]['head_url'] = OSS . str_replace('type', 'small', $user['head_url']);
                    } else {
                        $res[$k]['head_url'] = '/Public/Home/images/headimg.png';
                    }
                    $res[$k]['user_name'] = $user['user_name'];
                }
                $ret['status'] = 1;
                $ret['data']['id'] = I('commId');

                if (I('commId') == 1) {
                    $ret['data']['type'] = '商品咨询';
                }
                if (I('commId') == 2) {
                    $ret['data']['type'] = '支付相关';
                }
                if (I('commId') == 3) {
                    $ret['data']['type'] = '发票问题';
                }
                if (I('commId') == 4) {
                    $ret['data']['type'] = '其他问题';
                }

                $ret['data']['list'] = $res;
                $this->ajaxReturn($ret);
            } else {
                $ret['status'] = 0;
                $ret['info'] = "暂无数据，请联系客服";
                $this->ajaxReturn($ret);
            }
        } else {
            $this->redirect('product_details');
        }
    }

    /**
     * 把一个二维数组按照一个key分割成一个一维数组
     * @param $array
     * @param $column_name
     * @return array
     */
    public function array_column($array, $column_name)
    {

        return array_map(function ($element) use ($column_name) {
            return $element[$column_name];
        }, $array);
    }

    /**
     *多个页面的热卖车型接口
     */
    public function bestselling()
    {
        $sales = D('view_index_price')->where(array('isdelete' => 0))->order('createtime desc')->find();
        $sales['car_model_name'] = $sales['model_name'];
        $sales['car_model_imageurl'] = OSS . str_replace('type', 'small', $sales['car_model_imageurl']);
        $sales['url'] = '/index.php/car/details_unquotes?id=' . $sales['id'];
        $sales['discount'] = number_format($sales['discount'] / 10000, 2);
        $sales['low_price'] = number_format($sales['low_price'] / 10000, 2);

        $info = D('view_car_price')->where(array('isdelete' => 0, 'is_xunjia' => 1))->group('car_model_id')->order('model_access_quantity desc')->limit(0, 3)->select();
        foreach ($info as $key => $value) {
            $info[$key]['car_model_imageurl'] = OSS . str_replace('type', 'small', $value['car_model_imageurl']);
            $info[$key]['url'] = '/index.php/car/product_details?id=' . $value['id'];
            $info[$key]['discount'] = number_format($value['discount'] / 10000, 2);
            $info[$key]['low_price'] = number_format($value['low_price'] / 10000, 2);
            $info[$key]['auth_price'] = number_format($value['auth_price'] / 10000, 2);
        }

        $data['sales'] = $sales;
        $data['hot_model'] = $info;
        $this->ajaxReturn($data);


    }

    /********************************************************特价车***************************************************************************/
    /**
     *特价车列表页
     */
    public function special_price_car()
    {
        //添加城市筛选
        if (isset($_SESSION['province']) and $_SESSION['province'] != '') {
            $special_data['province_id'] = $_SESSION['province'];
        } else {
            if (isset($_SESSION['city']) and $_SESSION['city'] != '') {
                $special_data['city_id'] = $_SESSION['city'];
            }
        }
        $special_data['isdelete'] = 0;//是否删除
        /* $special_data['end_date']=array('EGT',date('Y-m-d H:i:s',time()));*/
        $special_data['status'] = array('between', array('2', '3'));
        //获得所有特价车品牌
        $brand = M('view_special_price_car')->where(array('status' => array('between', array('2', '3')), 'isdelete' => 0))->group('brand_id')->select();
        foreach ($brand as $key => $value) {
            $logo = M('brand')->where(array('id' => $value['brand_id']))->getField('logo');
            $brand[$key]['brand_logo'] = str_replace('type', 'small', $logo);
        }
        /*        //获得所有城市
                $sales_area['isdelete'] = 0;
                $sales_area['car_special_id'] = array('neq','');
                $city = M('sales_area')->where($sales_area)->group('sales_area_name')->select();*/
        //获得所有特价车

        $count = M('view_special_price_car')->where($special_data)->count();
        $Page = new \Think\Page($count, 5);
        $special_price_car = M('view_special_price_car')->where($special_data)->order('end_date desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($special_price_car as $key => $value) {
            $special_price_car[$key]['discount'] = number_format(($value['price'] - $value['special_price']) / 10000, 2);
            $special_price_car[$key]['price'] = number_format($value['price'] / 10000, 2);
            $special_price_car[$key]['special_price'] = number_format($value['special_price'] / 10000, 2);
            $special_price_car[$key]['start'] = strtotime($value['start_date']);
            $special_price_car[$key]['end'] = strtotime($value['end_date']);
            $special_price_car[$key]['car_image'] = str_replace('type', 'big', $value['car_image']);
            // $special_price_car[$key]['imgurl'] = str_replace("type",'big',M('car_exterior_color_image')->where(array('car_id'=>$value['car_id'],'exterior_color_id'=>$value['exterior_color_id']))->getField('imgurl'));
        }

        /*$this->assign('city',$city);*/
        $pagelist = $Page->show();
        $this->assign('count', $count);
        $this->assign('pagelist', $pagelist);
        $this->assign('brand', $brand);
        $this->assign('special_price_car', $special_price_car);
        $this->display();
    }

    /**
     *ajax获得特价车
     */
    public function ajax_special_price_car()
    {
        if ($_POST) {
            //判断是否有品牌提交
            if (isset($_POST['brandid']) and $_POST['brandid'] != '') {
                $data['brand_id'] = I('brandid');
            }
            //判读是否有价格区间提交
            if (isset($_POST['minprice']) and $_POST['minprice'] != '') {
                if ($_POST['minprice'] != 0) {
                    $min = $_POST['minprice'] * 10000;
                } else {
                    $min = 0;
                }
                $max = $_POST['maxprice'] * 10000;
                $data['special_price'] = array(array('EGT', $min), array('ELT', $max));
            }
            //判断是否有城市提交
            if (isset($_POST['extractcity']) and $_POST['extractcity'] != '') {
                $cityname = M('sales_area')->where(array('id' => I('extractcity')))->getField('sales_area_name');
                $sales_area['isdelete'] = 0;
                $sales_area['car_special_id'] = array('neq', '');
                $sales_area['sales_area_name'] = $cityname;
                $city = M('sales_area')->where($sales_area)->select();
                $str = '';
                //把car_special_id组合成一个新的字符串
                foreach ($city as $key => $value) {
                    $str .= $value['car_special_id'];
                    $str .= ',';
                }
                $results = substr($str, 0, strlen($str) - 1);//取字符串第一位到倒数第二位
                $results = explode(',', $results);//用,分割成一个新的数组
                $data['id'] = array('in', $results);//判断ID是否在里面
            }
            //特价车状态
            if (isset($_POST['status']) and !empty($_POST['status'])) {
                $data['status'] = I('status');
            } else {
                $data['status'] = array('between', array('2', '3'));
            }
            if (isset($_POST['end_date']) and !empty($_POST['end_date'])) {
                $order['end_date'] = I('end_date');
            }
            if (isset($_POST['discount']) and !empty($_POST['discount'])) {
                $order['discount'] = I('discount');
            }
            if (isset($_POST['special_price']) and !empty($_POST['special_price'])) {
                $order['special_price'] = I('special_price');
            }
            $data['isdelete'] = 0;
            //获得所有特价车
            $pagenum = I('pageNum');
            $count = M('view_special_price_car')->where($data)->count();
            $Page = new \Think\PageAjax($count, $pagenum);
            $special_price_car = M('view_special_price_car')->where($data)->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            foreach ($special_price_car as $key => $value) {
                $special_price_car[$key]['start_date'] = date("Y-m-d", strtotime($value['start_date']));
                $special_price_car[$key]['end_date'] = date("Y-m-d", strtotime($value['end_date']));
                $special_price_car[$key]['discount'] = number_format(($value['price'] - $value['special_price']) / 10000, 2);
                $special_price_car[$key]['price'] = number_format($value['price'] / 10000, 2);
                $special_price_car[$key]['special_price'] = number_format($value['special_price'] / 10000, 2);
                $special_price_car[$key]['imgurl'] = OSS . str_replace("type", 'big', $value['car_image']);
                $special_price_car[$key]['url'] = U('special_price_car_details', array('id' => $value['id']));
            }
            if ($special_price_car) {
                $ret['status'] = 1;
                $ret['list'] = $special_price_car;
                $ret['count'] = $count;
                $ret['pagelist'] = $Page->show();
            } else {
                $ret['status'] = 2;
                $ret['list'] = $special_price_car;
                $ret['info'] = "你选择的条件里暂无特价车";
            }

            $this->ajaxReturn($ret);
        }
    }

    /**
     *特价车详情页
     */
    public function special_price_car_details()
    {
        //判断特价车ID
        if (isset($_GET['id']) and $_GET['id'] != '') {
            $id = htmlspecialchars(trim($_GET['id']));
            $special_price_car = M('view_special_price_car')->where(array('id' => $id, 'isdelete' => 0))->find();
            if ($special_price_car['number'] < 0) {
                $special_price_car['number'] = 0;
            } else {
                $special_price_car['number'] = $special_price_car['number'];
            }
            /*$this->sale_city = implode(";",$array);*/
            //判断要多少订金 
            if ($special_price_car) {

                $special_price_car['interior_color_value'] = explode(' ', $special_price_car['interior_color_value']);//强制将其装换成数组
                if (count($special_price_car['interior_color_value']) == 1) {
                    $special_price_car['interior_color_value'][1] = $special_price_car['interior_color_value'][0];
                }
                $shangpai = M('sales_area')->where(array('isdelete' => 0, 'type' => 2, 'car_special_id' => $special_price_car['id']))->select();
                $this->array = $this->array_column($shangpai, 'sales_area_name');

                $special_price_car['imgurl'] = M('car_exterior_color_image')->where(array('isdelete' => 0, 'exterior_color_id' => $special_price_car['exterior_color_id']))->getField('imgurl');
                $special_price_car['imgurl'] = str_replace("type", 'big', $special_price_car['imgurl']);
                if ($special_price_car['special_price'] <= 200000) {
                    $special_price_car['deposit'] = number_format('2000');
                } elseif ($special_price_car['special_price'] <= 500000) {
                    $special_price_car['deposit'] = number_format('5000');
                } else {
                    $special_price_car['deposit'] = number_format('10000');
                }
                $special_price_car['start'] = strtotime($special_price_car['start_date']);
                $special_price_car['end'] = strtotime($special_price_car['end_date']);
                $special_price_car['discount'] = number_format($special_price_car['price'] - $special_price_car['special_price']);
                $special_price_car['price'] = number_format($special_price_car['price']);
                $special_price_car['special_price'] = number_format($special_price_car['special_price'] / 10000, 2);
                $special_price_car['auth_price'] = number_format(M('car')->where(array('id' => $special_price_car['car_id'], 'isdelete' => 0))->getField('auth_price') / 10000, 2);
                $this->nowtime = time();
                $this->assign('info', $special_price_car);
                //获得特价车礼包
                $where['min_price'] = array('ELT', $special_price_car['special_price']);
                $where['max_price'] = array('EGT', $special_price_car['special_price']);
                $data['_complex'] = $where;
                $data['isdelete'] = 0;
                $package = M('buy_car_package')->where($data)->find();
                $package['imgurl'] = str_replace("type", 'big', $package['imgurl']);
                $this->assign('package', $package);
            } else {
                $this->redirect('special_price_car');
            }
        } else {
            $this->redirect('special_price_car');
        }

        $this->display();
    }

    /********************************************************生成订单（公开底价车、不公开底价车、特价车）************************************************/
    /**
     *生成定车订单-----------公开底价车
     */
    public function ajax_order()
    {
        $login = session('user_id');
        if (!empty($login)) {
            $data['id'] = md5(microtime());
            $data['out_trade_no'] = $this->createOrderNo();
            $data['car_id'] = I('car_id');
            $data['exterior_color_id'] = I('excolor');
            $data['interior_color_id'] = I('incolor');
            $car_price_info = D('view_car_price')->where(array('car_id' => I('car_id'), 'exterior_color_id' => I('excolor'), 'interior_color_id' => I('incolor')))->order('low_price asc')->limit(0, 1)->find();
            $data['car_price_id'] = $car_price_info['id'];
            $data['carstyle'] = $car_price_info['brand_name'] . $car_price_info['car_model_name'] . $car_price_info['car_name'];
            $data['user_id'] = session('user_id');
            $data['pay_ip'] = $_SERVER['HTTP_X_REAL_IP'];
            $data['status'] = 0;
            $data['pay_obj'] = 2;
            $data['car_name'] = $car_price_info['car_name'];
            $data['exterior_color_name'] = $car_price_info['exterior_color_name'];
            $data['exterior_color_value'] = $car_price_info['exterior_color_value'];
            $data['interior_color_name'] = $car_price_info['interior_color_name'];
            $data['interior_color_value'] = $car_price_info['interior_color_value'];
            $data['exterior_img'] = $car_price_info['exterior_img'];
            $data['low_price_city_name'] = $car_price_info['city_name'];
            $data['compulsory_insurance'] = I('compulsory_insurance');
            $data['cess'] = I('cess');
            $data['licensing_fees'] = I('licensing_fees');
            $data['travel_tax'] = I('travel_tax');
            $data['car_loss_dang'] = I('car_loss_dang');
            $data['liability'] = I('liability');
            $data['car_liability'] = I('car_liability');
            $data['deductible_special'] = I('deductible_special');
            $data['type'] = 1;
            $data['id_4s'] = $car_price_info['user_id'];
            $data['low_price'] = $car_price_info['low_price'];
            if ($car_price_info['low_price'] <= 200000) {
                $car_price_info['deposit'] = 2000;
            } elseif ($car_price_info['low_price'] <= 500000) {
                $car_price_info['deposit'] = 5000;
            } else {
                $car_price_info['deposit'] = 10000;
            }
            $data['order_money'] = $car_price_info['deposit'];
            $data['money'] = $car_price_info['deposit'];
            $data['card_place'] = I('ucc_name');
            $data['url'] = U('Order/order_ok', array('pay_id' => $data['id']));
            $data['callback'] = U('pay/payback');
            $data['from_order'] = 'PC端';
            $z = M('pay')->add($data);
            if ($z) {
                $datar['status'] = 1;
                $datar['next'] = U('Order/order_info', array('pay_id' => $data['id']));
            } else {
                $datar['status'] = 0;
                $datar['info'] = '订单生成失败，请重试！';
            }
            $this->ajaxReturn($datar);

        } else {
            $this->redirect('Public/login');
        }
    }

    /**
     *生成定车订单----------已询价的底价车
     */
    public function ajax_car_price_pay()
    {
        if (isset($_SESSION['user_id'])) {
            $data['id'] = md5(microtime());
            $data['out_trade_no'] = $this->createOrderNo();
            $car_pirce_id = M('pay_area_low_price')->where(array('isdelete' => 0, 'id' => I('bpcity_id')))->getField('car_price_id');
            $car_price_info = M('view_car_price')->where(array('id' => $car_pirce_id))->find();
            $data['car_id'] = $car_price_info['car_id'];
            $data['exterior_color_id'] = $car_price_info['exterior_color_id'];
            $data['interior_color_id'] = $car_price_info['interior_color_id'];
            $data['car_price_id'] = $car_price_info['id'];
            $data['carstyle'] = $car_price_info['brand_name'] . $car_price_info['car_model_name'] . $car_price_info['car_name'];
            $data['user_id'] = session('user_id');
            $data['pay_ip'] = $_SERVER['HTTP_X_REAL_IP'];
            $data['status'] = 0;
            $data['pay_obj'] = 2;
            $data['car_name'] = $car_price_info['car_name'];
            $data['exterior_color_name'] = $car_price_info['exterior_color_name'];
            $data['exterior_color_value'] = $car_price_info['exterior_color_value'];
            $data['interior_color_name'] = $car_price_info['interior_color_name'];
            $data['interior_color_value'] = $car_price_info['interior_color_value'];
            $data['exterior_img'] = $car_price_info['exterior_img'];
            $data['low_price_city_name'] = $car_price_info['city_name'];
            $data['type'] = 1;
            $data['id_4s'] = $car_price_info['user_id'];
            /* $data['money']=I('inquiryFee');*/
            $data['low_price'] = $car_price_info['low_price'];
            if ($car_price_info['low_price'] <= 200000) {
                $car_price_info['deposit'] = 2000;
            } elseif ($car_price_info['low_price'] <= 500000) {
                $car_price_info['deposit'] = 5000;
            } else {
                $car_price_info['deposit'] = 10000;
            }
            $data['order_money'] = $car_price_info['deposit'];
            $data['money'] = $car_price_info['deposit'];
            $data['card_place'] = I('ucc_name');
            $data['url'] = U('Car/product_search');
            $data['callback'] = U('pay/payback');
            $data['from_order'] = 'PC端';
            $z = M('pay')->add($data);
            if ($z) {
                $datar['status'] = 1;
                $datar['next'] = U('Order/order_info', array('pay_id' => $data['id']));
            } else {
                $datar['status'] = 0;
                $datar['info'] = '订单生成失败，请重试！';
            }
            $this->ajaxReturn($datar);
        } else {
            $this->redirect('Public/login');
        }
    }

    /**
     *生成定车订单----------特价车
     */
    public function ajax_special_order()
    {
        $login = session('user_id');
        if (!empty($login)) {
            $data['id'] = md5(microtime());
            $data['out_trade_no'] = $this->createOrderNo();
            $data['car_id'] = M('special_price_car')->where(array('id' => I('car_id')))->getField('car_id');
            /*  $data['money']=I('deposit');*/
            $data['card_place'] = I('city');
            $data['car_special_id'] = I('car_special_id');
            $car_special_info = D('view_special_price_car')->where(array('id' => I('car_special_id')))->find();
            $data['exterior_color_id'] = $car_special_info['exterior_color_id'];
            $data['interior_color_id'] = $car_special_info['interior_color_id'];
            $data['carstyle'] = $car_special_info['brand_name'] . $car_special_info['car_model_name'] . $car_special_info['car_name'];
            $data['user_id'] = session('user_id');
            $data['pay_ip'] = $_SERVER['HTTP_X_REAL_IP'];
            $data['status'] = 0;
            $data['pay_obj'] = 2;
            $data['car_name'] = $car_special_info['car_name'];
            $data['exterior_color_name'] = $car_special_info['exterior_color_name'];
            $data['exterior_color_value'] = $car_special_info['exterior_color_value'];
            $data['interior_color_name'] = $car_special_info['interior_color_name'];
            $data['interior_color_value'] = $car_special_info['interior_color_value'];
            $data['exterior_img'] = $car_special_info['exterior_img'];
            $data['low_price_city_name'] = $car_special_info['city_name'];
            $data['type'] = 2;
            $data['id_4s'] = $car_special_info['user_id'];
            $data['low_price'] = $car_special_info['special_price'];
            if ($car_special_info['special_price'] <= 200000) {
                $car_special_info['deposit'] = 2000;
            } elseif ($car_special_info['special_price'] <= 500000) {
                $car_special_info['deposit'] = 5000;
            } else {
                $car_special_info['deposit'] = 10000;
            }
            $data['order_money'] = $car_special_info['deposit'];
            $data['money'] = $car_special_info['deposit'];
            $data['url'] = U('Usercenter/UC_order');
            $data['callback'] = U('pay/payback');
            $data['from_order'] = 'PC端';
            $z = M('pay')->add($data);
            if ($z) {
                //下单成功，库存数量减一
                M('special_price_car')->where(array('id' => I('car_special_id')))->setDec('number', 1);
                $datar['status'] = 1;
                $datar['next'] = U('Order/order_info', array('pay_id' => $data['id']));
            } else {
                $datar['status'] = 0;
                $datar['info'] = '订单生成失败，请重试！';
            }
            $this->ajaxReturn($datar);
        } else {
            $this->redirect('Public/login');
        }
    }

    /**
     * 生成订单号
     * 可根据自身的业务需求更改
     */
    public function createOrderNo()
    {
        $year_code = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        return $year_code[intval(date('Y')) - 2010] .
        strtoupper(dechex(date('m'))) . date('d') .
        substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
    }

    /**
     * 替换大中小的图片
     * @param string $type
     * @param $imgtype
     * @param $str
     * @return mixed
     */
    public function replace_img($type = "type", $imgtype, $str)
    {
        return str_replace($type, $imgtype, $str);
    }

    /********************************************************活动生成订单***************************************************************************/
    /**
     *生成定车订单-----------活动价格底价车
     */
    public function ajax_activity_order()
    {
        $login = session('user_id');
        if (!empty($login)) {
            $data['id'] = md5(microtime());
            $data['out_trade_no'] = $this->createOrderNo();
            $car_price_id = I('car_price_id');
            $car_price_info = M('view_car_price')->where(array('id' => $car_price_id))->find();
            $data['car_price_id'] = $car_price_info['id'];
            $data['car_name'] = $car_price_info['car_name'];
            $data['car_id'] = $car_price_info['car_id'];
            $data['carstyle'] = $car_price_info['brand_name'] . $car_price_info['car_model_name'] . $car_price_info['car_name'];
            $data['exterior_color_id'] = $car_price_info['exterior_color_id'];
            $data['exterior_color_name'] = $car_price_info['exterior_color_name'];
            $data['exterior_color_value'] = $car_price_info['exterior_color_value'];
            $data['interior_color_id'] = $car_price_info['interior_color_id'];
            $data['interior_color_name'] = $car_price_info['interior_color_name'];
            $data['interior_color_value'] = $car_price_info['interior_color_value'];
            $data['exterior_img'] = $car_price_info['exterior_img'];
            $data['low_price_city_name'] = $car_price_info['city_name'];
            $data['id_4s'] = $car_price_info['user_id'];
            $data['low_price'] = $car_price_info['low_price'];
            if ($car_price_info['low_price'] <= 200000) {
                $car_price_info['deposit'] = 2000;
            } elseif ($car_price_info['low_price'] <= 500000) {
                $car_price_info['deposit'] = 5000;
            } else {
                $car_price_info['deposit'] = 10000;
            }
            $data['order_money'] = $car_price_info['deposit'];
            $data['money'] = $car_price_info['deposit'];
            $data['user_id'] = session('user_id');
            $data['pay_ip'] = $_SERVER['HTTP_X_REAL_IP'];
            $data['status'] = 0;
            $data['pay_obj'] = 2;
            $data['type'] = 1;
            $data['url'] = U('Order/order_ok', array('pay_id' => $data['id']));
            $data['callback'] = U('pay/payback');
            $data['from_order'] = 'PC端';
            $z = M('pay')->add($data);
            if ($z) {
                $datar['status'] = 1;
                $datar['next'] = U('Order/order_info_activity', array('pay_id' => $data['id']));
            } else {
                $datar['status'] = 0;
                $datar['info'] = '订单生成失败，请重试！';
            }
            $this->ajaxReturn($datar);

        } else {
            $this->redirect('Public/login');
        }
    }

    public function displacement()
    {

        $carList = M('car')->field('id,car_name')->where(array('status' => 1, 'isdelete' => 0))->select();
        $regex = "/[1-4]{0,1}+\.+[0-9]{0,1}+[T|L]/";
        $regex1 = "/18T|280T/";//1.4T*/
        $regex2 = "/15N/";//1.5L*/
        $regex3 = "/300T|330T|30 TFSI/";//1.8T
        $regex4 = "/35 TFSI|40 TFSI|45 TFSI/";//2.0T
        $regex5 = "/50 TFSL/";//=3.0T
        foreach ($carList as $k => $v) {
            if (preg_match($regex, $v['car_name'], $res)) {
                M('car')->where(array('id' => $v['id']))->save(array('displacement' => $res['0']));
            }
            if (preg_match($regex1, $v['car_name'])) {
                M('car')->where(array('id' => $v['id']))->save(array('displacement' => '1.4T'));
            }
            if (preg_match($regex2, $v['car_name'])) {
                M('car')->where(array('id' => $v['id']))->save(array('displacement' => '1.5L'));
            }
            if (preg_match($regex3, $v['car_name'])) {
                M('car')->where(array('id' => $v['id']))->save(array('displacement' => '1.8T'));
            }
            if (preg_match($regex4, $v['car_name'])) {
                M('car')->where(array('id' => $v['id']))->save(array('displacement' => '2.0T'));
            }
        }
        exit;
    }

}