<?php
namespace Admin\Controller;
use Think\Controller;
/**
 * Class IndexController 首页控制器
 * @package Admin\Controller
 */
class IndexController extends CommonController
{
	/**
	 * 返回model类函数
     */
	protected function _model(){
 		return new \Think\Model();
 	}

	/**
	 *后台首页展示
     */
	public function index(){
		//获得数据库版本号
        $v = M('')->query("select VERSION() as ver");
        $mysql_version= $v[0]['ver'];
        $this->assign('mysql_version',$mysql_version);
		$this->display();
	}
    
}