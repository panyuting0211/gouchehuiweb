<?php
namespace Home\Controller;

use Think\Controller;

/**
 * Class ApiController前台首页接口
 * @package Home\Controller
 */
class ApiController extends CommonController
{

    /**
     *初始化页面
     */
    public function _initialize()
    {
        header("Access-Control-Allow-Origin: *");
    }

    /**
     *获得品牌的接口
     * (默认按照首字母排序）
     */
    public function getBrand()
    {
        //是否有POST的值传递
        if (IS_POST) {
            $data['isdelete'] = 0;
            $data['is_xunjia'] = 1;
            $data['car_status'] = 1;
            $res = D('view_car_price')->where($data)->field('brand_id,brand_name,logo')->distinct(ture)->select();
            /* if(isset($_POST['access_quantity'])){
                 //判断是否按点击量排序
                 $res = M('brand')->field('id,brand_name,logo,access_quantity,alif,fid')->where($data)->order('access_quantity')->select();
             }else{
                 //默认按照首字母排序
                 $res = M('brand')->field('id,brand_name,logo,access_quantity,alif,fid')->where($data)->order('alif')->select();
             }*/
            //判断是否有数据
            if ($res) {
                foreach ($res as $key => $value) {
                    $res[$key]['logo'] = OSS . str_replace("type", 'big', $value['logo']);
                    $res[$key]['url'] = '/index.php/car/product_search?brand=' . $value['brand_id'];
                }

                $ret['status'] = 1;
                $ret['list'] = $res;
                $ret['msg'] = '返回成功';
            } else {
                $ret['status'] = 0;
                $ret['msg'] = '暂无数据返回';
            }

            $this->ajaxReturn($ret);
        } else {
            $this->redirect('Index/index');
        }
    }

    /**
     * @Description: CURL传递
     * @Return:
     * @Author: 孙磊
     * @Date: 2016/9/19 11:40
     * @Version 2.0
     */
    public function curl_post($url, $data)
    {

        $url = $url;
        $post_data = $data;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        //打印获得的数据
        return $output;
    }

    public function demo_test()
    {
        $url = 'http://test3.gouchehui.com:8082/index.php/Api/getBrand';
        $data['access_quantity'] = 1;
        $res = $this->curl_post($url, $data);

        print_r($res);
    }

    /**
     * @Description:普通图片上传接口
     * @Return:
     * @Author: 孙磊
     * @Date: 2016/9/19 11:39
     * @Version 2.0
     */
    public function upload_file()
    {
        header("Access-Control-Allow-Origin: *");
        if (!empty($_FILES)) {
            $y = upload_local();
            if ($y) {
                $ret['code'] = 0;
                $ret['msg'] = '上传成功';
                $ret['img_url'] = str_replace("big", 'type', $y['img']['savepath'] . $y['img']['savename']);
                $this->ajaxReturn($ret);
            } else {
                $ret['code'] = -1;
                $ret['msg'] = '上传失败';
                $this->ajaxReturn($ret);
            }
        } else {
            $ret['code'] = -2;
            $ret['msg'] = '请上传文件';
            $this->ajaxReturn($ret);
        }
    }

    /**
     * @Description:发送短信的接口
     * @Return:
     * @Author: 孙磊
     * @Date: 2016/9/19 11:39
     * @Version 2.0
     */
    public function sendMessages()
    {
        header("Access-Control-Allow-Origin: *");
        if (IS_POST) {
            $mobile = I('mobile');
            $tpl_id = I('tpl_id');//短信模板ID4852
            $randStr = str_shuffle('1234567890');
            $code = substr($randStr, 0, 6);
            $app = "南京易橙汇";
            $tpl_value = urlencode("#code#=$code&#app#=$app");//变量名和变量值对
            $res = sendMessage($mobile, $tpl_id, $tpl_value);
            $reslut = json_decode($res, true);
            if ($reslut['error_code'] == 0) {
                /*     $code = S('code',$code,array('type'=>'file','expire'=>300));*/
                $ret['code'] = 0;
                $ret['md5code'] = md5($code . $mobile);
                $ret['mobile'] = $mobile;
                $ret['msg'] = "发送成功";
                $this->ajaxReturn($ret);
            } else {
                $ret['code'] = $reslut['error_code'];
                $ret['msg'] = $reslut['reason'];
                $this->ajaxReturn($res);
            }

        } else {
            $this->ajaxReturn("请传递正确的值！");
        }
    }

