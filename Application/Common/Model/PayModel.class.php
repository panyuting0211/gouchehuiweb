<?php
namespace Common\Model;
use Think\Model;

class PayModel extends Model
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


    /**
     * 添加到数据库
     * @return bool
     */
    public function pay_add()
    {
        $pay= D('pay');
        $pay->create();
        if($pay->add())
        {
            return true;
        }else
        {
            return false;
        }

    }

    /**
     * 查找数据库
     * @param $data
     * @return mixed
     */
    public function pay_find($data)
    {
        $data['isdelete']=0;
        $info=M('pay')->where($data)->find();
        return $info;
    }


    /**
     * 查找数据库
     * @param $data
     * @return mixed
     */
    public function pay_select($data)
    {
        $data['isdelete']=0;
        $info=M('pay')->where($data)->order('createtime desc')->select();
        return $info;
    }


    /**
     * 更新数据
     * @param $data
     * @return bool
     */
    public function pay_save($data)
    {
        $pay=D('pay');
        $pay->create($data);
        if($pay->save($data))
        {
            return true;
        }else
        {
            return false;
        }
    }




}