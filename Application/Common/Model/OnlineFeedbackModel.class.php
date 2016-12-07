<?php
namespace Common\Model;
use Think\Model;

/**
 * gch_online_feedback数据表
 * Class OnlineFeedbackModel
 * @package Common\Model
 */
class OnlineFeedbackModel extends Model
{
    /**
     * 自动验证----静态方式
     * @var array
     */
    protected $_validate = array(
        array('username','require','昵称必须填写！'), //默认情况下用正则进行验
        array('email','email','邮箱格式不正确',2),
        array('email','require','email必须填写！'), //默认情况下用正则进行验
        array('view','require','反馈内容必须填写！'), //默认情况下用正则进行验
        array('verifycode','require','验证码必须填写！'), //默认情况下用正则进行验证
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
     * 添加反馈数据到gch_online_feedback表中去
     * @return bool
     */
    public function feedback()
    {
        $onlinefeedback= D('OnlineFeedback');
        $onlinefeedback->create();
        if($onlinefeedback->add())
        {
            return true;
        }else
        {
            return false;
        }

    }

}