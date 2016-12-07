<?php
return array(
	//'配置项'=>'配置值'
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
    //商城数据库连接配置
    'DB_CONFIG1' => array(
        'DB_TYPE'               =>  'mysql',     // 数据库类型
        'DB_HOST'               =>  '127.0.0.1', // 服务器地址
        'DB_NAME'               =>  'mallaichego',          // 数据库名
        'DB_USER'               =>  'shopuser',      // 用户名
        'DB_PWD'                =>  'shoppw',        // 密码
        'DB_PORT'               =>  '3306',        // 端口
        'DB_PREFIX'             =>  'ecs_',    // 数据库表前缀
        'DB_FIELDTYPE_CHECK'    =>  false,       // 是否进行字段类型检查
        'DB_FIELDS_CACHE'       =>  false,        // 启用字段缓存
        'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
        'APP_BEGIN'             =>   array("Getcoding"),
    ),
    //腾讯QQ登录配置
    'THINK_SDK_QQ' => array(
        'APP_KEY' => '101290011', //应用注册成功后分配的 APP ID
        'APP_SECRET' => '812184027c1a730a4a57e311c57339cc', //应用注册成功后分配的KEY
        'CALLBACK' => 'http://member.gouchehui.com/index.php/public/callback?type=qq',
    ),
    //微信登录配置
    'THINK_SDK_WEIXIN' => array(
        'APP_KEY'    => 'wxca2e1232da6f007b', //应用注册成功后分配的 APP ID
        'APP_SECRET' => '0d6877afd4af7743f48bd3fa559d87a4', //应用注册成功后分配的KEY
        'CALLBACK'   => 'http://member.gouchehui.com/index.php/public/callbackweixin?type=weixin',
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
);
