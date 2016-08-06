<?php
return array(
	//'配置项'=>'配置值'
	'DATA_CACHE_TYPE'	=> 'File',
	'DATA_CACHE_PREFIX'	=> 'mideo',
	'DATA_CACHE_TIME'	=> 60,

	// 'DATA_CACHE_TYPE' => 'Memcache',
 //    'DATA_CACHE_PREFIX'  => 'mideo',
 //    'MEMCACHE_HOST'   => 'tcp://127.0.0.1:11211',
 //    'DATA_CACHE_TIME' => '60',

    'LOG_RECORD' => true, // 开启日志记录
    'LOG_LEVEL'  =>'ERR', // 只记录 ERR 错误

	//数据库相关
    'DB_PREFIX' => '',
    //'DB_DSN' => 'mysql://thehotgames:weekmovie2013@mysql.adonads.com:3306/mideo',//数据库配置

    //数据库配置信息
    'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_NAME'   => 'mideo', // 数据库名

    'DB_HOST'   => '127.0.0.1', // 美国内网服务器地址
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => '', // 密码

    'DB_PORT'   => 3306, // 端口
    'DB_PARAMS' =>  array(), // 数据库连接参数
    'DB_PREFIX' => '', // 数据库表前缀
    'DB_CHARSET'=> 'utf8', // 字符集
    'DB_DEBUG'  =>  false, // 数据库调试模式 开启后可以记录SQL日志

    // 'DB_HOST'   => '10.88.147.13', // 服务器地址
    // 'DB_USER'   => 'thehotgames', // 用户名
    // 'DB_PWD'    => 'weekmovie2013', // 密码

    'DEFAULT_LANG' => 'en-us',
    'ERROR_PAGE'=>'/Public/error.html',

    //打开页面跟踪
    'SHOW_PAGE_TRACE' => false,

    'TMPL_PARSE_STRING' => array(
        '__CSS__' => __ROOT__.'/Public/css',
        '__JS__' => __ROOT__.'/Public/js',
        '__UEDITOR__' => __ROOT__.'/Public/ueditor1432'
    ),

    //开启路由
    'URL_ROUTER_ON' => true,
    'DEFAULT_TIMEZONE'=>'America/Sao_Paulo',
);