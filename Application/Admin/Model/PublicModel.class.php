<?php

namespace Admin\Model;
use Think\Model;

class PublicModel extends Model
{
	//校验用户名和密码
	function checkNamePwd($name,$pwd)
	{
		$info=$this->getByadmin_name($name);
// 		show_bug($info);
		if ($info != null) {
			if ($info['pwd'] != $pwd) {
				return false;
			}else {
				return $info;
			}
		}else {
			return false;
		}
	}
}