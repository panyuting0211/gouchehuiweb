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


function show_bug($msg) 
{
        echo "<pre style='color:red'>";
		var_dump($msg);
		echo "</pre>";
}


// 应用入口文件
header("content-type:text/html;charset=utf-8");


//定义后台css,images,js常量
// define('SITE_URL','http://127.0.0.1' );
define('ADMIN_CSS_URL','/Public/Admin/css/');
define('ADMIN_IMG_URL','/Public/Admin/images/');
define('ADMIN_JS_URL','/Public/Admin/js/');
define('ADMIN_IMG_UPLOAD','/Public');

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',true);
define('OSS', 'http://yichenghui.oss-cn-shanghai.aliyuncs.com/gouchehui/web');

// 绑定Admin模块到当前入口文件
define('BIND_MODULE','Admin');

// 定义应用目录
define('APP_PATH','./Application/');

// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';


// 亲^_^ 后面不需要任何代码了 就是如此简单