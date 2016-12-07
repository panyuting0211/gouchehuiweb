<?php
namespace Common\Model;
use Think\Model;

class CoinModel extends Model
{
    /**
     * 自动验证----静态方式
     * @var array
     */
    protected $_validate = array(
        array('role','require','角色必须填写！'), //默认情况下用正则进行验
        array('coin','require','车币必须填写！'), //默认情况下用正则进行验
        array('info','require','车币详情必须填写！'), //默认情况下用正则进行验

    );
    /**
     * 自动完成----静态方式
     * @var array
     */
    protected $_auto = array (
        array('id','encrypts',1,'callback') , // 对id字段在新增的时候使md5函数处理
        array('updatetime','formtime',2,'callback'), // 对update_time字段在更新的时候写入当前时间戳
    );

    /**
     * id生成规则---自动完成callback调用方法
     * @return string
     */
    protected function encrypts()
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
     * @param $data
     * @return mixed
     */
    public function coin_find($data)
    {
        $data['delete']=0;
        $info=M('coin')->where($data)->order('createtime desc')->find();
        return $info;

    }


    /**
     * 添加积分详情到数据库
     * @return bool
     */
    public function coin_add($data)
    {
        $coin= D('Coin');
        $coin->create($data);
        $coin->user_id=$data;
        if($coin->add())
        {
            return true;
        }else
        {
            return false;
        }

    }

    /**
     * 新用户首次注册加10车币
     * 添加积分详情到数据库
     * @return bool
     */
    public function coin_register()
    {
        M('UserGeneral')->where(array('id'=>session('user_id')))->setInc('total_coin',10);
        $coin= D('Coin');
        $data['user_id']=session('user_id');
        $data['coin']=10;
        $data['role']=1;
        $data['info']='新用户首次注册加10车币';
        $coin->create($data);
        $coin->add();

    }

    /**
     * 首次完善资料加50车币
     * 添加积分详情到数据库
     * @return bool
     */
    public function coin_accinfo()
    {
        M('UserGeneral')->where(array('id'=>session('user_id')))->setInc('total_coin',50);
        $coin= D('Coin');
        $data['user_id']=session('user_id');
        $data['coin']=50;
        $data['role']=1;
        $data['types']='accinfo';
        $data['info']='首次完善资料加50车币';
        $coin->create($data);
        $coin->add();

    }

    /**
     * 首次上传头像加10车币
     * 添加积分详情到数据库
     * @return bool
     */
    public function coin_headimg()
    {
        M('UserGeneral')->where(array('id'=>session('user_id')))->setInc('total_coin',10);
        $coin= D('Coin');
        $data['user_id']=session('user_id');
        $data['coin']=10;
        $data['role']=1;
        $data['types']='headimg';
        $data['info']='首次上传头像加10车币';
        $coin->create($data);
        $coin->add();

    }

    /**
     * 每日打卡增加5积分
     * 添加积分详情到数据库
     * @return bool
     */
    public function coin_everyday()
    {
        M('UserGeneral')->where(array('id'=>session('user_id')))->setInc('total_coin',5);
        $coin= D('Coin');
        $data['user_id']=session('user_id');
        $data['coin']=5;
        $data['role']=1;
        $data['types']='everyday';
        $data['info']='打卡增加5车币';
        $coin->create($data);
        if($coin->add())
        {
            return true;
        }else
        {
            return false;
        }

    }


}