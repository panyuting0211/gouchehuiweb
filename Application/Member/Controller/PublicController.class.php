<?php
namespace Member\Controller;
use Think\Controller;
use ThinkOauth;
use Org\Net\Http;


/**
 * Class PublicController公用模块控制器
 * @package Home\Controller
 */
class PublicController extends Controller {

    /**
     *空控制器
     */
    public function _empty()
    {
        $this->redirect('login');
    }

	/**
	 *登录
	 */
	public function login()
	{
		if($_COOKIE['token']){
			$this->redirect('Index/index');
		}
		if(I('get.url')){
			$this->assign('url',I('get.url'));
		}else{
			if(!empty($_SERVER['HTTP_REFERER']))
			{
				session('lasturl',$_SERVER['HTTP_REFERER']);
			}else
			{
				session('lasturl',U('Index/index'));
			}

		}
		$this->display();
	}

	public function login_test()
	{
		if($_COOKIE['token']){
			$this->redirect('Index/index');
		}
		if(I('get.url')){
			$this->assign('url',I('get.url'));
		}else{
			if(!empty($_SERVER['HTTP_REFERER']))
			{
				session('lasturl',$_SERVER['HTTP_REFERER']);
			}else
			{
				session('lasturl',U('Index/index'));
			}

		}
		$this->display();
	}

	/**
	 *处理登陆逻辑
	 */
	public function dologin(){
		//判断用户名和密码是否为空
		$data['user_name']=I('user_name');
		$data['password']=md5(I('password'));
		$data['role']=I('role');
		if(!empty($data['user_name']) && !empty($data['password']) && !empty($data['role'])){
			if($data['role']==1)
			{
				$info=D('UserGeneral')->user_find($data);
			}
			if($data['role']==2)
			{	
				//当为4S登录的时候调用函数
				//$info = $this->doLogin4S($data['user_name'],I('password'));
				//print_r($info);exit;
				$info=D('User4s')->user_find($data);
			}
			if($data['role']==3)
			{
				$info=D('UserDealer')->user_find($data);
			}
			if($data['role']==4)
			{
				$info=D('UserBusiness')->user_find($data);
			}
			if($info){

				//判断用户是否被禁用
				if($info['status'] == 0){
					$this->error("您的账号已被锁定!");
				}else
				{
					//修改登录信息
					$datasave['id']=$info['id'];
					$datasave['logintime'] = time();
					$datasave['loginip'] = $_SERVER['HTTP_X_REAL_IP'];
					if($data['role']==1)
					{
						D('UserGeneral')->user_save($datasave);
						//登陆成功，设置cookie
						$token=authcode($info['id'],'ENCODE','gouchehui');
						cookie('token',$token,array('expire'=>3600,'domain'=>'gouchehui.com'));
						//判断是否是首次登陆
						$data_coin['user_id']=$info['id'];
						$data_coin['role']=1;
						$data_coin['coin']=10;
						$data_coin['types']='first_login';
						$find = M("coin")->where($data_coin)->find();
						if(!$find){
							M("user_general")->where(array('id'=>$info['id']))->setInc('total_coin',10);
							$data_coin['id']=md5(microtime());
							$data_coin['info']='首次登陆，加10车币';
							M("coin")->add($data_coin);
						}
						//每日登录加积分
						//判断用户是否是今天第一次登录
	/*					$dataa['user_id'] = $info['id'];
						$dataa['role']=1;
						$dataa['types'] = 'login';
						$date_today = date('Y-m-d',time());//今天凌晨的时间
						$date_tomor =  date('Y-m-d',strtotime('+1 day'));//明天凌晨的时间
						$date_7 = date('Y-m-d',strtotime('-7 day'));//7天前凌晨的时间
						$dataa['createtime']  = array(array('EGT',$date_today),array('LT',$date_tomor),'and');
						$today_login = M('coin')->where($dataa)->find();
						if(!$today_login){
							//如果是首次登陆
							//判断是否是连续七天登录
							$datas['user_id'] =$info['id'];
							$datas['types'] = 'login';
							$datas['create_time'] = array(array('EGT',$date_7),array('LT',$date_tomor),'and');
							$count = M('coin')->where($datas)->count();
							if($count >= 6){
								//加3分
								M('user_general')->where(array('id'=>$info['id']))->setInc('total_coin',3);
								$data_score['id']=md5(microtime());
								$data_score['user_id']=$info['id'];
								$data_score['role']=1;
								$data_score['coin']=3;
								$data_score['types']='login';
								$data_score['info']='连续7天以上登录加3车币';
								M('coin')->add($data_score);
							}else{
								//加一分
								M('user_general')->where(array('id'=>$info['id']))->setInc('total_coin');
								$data_score['id']=md5(microtime());
								$data_score['user_id']=$info['id'];
								$data_score['coin']=1;
								$data_score['role']=1;
								$data_score['types']='login';
								$data_score['info']='连续7天以下登录加1车币';
								M('coin')->add($data_score);
							}
						}*/
						//判断是否有URL链接
						if(I('next')){
							header('Location:'.I('next'));
						}else{
							$this->redirect('MemberGeneral/index');
						}
					}
					if($data['role']==2)
					{
						D('User4s')->user_save($datasave);
						session('user_id',$info['id']);
						session('user_name',$info['user_name']);
						$this->redirect('Member4s/index');
					}
					if($data['role']==3)
					{
						D('UserDealer')->user_save($datasave);
						session('user_id',$info['id']);
						session('user_name',$info['user_name']);
						$this->redirect('MemberDealer/index');
					}
					if($data['role']==4)
					{
						D('UserBusiness')->user_save($datasave);
						session('user_id',$info['id']);
						session('user_name',$info['user_name']);
						$this->redirect('MemberBusiness/index');
					}

				}
			} else
			{
				$this->error("账号或者密码错误，请重试！");
				/*show_bug('账号或者密码错误，请重试！');*/
			}
		}else{
			$this->error("账号和密码不能为空");
		}
	}

