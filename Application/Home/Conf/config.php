<?php
namespace Home\Conf;
define('JAVA_API', 'http://192.168.13.111/YchLrestServer/api/');
$SITE_URL = "http://test3.gouchehui.com:8082//";
define('URL_CALLBACK', "" . $SITE_URL . "member.php/public/callback?type=");
define('LOGIN_URL', 'http://test3.gouchehui.com:8082/member.php/Public/');
define('MEMBER_URL', 'http://test3.gouchehui.com:8082/member.php/MemberGeneral/');
define('KEFU_EMAIL','2853051951@qq.com');
return array(
	//数据库配置
	'DB_TYPE'               =>  'mysql',     // 数据库类型
	'DB_HOST'               =>  '127.0.0.1', // 服务器地址
	'DB_NAME'               =>  'gouchehui2.0',          // 数据库名
	'DB_USER'               =>  'gchuser',      // 用户名
	'DB_PWD'                =>  'gchpw',        // 密码
	'DB_PORT'               =>  '3306',        // 端口
	'DB_PREFIX'             =>  'gch_',    // 数据库表前缀
	'DB_FIELDTYPE_CHECK'    =>  false,       // 是否进行字段类型检查
	'DB_FIELDS_CACHE'       =>  false,        // 启用字段缓存
	'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
	'APP_BEGIN'             =>   array("Getcoding"),
	// 配置邮件发送服务器
	'MAIL_HOST' =>'smtp.163.com',
	'MAIL_SMTPAUTH' =>TRUE, //启用smtp认证
	'MAIL_USERNAME' =>'15358309720',
	'MAIL_FROM' =>'15358309720@163.com',
	'MAIL_FROMNAME' =>'购车惠',
	'MAIL_PASSWORD' =>'sun3699',
	'MAIL_CHARSET' =>'utf-8',
	'MAIL_ISHTML' =>TRUE, // 是否HTML格式邮件
	//'配置项'=>'配置值'
	'URL_CASE_INSENSITIVE'  =>  true,
	'TMPL_CACHE_ON' => false,//禁止模板编译缓存
	'HTML_CACHE_ON' => false,//禁止静态缓存
	// 允许访问的模块列表
	'URL_HTML_SUFFIX' => '',//去除.html
	'URL_MODEL'             =>1,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式
	'SHOW_PAGE_TRACE' => false,
		'URL_ROUTER_ON' => true,
		'URL_ROUTE_RULES' => array(
				'/^login/' => 'Login/lists',
		),
	//腾讯QQ登录配置
		'THINK_SDK_QQ' => array(
				'APP_KEY' => '101290011', //应用注册成功后分配的 APP ID
				'APP_SECRET' => '812184027c1a730a4a57e311c57339cc', //应用注册成功后分配的KEY
				'CALLBACK' => 'http://www.gouchehui.com/index.php/public/callback?type=qq',
		),
	//微信登录配置
		'THINK_SDK_WEIXIN' => array(
				'APP_KEY'    => 'wxca2e1232da6f007b', //应用注册成功后分配的 APP ID
				'APP_SECRET' => '0d6877afd4af7743f48bd3fa559d87a4', //应用注册成功后分配的KEY
				'CALLBACK'   => 'http://www.gouchehui.com/index.php/public/callbackweixin?type=weixin',
		),
	//新浪微博配置
		'THINK_SDK_SINA' => array(
				'APP_KEY' => '', //应用注册成功后分配的 APP ID
				'APP_SECRET' => '', //应用注册成功后分配的KEY
				'CALLBACK' => URL_CALLBACK . 'sina',
		),
	//人人网配置
		'THINK_SDK_RENREN' => array(
				'APP_KEY' => '', //应用注册成功后分配的 APP ID
				'APP_SECRET' => '', //应用注册成功后分配的KEY
				'CALLBACK' => URL_CALLBACK . 'renren',
		),

	//开启静态缓存
	/*'HTML_CACHE_ON' => true,
    'HTML_CACHE_RULES' => array(
    	'Index:index' => array('{:action}',600),
    	'Car:product_search' => array('{:action}',600),
    	'Car:product_details' => array('{:action}_{id}',600),
      ),*/

    //memcached缓存
//	'DATA_CACHE_TYPE' => 'Memcache',//缓存方式
//	'MEMCACHE_HOST'   => 'tcp://127.0.0.1:11211',// 缓存服务器地址
//	'DATA_CACHE_TIME' => '3600',//指定默认的缓存时长为3600 秒,没有会出错

/*//SQL解析缓存
	'DB_SQL_BUILD_CACHE' => true,
	'DB_SQL_BUILD_QUEUE' => 'xcache',
	'DB_SQL_BUILD_LENGTH' => 20, // SQL缓存的队列长度*/
);