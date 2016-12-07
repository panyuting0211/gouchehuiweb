<?php
namespace Admin\Controller;

use Think\Controller;

/**
 * Class CarmanageController 汽车管理控制器
 * @package Admin\Controller
 */
class CarmanageController extends CommonController
{
    /************************************************所有车款--品牌******************************************************************/
    /**
     *所有车款--品牌
     */
    public function allcar()
    {
        $this->display();
    }

    /**
     *所有车款--添加品牌
     */
    public function addcar_brand()
    {
        $brand = M('brand');

        if (!empty($_FILES)) {
            $z = upload_local();

            if ($z) {
                $_POST['logo'] = str_replace('big', 'type', $z['logo']['savepath'] . $z['logo']['savename']);
            } else {
                show_bug('图片上传失败');
            }
        }

        if (!empty($_POST)) {
            $brand->create();
            $brand->id = md5(microtime());
            $brand->createuser = session('admin_name');
            $z = $brand->add();
            if ($z) {
                $this->success('添加成功');
            } else {
                $this->error('添加失败');
            }
        }
        $this->bigBrandList = M('big_brand')->select();
        $this->display();
    }

    /**
     *所有车款--添加大品牌
     */
    public function addBigBrand()
    {
        $bigBrand = M('big_brand');
        if (!empty($_POST)) {
            $data['id'] = md5(microtime());
            $data['big_brand_name'] = I('big_brand_name');
            $data['create_time'] = time();
            $data['create_user'] = session('admin_name');
            $z = $bigBrand->add($data);
            if ($z) {
                $this->success('添加成功');
            } else {
                $this->error('添加失败');
            }
        }
        $this->display('addbigbrand');
    }

    /**
     *所有车款--编辑品牌
     */
    public function editcar_brand()
    {
        $brand = M('brand');
        $brandid = I('brandid');
        $info = $brand->where(array('id' => $brandid))->find();
        $info['logo'] = str_replace('type', 'small', $info['logo']);
        if (!empty($_FILES)) {
            $z = upload_local();
            if ($z) {
                $_POST['logo'] = str_replace('big', 'type', $z['logo']['savepath'] . $z['logo']['savename']);
            } else {
                show_bug('图片上传失败');
            }
        }

        if (!empty($_POST)) {
            $brand->create();
            $brand->updatetime = date('Y-m-d H:i:s', time());
            $brand->updateuser = session('admin_name');
            $zz = $brand->where(array('id' => I('id')))->save();
            if ($zz) {
                $this->success('修改成功');
            } else {
                $this->error('修改失败');
            }

        }
        $this->assign('info', $info);
        $this->bigBrandList = M('big_brand')->select();
        $this->display();
    }

