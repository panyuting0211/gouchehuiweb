<?php
namespace Common\Model;
use Think\Model;

class ScoreExchangeModel extends Model
{
    /**
     * 自动验证----静态方式
     * @var array
     */
    protected $_validate = array(
        array('goods_id','require','商品id必须填写！'), //默认情况下用正则进行验
        array('goods_name','require','商品名称必须填写！'), //默认情况下用正则进行验
        array('score_value','require','消耗积分必须填写！'), //默认情况下用正则进行验

    );
    /**
     * 自动完成----静态方式
     * @var array
     */
    protected $_auto = array (
        array('id','encrypt',1,'callback') , // 对id字段在新增的时候使md5函数处理
        array('out_trade_no','createOrderNo',1,'callback') ,
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
     * 生成订单号
     * 可根据自身的业务需求更改
     */
    protected function createOrderNo() {
        $year_code = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        return $year_code[intval(date('Y')) - 2010] .
        strtoupper(dechex(date('m'))) . date('d') .
        substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('d', rand(0, 99));
    }

    /**
     * 添加积分兑换商品到数据库
     * @return bool
     */
    public function score_goods_add()
    {
        $score= D('ScoreExchange');
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
     * 查询数据库
     * @return bool
     */
    public function score_goods_select($data)
    {
        $data['isdelete']=0;
        $info=M('ScoreExchange')->where($data)->order('createtime desc')->select();
        return $info;

    }

    /**
     * 查询数据库
     * @return bool
     */
    public function score_goods_find($data)
    {
        $data['isdelete']=0;
        $info=M('ScoreExchange')->where($data)->find();
        return $info;

    }



}