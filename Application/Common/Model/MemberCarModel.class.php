<?php
namespace Common\Model;
use Think\Model;

class MemberCarModel extends Model
{
    /**
     * 自动验证----静态方式
     * @var array
     */
    protected $_validate = array(

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

    public function car_find($data)
    {
        $data['delete']=0;
        $info=M('MemberCar')->where($data)->find();
        return $info;

    }


    /**
     * 添加积分详情到数据库
     * @return bool
     */
    public function car_add($data)
    {
        $car= D('MemberCar');
        $car->create($data);
        if($car->add())
        {
            return true;
        }else
        {
            return false;
        }

    }




}