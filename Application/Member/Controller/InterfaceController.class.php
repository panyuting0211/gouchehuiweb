<?php
namespace Member\Controller;
use Think\Controller;

/**
 * Class MemberGeneralController前台会员中心
 * @package Member\Controller
 */
class  InterfaceController extends Controller
{
    /**
     *商城调用接口----用户信息
     */
    public function user_info()
    {
        $data['id']=I('user_id');
        $info=D('UserGeneral')->user_find($data);
        $info = serialize($info);
        echo $info;
    }

    /**
     *商城调用接口----收货地址
     */
    public function address_info()
    {
        $data['user_id']=I('user_id');
        $data['role']=1;
        $info=D('ReceiptAddress')->address_select($data);
        $info = serialize($info);
        echo $info;
    }

    /**
     *商城调用接口----修改收货地址
     */
    public function receipt_address_save()
    {
        if(!empty($_POST))
        {
            $address = D("ReceiptAddress"); // 实例化User对象
            if (!$address->create($_POST)){ // 指定新增数据
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $data['status']=1;
                $data['msg']=$address->getError();
            }else{
                session('user_id',$_POST['user_id']);
                // 验证通过 可以进行其他数据操作
                if($address->address_save())
                {
                    $data['status']=2;
                    $data['msg']='地址修改成功！';
                }else{
                    $data['status']=1;
                    $data['msg']='地址修改失败！';
                }
            }
            $info = serialize($data);
            echo $info;
        }
    }

    /**
     *添加收货地址
     */
    public function receipt_address_add()
    {
        if(!empty($_POST))
        {
            $_POST['role']=1;
            $address = M("ReceiptAddress"); // 实例化User对象
            $_POST['receipt_province'] = M('data_province')->where(array('ProvinceID'=>$_POST['province_id']))->getfield('ProvinceName');
            $_POST['receipt_city'] = M('data_city')->where(array('CityID'=>$_POST['city_id']))->getfield('CityName');
            $_POST['receipt_quarter'] = M('data_district')->where(array('DistrictID'=>$_POST['quarter_id']))->getfield('DistrictName');
            $_POST['id']=md5(microtime());
            if (!$address->create($_POST)){ // 指定新增数据
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $data['status']=1;
                $data['msg']=$address->getError();
            }else{
                if ($_POST['status']==1)
                {
                    $find = $address->where(array('user_id'=>$_POST['user_id'],'status'=>1))->select();
                    if($find){
                        $address->where(array('user_id'=>$_POST['user_id']))->save(array('status'=>0));
                    }
                }
                //判断是否大于3个
                $count1=$address->where(array('isdelete'=>0,'user_id'=>$_POST['user_id']))->select();
                $count = count($count1,2);

                if($count>3){
                    $data['status']=1;
                    $data['msg']='地址最多只能添加3个！';
                }else{
                    // 验证通过 可以进行其他数据操作
                    if($address->add($_POST))
                    {
                        $data['status']=2;
                        $data['msg']='地址添加成功！';
                    }else{
                        $data['status']=1;
                        $data['msg']='地址添加失败！';
                    }
                }

            }
            $info = serialize($data);
            echo $info;
        }

    }

    /**
     *删除收货地址
     */
    public function receipt_address_delete()
    {
        $data['updatetime']=date('Y-m-d H:i:s',time());
        $data['updateuser']=M('user_general')->where(array('id'=>$_POST['user_id']))->getField('user_name');
        $data['isdelete']=1;
        $data['id']=$_POST['id'];
        if (M('receipt_address')->save($data))
        {
            $data['status']=1;
            $data['msg']='删除成功！';
        }else
        {
            $data['status']=2;
            $data['msg']='删除失败！';
        }
        $info = serialize($data);
        echo $info;

    }

    /**
     *商城积分增加
     */
    public function score_increase()
    {
        $score=D('Score');
        if(!empty($_POST['points']))
        {
            M('user_general')->where(array('id'=>$_POST['user_id']))->setInc('total_jifen',$_POST['points']);
            $data1['id']=md5(microtime());
            $data1['user_id']=$_POST['user_id'];
            $data1['role']=1;
            $data1['flag'] = $_POST['flag'];
            $data1['from_id'] = $_POST['from_id'];
            $data1['score']=$_POST['points'];
            $data1['info']=$_POST['info'];
            $data1['createuser']=M('user_general')->where(array('id'=>$_POST['user_id']))->getField('user_name');
            $z1=$score->add($data1);
            if ($z1)
            {
                $data['status']=1;
                $data['msg']='操作成功！';
            }else{
                $data['status']=2;
                $data['msg']='操作失败！';

            }
        }
        $info = serialize($data);
        echo $info;
    }

    /**
     *积分减少
     */
    public function score_decrease()
    {
        if (!empty($_POST['integral']))
        {
            $score=D('Score');
            M('user_general')->where(array('id'=>$_POST['user_id']))->setDec('total_jifen',$_POST['integral']);
            $data2['id']=md5(microtime());
            $data2['user_id']=$_POST['user_id'];
            $data2['role']=1;
            $data2['flag'] = $_POST['flag'];
            $data2['from_id'] = $_POST['from_id'];
            $data2['score']=$_POST['integral'];
            $data2['info']=$_POST['info'];
            $data2['createuser']=M('user_general')->where(array('id'=>$_POST['user_id']))->getField('user_name');
            $z2=$score->add($data2);
            if ($z2)
            {
                $data['status']=1;
                $data['msg']='操作成功！';
            }else{
                $data['status']=2;
                $data['msg']='操作失败！';
            }
        }

        $info = serialize($data);
        echo $info;

    }

    /**
     *获取地区
     */
    public function getinfo()
    {
        $type=I('type');
        $quarter=I('quarter');
        if($type==1 and $quarter==0)
        {
            $info=M('data_province')->select();
            foreach($info as $key=>$value)
            {
                $data[$key]['name']=$value['provincename'];
                $data[$key]['value']=$value['provinceid'];
            }
        }elseif($type==2 and !empty($quarter))
        {
            $info=M('data_city')->where(array('ProvinceID'=>$quarter))->select();
            foreach($info as $key=>$value)
            {
                $data[$key]['name']=$value['cityname'];
                $data[$key]['value']=$value['cityid'];
            }

        }elseif($type==3 and !empty($quarter))
        {
            $info=M('data_district')->where(array('CityID'=>$quarter))->select();
            foreach($info as $key=>$value)
            {
                $data[$key]['name']=$value['districtname'];
                $data[$key]['value']=$value['districtid'];
            }
        }

        $allinfo=serialize($data);
        echo $allinfo;

    }


}