<?php
namespace Common\Model;
use Think\Model;

class UserModel extends Model
{
    /**
     * 多维数组查询
     * @param array $data
     * @return mixed
     */
    public function select($data)
    {
        $data['isdelete']=0;
       /* $info=M('user')->where($data)->select();*/
        $info=$this->query('select * from gch_user');
        return $info;
    }

    /**
     * 一维数组查询
     * @param array|mixed $data
     * @return mixed
     */
    public function find($data)
    {
        $data['isdelete']=0;
        $info=M('user')->where($data)->find();
        return $info;
    }

    /**
     * 修改
     * @param mixed|string $data
     */
    public function save($data)
    {
        $info=M('user')->save($data);
        return $info;
    }
	
}