    /**
     * @Description:手机发送验证码验证
     * @Return:
     * @Author: 孙磊
     * @Date: 2016/9/19 11:37
     * @Version 2.0
     */
    public function verifyMessages()
    {
        header("Access-Control-Allow-Origin: *");
        if (IS_POST) {
            $code = I('code');
            $mobile = I('mobile');
            $md5code = I('md5code');
            if (md5($code . $mobile) != $md5code) {
                $ret['code'] = -1;
                $ret['msg'] = "验证失败";
                $this->ajaxReturn($ret);
            } else {
                $ret['code'] = 0;
                $ret['msg'] = "验证成功";
                $this->ajaxReturn($ret);
            }
        } else {
            $this->ajaxReturn("请传递正确的值！");
        }
    }

    /**
     * @Description:base64图片上传
     * @Return:
     * @Author: 孙磊
     * @Date: 2016/9/19 11:29
     * @Version 2.0
     */
    public function uploadBase64()
    {
        header("Access-Control-Allow-Origin: *");
//        $base64 = "data:image/png;base64,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";
        if (IS_POST) {
            $base64 = I('base64');
            preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $result);
            $base64 = str_replace($result[1], '', $base64);
            $img = base64_decode($base64);
            $user_id = I('user_id');
            $rootPath = './Images';
            $path = $rootPath . '/Upload/headimg/';
            $imgname = md5($user_id) . '.jpg';
            $a = file_put_contents($path . $imgname, $img);//返回的是字节数

            $file['tmp_name'] = $path . $imgname;
            $file['size'] = $a;
            $Aliyun = new \Think\Upload\Driver\Aliyun ();
            $save = $Aliyun->save($file);
            if ($save) {
                $up = M('user_4s')->where(array('id' => $user_id))->save(array('head_url' => '/Upload/headimg/' . $imgname));
                $data['code'] = 0;
                $data['msg'] = "上传成功！";
                $this->ajaxReturn($data);
            } else {
                $data['code'] = -1;
                $data['msg'] = "上传失败！";
                $this->ajaxReturn($data);
            }
        } else {
            $this->ajaxReturn("请上传正确的数据");
        }
    }

    /**
     * @Description:base64图片上传(微信)
     * @Return:
     * @Author: 孙磊
     * @Date: 2016/9/19 11:29
     * @Version 2.0
     */
    public function uploadBase64WeiXin()
    {
        header("Access-Control-Allow-Origin: *");
        if (IS_POST) {
            $base64 = I('base64');
            preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $result);
            $base64 = str_replace($result[1], '', $base64);
            $img = base64_decode($base64);
            $dir_name = I('dir_name');
            $rootPath = './Images';
            $path = $rootPath . '/Upload/' . $dir_name . '/';
            $imgname = md5(time()) . '.jpg';
            $a = file_put_contents($path . $imgname, $img);//返回的是字节数

            $file['tmp_name'] = $path . $imgname;
            $file['size'] = $a;
            $Aliyun = new \Think\Upload\Driver\Aliyun ();
            $save = $Aliyun->save($file);
            if ($save) {
                $data['code'] = 0;
                $data['url'] = '/Upload/' . $dir_name . '/' . $imgname;
                $data['msg'] = "上传成功！";
                $this->ajaxReturn($data);
            } else {
                $data['code'] = -1;
                $data['msg'] = "上传失败！";
                $this->ajaxReturn($data);
            }
        } else {
            $this->ajaxReturn("请上传正确的数据");
        }
    }

    /**
     * @Description:将gch_pay和gch_user_activity中未判别的数据作判别处理
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/9/20 17:02
     * @Version 2.0
     */
    public function datapanbie()
    {
        $user = M('user_activity')->where(array('isdelete' => 0, 'cus_character' => array('exp', 'is not null')))->select();
        foreach ($user as $key => $value) {
            $user_zz = M('user_activity')->where(array('isdelete' => 0, 'tel' => $value['tel'], 'cus_character' => array('exp', 'is null')))->select();
            foreach ($user_zz as $k => $v) {
                $datau['id'] = $v['id'];
                $datau['status_track'] = 2;
                $datau['cus_name'] = $value['cus_name'];
                $datau['cus_remark'] = '系统统一判别';
                $datau['cus_character'] = $value['cus_character'];
                M('user_activity')->save($datau);
            }
        }

        $pay = M('pay')->where(array('isdelete' => 0, 'cus_character' => array('exp', 'is not null'), 'pay_obj' => 1))->select();
        foreach ($pay as $key => $value) {
            $pay_zz = M('pay')->where(array('isdelete' => 0, 'user_id' => $value['user_id'], 'cus_character' => array('exp', 'is null'), 'pay_obj' => 1))->select();
            foreach ($pay_zz as $k => $v) {
                $datau['id'] = $v['id'];
                $datau['status_track'] = 2;
                $datau['cus_name'] = $value['cus_name'];
                $datau['cus_remark'] = '系统统一判别';
                $datau['cus_character'] = $value['cus_character'];
                M('pay')->save($datau);
            }
        }

        show_bug("完成");

    }

    /**
     * @Description：清除缓存
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/10/8 10:01
     * @Version 2.0
     */
    public function clear()
    {
        /* $memcache = new \Think\Cache\Driver\Memcache();
         $memcache->clear();*/
        S('bbs_user_info', null);
    }

    /**
     * @Description:汽车模糊搜索
     * @Return:
     * @Author: 孙磊
     * @Date: 2016/10/9 10:01
     * @Version 2.0
     */
    public function carSearch()
    {
        $select = I('select');
        // print_r(S($select.'_carlist_cache'));exit;
        if (!empty($select)) {
            //对搜索的关键字自增查询数量
            $search_key = M('search_key')->where(array('search_key' => $select, 'isdelete' => 0))->find();
            if ($search_key) {
                M('search_key')->where(array('id' => $search_key['id']))->setInc('search_count'); // 搜索量+1
            } else {
                $arr['id'] = md5(microtime());
                $arr['search_key'] = $select;
                $arr['search_count'] = 1;
                M('search_key')->add($arr);
            }
            //缓存查询数据
            if (S($select . '_carlist_cache') != '') {
                $data['status'] = 1;
                $data['list'] = S($select . '_carlist_cache');
            } else {
                //查询是否有品牌
                $arr['brand_name'] = array('like','%'.$select.'%');
                $brandlist = M('brand')->where($arr)->select();
                if($brandlist){
                    $modellist = array();
                    //通过品牌检索该品牌下每个车型中官方报价最低价和最高价
                    foreach($brandlist as $k=>$v) {
                        $sql = "SELECT
                                    gb.brand_name,gcm.id,gcm.car_model_name,MIN(gc.auth_price) as min_price,MAX(gc.auth_price) as maxprice
                                FROM
                                    gch_car_model AS gcm,gch_car as gc,gch_brand as gb,gch_view_car_price as gvcp
                                WHERE
                                    gcm.brand_id = " . $v['id'] . " and gcm.id=gc.car_model_id AND gb.id =gcm.brand_id and gcm.id=gvcp.car_model_id
                                GROUP BY gcm.car_model_name";
                        $res = M()->query($sql);
                        foreach ($res as $k1 => $v1) {
                            $res[$k1]['url'] = U('car/product_search?select=' .$v1['car_model_name']);
                            $num = count($modellist);
                            if ($num < 10) {
                                array_push($modellist, $res[$k1]);
                            }
                        }
                    }
                    $data['list'] = $modellist;
                    $data['type'] = 'brand';
                    $data['status'] = 1;
                    S($select . '_carlist_cache', $modellist, array('type' => 'file', 'expire' => 3600 * 24));//缓存结果一天
                }else{
                    //模糊查询车款
                    $car_list['_string'] = "concat (brand_name,car_model_name,car_name) like '%" . $select . "%'";
                    $car_list['isdelete'] = 0;
                    $car_list['car_status'] = 1;
                    $carlist = D('view_car_price')->field('brand_name,car_model_name,car_name')->where($car_list)->group('car_model_id')->limit('10')->select();
                    if ($carlist) {
                        foreach ($carlist as $k => $v) {
                            $brand_arr['brand_name'] = array('like', "%$select%");
                            $brand_find = M('brand')->where($brand_arr)->find();
                            $model_arr['model_name'] = array('like', "%$select%");
                            $model_find = M('brand')->where($model_arr)->find();
                            //判断用户输入的是否是查询品牌车型
                            if ($brand_find != '' and $model_find == '') {
                                $carlist[$k]['carstyle'] = $v['brand_name'];
                            } elseif ($brand_find != '' and $model_find != '') {
                                $carlist[$k]['carstyle'] = $v['brand_name'] . $v['car_model_name'];
                            } else {
                                $carlist[$k]['carstyle'] = $v['brand_name'] . $v['car_model_name'] . $v['car_name'];
                            }
                            $carlist[$k]['url'] = U('car/product_search?select=' . $carlist[$k]['carstyle']);
                        }
                        $data['list'] = $carlist;
                        $data['type'] = 'car';
                        $data['status'] = 1;
                        S($select . '_carlist_cache', $carlist, array('type' => 'file', 'expire' => 3600 * 24));//缓存结果一天
                    } else {
                        $data['status'] = 0;
                        $data['msg'] = "暂无查询结果";
                    }
                }
            }
            $this->ajaxReturn($data);
        } else {
            $this->ajaxReturn('数据不能为空');
        }
    }

    /**
     * @Description:一元抢添加活动订单
     * @Return:
     * @Author: 孙磊
     * @Date: 2016/10/24 15:47
     * @Version 2.0
     */
    public function addActivityOrder()
    {
        $car_id = I('car_id');
        if (IS_POST) {
            $data['id'] = md5(microtime());
            $data['out_trade_no'] = $this->createOrderNo();
            $data['user_id'] = I('user_id');
            $data['brand_id'] = I('brand_id');
            $data['brand_name'] = I('brand_name');
            $data['car_model_id'] = I('car_model_id');
            $data['car_model_name'] = I('car_model_name');
            if (!empty($car_id)) {
                $data['car_id'] = $car_id;
                $data['car_name'] = I('car_name');
                $data['carstyle'] = I('brand_name') . I('car_model_name') . I('car_name');
            } else {
                $data['carstyle'] = I('brand_name') . I('car_model_name');
            }
            $data['money'] = 1;
            $data['status'] = 0;
            $data['pay_obj'] = 4;
            $data['pay_ip'] = $_SERVER['HTTP_X_REAL_IP'];
            $data['url'] = '/index.php/Order/order_active_ok?pay_id=' . $data['id'];
            $data['from_caractivityid'] = I('from_caractivityid');//活动ID
            $data['from_activityid'] = I('from_activityid');//活动车款表ID
            $data['from_order'] = I('from_order');
            $data['user_id']= I('user_id');
            if ($data['user_id'] == '' || $data['brand_id'] == '' || $data['brand_name'] == ''  || $data['car_model_id'] == '' || $data['car_model_name'] == '' || $data['from_caractivityid'] == '' || $data['from_activityid'] == '') {
                $this->ajaxReturn("数据填写有误,所有数据都不能为空！");
            }
            $find = M('pay')->where(array('user_id'=>$data['user_id'],'pay_obj'=>4,'from_caractivityid'=>$data['from_caractivityid']))->find();
            if($find){
                $ret['code'] = 1;
                $ret['msg'] = "您已经参加该车款的活动";
            }else{
            $add = M('pay')->add($data);
            if ($add) {
                $ret['code'] = 0;
                $ret['out_trade_no'] = $data['out_trade_no'];
                $ret['id'] = $data['id'];
                $ret['url'] = '/index.php/order/order_active_pay?pay_id=' . $data['id'];
                $ret['msg'] = "抢购成功";
            } else {
                $ret['code'] = -1;
                $ret['msg'] = "抢购失败";
            }
            }
            $this->ajaxReturn($ret);
        } else {
            $this->ajaxReturn("非法操作！");
        }
    }

    /**
     * @Description:一元抢活动订单完善
     * @Return:
     * @Author: 孙磊
     * @Date: 2016/10/24 16:44
     * @Version 2.0
     */
    public function perfectOrder()
    {
        header("Access-Control-Allow-Origin: *");
        if (IS_POST) {
            $out_trade_no = I('out_trade_no');
            $find = M('pay')->where(array('out_trade_no' => $out_trade_no, 'isdelete' => 0))->find();
            if ($find) {
                $data['status'] = 4;
                $data['buyer_tel'] = I('buyer_tel');
                $data['car_id'] = I('car_id');
                $data['car_name'] = I('car_name');
                $data['name_4s'] = I('name_4s');
                $data['low_price'] = I('low_price');
                $data['credit_fee'] = I('credit_fee');
                $data['licensing_fees'] = I('licensing_fees');
                $data['exterior_img'] = I('exterior_img');
                $data['user_remark'] = I('user_remark');
                $data['carstyle'] = $find['brand_name'] . ' ' . $find['car_model_name'] . ' ' . I('car_name');
                if ($data['name_4s'] == '' || $data['low_price'] == '' || $data['licensing_fees'] == '') {
                    $this->ajaxReturn("填写数据有误,必须填写的数据不能为空");
                }
                $save = M('pay')->where(array('id' => $find['id']))->save($data);
                if ($save) {
                    $ret['url'] = '/member.php/MemberGeneral/n_uc_active_list';
                    $ret['code'] = 0;
                    $ret['msg'] = "修改成功";
                } else {
                    $ret['code'] = -1;
                    $ret['msg'] = "修改失败";
                }

                $this->ajaxReturn($ret);
            } else {
                $this->ajaxReturn("订单不存在！");
            }
        } else {
            $this->ajaxReturn("非法操作！");
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

}