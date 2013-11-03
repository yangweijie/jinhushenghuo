<?php
return array(

	//'配置项'=>'配置值'
	'URL_MODEL'				=>2,
    'APP_AUTOLOAD_PATH'     => 'Controller',// 自动加载机制的自动搜索路径,注意搜索顺序
	'MULTI_MODULE'=>false,
    'DEFAULT_USER'          => 'administrator',
    'DEFAULT_PWD'           => '123456',
    /* SESSION配置 */
    'SESSION_PREFIX'=> 'admin', //session前缀
    'VAR_SESSION_ID'=> 'session_id',
    'FILE_SYSTEM_ENCODE'=>IS_WIN? 'GBK' : 'UTF-8',

    /*cookie记住我时间长度*/
    'REMEMBER_ME_TIME'=> 86400*365,//365天 86400*365
    /* 项目其他配置 */
    'USER_AUTH_KEY'      => 'admin_user',
    'USER_AUTH_SIGN_KEY' => 'admin_user_sign',

    'FRONT_USER_AUTH_KEY'      => 'front_user',
    'FRONT_USER_AUTH_SIGN_KEY' => 'front_user_sign',

	/* 数据库设置 */
    'DB_TYPE'               => 'mysqli',     // 数据库类型
    'DB_HOST'               => '127.0.0.1', // 服务器地址
    'DB_NAME'               => 'jinhushenghuo', // 数据库名
    'DB_USER'               => 'root',      // 用户名
    'DB_PWD'                => '',          // 密码
    'DB_PORT'               => '3306',      // 端口
    'DB_PREFIX'             => 'think_',    // 数据库表前缀
    'SHOW_PAGE_TRACE'       =>1,
);