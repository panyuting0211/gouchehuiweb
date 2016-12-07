<?php
namespace Common\Model;
use Think\Model;

class ReceiptAddressModel extends Model
{
    /**
     * 自动验证----静态方式
     * @var array
     */
    protected $_validate = array(
        array('role','require','用户角色必须填写！'), //默认情况下用正则进行验
        array('role',array(1,2,3,4),'值的范围不正确！',2,'in'), // 当值不为空的时候判断是否在一个范围内
        array('receiver','require','联系人必须填写！'), //默认情况下用正则进行验
        array('telphone','require','联系方式必须填写！'), //默认情况下用正则进行验
        array('receipt_province','require','地址省份必须填写！'), //默认情况下用正则进行验
        array('receipt_city','require','地址城市必须填写！'), //默认情况下用正则进行验
        array('receipt_address','require','详细地址必须填写！'), //默认情况下用正则进行验

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
     * 添加联系地址到数据库
     * @return bool
     */
    public function address_add()
    {
        $model= D('ReceiptAddress');
        $model->create();
        $model->user_id=$_SESSION['user_id'];
        $find = $model->where(array('user_id'=>$_SESSION['user_id'],'status'=>1))->select();
        if($find){
            $model->where(array('user_id'=>$_SESSION['user_id']))->save(array('status'=>0));
        }
        if($model->add())
        {
            return true;
        }else
        {
            return false;
        }

    }

    /**
     * 添加联系地址到数据库
     * @return bool
     */
    public function address_save()
    {
        $model= D('ReceiptAddress');
        $model->create();
        $find = $model->where(array('user_id'=>session('user_id'),'status'=>1))->select();
        if($find){
            $model->where(array('user_id'=>session('user_id')))->save(array('status'=>0));
        }
        if($model->save())
        {
            return true;
        }else
        {
            return false;
        }

    }

    /**
     * 地址列表
     * @return bool
     */
    public function address_select($data)
    {
        $data['isdelete']=0;
        $model= D('ReceiptAddress');
        $info=$model->where($data)->select();
        return $info;

    }

    /**
     * 地址
     * @return bool
     */
    public function address_find($data)
    {
        $data['isdelete']=0;
        $model= D('ReceiptAddress');
        $info=$model->where($data)->find();
        return $info;

    }




}