<?php

namespace Admin\Model;
use Think\Model;

class QuestionModel extends Model
{
//通过重写父类属性实现表单验证
	protected $_auto = array (
			array('question','require','密码必须1！'), //默认情况下用正则进行验证
			array('q_time','time',1,'function'),
	);
	protected $_validate = array(
			array('question','require','账号名必须填写！'), //默认情况下用正则进行验证
			array('email','email','邮箱格式不正确',2),
			array('email','','帐号名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
			array('password','require','密码必须填写2！'), //默认情况下用正则进行验证
	);

// 	protected $_auto  =  array(
// 			array('status','1',1),
// 			array('create_time','time',1,'function'),
// 	);
	
}