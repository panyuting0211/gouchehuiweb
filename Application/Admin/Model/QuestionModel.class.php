<?php

namespace Admin\Model;
use Think\Model;

class QuestionModel extends Model
{
//ͨ����д��������ʵ�ֱ���֤
	protected $_auto = array (
			array('question','require','�������1��'), //Ĭ������������������֤
			array('q_time','time',1,'function'),
	);
	protected $_validate = array(
			array('question','require','�˺���������д��'), //Ĭ������������������֤
			array('email','email','�����ʽ����ȷ',2),
			array('email','','�ʺ������Ѿ����ڣ�',0,'unique',1), // ��������ʱ����֤name�ֶ��Ƿ�Ψһ
			array('password','require','���������д2��'), //Ĭ������������������֤
	);

// 	protected $_auto  =  array(
// 			array('status','1',1),
// 			array('create_time','time',1,'function'),
// 	);
	
}