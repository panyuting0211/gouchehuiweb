<?php
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
	//'配置项'=>'配置值'
	 'URL_MODEL'             =>1,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
	 'DATA_BACKUP_PATH'      =>  './Data/',
	 'DATA_BACKUP_PART_SIZE' =>  20971520,
	 'DATA_BACKUP_COMPRESS'  =>  1,
	 'DATA_BACKUP_COMPRESS_LEVEL'=> 9,
	 'SHOW_PAGE_TRACE' => true,
	 'TMPL_CACHE_ON' => false,//禁止模板编译缓存
	 'HTML_CACHE_ON' => false,//禁止静态缓存
		
	 //RBAC权限配置
	 'RBAC_SUPERADMIN'=>'admin',//超级管理员
	 'ADMIN_AUTH_KEY'=>'superadmin',//超级管理员识别
		
	 'USER_AUTH_ON'=>true, //是否需要认证
	 'USER_AUTH_TYPE'=>1, //认证类型 1登录 2实时认证
	 'USER_AUTH_KEY'=>'authId', //认证识别号,此名称可以自定义
	 //'REQUIRE_AUTH_MODULE'=>'',  //需要认证模块
	 'NOT_AUTH_MODULE'=>'Public,Index,Ajax,Password', //无需认证模块，和上面重复
	 'NOT_AUTH_ACTION'=>'index', //无需认证操作
	 //'USER_AUTH_GATEWAY'=>'', //认证网关
	 //'RBAC_DB_DSN'=>'mysql://root:123456@localhost:3306/guochehui',  //数据库连接DSN
	 'RBAC_ROLE_TABLE'=>'gch_role', //角色表名称
	 'RBAC_USER_TABLE'=>'gch_role_manager', //用户角色名称
	 'RBAC_ACCESS_TABLE'=>'gch_access', //权限表名称
	 'RBAC_NODE_TABLE'=>'gch_node', //节点表名称
	 'SHOW_PAGE_TRACE' => false,
	 'DB_FIELDS_CACHE'=>false,

   /* 'LAYOUT_ON'=>true,
    'LAYOUT_NAME'=>'layout',*/

	
);