    /**
     *所有车款--删除品牌
     */
    public function deletecar_brand()
    {
        $brand = M('brand');
        $brandid = I('id');
        $data['updatetime'] = date('Y-m-d H:i:s', time());
        $data['updateuser'] = session('admin_name');
        $data['isdelete'] = 1;
        $z = $brand->where(array('id' => $brandid))->save($data);
        if ($z) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }


    }

    /************************************************所有车款--车型******************************************************************/


    /**
     *车型列表
     */
    public function car_model()
    {
        $brand = M('brand');
        $car_type = M('car_type');
        $car_model = M('car_model');

        $infobrand = $brand->where(array('isdelete' => 0))->order('alif')->select();
        foreach ($infobrand as $key => $value) {
            $infobrand[$key]['brand_name'] = $infobrand[$key]['alif'] . $infobrand[$key]['brand_name'];
        }
        $status = I('status');
        $select_brand_id = I('select_brand_id');
        $select_model_id = I('select_model_id');
        // $search_val		=I('search_val');
        /*搜索值搜索*/
        // if(!empty($search_val)){
        // 	$where['car_model_name']=array('like','%'.$search_val.'%');
        // 	$where22['brand_name']	=array('like','%'.$search_val.'%');
        // 	$where22['isdelete']=0;
        // 	$search_brand_id=M('brand')->where($where22)->select();
        // 	if(!empty($search_brand_id)){
        // 		foreach ($search_brand_id as $key => $value) {//将二维数组转换成一维数组
        // 			$search_brand_id2[]=$value['id'];
        // 		}
        // 		$where['brand_id']=array('in',$search_brand_id2);
        // 		$where['_logic']='or';
        // 	}
        // 	$data['_complex']=$where;
        // 	$_SESSION['search_val']=$search_val;
        // }else{
        // 	if(array_key_exists('search_val',$_POST)){
        // 				unset($_SESSION['search_val']);
        // 	}
        // 	else{
        // 		$search_val=$_SESSION['search_val'];
        // 		$where['car_model_name']=array('like',"%".$search_val."%");
        // 		$where22['brand_name']=array('like',"%".$search_val."%");
        // 		$search_brand_id=$brand->where($where22)->field('id')->select();
        // 		if(!empty($search_brand_id)){
        // 			foreach ($search_brand_id as $key => $value) {//将二维数组转换成一维数组
        // 				$search_brand_id2[]=$value['id'];
        // 			}
        // 			$where['brand_id']=array('in',$search_brand_id2);
        // 			$where['_logic']='or';
        // 		}
        // 		$data['_complex']=$where;
        // 	}
        // }
        $data['isdelete'] = 0;
        /**品牌搜索*/
        if (!empty($select_brand_id)) {
            $data['brand_id'] = $select_brand_id;
            $_SESSION['select_brand_id'] = $select_brand_id;
        } else {
            if (array_key_exists('select_brand_id', $_POST)) {
                unset($_SESSION['select_brand_id']);
            } else {
                if (array_key_exists('select_brand_id', $_SESSION)) {
                    $select_brand_id = $_SESSION['select_brand_id'];
                }
            }
        }
        $in_model_info = null;
        if (!empty($select_brand_id)) {
            $data['brand_id'] = $select_brand_id;
            $in_model_info = M('car_model')->where($data)->order('car_model_name')->field('id,car_model_name')->select();
        }

        /**车型搜索*/
        if (!empty($select_model_id)) {
            $data['id'] = $select_model_id;
            $_SESSION['select_model_id'] = $select_model_id;
        } else {
            if (array_key_exists('select_model_id', $_POST)) {
                unset($_SESSION['select_model_id']);
            } else {
                if (array_key_exists('select_model_id', $_SESSION)) {
                    $select_model_id = $_SESSION['select_model_id'];
                }
            }
        }
        if (!empty($select_model_id)) {
            $data['id'] = $select_model_id;
        }

        if ($status != '') {
            $data['status'] = $status;
            $_SESSION['model_status'] = $status;
        } else {
            if (array_key_exists('status', $_POST)) {
                unset($_SESSION['model_status']);
            } else {
                if (array_key_exists('model_status', $_SESSION)) {
                    $status = $_SESSION['model_status'];
                }
            }
        }
        if ($status != '') {
            $data['status'] = $status;
        }

        $count = $car_model->where($data)->count();
        $page = new \Think\Page($count, 15);//分页，每页显示15条
        $infomodel = $car_model->where($data)->order('brand_id,car_model_name')->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($infomodel as $key => $value) {
            $infomodel[$key]['brand_name'] = $brand->where(array('id' => $value['brand_id'], 'isdelete' => 0))->getField('brand_name');
            $infomodel[$key]['car_type_name'] = $car_type->where(array('id' => $value['car_type_id'], 'isdelete' => 0))->getField('car_type_name');
        }
        $pagelist = $page->show();//分页显示
        $this->assign('count', $count);
        $this->assign('pagelist', $pagelist);
        $this->assign('infomodel', $infomodel);
        $this->assign('select_model_id', $select_model_id ? $select_model_id : null);
        $this->assign('select_brand_id', $select_brand_id ? $select_brand_id : null);
        $this->assign('select_status', $status);
        $this->assign('infobrand', $infobrand);
        $this->assign('in_model_info', $in_model_info ? $in_model_info : null);
        $this->display();
    }

    /**
     *添加车型
     */
    public function addcar_model()
    {
        $brand = M('brand');
        $car_type = M('car_type');
        $car_model = M('car_model');
        $infobrand = $brand->where(array('isdelete' => 0))->order('alif')->select();
        foreach ($infobrand as $key => $value) {
            $infobrand[$key]['brand_name'] = $infobrand[$key]['alif'] . $infobrand[$key]['brand_name'];
        }
        $infocartype = $car_type->where(array('isdelete' => 0))->select();
        //接收从品牌过来的固定品牌
        $brand_id = I('brand_id');

        if (!empty($_FILES)) {
            $z = upload_local();
            if ($z) {
                $_POST['imgurl'] = str_replace('big', 'type', $z['imgurl']['savepath'] . $z['imgurl']['savename']);
            } else {
                show_bug('图片上传失败');
            }
        }
        if (!empty($_POST)) {
            $car_model->create();
            $car_model->status = (int)I('status');
            $filter_model = $car_model->where(array('car_model_name' => I('car_model_name'), 'brand_id' => I('brand_id'), 'isdelete' => 0))->find();
            if ($filter_model) {
                $this->error('信息重复啦');
            } else {
                $car_model->id = md5(microtime());
                $car_model->createuser = session('admin_name');
                $z = $car_model->add();
                if ($z) {
                    $this->success('添加成功');
                } else {
                    $this->error('添加失败');
                }
            }

        }

        $this->assign('infobrand', $infobrand);
        $this->assign('infocartype', $infocartype);
        $this->assign('brand_id', $brand_id);
        $this->display();
    }

    /**
     *编辑车型
     */
    public function editcar_model()
    {
        $brand = M('brand');
        $car_type = M('car_type');
        $car_model = M('car_model');

        $infobrand = $brand->where(array('isdelete' => 0))->select();
        $infocartype = $car_type->where(array('isdelete' => 0))->select();

        $modelid = I('modelid');
        $infom = $car_model->where(array('id' => $modelid, 'isdelete' => 0))->find();
        $infom['imgurl'] = str_replace('type', 'small', $infom['imgurl']);
        $infob = $brand->where(array('id' => $infom['brand_id'], 'isdelete' => 0))->find();
        $infot = $car_type->where(array('id' => $infom['car_type_id'], 'isdelete' => 0))->find();
        $this->assign('infob', $infob);
        $this->assign('infom', $infom);
        $this->assign('infot', $infot);
        $this->assign('infobrand', $infobrand);
        $this->assign('infocartype', $infocartype);

        if (!empty($_FILES)) {
            $z = upload_local();

            if ($z) {
                $_POST['imgurl'] = str_replace('big', 'type', $z['imgurl']['savepath'] . $z['imgurl']['savename']);
            } else {
                show_bug('未修改图片');
            }
        }

        if (!empty($_POST)) {
            $status = (int)I('status');
            if ($status == 0) {
                $res = M('car')->where(array('car_model_id' => I('mid')))->find();
                if ($res) {
                    $data['updatetime'] = date('Y-m-d H:i:s', time());
                    $data['updateuser'] = session('admin_name');
                    $data['status'] = 0;
                    M('car')->where(array('car_model_id' => I('mid')))->save($data) or $this->error('系统出错！');
                }
            }
            $car_model->create();
            $car_model->status = $status;
            $car_model->updatetime = date('Y-m-d H:i:s', time());
            $car_model->updateuser = session('admin_name');
            $zz = $car_model->where(array('id' => I('mid')))->save();
            if ($zz) {
                $this->success('修改成功', U('car_model'));
            } else {
                $this->error('修改失败');
            }
        }
        $this->display();
    }

    /**
     *删除车型
     */
    public function deletecar_model()
    {
        $car_model = M('car_model');
        $modelid = I('modelid');
        $data['updatetime'] = date('Y-m-d H:i:s', time());
        $data['updateuser'] = session('admin_name');
        $data['isdelete'] = 1;
        $z = $car_model->where(array('id' => $modelid))->save($data);
        if ($z) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }

    }

    /**
     *车型关注
     */
    public function model_focus()
    {
        $model_id = I('model_id');
        $count = M('user_attention_car_model')->where(array('car_model_id' => $model_id, 'isdelete' => 0))->count();
        $page = new \Think\Page($count, 15);
        $pagelist = $page->show();
        $info = M('user_attention_car_model')->where(array('car_model_id' => $model_id, 'isdelete' => 0))->order('createtime desc')->limit($page->firstRow . ',' . $page->listRows)->field('car_model_id,createtime,isdelete,user_id')->select();
        foreach ($info as $key => $value) {
            $info[$key]['user_name'] = M('user')->where(array('id' => $value['user_id'], 'isdelete' => 0))->getField('user_name');
            $info[$key]['nick'] = M('user')->where(array('id' => $value['user_id'], 'isdelete' => 0))->getField('nick');
        }

        $this->assign('info', $info);
        $this->assign('pagelist', $pagelist);
        $this->display();
    }

    /**
     *车型动态
     */
    public function model_dongtai()
    {
        $model_id = I('model_id');
        $this->model_id = $model_id;
        $this->info = M('attention_push')->where(array('car_model_id' => $model_id, 'isdelete' => 0))->order('createtime desc')->field('id,content,createtime')->select();
        $this->display();
    }

    /**
     *添加车型动态
     */
    public function addmodel_dongtai()
    {
        $data['content'] = I('content');
        $data['car_model_id'] = I('car_model_id');
        $attention_push = M('attention_push');
        // $attention_push->create();
        $data['createuser'] = $_SESSION['admin_name'];
        $data['id'] = md5(microtime());
        $zz = $attention_push->add($data);
        if ($zz) {
            $this->success('添加成功');
        } else {
            $this->error('添加失败');
        }
    }

    /**
     *删除车型动态
     */
    public function del_model_dongtai()
    {
        $data['id'] = I('id');
        $data['updatetime'] = date('Y-m-d H:i:s', time());
        $data['updateuser'] = session('admin_name');
        $data['isdelete'] = 1;
        $zz = M('attention_push')->save($data);
        if ($zz) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /************************************************所有车款--车款************************************************************/

    /**
     *车款列表
     */
    public function carlist()
    {
        $brand = M('brand');
        $car_model = M('car_model');
        $car = M('car');

        $view_car = M('view_car');
        $select_brand = $view_car->where(array('isdelete' => 0))->group('brand_id')->order('brand_alif')->select();
        foreach ($select_brand as $key => $value) {
            $select_brand[$key]['brand_name'] = $select_brand[$key]['brand_alif'] . $select_brand[$key]['brand_name'];
        }
        $brand_id = I('brand_id');//传入过来的4个参数。
        $model_id = I('model_id');
        $car_id = I('car_id');
        $status = I('status');

        $data['isdelete'] = 0;
        if (!empty($brand_id)) {
            $data['brand_id'] = $brand_id;
            $_SESSION['brand_id'] = $brand_id;
        } else {
            if (array_key_exists('brand_id', $_POST)) {
                unset($_SESSION['brand_id']);
            } else {
                if (array_key_exists('brand_id', $_SESSION)) {
                    $brand_id = $_SESSION['brand_id'];
                }
            }
        }
        $in_model_info = '';
        if (!empty($brand_id)) {
            $data['brand_id'] = $brand_id;
            $in_model_info = M('car_model')->where($data)->order('car_model_name')->distinct(true)->field('id,car_model_name')->select();
        }

        if (!empty($model_id)) {
            $data['car_model_id'] = $model_id;
            $_SESSION['model_id'] = $model_id;
        } else {
            if (array_key_exists('model_id', $_POST)) {
                unset($_SESSION['model_id']);
            } else {
                if (array_key_exists('brand_id', $_SESSION)) {
                    $model_id = $_SESSION['model_id'];
                }
            }
        }

        $in_car_info = '';
        if (!empty($model_id)) {
            $data['car_model_id'] = $model_id;
            $in_car_info = M('car')->where($data)->order('car_name')->order('`order` desc')->distinct(true)->field('id,car_name')->select();
        }

        if (!empty($car_id)) {
            $data['id'] = $car_id;
            $_SESSION['car_id'] = $car_id;
        } else {
            if (array_key_exists('car_id', $_POST)) {
                unset($_SESSION['car_id']);
            } else {
                if (array_key_exists('car_id', $_SESSION)) {
                    $car_id = $_SESSION['car_id'];
                }
            }
        }
        if (!empty($car_id)) {
            $data['id'] = $car_id;
        }

        if ($status != '') {
            $data['car_status'] = $status;
            $_SESSION['status'] = $status;
        } else {
            if (array_key_exists('status', $_POST)) {
                unset($_SESSION['status']);
            } else {
                if (array_key_exists('status', $_SESSION)) {
                    $status = $_SESSION['status'];
                }
            }
        }
        if ($status != '') {
            $data['car_status'] = $status;
        }

        $count = $view_car->where($data)->distinct(true)->count();
        $page = new \Think\Page($count, 15);

        $info = $view_car->where($data)->order('brand_alif,car_model_name,`ORDER` desc')->limit($page->firstRow . ',' . $page->listRows)->distinct(true)->select();

        foreach ($info as $key => $value) {
            $exterior_color_info[$key] = M('view_exterior_color')->where(array('car_id' => $value['id'], 'isdelete' => 0))->order('createtime')->field('color_name,color_value')->select();
            $interior_color_info[$key] = M('view_interior_color')->where(array('car_id' => $value['id'], 'isdelete' => 0))->order('createtime')->field('color_name,color_value')->select();
            foreach ($interior_color_info[$key] as $k => $v) {
                $interior_color_info[$key][$k]['color_value'] = explode(' ', $interior_color_info[$key][$k]['color_value']);
                if (count($interior_color_info[$key][$k]['color_value']) == 1) {
                    $interior_color_info[$key][$k]['color_value'][1] = $interior_color_info[$key][$k]['color_value'][0];
                }
            }
            $res = M('car_color_image')->where(array('car_id' => $value['id'], 'isdelete' => 0))->find();
            if ($res) {
                $info[$key]['photo_status'] = 1;
            } else {
                $info[$key]['photo_status'] = 0;
            }
        }
        $pagelist = $page->show();//分页显示
        $this->assign('count', $count);
        $this->assign('pagelist', $pagelist);
        $this->assign('exterior_color_info', $exterior_color_info);
        $this->assign('interior_color_info', $interior_color_info);
        $this->assign('select_brand_id', $brand_id ? $brand_id : null);
        $this->assign('select_model_id', $model_id ? $model_id : null);
        $this->assign('select_car_id', $car_id ? $car_id : null);
        $this->assign('select_status', $status);
        $this->assign('select_brand', $select_brand);
        $this->assign('info2', $info);
        $this->assign('in_model_info', $in_model_info ? $in_model_info : null);
        $this->assign('in_car_info', $in_car_info ? $in_car_info : null);

        $this->display();
    }

    /**
     *添加车款
     */
    public function addcar()
    {
        $brand = M('brand');
        $car_model = M('car_model');
        $car = M('car');
        //获取传入参数
        $brand_id = I('brand_id');
        $model_id = I('model_id');
        $car_name = I('car_name');
        $car_order = I('order');
        //$description = I('description');
        $status = (int)I('status');
        $auth_price = I('auth_price');

        $infom = $car_model->where(array('id' => $model_id))->find();

        $infomodel = $brand->where(array('isdelete' => 0))->order('alif')->select();
        foreach ($infomodel as $key => $value) {
            $infomodel[$key]['brand_name'] = $infomodel[$key]['alif'] . $infomodel[$key]['brand_name'];
        }

        if (!empty($_POST)) {
            if (empty($brand_id) || empty($model_id) || empty($car_name) || empty($car_order) || empty($auth_price)) {
                $this->error('参数不能为空');
            }
            $filter_car = $car->where(array('car_name' => $car_name, 'car_model_id' => $model_id, 'isdelete' => 0))->find();
            if ($filter_car) {
                $this->error('车款已存在，请勿重复添加');
            } else {
                $car_id = md5(microtime());
                $data['id'] = $car_id;
                $data['status'] = $status;
                $data['car_model_id'] = I('model_id');
                $data['car_name'] = I('car_name');
                $data['order'] = $car_order;
                $data['auth_price'] = $auth_price;
                $data['description'] = I('description');
                $data['createuser'] = $_SESSION['admin_name'];
                $data['displacement'] = I('displacement');
                if (M('car')->add($data)) {
                    $this->success('添加成功');
                } else {
                    $this->error('添加失败');
                }
            }
        }
        $this->assign('brand_id', $brand_id);
        $this->assign('infom', $infom);
        $this->assign('infomodel', $infomodel);
        $this->display();
    }

    /**
     *编辑车款
     */
    public function editcar()
    {
        $car_id = I('car_id');
        $info = M('view_car')->where(array('id' => $car_id))->find();
        if (!empty($_POST)) {
            $data2['car_name'] = I('car_name');
            //$data2['description'] = I('description');
            $data2['order'] = I('order');
            $data2['status'] = (int)I('status');
            $data2['auth_price'] = I('auth_price');
            $data2['displacement'] = I('displacement');
            $data2['updatetime'] = date('Y-m-d H:i:s', time());
            $data2['updateuser'] = session('admin_name');
            if (M('car')->where(array('id' => $car_id))->save($data2)) {
                $this->success('修改成功', U('carlist'));
            } else {
                $this->error('修改失败');
            }
        }
        $this->assign('info', $info);
        $this->display();
    }

    /**
     *删除车款
     */
    public function deletecar()
    {
        $car_id = I('car_id');
        $data['isdelete'] = 1;
        $data['updatetime'] = date('Y-m-d H:i:s', time());
        $data['updateuser'] = session('admin_name');
        $z = M('car')->where(array('id' => $car_id))->save($data);
        if ($z) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }

    }

    /**
     *车款-所有图片
     */
    public function carimage()
    {
        $car_color_image = M('car_color_image');
        $car_id = I('car_id');
        $exterior_info = M('view_exterior_color')->where(array('car_id' => $car_id, 'isdelete' => 0))->field('id,color_name')->select();
        $interior_info = M('view_interior_color')->where(array('car_id' => $car_id, 'isdelete' => 0))->field('id,color_name')->select();
        $type = I('type');
        $imgurl = I('images');
        $carimage = I('carimage');
        if (I('exterior_color_id') != '') {
            if (I('exterior_color_id') == 'all') {//如果选择应用到所有外观颜色，则是二维数组信息，否则强制写成[0][1]的二维数组
                $exterior_color_id = M('exterior_color')->where(array('car_id' => I('car_id'), 'isdelete' => 0))->field('id')->select();
                $_SESSION['exterior_color_id'] = $exterior_color_id[0]['id'];
            } else {
                $exterior_color_id[0]['id'] = I('exterior_color_id');
                $_SESSION['exterior_color_id'] = $exterior_color_id[0]['id'];
            }
        }
        if (I('interior_color_id') != '') {
            if (I('interior_color_id') == 'all') {
                $interior_color_id = M('interior_color')->where(array('car_id' => I('car_id'), 'isdelete' => 0))->field('id')->select();
                $_SESSION['interior_color_id'] = $interior_color_id[0]['id'];
            } else {
                $interior_color_id[0]['id'] = I('interior_color_id');
                $_SESSION['interior_color_id'] = $interior_color_id[0]['id'];
            }
        }
        ////跳转后，页面底部显示为所上传的内饰和外观颜色的图片，多个外观或者内饰，则默认为第一个。
        $select_exterior_id = '';
        $select_interior_id = '';
        if (array_key_exists('exterior_color_id', $_SESSION)) {
            $select_exterior_id = $_SESSION['exterior_color_id'];//将传入过来的条件保存，并传给模板。
        }
        $this->assign('select_exterior_id', $select_exterior_id ? $select_exterior_id : null);
        if (array_key_exists('exterior_color_id', $_SESSION)) {
            $select_interior_id = $_SESSION['interior_color_id'];
        }
        $this->assign('select_interior_id', $select_interior_id ? $select_interior_id : null);

        $sql_delete = "update gch_car_color_image set isdelete=1 where ";
        if (I('exterior_color_id') != '' && I('interior_color_id') != '') {//先判断颜色对应图片是否存在，存在删除，删除再添加.
            foreach ($exterior_color_id as $k => $v) {//将所有的删除操作，拼接成一条sql，执行。
                foreach ($interior_color_id as $k1 => $v1) {
                    $sql_delete .= "(exterior_color_id = '" . $v['id'] . "' and interior_color_id ='" . $v1['id'] . "' and car_id = '" . $car_id . "' and type = '" . $type . "' and isdelete=0) or ";
                }
            }
            $sql_delete = substr($sql_delete, 0, strlen($sql_img) - 4);
            M()->execute($sql_delete);
        }
        if (!empty($carimage)) {
            if (I('images') == '' || I('exterior_color_id') == '' || I('interior_color_id') == '') {
                $this->error("上传参数不能为空");
            } else {
                $sql_img = "insert into gch_car_color_image (id,car_id,type,imgurl,exterior_color_id,interior_color_id) values";
                foreach ($imgurl as $key => $value) {//将所有的添加操作，拼接成一条sql执行
                    foreach ($exterior_color_id as $k => $v) {
                        foreach ($interior_color_id as $k1 => $v1) {
                            $imgurl_str = str_replace('center', 'type', $value);
                            $sql_img .= "(REPLACE (uuid(), \"-\", \"\"),'" . $car_id . "','" . $type . "','" . $imgurl_str . "','" . $v['id'] . "','" . $v1['id'] . "'),";
                        }
                    }
                }
                $sql_img = substr($sql_img, 0, strlen($sql_img) - 1);
                $add = M()->execute($sql_img);
                $this->success('添加成功', U('Carmanage/carimage?car_id=' . $car_id . '&new_interior=' . $_SESSION['interior_color_id'] . '&new_exterior=' . $_SESSION['exterior_color_id']));
            }
        }

        $waiguan = '';
        $neishi = '';
        $kongjian = '';
        $xijie = '';
        if (I('new_interior') && I('new_exterior')) {
            $waiguan = $car_color_image->where(array('car_id' => $car_id, 'isdelete' => 0, 'type' => 0, 'exterior_color_id' => I('new_exterior'), 'interior_color_id' => I('new_interior')))->order('imgurl')->select();
            $neishi = $car_color_image->where(array('car_id' => $car_id, 'isdelete' => 0, 'type' => 1, 'exterior_color_id' => I('new_exterior'), 'interior_color_id' => I('new_interior')))->order('imgurl')->select();
            $kongjian = $car_color_image->where(array('car_id' => $car_id, 'isdelete' => 0, 'type' => 2, 'exterior_color_id' => I('new_exterior'), 'interior_color_id' => I('new_interior')))->order('imgurl')->select();
            $xijie = $car_color_image->where(array('car_id' => $car_id, 'isdelete' => 0, 'type' => 3, 'exterior_color_id' => I('new_exterior'), 'interior_color_id' => I('new_interior')))->order('imgurl')->select();
            foreach ($waiguan as $key => $value) {
                $waiguan[$key]['imgurl'] = str_replace('type', 'big', $waiguan[$key]['imgurl']);
            }
            foreach ($neishi as $key => $value) {
                $neishi[$key]['imgurl'] = str_replace('type', 'big', $neishi[$key]['imgurl']);
            }
            foreach ($kongjian as $key => $value) {
                $kongjian[$key]['imgurl'] = str_replace('type', 'big', $kongjian[$key]['imgurl']);
            }
            foreach ($xijie as $key => $value) {
                $xijie[$key]['imgurl'] = str_replace('type', 'big', $xijie[$key]['imgurl']);
            }
        }
        $this->assign('waiguan', $waiguan);
        $this->assign('neishi', $neishi);
        $this->assign('kongjian', $kongjian);
        $this->assign('xijie', $xijie);
        $this->assign('new_interior', I('new_interior'));
        $this->assign('new_exterior', I('new_exterior'));

        $this->assign('car_id', $car_id);
        $this->assign('exterior_info', $exterior_info);
        $this->assign('interior_info', $interior_info);
        $this->car_info = M('view_car')->where(array('id' => $car_id))->find();
        $this->display();
    }

    /**
     *ajax多图上传方法
     */
    public function uploadify()
    {
        // $m = date('Ymd');
        // $targetFolder = $_POST['url']; // Relative to the root
        // $targetPath = $m.'/';
        $verifyToken = md5($_POST['timestamp']);

        if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
            $name = time() . rand();    //设置上传图片的规则
            $z = upload_local();
            if ($z) {
                echo str_replace('big', 'center', $z['Filedata']['savepath'] . $z['Filedata']['savename']);
            } else {
                $this->error('图片上传失败');
            }
        }
    }

    /**
     *删除图片
     */
    public function deleteimage()
    {
        $car_color_image = M('car_color_image');
        $id = I('color_img_id');
        $data['updatetime'] = date('Y-m-d H:i:s', time());
        $data['updateuser'] = session('admin_name');
        $data['isdelete'] = 1;
        $z = $car_color_image->where(array('id' => $id))->save($data);
        if ($z) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }

    }

    /************************************************所有车款--车款--颜色***********************************************************/

    /**
     *汽车外观颜色
     */
    public function car_exterior_color()
    {
        $car_id = I('car_id');
        $exterior_info = M('view_exterior_color')->where(array('car_id' => $car_id, 'isdelete' => 0))->order('createtime')->field('color_name,color_value,id')->select();
        foreach ($exterior_info as $key => $value) {
            $exterior_info[$key]['imgurl'] = M('car_exterior_color_image')->where(array('car_id' => $car_id, 'exterior_color_id' => $value['id'], 'isdelete' => 0))->getField('imgurl');
            $exterior_info[$key]['imgurl'] = str_replace('type', 'big', $exterior_info[$key]['imgurl']);
        }
        $car_info = M('view_car')->where(array('id' => $car_id, 'isdelete' => 0))->find();
        $this->assign('car_info', $car_info);
        $this->assign('exterior_info', $exterior_info);
        $this->display();

    }

    /**
     *修改外观颜色
     */
    public function change_color()
    {
        $id = trim(I('id'));
        $color_value = trim(I('color_value'));
        $color_name = trim(I('color_name'));
        //判断信息是否为空
        if (empty($id) || empty($color_name) || empty($color_value)) {
            $this->ajaxReturn('信息不能为空');
        } else {
            $where['color_name'] = $color_name;
            $where['color_value'] = $color_value;
            $where['isdelete'] = 0;
        }
        //判断信息是否提交一样
        $exterior_info = M('view_exterior_color')->where($where)->where(array('id' => $id))->find();
        // echo M('view_exterior_color')->getLastSql();exit();

        if (!empty($exterior_info)) {
            $this->ajaxReturn('你提交的信息和原本一样');
        }
        //看看是否需要创建新的颜色值
        $res1 = M('color')->where($where)->find();
        if (empty($res1)) {
            $data['id'] = md5(microtime());
            $data['color_name'] = $color_name;
            $data['color_value'] = $color_value;
            $data['type'] = 1;
            $data['createuser'] = $_SESSION['admin_name'];

            $res2 = M('color')->add($data);
            if (empty($res2)) {
                $this->ajaxReturn('系统出错,稍后再试');
            }
            $exterior_color_id = $data['id'];
        } else {
            $exterior_color_id = $res1['id'];
        }
        $datacolor['updatetime'] = date('Y-m-d H:i:s', time());
        $datacolor['updateuser'] = session('admin_name');
        $datacolor['color_id'] = $exterior_color_id;
        $res3 = M('exterior_color')->where(array('id' => $id))->save($datacolor);
        if (!empty($res3)) {
            $this->ajaxReturn('修改成功');
        } else {
            $this->ajaxReturn('系统出错,稍后再试');
        }
    }

    /**
     *删除外观颜色
     */
    public function delete_exterior_color()
    {
        $id = I('id');

        M('exterior_color')->startTrans();//开启事务
        $data['updatetime'] = date('Y-m-d H:i:s', time());
        $data['updateuser'] = session('admin_name');
        $data['isdelete'] = 1;
        $de1 = M('exterior_color')->where(array('id' => $id, 'isdelete' => 0))->save($data);
        $re3 = M('car_exterior_color_image')->where(array('exterior_color_id' => $id, 'isdelete' => 0))->find();
        if (!empty($re3)) {
            M('car_exterior_color_image')->where(array('exterior_color_id' => $id, 'isdelete' => 0))->save($data);
        }
        if ($de1) {
            M('exterior_color')->commit();//成功提交
            $this->success('删除成功');
        } else {
            M('exterior_color')->rollback();//失败回滚
            $this->error('删除失败');
        }
    }

    /**
     *外观颜色上传处理
     */
    public function exterior_colorupHandle()
    {
        $car_id = I('car_id');
        $color_name = trim(I('exterior_color_name'));
        $color_value = trim(I('exterior_color_value'));
        if ($color_name == '' || $color_value == '') {//颜色名称和颜色值不能为空
            $this->error("参数不能为空");
        }
        //获取外观颜色id，看看是否存在，不存在就添加。
        $exterior_info = M('view_exterior_color')->where(array('car_id' => $car_id, 'color_value' => $color_value, 'color_name' => $color_name, 'isdelete' => 0))->getField('id');
        $color_id = M('color')->where(array('color_name' => $color_name, 'color_value' => $color_value, 'type' => 1, 'isdelete' => 0))->getField('id');
        if (empty($exterior_info)) {
            if (empty($color_id)) {//判断颜色库中是否存在对应颜色，没有就添加
                $data1['id'] = md5(microtime());
                $data1['color_name'] = $color_name;
                $data1['color_value'] = $color_value;
                $data1['type'] = 1;
                $data1['createuser'] = $_SESSION['admin_name'];
                M('color')->add($data1) or $this->error("系统出错");
                $color_id = $data1['id'];
            }

            $data2['id'] = md5(microtime());
            $data2['color_id'] = $color_id;
            $data2['car_id'] = $car_id;
            $data2['createuser'] = $_SESSION['admin_name'];
            M('exterior_color')->add($data2) or $this->error("系统出错");//添加外观颜色

            //添加外观颜色，自动在car_color_image表中复制所有的图片与这个外观颜色对应。
            $data3['car_id'] = $car_id;//
            $data3['isdelete'] = 0;
            $data3['interior_color_id'] = array('neq', '0');

            $info = M('car_color_image')->where($data3)->distinct(true)->field('interior_color_id,type,imgurl')->select();
            if ($info) {//如果info不为空，自动复制，不加此条件，在没图片的时候回报错。
                $sql_img = "insert into gch_car_color_image (id,car_id,type,imgurl,exterior_color_id,interior_color_id) values";
                foreach ($info as $key => $value) {
                    $sql_img .= "(REPLACE (uuid(), \"-\", \"\"),'" . $car_id . "','" . $value['type'] . "','" . $value['imgurl'] . "','" . $data2['id'] . "','" . $value['interior_color_id'] . "'),";
                }
                $sql_img = substr($sql_img, 0, strlen($sql_img) - 1);
                M()->execute($sql_img) or $this->error('未能成功复制图片');
            }

        }

        //获取外观颜色id，看看是否存在
        $exterior_color_id = M('view_exterior_color')->where(array('car_id' => $car_id, 'color_value' => $color_value, 'color_name' => $color_name, 'isdelete' => 0))->getField('id');
        $res1 = M('car_exterior_color_image')->where(array('car_id' => $car_id, 'exterior_color_id' => $exterior_color_id, 'isdelete' => 0))->Field('id')->select();
        if (!empty($res1)) {//如果外观颜色对应的图片存在，则删除。
            $data['updatetime'] = date('Y-m-d H:i:s', time());
            $data['updateuser'] = session('admin_name');
            $data['isdelete'] = 1;
            M('car_exterior_color_image')->where(array('car_id' => $car_id, 'exterior_color_id' => $exterior_color_id))->save($data);
        }
        $imgurl = I('images');

        if (empty($imgurl)) {
            $this->error("图片上传不能为空");
        } else {//重新添加图片，外观图片采取覆盖的方式添加
            foreach ($imgurl as $key => $value) {
                $data1['id'] = md5(microtime());
                $data1['exterior_color_id'] = $exterior_color_id;
                $data1['car_id'] = $car_id;
                $data1['imgurl'] = str_replace('center', 'type', $value);
                $data1['createuser'] = $_SESSION['admin_name'];
                if (M('car_exterior_color_image')->add($data1)) {
                    $this->success("添加成功");
                } else {
                    $this->error("添加失败");
                }
            }

        }

    }

    /**
     *内饰颜色
     */
    public function car_interior_color()
    {
        $car_id = I('car_id');
        $car_info = M('view_car')->where(array('id' => $car_id))->find();
        $interior_color_info = M('view_interior_color')->where(array('car_id' => $car_id, 'isdelete' => 0))->order('createtime')->select();
        foreach ($interior_color_info as $k => $v) {
            $interior_color_info[$k]['color_value'] = explode(' ', $interior_color_info[$k]['color_value']);
            if (count($interior_color_info[$k]['color_value']) == 1) {
                $interior_color_info[$k]['color_value'][1] = $interior_color_info[$k]['color_value'][0];
            }
        }
        $this->assign('car_info', $car_info);
        $this->assign('interior_color_info', $interior_color_info);

        $this->display();

    }

    /**
     *内饰颜色上传处理
     */
    public function interior_colorupHandle()
    {
        $data['color_name'] = trim(I('interior_color_name'));
        $data['color_value'] = trim(I('interior_color_value'));
        $data['car_id'] = I('car_id');
        $data['isdelete'] = 0;
        if ($data['color_name'] == '' || $data['color_value'] == '') {
            $this->error("颜色值和颜色名称不能为空");
        }
        $res = M('view_interior_color')->where($data)->getField('id');
        if ($res != '') {//判断颜色参数是否存在
            $this->error("参数已存在，请勿重复添加");
        } else {
            $color_id = M('color')->where(array('color_name' => trim(I('interior_color_name')), 'color_value' => trim(I('interior_color_value')), 'type' => 2, 'isdelete' => 0))->getField('id');
            if (empty($color_id)) {//判断颜色库中是否存在颜色数据
                $data1['id'] = md5(microtime());
                $data1['type'] = 2;
                $data1['createuser'] = $_SESSION['admin_name'];
                $data1['color_value'] = $data['color_value'];
                $data1['color_name'] = $data['color_name'];
                M('color')->add($data1) or $this->error("系统出错");
                $color_id = $data1['id'];
            }
            $data2['id'] = md5(microtime());
            $data2['car_id'] = I('car_id');
            $data2['color_id'] = $color_id;
            $data2['createuser'] = $_SESSION['admin_name'];

            $data3['car_id'] = I('car_id');//添加内饰颜色自动在car_color_image中复制颜色图片
            $data3['isdelete'] = 0;
            $data3['interior_color_id'] = array('neq', '0');

            $info = M('car_color_image')->where($data3)->distinct(true)->field('exterior_color_id,type,imgurl')->select();
            if ($info) {//如果info不为空，自动复制，不加此条件，在没图片的时候回报错。
                $sql_img = "insert into gch_car_color_image (id,car_id,type,imgurl,exterior_color_id,interior_color_id) values";
                foreach ($info as $key => $value) {
                    $sql_img .= "(REPLACE (uuid(), \"-\", \"\"),'" . I('car_id') . "','" . $value['type'] . "','" . $value['imgurl'] . "','" . $value['exterior_color_id'] . "','" . $data2['id'] . "'),";
                }
                $sql_img = substr($sql_img, 0, strlen($sql_img) - 1);
                M()->execute($sql_img) or $this->error('未能成功复制图片');
            }

            if (M('interior_color')->add($data2)) {
                $this->success("添加成功");
            } else {
                $this->error("添加失败");
            }

        }
    }

    /**
     *删除内饰颜色
     */
    public function delete_interior()
    {
        //删除颜色，同时删除图片
        M('interior_color')->startTrans();//开启事务
        $data['updatetime'] = date('Y-m-d H:i:s', time());
        $data['updateuser'] = session('admin_name');
        $data['isdelete'] = 1;
        $res1 = M('interior_color')->where(array('id' => I('id'), 'isdelete' => 0))->save($data);
        $res2 = M('car_color_image')->where(array('interior_color_id' => I('id'), 'isdelete' => 0))->find();
        if ($res2) {
            $res2 = M('car_color_image')->where(array('interior_color_id' => I('id'), 'isdelete' => 0))->save($data);
        } else {
            $res2 = 1;
        }

        if ($res1 && $res2) {//没有用&&res2，是因为可能没有图片给你删
            M('interior_color')->commit();//成功提交
            $this->success('删除成功');
        } else {
            M('interior_color')->rollback();//失败回滚
            $this->error('删除失败');
        }
    }

    /************************************************所有车款--车款--公开底价车款设置***********************************************/

    /**
     *首页空开底价设置
     */
    public function set_public_carprice()
    {
        //添加的公开底价的所有品牌
        $brand_info = M('brand')->where(array('isdelete' => 0))->order('alif')->field('id,brand_name,alif')->select();
        foreach ($brand_info as $key => $value) {
            $brand_info[$key]['brand_name'] = $brand_info[$key]['alif'] . $brand_info[$key]['brand_name'];
        }
        $this->assign('brand_info', $brand_info);

        //已经存在的公开底价的品牌筛选
        $brand_info_2 = M('index_car_price')->where(array('isdelete' => 0))->order('brand_alif')->group('brand_id')->field('id,brand_name,brand_alif,brand_id')->select();
        foreach ($brand_info_2 as $key => $value) {
            $brand_info_2[$key]['brand_name'] = $brand_info_2[$key]['brand_alif'] . $brand_info_2[$key]['brand_name'];
        }
        $this->assign('brand_info_2', $brand_info_2);


        $index_car_price = M('index_car_price');
        if (!empty($_POST)) {
            if (I('exterior_color_id') == '' || I('interior_color_id') == '') {
                if (I('model_id') == '') {
                    $this->error('至少车型不能为空！');
                } else {
                    //车款级别批量添加功能
                    if (I('car_id') == '') {
                        $allCar = M('view_car_color')->where(array('brand_id' => I('brand_id'), 'car_model_id' => I('model_id'), 'isdelete' => 0, 'car_status' => 1))->group('car_id,exterior_color_id,interior_color_id')->select();
                    } else {
                        $allCar = M('view_car_color')->where(array('brand_id' => I('brand_id'), 'car_model_id' => I('model_id'), 'car_id' => I('car_id'), 'isdelete' => 0, 'car_status' => 1))->group('exterior_color_id,interior_color_id')->select();
                    }
                    if ($allCar) {
                        $sql = "insert into gch_index_car_price (id,car_id,createuser,status,exterior_color_id,interior_color_id,brand_alif,brand_id,brand_name,model_name,model_id,isbaojia) values";
                        foreach ($allCar as $k => $v) {
                            $find = M('index_car_price')->where(array('car_id' => $v['car_id'], 'exterior_color_id' => $v['exterior_color_id'], 'interior_color_id' => $v['interior_color_id'], 'isdelete' => 0))->find();
                            if ($find) {
                                //删除之前添加过的记录
                                unset($allCar[$k]);
                            } else {
                                $baojia = M('car_price')->field('id')->where(array('car_id' => $v['car_id'], 'exterior_color_id' => $v['exterior_color_id'], 'interior_color_id' => $v['interior_color_id'], 'isdelete' => 0))->find();
                                if ($baojia) {
                                    $isbaojia = 1;
                                    $xunjia = M('car_price')->field('id')->where(array('car_id' => $v['car_id'], 'exterior_color_id' => $v['exterior_color_id'], 'interior_color_id' => $v['interior_color_id'], 'isdelete' => 0, 'is_xunjia' => 1))->find();
                                    if ($xunjia) {
                                        M('car_price')->where(array('id' => $xunjia['id']))->save(array('is_xunjia' => 2, 'updateuser' => $_SESSION['admin_name'], 'updatetime' => date('Y-m-d H:i:s', time())));
                                    }
                                } else {
                                    $isbaojia = 0;
                                }
                                $sql .= "(REPLACE (uuid(), \"-\", \"\"),'" . $v['car_id'] . "','" . $_SESSION['admin_name'] . "','2','" . $v['exterior_color_id'] . "','" . $v['interior_color_id'] . "','" . $v['brand_alif'] . "','" . $v['brand_id'] . "','" . $v['brand_name'] . "','" . $v['car_model_name'] . "','" . $v['car_model_id'] . "','" . $isbaojia . "'),";
                            }
                        }
                        $sql = substr($sql, 0, strlen($sql) - 1);
                        $len = strlen($sql);
                        if ($len <= 163) {
                            $this->error('您选择的车款之前已添加完毕！');
                        } else {
                            $add = M()->execute($sql);
                            if ($add) {
                                $this->success('添加成功');
                            } else {
                                $this->error('添加失败');
                            }
                        }

                    }

                }
            } else {
                $index_car_price->create();
                $index_car_price->id = md5(microtime());
                $index_car_price->brand_alif = M('brand')->where(array('id' => I('brand_id'), 'isdelete' => 0))->getField('alif');
                $index_car_price->model_name = M('car_model')->where(array('id' => I('model_id'), 'isdelete' => 0))->getField('car_model_name');
                $index_car_price->createuser = $_SESSION['admin_name'];
                $index_car_price->brand_name = M('brand')->where(array('id' => I('brand_id'), 'isdelete' => 0))->getField('brand_name');
                $re = M('view_car_price')->where(array('car_id' => I('car_id'), 'exterior_color_id' => I('exterior_color_id'), 'interior_color_id' => I('interior_color_id'), 'isdelete' => 0))->find();
                $re2 = M('index_car_price')->where(array('car_id' => I('car_id'), 'exterior_color_id' => I('exterior_color_id'), 'interior_color_id' => I('interior_color_id'), 'isdelete' => 0))->find();
                if ($re2) {
                    $this->error('车款已存在，请勿重复添加');
                }
                if ($re) {
                    $index_car_price->isbaojia = 1;
                    $isxunjia['is_xunjia'] = 2;
                    $re = M('view_car_price')->where(array('car_id' => I('car_id'), 'exterior_color_id' => I('exterior_color_id'), 'interior_color_id' => I('interior_color_id'), 'isdelete' => 0))->save($isxunjia);
                } else {
                    $index_car_price->isbaojia = 0;
                }
                $zz = $index_car_price->add();
                if ($zz) {
                    $this->success('添加成功');
                } else {
                    $this->error('添加失败');
                }
            }
        }
        $this->assign('carprice_info', $carprice_info);
        $this->display();
    }

    /**
     *删除首页公开底价设置
     */
    public function del_index_car_price()
    {
        $id = I('id');
        $data['updatetime'] = date('Y-m-d H:i:s', time());
        $data['updateuser'] = session('admin_name');
        $data['isdelete'] = 1;
        $zz = M('index_car_price')->where(array('id' => $id))->save($data);
        if ($zz) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     *首页空开底价车图片展示
     */
    public function car_index_image()
    {
        $id = I('id');
        if (empty($id)) {
            $this->error('非法操作');
        }
        $imginfo = M('index_car_price')->where(array('id' => $id, 'isdelete' => 0))->find();

        $imginfo['imgurl'] = str_replace('type', 'big', $imginfo['imgurl']);

        $imginfo['car_name'] = M('car')->where(array('id' => $imginfo['car_id']))->getField('car_name');

        $this->assign('id', $id);
        $this->assign('imginfo', $imginfo);
        $this->display();
    }

    /**
     *首页公开底价车图片上传处理
     */
    public function car_index_image_uphanddle()
    {
        $id = I('id');
        if (!empty($_FILES)) {
            $z = upload_local();
            if ($z) {
                $bigimg = str_replace('big', 'type', $z['imgurl']['savepath'] . $z['imgurl']['savename']);
            } else {
                show_bug('图片上传失败');
            }
        } else {
            $this->error('图片上传不能为空');
        }
        $data['id'] = $id;
        $data['imgurl'] = $bigimg;
        $data['updatetime'] = date('Y-m-d H:i:s', time());
        $data['updateuser'] = session('admin_name');
        if (M('index_car_price')->save($data)) {
            $this->success('添加成功');
        } else {
            $this->error('添加失败');
        }
    }

    /************************************************所有车款--车款--价格趋势***************************************************/

    /**
     *所有车款价格趋势
     */
    public function priceline()
    {
        $this->display();
    }

    /************************************************车型分类******************************************************************/
    /**
     *车型分类列表
     */
    public function cartype()
    {
        $this->display();
    }

    /**
     *添加车型分类
     */
    public function addcartype()
    {
        $cartype = M('car_type');

        if (!empty($_FILES)) {
            $z = upload_local();
            if ($z) {
                $bigimg = str_replace('big', 'type', $z['logo']['savepath'] . $z['logo']['savename']);
            } else {
                show_bug('ÎÄ¼þÉÏ´«Ê§°Ü£¡');
            }
        }

        if (!empty($_POST)) {
            $data['id'] = md5(microtime());
            $data['car_type_name'] = I('typename');
            $data['logo'] = $bigimg;
            $data['createuser'] = session('admin_name');
            $info = $cartype->where(array('car_type_name' => I('typename'), 'isdelete' => 0))->find();
            if ($info) {
                $this->error('车型分类已存在');
            } else {
                $zz = $cartype->add($data);
                if ($zz) {
                    $this->success('添加成功');
                } else {
                    $this->error('添加失败');
                }
            }
        }
        $this->display();
    }

    /**
     *编辑车型分类
     */
    public function editcartype()
    {
        $cartype = M('car_type');
        $typeid = I('typeid');
        $info = $cartype->where(array('id' => $typeid, 'isdelete' => 0))->find();
        $info['logo'] = str_replace('type', 'big', $info['logo']);


        if (!empty($_FILES)) {
            $z = upload_local();
            if ($z) {
                $_POST['logo'] = str_replace('big', 'type', $z['logo']['savepath'] . $z['logo']['savename']);
            } else {
                show_bug('图片上传失败');
            }
        }

        if (!empty($_POST)) {
            $cartype->create();
            $cartype->updatetime = date('Y-m-d H:i:s', time());
            $cartype->updateuser = session('admin_name');
            $zz = $cartype->where(array('id' => I('id')))->save();
            if ($zz) {
                $this->success('修改成功');
            } else {
                $this->error('修改失败');
            }

        }
        $this->assign('info', $info);
        $this->display();
    }

    /**
     *删除车型ajax
     */
    public function deletecartype_ajax()
    {
        $id = I('id');
        if (!empty($id)) {
            $data['updatetime'] = date('Y-m-d H:i:s', time());
            $data['updateuser'] = session('admin_name');
            $data['isdelete'] = 1;
            $z = M('car_type')->where(array('id' => $id))->save($data);
            $this->ajaxReturn($z);
        }
    }

    /************************************************特价车******************************************************************/

    /**
     *特价车列表
     */
    public function carspecial()
    {
        //获得开始和结束时间
        $time = date('Y-m-d');
        $endtime = strtotime($time . '23:59:59');
        $starttime = $endtime - 3600 * 24 * 31 + 1; // 默认查询一个月内下订单信息
        $this->assign('endtime', !empty($endtime) ? $endtime : null);
        $this->assign('starttime', !empty($starttime) ? $starttime : null);
        $brand = M('brand')->distinct('true')->field('id,brand_name')->where(array('isdelete' => 0))->select();
        $this->assign('brand', $brand);
        $this->display();
    }

    /**
     *删除特价车
     */
    public function deletecarspecial()
    {
        $id = I('id');
        $data['updatetime'] = date('Y-m-d H:i:s', time());
        $data['updateuser'] = session('admin_name');
        $data['isdelete'] = 1;
        $res = M('special_price_car')->where(array('id' => $id))->save($data);
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     *特价车详情
     */
    public function special_details()
    {
        $id = I('id');
        $info = D('view_special_price_car')->where(array('id' => $id))->find();
        $info['car_image'] = OSS . str_replace('type', 'small', $info['car_image']);
        $area_info = M("sales_area")->where(array('car_special_id' => $id, 'isdelete' => 0))->select();
        foreach ($area_info as $k => $v) {
            $info_area[$k]['area'] = $v['sales_area_name'];
            $info_area[$k]['lvl'] = $v['sales_area_level'];

        }
        $info['cities'] = $info_area;
        $this->assign('info', $info);

        if (!empty($_FILES)) {
            $y = upload_local();
            if ($y) {
                $_POST['car_image'] = str_replace("big", 'type', $y['image']['savepath'] . $y['image']['savename']);
            }
            $z = M('special_price_car')->where(array('id' => $id))->save(array('car_image' => $_POST['car_image'], 'updatetime' => date('Y-m-d H:i:s', time()), 'updateuser' => session('admin_user')));
            if ($z) {
                $this->success('图片修改成功');
            } else {
                $this->error('图片修改失败');
            }
        }
        $this->display();
    }

    /**
     *审核特价车
     */
    public function check_special()
    {
        $confirm = I('confirm');
        $data['id'] = I('car_special_id');
        if ($confirm == 1) {
            $info = M('special_price_car')->where($data)->find();
            $time = date('Y-m-d H:i:s', time());
            if ($time < $info['start_date']) {
                $datac['status'] = 2;
            } elseif ($time >= $info['start_date'] and $time <= $info['end_date']) {
                $datac['status'] = 3;
            } elseif ($time > $info['end_date']) {
                $datac['status'] = 5;
            }
        } else {
            $datac['remark'] = I('remark');
            $datac['status'] = 6;
        }
        $datac['updatetime'] = date('Y-m-d H:i:s', time());
        $datac['updateuser'] = session('admin_name');
        $z = M('special_price_car')->where($data)->save($datac);
        if ($z) {
            $datar['status'] = 1;
            $datar['msg'] = '审核成功！';
        } else {
            $datar['status'] = 2;
            $datar['msg'] = '审核失败！';
        }
        $this->ajaxReturn($datar);
    }


    /************************************************底价车******************************************************************/

    /**
     *底价车列表
     */
    public function carprice()
    {
        $view_car_price = D('view_car_price');
        $city = $view_car_price->where(array('isdelete' => 0))->distinct(true)->field('city_name')->select();
        $brand_info_new = M('view_car_price')->distinct(true)->order('brand_alif')->field('brand_name,brand_alif')->select();//获取所有品牌
        foreach ($brand_info_new as $key => $value) {
            $brand_info_new[$key]['brand_name'] = $brand_info_new[$key]['brand_alif'] . $brand_info_new[$key]['brand_name'];
        }

        $this->assign('city_name', $city);
        $this->assign('brand_info_new', $brand_info_new);
        $this->display();
    }

    /************************************************礼包******************************************************************/
    /**
     *礼包列表
     */
    public function carspecial_gift()
    {
        $gift = M('buy_car_package');
        if (!empty($_FILES)) {
            $z = upload_local();
            if ($z) {
                $_POST['imgurl'] = str_replace('big', 'type', $z['imgurl']['savepath'] . $z['imgurl']['savename']);
            } else {
                $this->error('图片上传失败');
            }
        }
        if (!empty($_POST)) {
            $min_price = I('min_price');
            $max_price = I('max_price');
            $price = I('price');
            $imgurl = I('imgurl');
            $description = I('description');
            $gift_min_price = $gift->where(array('isdelete' => 0))->min('min_price');
            $gift_max_price = $gift->where(array('isdelete' => 0))->max('max_price');
            if ($min_price == '' || empty($max_price) || empty($price) || empty($imgurl) || empty($description)) {
                $this->error('参数不能为空');
            } else {
                if ($_POST['brand_id'] != '') {
                    $gift->brand_id = I('brand_id');
                } else {
                    if (($min_price >= $gift_min_price && $min_price <= $gift_max_price) || ($max_price >= $gift_min_price && $max_price <= $gift_max_price)) {
                        $this->error('价格区间不能重复且不能交叉');
                    }
                }

            }

            $gift->create();
            $gift->id = md5(microtime());
            $gift->createuser = $_SESSION['admin_name'];
            $z = $gift->add();
            if ($z) {
                $this->success('添加成功', U('Carmanage/carspecial_gift'));
            } else {
                $this->error('添加失败');
            }

        }
        $this->brand = M('brand')->where(array('isdelete' => 0))->field('id,brand_name')->order('alif')->select();
        $info = M('buy_car_package')->where(array('isdelete' => 0))->order('createtime desc')->select();
        $count = M('buy_car_package')->where(array('isdelete' => 0))->count();
        /* $info = M('buy_car_package')->where(array('isdelete' => 0))->order('createtime desc')->select();*/
        foreach ($info as $key => $value) {
            $info[$key]['brand_name'] = M('brand')->where(array('id' => $value['brand_id']))->getField('brand_name');
            $info[$key]['imgurl'] = str_replace('type', 'big', $info[$key]['imgurl']);
        }
        $this->assign('count', $count);
        $this->assign('info', $info);
        $this->display();
    }

    /**
     *删除礼包
     */
    function del_gift()
    {
        $data['id'] = I('id');
        // echo ($data['id']);
        $data['updatetime'] = date('Y-m-d H:i:s', time());
        $data['updateuser'] = session('admin_name');
        $data['isdelete'] = 1;
        $zz = M('buy_car_package')->save($data);
        if ($zz) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
        * @Description:热门品牌列表页
        * @Return:
        * @Author: 孙磊
        * @Date: 2016/11/3 15:09
        * @Version 2.0
        */
    public function HotBrand(){

        $this->render();
    }
    /**
        * @Description:添加或者编辑热门品牌
        * @Return:
        * @Author: 孙磊
        * @Date: 2016/11/3 16:45
        * @Version 2.0
        */
    public function AddHotBrand(){
        $infobrand = M('brand')->where(array('isdelete' => 0))->order('alif')->select();
        foreach ($infobrand as $key => $value) {
            $infobrand[$key]['brand_name'] = $infobrand[$key]['alif'] . $infobrand[$key]['brand_name'];
        }
        //判断是否是修改数据
        if(isset($_GET['id']) && $_GET['id']!=''){
            $id = I('id');
            $info = M('hot_brand')->where(array('id'=>$id))->find();
            $this->assign('info',$info);
        }
        //判断是否是添加或者编辑数据
        if(IS_POST){
            //修改
            if(isset($_POST['id']) && $_POST['id']!=''){
                $save = M('hot_brand')->where(array('id'=>I('id')))->save($_POST);
                if($save){
                    $this->success("修改成功",U('hotbrand'));
                }else{
                    $this->error("修改失败！");
                }
            }
            //判断提交的数据是否为空
            if($_POST['brand_id'] == '' || $_POST['order'] ==''){
                $this->error("数据提交有误,不能为空！");
            }
            //判断之前是否已经添加
            $find = M('hot_brand')->where(array('brand_id'=>I('brand_id'),'client'=>I('client')))->find();
            if($find){
                $this->error("该数据已经添加过！");
            }else{
                //没有添加执行添加操作
                $_POST['createuser'] = $_SESSION['admin_name'];
                if(M('hot_brand')->add($_POST)){
                    $this->success("添加成功",U('hotbrand'));
                }else{
                    $this->error("添加失败！");
                }
            }
        }
        $this->assign("infobrand",$infobrand);
        $this->render('addhotbrand');
    }

}