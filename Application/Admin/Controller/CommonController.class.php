<?php
/**
 * Class CommonController 基础控制器
 * @package Admin\Controller
 */
namespace Admin\Controller;
use Think\Controller;
class CommonController extends Controller {

	/**
	 *前置函数
     */
	public function _initialize(){
		$session_name = session_name();
		if(!isset($_SESSION[C('USER_AUTH_KEY')])){
			$this->redirect('Public/login'); 
		}
	//RBAC认证
		$notAuth = in_array(CONTROLLER_NAME, explode(',', C('NOT_AUTH_MODULE'))) || in_array(ACTION_NAME, C('NOT_AUTH_ACTION'));
		
		if(C('USER_AUTH_ON') && !$notAuth){
			
			$rbac = new \Org\Util\Rbac();
			$rbac::AccessDecision() || $this->error("你没有对应的权限");
		}
		//超级管理员登录
		if(I('pid','','int')){
			$date['pid'] = I('pid','',int);
		}

		if(session(C('ADMIN_AUTH_KEY'))){		

			$commonNode = D('node')->where(array('level'=>'2','display'=>1))->order('sort')->relation(true)->select();
		}else{

			//取出所有权限节点
			$commonNode = D('node')->where(array('level'=>'2','display'=>1))->order('sort')->relation(true)->select();
			$modul = '';
			$node_id='';
			$accessList = $_SESSION['_ACCESS_LIST'];
			foreach($accessList as $k=>$v){
				foreach ($v as $k1=>$v1){
					$modul = $modul.','.$k1;
					foreach($v1 as $k2=>$v2){
						$node_id = $node_id.','.$v2;
					}
				}
			}
			//去除没有权限的节点
			foreach ($commonNode as $k=>$v){
				if(!in_array(strtoupper($v['name']),explode(',',$modul))){			
					unset($commonNode[$k]);
				}else{
					//模块里的操作方法
					foreach ($v['node'] as $k1=>$v1){
						if(!in_array($v1['id'],explode(',',$node_id))){
							unset($commonNode[$k]['node'][$k1]);//一层一层下来的 
							
						}
					}
				}
			}		
		}
		$this->assign('commonNode',$commonNode);


		$this->cname = strtolower(CONTROLLER_NAME);//获得控制器名称
		
		$this->ctitle = M('node')->where(array('name'=>CONTROLLER_NAME,'display'=>1))->getField('title');//获得控制器中文名
		
		$this->atitle = M('node')->where(array('name'=>ACTION_NAME))->getField('title');//获得方法中文名

		
	}
	/**
	    * @Description:模板从新加载（页面跳转不全部刷新）
	    * @Return:
	    * @Author: 孙磊
	    * @Date: 2016/11/3 16:59
	    * @Version 2.0
	    */
	protected function render($data) {
		if (array_key_exists('HTTP_X_PJAX', $_SERVER) && $_SERVER['HTTP_X_PJAX']) {
			$this->display($data,'','','','pjax/'); //浏览器支持Pjax功能，直接渲染输出页面
		} else {
			layout(true); //开启模板
			$this->display($data); //浏览器不支持Pjax功能，使用默认的链接响应机制（加载模板）
		}
	}
}