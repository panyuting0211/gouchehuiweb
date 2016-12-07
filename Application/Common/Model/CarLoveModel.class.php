<?php
namespace Common\Model;
use Think\Model;

class CarLoveModel extends Model
{
    /**
     * 自动验证----静态方式
     * @var array
     */
    protected $_validate = array(
        array('car_id','require','车款必须选择！'), //默认情况下用正则进行验证
        array('province_id','require','购车省份必须选择！'), //默认情况下用正则进行验证
        array('city_id','require','购车城市必须选择！'), //默认情况下用正则进行验证

    );
    /**
     * 自动完成----静态方式
     * @var array
     */
    protected $_auto = array (
        array('id','encrypt',1,'callback') , // 对id字段在新增的时候使md5函数处理
        array('updatetime','formtime',2,'callback'), // 对update_time字段在更新的时候写入当前时间戳
    );

    /**
     * id生成规则---自动完成callback调用方法
     * @return string
     */
    protected function encrypt()
    {
        $id=md5(microtime());
        return $id;
    }

    /**
     * 更新时间的格式化
     * @return bool|string
     */
    public function formtime()
    {
        return date("Y-m-d H:i:s",time());
    }


    /**
     * 添加积分兑换商品到数据库
     * @return bool
     */
    public function car_add()
    {
        $car= D('CarLove');
        $car->create();
        $car->user_id=session('user_id');
        if($car->add())
        {
            return true;
        }else
        {
            return false;
        }

    }


    /**
     * 查询数据库
     * @return bool
     */
    public function car_find($data)
    {
        $data['isdelete']=0;
        $info=M('CarLove')->where($data)->find();
        return $info;

    }

    /**
     * 查询数据库
     * @return bool
     */
    public function car_select($data)
    {
        $data['isdelete']=0;
        $info=M('CarLove')->where($data)->select();
        return $info;

    }


    /**
     * 更新数据库
     * @return bool
     */
    public function car_save($data)
    {
        $car=D('CarLove');
        $car->create($data);
        if($car->save($data))
        {
            return true;
        }else
        {
            return false;
        }

    }




}