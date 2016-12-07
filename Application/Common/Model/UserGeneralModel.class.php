<?php
namespace Common\Model;
use Think\Model;

class UserGeneralModel extends Model
{
    /**
     * 自动验证----静态方式
     * @var array
     */
    protected $_validate = array(
        array('tel','require','手机号必须！'), //默认情况下用正则进行验证
//        array('tel','','用户名已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
        array('password','checkPwd','密码格式不正确',0,'function'), // 自定义函数验证密码格式
      /*  array('password','existPwd','原始密码不正确',0,'function',2), // 自定义函数验证密码格式(修改密码)
        array('newpassword','checkPwd','密码格式不正确',0,'function',2), // 自定义函数验证密码格式(修改密码)
        array('repassword','newpassword','确认密码不正确',0,'confirm',2), // 验证确认密码是否和密码一致(修改密码)*/


    );
    /**
     * 自动完成----静态方式
     * @var array
     */
    protected $_auto = array (
        array('id','encrypt',1,'callback') , // 对id字段在新增的时候使md5函数处理
        array('password','md5',1,'function') , // 对password字段在新增和编辑的时候使md5函数处理
        array('user_name','tel',1,'field'), // 对tel字段映射到user_name字段
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
     * 验证密码必须大于6位
     * @param $password
     * @return bool
     */
    function checkPwd($password)
    {
        if(strlen($password)>6)
        {
            return true;
        }else
        {
            return false;
        }

    }

    /**
     *修改密码--判断原始密码是否正确
     */
    function existPwd($password)
    {
        $pass=M('UserGeneral')->where(array('id'=>session('user_id'),'isdelete'=>0))->getField('password');
        if($pass==md5($password))
        {
            return true;
        }else
        {
            return false;
        }

    }

    /**
     * 添加到数据库
     * @return bool
     */
    public function user_add()
    {
        $user= D('UserGeneral');
        $user->create();
        $user->status=1;
        $user->role=1;
        $user->logintime=time();
        $user->loginip = $_SERVER['HTTP_X_REAL_IP'];
        if($user->add())
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
        $info=M('user_general')->where($data)->find();
        return $info;
    }

    /**
     * 更新数据
     * @return bool
     */
    public function user_save()
    {
        $user=D('UserGeneral');
        $user->create();
        if($user->save())
        {   
            return true;
        }else
        {   
            return false;
        }
    }




}