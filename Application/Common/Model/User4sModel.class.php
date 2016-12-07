<?php
namespace Common\Model;
use Think\Model;

class User4sModel extends Model
{
    protected $tableName = 'user_4s';
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
     * @param $data
     * @return bool
     */
    public function user_add($data)
    {
        $score= D('Score');
        $score->create();
        $score->user_id=session('user_id');
        if($score->add())
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
    public function user_find($data)
    {
        $data['isdelete']=0;
        $info=M('user_4s')->where($data)->find();
        return $info;
    }

    /**
     * 更新数据
     * @param $data
     */
    public function user_save($data)
    {
        $user=D('User4s');
        $user->create($data);
        $user->updateuser=session('user_name');
        $user->save($data);
    }




}