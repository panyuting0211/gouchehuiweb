<?php
namespace Common\Conf;
define('OSS', 'http://yichenghui.oss-cn-shanghai.aliyuncs.com/gouchehui/web'); //把*替换成对应的Bucket 由于经常用到该链接，所以定义成常量
/*define('OSS', '/Public'); //把*替换成对应的Bucket 由于经常用到该链接，所以定义成常量*/
return array(
	//'配置项'=>'配置值'
	/*'SHOW_PAGE_TRACE' => false,   //调试模式*/
    'ERROR_PAGE'   => __ROOT__.'/Public/404.html',
	// 允许访问的模块列表
		'TMPL_CACHE_ON' => false, //清楚页面缓存
		'MODULE_ALLOW_LIST'    =>    array('Home'),
		'DEFAULT_MODULE'       =>    'Home',  // 默认模块
	//配置OSS云图片上传
		'UPLOAD_SITEIMG_OSS' => array (
				'maxSize' => 5 * 1024 * 1024,//文件大小
				'rootPath' => './',
				'saveName' => array ('uniqid', ''),
				'savePath' => 'gouchehui/web/Upload/',    //保存路径
				'driver' => 'Aliyun',
				'driverConfig' => array (
						'AccessKeyId' => 'DFXeIpT7iw1oA69l',    //AccessKeyId
						'AccessKeySecret' => 'MynjmA7k9AIuLFQADLgphhhmmV3PwR',//AccessKeySecret
						'domain' => OSS,        //
						'Bucket' => 'yichenghui',         //Bucket
						'Endpoint' => 'http://oss-cn-shanghai.aliyuncs.com',
					//如果是杭州的服务器
					//Endpoint设置成
					//'Endpoint' => 'http://oss-cn-hangzhou.aliyuncs.com',
				),
		),
		'SSLCERT_PATH' => './Public/cert/apiclient_cert.pem',
		'SSLKEY_PATH' => './Public/cert/apiclient_key.pem',
		'CURL_PROXY_HOST' => '0.0.0.0',//"10.152.18.220
		'CURL_PROXY_PORT' => 0,//8080;

		'APP_SUB_DOMAIN_DEPLOY'   =>    1, // 开启子域名配置
		'APP_SUB_DOMAIN_RULES'    =>    array(
				'www'         => 'Home',  // www子域名指向主站
				'member'      => 'Member',  // Member子域名指向用户中心
				
		),

);