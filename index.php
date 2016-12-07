<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件
//制作一个输出调试函数

function show_bug($msg) 
{
        echo "<pre style='color:red'>";
		var_dump($msg);
		echo "</pre>" ;
}
header("content-type:text/html;charset=utf-8");

//定义css,images,js常量
//define('SITE_URL','http://127.0.0.1' );
define('CSS_URL','/Public/Home/css/');
define('IMG_URL','/Public/Home/images/');
define('JS_URL','/Public/Home/js/');
define('IMG_UPLOAD','/Public/');
/*define('OSS','/Public');*/
define('OSS', 'http://yichenghui.oss-cn-shanghai.aliyuncs.com/gouchehui/web');
//定义后台css,images,js常量

define('ADMIN_CSS_URL','/Public/Admin/css/');
define('ADMIN_IMG_URL','/Public/Admin/images/');
define('ADMIN_JS_URL','/Public/Admin/js/');

//定义车生活css,images,js常量

define('CARLIFE_CSS_URL','/Public/Carlife/css/');
define('CARLIFE_IMG_URL','/Public/Carlife/images/');
define('CARLIFE_JS_URL','/Public/Carlife/js/');

define('SHOWDOC','/Public/Showdoc/');

/*//开启GZIP压缩
define( "GZIP_ENABLE", function_exists ( 'ob_gzhandler') );
ob_start( GZIP_ENABLE ? 'ob_gzhandler': null );*/
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',true);
// 定义应用目录
define('APP_PATH','./Application/');

// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单
