<?php
namespace Common\Model;
use Think\Model;

class ScoreModel extends Model
{
    /**
     * 自动验证----静态方式
     * @var array
     */
    protected $_validate = array(
        array('score','require','积分必须填写！'), //默认情况下用正则进行验
        array('info','require','积分详情必须填写！'), //默认情况下用正则进行验

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
     * 添加积分详情到数据库
     * @param $data
     * @return bool
     */
    public function score_add($data)
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




}