	/**
	 *处理登陆逻辑
	 */
	public function dologin_test(){
		//判断用户名和密码是否为空
		$data['user_name']=I('user_name');
		$data['password']=md5(I('password'));
		$data['role']=I('role');
		if(!empty($data['user_name']) && !empty($data['password']) && !empty($data['role'])){
			if($data['role']==1)
			{
				$info=D('UserGeneral')->user_find($data);
			}
			if($data['role']==2)
			{	
				//当为4S登录的时候调用函数
				$info = $this->doLogin4S($data['user_name'],I('password'));
				print_r($info);exit;
				//$info=D('User4s')->user_find($data);
			}
			if($data['role']==3)
			{
				$info=D('UserDealer')->user_find($data);
			}
			if($data['role']==4)
			{
				$info=D('UserBusiness')->user_find($data);
			}
			if($info){
			} else
			{
				$this->error("账号或者密码错误，请重试！");
				/*show_bug('账号或者密码错误，请重试！');*/
			}
		}else{
			$this->error("账号和密码不能为空");
		}
	}

	/**
	 *登录为4S的操作
	 */
	public function doLogin4S($user_name,$password){

		$url =  'http://192.168.13.111/YchLrestServer/api/login/auth/4s/web';
		$date['username'] = $user_name;
		$date['password'] = $password;
		$data_string = json_encode($date);
		$header = array('Content-Type: application/json','Content-Length: ' . strlen($data_string));
		//$res = http($url,$data_string,'POST');

		$ch = curl_init('http://192.168.13.111/YchLrestServer/api/login/auth/4s/web');                                                                      
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
		    'Content-Type: application/json',                                                                                
		    'Content-Length: ' . strlen($data_string))                                                                       
		);
		
		$result = curl_exec($ch);
		
		return $result;
	}

	/**
	 *注册
	 */
	public function reg(){
		$this->display();
	}

	/**
	 *注册处理
	 */
	public function doreg()
	{
		if($_POST) {
			$tel = I('tel');
			if ($tel == $_SESSION['tel']) {
				$user=D('UserGeneral');
				if (!$user->create()){
					// 如果创建失败 表示验证没有通过 输出错误提示信息
					$this->error($user->getError());
				}else{
					// 验证通过 可以进行其他数据操作
					$code=I('code');
					//设置验证码过期时间
					if($_SESSION['lost_time']<time()){
						unset($_SESSION['lost_time']);
						unset($_SESSION['code']);
						$this->error('验证码有效期过期！');
					}else{
						if($code==session('code'))
						{
							/*$_POST['user_name'] = $_POST['tel'];
							$_POST['password'] = md5($_POST['password']);*/
                            $user_tel=M('user_general')->where(array('user_name'=>I('tel'),'isdelete'=>0))->find();
                            if ($user_tel)
                            {
                                $this->error('该用户名已存在！');
                            }else
                            {
                                $info = $user->user_add();
                            }

							if($info){
								//注册成功，设置cookie
								$datafind['user_name']=$tel;
								$info=$user->user_find($datafind);
								$token=authcode($info['id'],'ENCODE','gouchehui');
								cookie('token',$token,array('expire'=>3600,'domain'=>'gouchehui.com'));
								session('user_id',$info['id']);
								session('tel','');
								//获取10车币
								D('Coin')->coin_register();
								$this->success('注册成功',U('Index/index@www.gouchehui.com'),2);
							}else{
								$this->error('注册失败，请稍后重试！');
							}
						}else
						{
							$this->error('验证码错误！');
						}
					}
				}
			} else {
				$this->error('请输入获得验证码的手机号');
			}

		}

	}

	/**
	 *ajax提交的登录
	 */
	public function ajax_dologin(){
		//判断用户名和密码是否为空
		if(I('user_name') and I('password')){
			//判断是否是跨域提交
			if($_GET['callback']){
				$callback = I('callback');
			}
			$res=D('UserGeneral')->user_find(array('user_name'=>I('user_name'),'password'=>md5(I('password'))));
			if($res){
				//判断用户是否被禁用
				if($res['status'] == 0){
					$ret['status'] = 0;//用户被禁用了
					$ret['info'] = '用户已被禁用';//用户被禁用了
					$this->ajaxReturn($ret);
				}
				$token=authcode($res['id'],'ENCODE','gouchehui');
				cookie('token',$token,array('expire'=>3600,'domain'=>'gouchehui.com'));
				$data['logintime'] = time();
				$data['loginip'] = $_SERVER['HTTP_X_REAL_IP'];
				//保存登录信息
				$save = M('user_general')->where(array('id'=>$res['id']))->save($data);
				if($save){
					//登陆积分
					/*if($res['role']==1)
					{
						$current_time=date("Y-m-d",$data['logintime']);
						$last_time=date("Y-m-d",$res['logintime']);
						if($current_time != $last_time)
						{*/
					//判断是否是首次登陆

					//M("user_general")->where(array('id'=>$res['id']))->setInc('total_coin',10);
					//$data_score['id']=md5(microtime());
					$data_score['user_id']=$res['id'];
					$data_score['role']=1;
					$data_score['coin']=10;
					$data_score['types']='first_login';

					$find = M("coin")->where($data_score)->find();
					if(!$find){
						M("user_general")->where(array('id'=>$res['id']))->setInc('total_coin',10);
						$data_score['id']=md5(microtime());
						$data_score['info']='首次登陆，加10车币';
						M("coin")->add($data_score);
					}
					/*		}
                        }*/
					//每日登录加积分
					//判断用户是否是今天第一次登录
/*					$info['id'] = $res['id'];
					$dataa['user_id'] = $info['id'];
					$dataa['role']=1;
					$dataa['types'] = 'login';
					$date_today = date('Y-m-d',time());//今天凌晨的时间
					$date_tomor =  date('Y-m-d',strtotime('+1 day'));//明天凌晨的时间
					$date_7 = date('Y-m-d',strtotime('-7 day'));//7天前凌晨的时间
					$dataa['createtime']  = array(array('EGT',$date_today),array('LT',$date_tomor),'and');
					$today_login = M('coin')->where($dataa)->find();
					if(!$today_login){
						//如果是首次登陆
						//判断是否是连续七天登录
						$datas['user_id'] =$info['id'];
						$datas['types'] = 'login';
						$datas['create_time'] = array(array('EGT',$date_7),array('LT',$date_tomor),'and');
						$count = M('coin')->where($datas)->count();
						if($count == 7){
							//加3分
							M('user_general')->where(array('id'=>$info['id']))->setInc('total_coin',3);
							$data_score['id']=md5(microtime());
							$data_score['user_id']=$info['id'];
							$data_score['coin']=3;
							$data_score['role']=1;
							$data_score['types']='login';
							$data_score['info']='连续7天以上登录加3车币';
							M('coin')->add($data_score);
						}else{
							//加一分
							M('user_general')->where(array('id'=>$info['id']))->setInc('total_coin');
							$data_score['id']=md5(microtime());
							$data_score['user_id']=$info['id'];
							$data_score['coin']=1;
							$data_score['role']=1;
							$data_score['types']='login';
							$data_score['info']='连续7天以下登录加1车币';
							M('coin')->add($data_score);
						}
					}*/
					$ret['status'] = 1;//登录成功
					$ret['info'] = '登录成功';//用户被禁用了
					$ret['next'] = I('next');
					die($callback.'('.json_encode($ret).')');

				}
			}else{
				$resu = M('user_general')->where(array('user_name'=>I('user_name')))->find();

				if($resu){
					$ret['status'] = -1;//密码错误
					$ret['info'] = '密码错误';//密码错误
					if($callback){
						die($callback.'('.json_encode($ret).')');
					}else{
						$this->ajaxReturn($ret);
					}
				}else{
					$ret['status'] = -2;//账号不存在
					$ret['info'] = '账号不存在';//账号不存在
					die($callback.'('.json_encode($ret).')');
				}
			}
		}else{
			$ret['status'] = -3;//账号和密码不能为空
			$ret['info'] = '账号和密码不能为空';//账号和密码不能为空
			die($callback.'('.json_encode($ret).')');
		}
	}

	/**
	 *快速注册
	 */
	public function doreg_ajax()
	{
		$callback = I('callback');
		$tel = I('user_name');
		if($tel == $_SESSION['tel']){
			$data['user_name'] = I('user_name');
			$data['tel'] = I('user_name');
		}else{
			$datar['status']=1;
			$datar['info']='请输入获得验证码的手机号';
			die($callback.'('.json_encode($datar).')');


		}
		$find = M('user_general')->where(array('user_name' =>$data['user_name'],'isdelete'=>0))->find();
		if($find){
			$datar['status']=1;
			$datar['info']='用户名已存在';
			die($callback.'('.json_encode($datar).')');
		}

		$code=I('code');
		if($_SESSION['lost_time']<time()){
			unset($_SESSION['lost_time']);
			unset($_SESSION['code']);
			$datar['status']=1;
			$datar['info']='验证码有效期过期！';
			die($callback.'('.json_encode($datar).')');
		}
		if($code==session('code'))
		{
			if(!empty($_GET['password']))
			{
				if(strlen($_GET['password'])<6){
					$datar['status']=1;
					$datar['info']='请输入正确的密码（密码长度最少6位以上）';
				}else
				{
					$data['id'] = md5(microtime());
					$data['password'] = md5(I('password'));
					$data['status'] = 1;//启用
					$data['role'] = 1;//普通用户
				/*	$data['createtime'] = time();*/
					$data['logintime'] = time();
					$data['loginip'] = $_SERVER['HTTP_X_REAL_IP'];
					$data['my_num'] = $this->randomkeys();
					$info = M('user_general')->add($data);
					if($info){
						$token=authcode($data['id'],'ENCODE','gouchehui');
						cookie('token',$token,array('expire'=>3600,'domain'=>'gouchehui.com'));
						$datar['status']=0;
						$datar['next']=I('next');

					}else{
						$datar['status']=1;
						$datar['info']='注册失败';
						die($callback.'('.json_encode($datar).')');
					}
				}
			}else
			{
				$datar['status']=1;
				$datar['info']='密码不能为空！';
				die($callback.'('.json_encode($datar).')');
			}
		}else
		{
			$datar['status']=1;
			$datar['info']='验证码错误';
			die($callback.'('.json_encode($datar).')');
		}
		die($callback.'('.json_encode($datar).')');
	}

	/**
	 *退出
	 */
	public function loginOut(){
		unset($_SESSION['user_id']);
		unset($_SESSION['lasturl']);
		cookie('token',null,array('expire'=>time()-1,'domain'=>'gouchehui.com'));
		$this->redirect('Public/login');
		/*if(isset($_GET['callback']) and $_GET['callback']!=''){
			$callback = urldecode(I('callback'));
			header('Location:'.$callback);
		}elseif(!empty($_SERVER['HTTP_REFERER']))
		{
			header('Location:'.$_SERVER['HTTP_REFERER']);
		}else
		{
			$this->redirect('Public/login');
		}*/

	}

	/**
	 * 短信验证处理---4852
	 * @param string $tel
	 * @return string
	 */
	public function noteVerify($tel=''){
		if(I('tel')){
			if(I('callback') == 'code'){
				$callback = I('callback');
			}
			$mobile = I('tel');//接收短信的手机号码
			$user = D('UserGeneral');
			$find =  $user->user_find(array('user_name'=>$mobile));
			if($find){
				$data['status'] = 1;
				$data['msg'] = "手机号已存在!";
				die($callback.'('.json_encode($data).')');
			}
		}else{
			$mobile = $tel;
		}
		$key = 'a69a5764f670ccd1f473f21e1f4dad9c';//应用APPKEY
		$dtype = 'json';//支持格式
		$tpl_id = '4852';//短信模板ID
		$randStr = str_shuffle('1234567890');
		$code = substr($randStr,0,6);
		$app = "南京易橙汇会员";
		$tpl_value = urlencode("#code#=$code&#app#=$app");//变量名和变量值对
		$url = 'http://v.juhe.cn/sms/send?mobile='.$mobile.'&tpl_id='.$tpl_id.'&tpl_value='.$tpl_value.'&key='.$key;
		$ress = file_get_contents($url);
		$reslut = json_decode($ress,true);

		if($reslut['error_code']==0){
			session('tel',$mobile);
			$time = 300;//五分钟过期
			$_SESSION['code'] = $code;
			$_SESSION['lost_time'] = time()+$time;
			$data['status'] =  $reslut['error_code'];
		}else{
			$data['status'] =  $reslut['error_code'];
		}
		if(I('tel')){
			if($callback){
				die($callback.'('.json_encode($data).')');
			}else{
				$this->ajaxReturn($data);
			}

		}else{
			return( json_encode($data));
		}


	}

	/**
	 * 短信验证接口----通用型（5774）
	 * @return string
	 */
	public function note()
	{
	    $tel=I('tel');
		if(!empty($tel)){
			$mobile = $_POST['tel'];//接收短信的手机号码
			$key = 'a69a5764f670ccd1f473f21e1f4dad9c';//应用APPKEY
			$tpl_id = '5774';//短信模板ID
			$randStr = str_shuffle('1234567890');
			$code = substr($randStr,0,6);
			$tpl_value = urlencode("#code#=$code");//变量名和变量值对
			$url = 'http://v.juhe.cn/sms/send?mobile='.$mobile.'&tpl_id='.$tpl_id.'&tpl_value='.$tpl_value.'&key='.$key;
			$ress = file_get_contents($url);
			$reslut = json_decode($ress,true);
			if($reslut['error_code']==0){
				session('tel',$mobile);
				$time = 300;//五分钟过期
				$_SESSION['code'] = $code;
				$_SESSION['lost_time'] = time()+$time;
				//cookie ('code',$code,300);//验证码有效时间为5分钟
				$data['status'] =  $reslut['error_code'];
			}else{
				$data['status'] =  $reslut['error_code'];
			}
		}else{
			$data['status']=0;
			$data['msg']='手机号不能为空';

		}
		$this->ajaxReturn($data);

	}

	/**
	 *用户名检测
	 */
	public function userNameCheck(){
		$user=D('UserGeneral');
		$datapost['user_name']=I('tel');
		//判断账号不能为空
		if($datapost['user_name'] == ''){
			$data['status'] = -2;
			$data['info'] = "请输入正确的账号";
			$this->ajaxReturn($data,'JSON');
		}
		//查看是否存在这个账号
		$find = $user->user_find($datapost);
		if($find){
			$data['status'] = -1;
			$data['info'] = "用户名已存在!";
			$this->ajaxReturn($data,'JSON');
		}else{
			$data['status'] = 1;
			$data['info'] = "用户名可以使用!";
			$this->ajaxReturn($data,'JSON');
		}

	}

	/**
	 *生成验证码
	 */
	public function verify(){
		import('ORG.Util.Image');
		ob_end_clean();
		$config=array(
				'fontSize'  =>  17,              // 验证码字体大小(px)
				'imageH'    =>  42,               // 验证码图片高度
				'imageW'    =>  115,               // 验证码图片宽度
				'fontttf'   =>  '4.ttf',             // 验证码字体，不设置随机获取
				'length'    =>  4               // 验证码位数
		);
		$verify = new \Think\Verify($config);
		$verify->entry();
	}

	/**
	 * 检测邮箱有效性
	 * @param $str
	 * @return bool
	 */
	function isEmail($str)
	{
		if (!$str)
		{
			return false;
		}
		return preg_match('#[a-z0-9&\-_.]+@[\w\-_]+([\w\-.]+)?\.[\w\-]+#is', $str) ? true : false;
	}

	/**
	 * 检测手机号码有效性
	 * @param $num
	 * @return bool
	 */
	function isMobile($num)
	{
		if (!$num)
		{
			return false;
		}
		return preg_match('#^13[\d]{9}$|14^[0-9]\d{8}|^15[0-9]\d{8}$|^18[0-9]\d{8}$#', $num) ? true : false;
	}

	/**
	 *找回密码---填写账号和验证码
	 */
	public function findpw_verification()
	{
		$this->display();
	}

	/**
	 *找回密码-验证码验证
	 */
	public function codeCheck(){
		$tel=I('user_name');
		if($tel == $_SESSION['tel']) {
			//判读验证码是否过期
			$code = I('code');
			//设置验证码过期时间
			if ($_SESSION['lost_time'] < time()) {
				unset($_SESSION['lost_time']);
				unset($_SESSION['code']);
				$this->error('验证码有效期过期！', U('findpw_verification'));
			} else {
				if ($code == session('code')) {
					session('uname', I('user_name'));
					$this->redirect('findpw_resetPw');
				} else {
					$this->error('验证码错误！');
				}

			}
		} else{
			$this->error('请输入发送短信的手机号！');
		}

	}

	/**
	 *找回密码-重置密码
	 */
	public function findpw_resetPw(){
		$uname = !empty($_SESSION['uname'])?$_SESSION['uname']:'';
		$this->assign('user_name',$uname);
		$this->display();
	}

	/**
	 *找回密码-重置密码-表单提交
	 */
	public function resetPwdHandle(){

		if($_POST){
			if(I('password') != I('repassword')){
				$this->error('两次输入的密码不一样，请重新输入密码');
			}else{
				$data['password'] = MD5(I('password'));
				$data['status'] = 1;
				$res = M('user_general')->where(array('user_name'=>I('user_name')))->save($data);
				if($res !== flase){
					session('uname',null);
					if($_COOKIE['code']){
						setcookie('code',NULL,time()-1,'/');
					}
					$this->redirect('findpw_ok');
				}else{
					$this->error('找回密码失败,请稍后重试');
				}
			}

		}
	}

	/**
	 *找回密码-找回密码成功
	 */
	public function findpw_ok()
	{
		$this->display();
	}

	/**
	 * 生成邀请码
	 * @return string
	 */
	protected function randomkeys() {
		$pattern = '1234567890';
		$key = '';
		for($i=0;$i<=5;$i++){
			$key .= $pattern{mt_rand(0,9)};    //生成php随机数
		}
		$res = M('user')->where(array('my_num'=>$key))->find();
		if($res){
			$this->randomkeys();
		}else{
			return $key;
		}
	}

	/**
	 *页面公共底部
	 */
	public function footer(){
		$this->display();
	}

	/**
	 * 数组转xml---微信登陆数据格式处理
	 * @param $arr
	 * @param int $dom
	 * @param int $item
	 * @return string
	 */
	function arrtoxml($arr, $dom=0, $item=0){
		if (!$dom){
			$dom = new \DOMDocument("1.0");
		}
		if(!$item){
			$item = $dom->createElement("xml");
			$dom->appendChild($item);
		}
		foreach ($arr as $key=>$val){
			$itemx = $dom->createElement(is_string($key)?$key:"item");
			$item->appendChild($itemx);
			if (!is_array($val)){
				$text = $dom->createTextNode($val);
				$itemx->appendChild($text);

			}else {
				arrtoxml($val,$dom,$itemx);
			}
		}
		return $dom->saveXML($item);
	}

	/**
	 * 第三方登陆入口
	 * @param null $type
	 */
	public function loginsdk($type = null) {
		empty($type) && $this->error('参数错误');
		import('Org.ThinkSDK.ThinkOauth');
		$sns = ThinkOauth::getInstance($type);
		//跳转到授权页面
		redirect($sns->getRequestCodeURL($type));
	}

	/**
	 * 微信登陆回调方法
	 * @param null $type
	 * @param null $code
	 */
	public function callbackweixin($type = null, $code = null) {
		header("Content-type: text/html; charset=utf-8");
		(empty($type) || empty($code)) && $this->error('参数错误');
		import('Org.ThinkSDK.ThinkOauth');
		$sns = ThinkOauth::getInstance($type);
		$extend = null;
		$tokenArr = $sns->getAccessToken($type,$code, $extend);
		$openid = $tokenArr['openid'];
		$token = $tokenArr['access_token'];

		Session("access_token", $token);
		$weixininfo = $sns->call('sns/userinfo',$tokenArr);
		$unionid=$weixininfo['unionid'];
		//获取当前登录用户信息
		if ($unionid) {
			$userinfo = M("user_general")->field('id,user_name')->where(array('wx_open_id'=>$unionid,'isdelete'=>0))->find();
			if (!empty($userinfo['user_name'])) { //若是有该账号已经绑定手机直接登录
				//登录成功，设置cookie
				$token=authcode($userinfo['id'],'ENCODE','gouchehui');
				cookie('token',$token,array('expire'=>3600,'domain'=>'gouchehui.com'));
				//更新登录信息
				$datalogin['logintime']=time();
				$datalogin['loginip']=$_SERVER['HTTP_X_REAL_IP'];
				M('user_general')->where(array('id'=>$userinfo['id']))->save($datalogin);
				echo "<script>document.location.href='" . __APP__ . "';</script>";
				exit;
			} else { //没有绑定手机,先绑定手机
				$data['nick'] = $weixininfo['nickname'];
				$data['wx_open_id'] = $weixininfo['unionid'];
				//将远程头像获取到本地服务器
				/*	Http::curlDownload($weixininfo['headimgurl'], './Public/Upload/headimg/' . $weixininfo['unionid']);*/
				//将缓存到本地服务器的头像上传至网站指定位置
				/*$data['head_url'] = '/Upload/headimg/' . $openid;*/
				$this->redirect('reg_binding',$data);
			}

		} else {
			echo "<script>alert('系统出错;请稍后再试！');document.location.href='" . __APP__ . "';</script>";
		}
	}

	/**
	 * QQ登陆回调方法
	 * @param null $type
	 * @param null $code
	 */
	public function callback($type = null, $code = null) {
		header("Content-type: text/html; charset=utf-8");
		(empty($type) || empty($code)) && $this->error('参数错误');
		import('Org.ThinkSDK.ThinkOauth');
		$sns = ThinkOauth::getInstance($type);

		//腾讯微博需传递的额外参数
		$extend = null;
		if ($type == 'tencent') {
			$extend = array('openid' => $this->_get('openid'), 'openkey' => $this->_get('openkey'));
		}
		$tokenArr = $sns->getAccessToken($type,$code, $extend);
		$openid = $tokenArr['openid'];
		$token = $tokenArr['access_token'];
		Session("openid", $openid);
		Session("access_token", $token);
		$qqinfo = $sns->call('user/get_user_info',$tokenArr);
		//获取当前登录用户信息
		if ($openid) {
			$field = strtolower($type);
			Session("field", $field);
			$userinfo = M("user_general")->field('id,user_name')->where(array('qq_open_id' => $openid, 'isdelete' => 0))->find();
			if (!empty($userinfo['user_name'])) {
				//设置登录cookie
				$token=authcode($userinfo['id'],'ENCODE','gouchehui');
				cookie('token',$token,array('expire'=>3600,'domain'=>'gouchehui.com'));
				//更新登录信息
				$datalogin['logintime'] = time();
				$datalogin['loginip'] = $_SERVER['HTTP_X_REAL_IP'];
				M('user_general')->where(array('id' => $userinfo['id']))->save($datalogin);
				echo "<script>document.location.href='" . __APP__ . "';</script>";
				exit;
			} else {
				//没有完善手机号的情况下，绑定手机号
				$data['nick'] = $qqinfo['nickname'];
				$data['qq_open_id'] = $openid;
				//将远程头像获取到本地服务器
				/*Http::curlDownload($qqinfo['figureurl_qq_1'], './Public/Upload/headimg/' . $openid);*/
				//将缓存到本地服务器的头像上传至网站指定位置
				/*$data['head_url'] = '/Upload/headimg/'.$openid;*/
				$this->redirect('reg_binding',$data);
			}
		} else {
			echo "<script>alert('系统出错;请稍后再试！');document.location.href='" . __APP__ . "';</script>";
		}
	}

	/**
	 * 第三方登录绑定页面
	 **/
	public function reg_binding()
	{
		$data['qq_open_id']=I('qq_open_id');
		$data['wx_open_id']=I('wx_open_id');
		$data['nick']=I('nick');
		$this->assign('data',$data);
		$this->display();
	}

	/**
	 *第三方登录绑定处理
	 */
	public function dobind()
	{
		$qq_open_id=I('qq_open_id');
		$wx_open_id=I('wx_open_id');
		if(!empty($qq_open_id) && empty($wx_open_id))
		{
			$data['qq_open_id']=I('qq_open_id');
		}elseif(empty($qq_open_id) && !empty($wx_open_id))
		{
			$data['wx_open_id']=I('wx_open_id');
		}
		$tel=I('mobile');
		$username=M('user_general')->where(array('user_name'=>$tel))->getField('user_name');
		if(empty($username))
		{
			$code=I('code');
			//设置验证码过期时间
			if($_SESSION['lost_time']<time()){
				unset($_SESSION['lost_time']);
				unset($_SESSION['code']);
				$this->error('验证码有效期过期！');
			}else{
				if($code==session('code'))
				{
					$data['nick']=I('nick');
					$data['logintime'] = time();
					$data['loginip'] = $_SERVER['HTTP_X_REAL_IP'];
					$data['password']=md5(I('password'));
					$data['id']=md5(microtime());
					$data['user_name']=I('mobile');
					$data['my_num'] = $this->randomkeys();
					$data['createuser'] =I('mobile') ;
					$data['role'] = 1;
					$data['status']=1;
					$data['tel']=I('mobile');
					$z=M('user_general')->add($data);
					if($z) {
						$token=authcode($data['id'],'ENCODE','gouchehui');
						cookie('token',$token,array('expire'=>3600,'domain'=>'gouchehui.com'));
						$this->success('添加成功', U('index/index'));
					}else
					{
						$this->error('添加失败');
					}
				} else
				{
					$this->error('验证码错误！');
				}
			}
		}else
		{
			$data['nick']=I('nick');
			$data['logintime'] = time();
			$data['loginip'] = $_SERVER['HTTP_X_REAL_IP'];
			$data['password']=md5(I('password'));
			$tel=I('mobile');
			$info=M('user_general')->where(array('user_name'=>$tel,'isdelete'=>0))->find();
			if(!empty($info))
			{
				$z=M('user_general')->where(array('user_name'=>$tel))->save($data);
				if($z) {
					$token=authcode($info['id'],'ENCODE','gouchehui');
					cookie('token',$token,array('expire'=>3600,'domain'=>'gouchehui.com'));
					$this->success('绑定成功', U('index/index'));
				}else
				{
					$this->error('绑定失败');
				}
			}else{
				$this->error('手机号不存在');
			}
		}

	}
	/************************************************接口信息******************************************************************/
	/**
	 *品牌
	 */
	public function Gbrand()
	{
		$info=M('brand')->where(array('isdelete'=>0))->select();
		foreach($info as $key=>$value)
		{
			$data[$key]['name']=$value['brand_name'];
			$data[$key]['value']=$value['id'];
		}
		$this->ajaxReturn($data);
	}

	/**
	 *车型
	 */
	public function Gcar_model()
	{
		$brand_id=I('brand');
		$info=M('car_model')->where(array('brand_id'=>$brand_id,'isdelete'=>0))->select();
		foreach($info as $key=>$value)
		{
			$data[$key]['name']=$value['car_model_name'];
			$data[$key]['value']=$value['id'];
		}
		$this->ajaxReturn($data);
	}

	/**
	 *车款
	 */
	public function Gcar()
	{
		$car_model_id=I('model');
		$info=M('car')->where(array('car_model_id'=>$car_model_id,'isdelete'=>0))->select();
		foreach($info as $key=>$value)
		{
			$data[$key]['name']=$value['car_name'];
			$data[$key]['value']=$value['id'];
		}
		$this->ajaxReturn($data);
	}

	/**
	 *外观颜色
	 */
	public function Gcolor()
	{
		$car_id=I('car_id');
		$ex_info=D('view_exterior_color')->where(array('car_id'=>$car_id,'isdelete'=>0))->select();
		foreach($ex_info as $key=>$value)
		{
			$data1[$key]['name']=$value['color_name'];
			$data1[$key]['value']=$value['id'];
		}
		$info['exterior_color']=$data1;
		$in_info=D('view_interior_color')->where(array('car_id'=>$car_id,'isdelete'=>0))->select();
		foreach($in_info as $key=>$value)
		{
			$data2[$key]['name']=$value['color_name'];
			$data2[$key]['value']=$value['id'];
		}
		$info['interior_color']=$data2;
		$info['auth_price']=M('car')->where(array('id'=>$car_id,'isdelete'=>0))->getField('auth_price');
		$this->ajaxReturn($info);
	}

	/**
	 *省份
	 */
	public function Gprovince()
	{
		$info=M('data_province')->select();
		foreach($info as $key => $value)
		{
			$data[$key]['name']=$value['provincename'];
			$data[$key]['value']=$value['provinceid'];
		}
		$this->ajaxReturn($data);
	}

	/**
	 *城市
	 */
	public function Gcity()
	{
		$province_id=I('province');
		$info=M('data_city')->where(array('ProvinceID'=>$province_id))->select();
		foreach($info as $key => $value)
		{
			$data[$key]['name']=$value['cityname'];
			$data[$key]['value']=$value['cityid'];
		}
		$this->ajaxReturn($data);
	}

	/**
	 *地区
	 */
	public function Gdistrict()
	{
		$city_id=I('city');
		$info=M('data_district')->where(array('CityID'=>$city_id))->select();
		foreach($info as $key => $value)
		{
			$data[$key]['name']=$value['districtname'];
			$data[$key]['value']=$value['districtid'];
		}
		$this->ajaxReturn($data);
	}
}