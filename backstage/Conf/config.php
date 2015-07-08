<?php
/** DB配置 **/
$DB_Config = include "db.conf.php";
/** 栏目配置 **/
$MEUN_Config = include "menu.conf.php";

$DB_PREFIX = $DB_Config['DB_PREFIX'];
define("WEB_ROOT", dirname(__FILE__) . "/");
define("STATIC_ROOT", dirname(WEB_ROOT).'/static/');
define("Tpl_ROOT", dirname(WEB_ROOT).'/web/Tpl/');

$initConfig = array (
	/** 系统配置 **/
	'SITE_INFO' => array(
        'name' => 'WMS后台管理系统',
		'version' => '',
        'icp' => '',
        'service' => '',
        'tel' => '',
        'fax' => '',
        'address' => '', 
        'postcode' => '',
        'keyword' => '内容管理系统',
        'description' => '',
    ),
	'WEB_ROOT' => 'http://backstage.cms.com/',
	'HOME_ROOT' => 'http://www.cms.com/',
	'AUTH_CODE' => 'SzNeTu',
	'ADMIN_AUTH_KEY' => 'admin',
	'webPath' => '/',
	'UPLOAD_PATH' => array(
    	/** 友情链接/合作伙伴 **/
        'Link' => STATIC_ROOT.'uploads/backstage_link',
    	/** 资讯编辑器中的IMG **/
    	'News_Img' => STATIC_ROOT.'uploads/backstage_news_img',
    	/** 广告图片上传目录**/
    	'AD' => STATIC_ROOT.'uploads/backstage_ad_img',
    	/** 配置 APK Or Images Or Pdf Or Excel上传目录**/
    	'Config_Atta' => STATIC_ROOT.'uploads/config_atta',
	),
	'TOKEN' => array(
        'admin_marked' => 'wms_admin',
		'admin_timeout' => '3600',
		'member_marked' => 'wms_user',
		'member_timeout' => '3600',
    ),
    /** 参数加密配置 **/
    'PARAMS_AUTH_CODE' => 'q7w9e6d8c4v1f4r',
    /** 设备平台类别 **/
    'PLATFORM_TYPE' => array(
    	'1' => 'Android',
    	'2' => 'Ios',
    	'3' => 'PC',
    	'4' => '其他平台'
    ),
	/** 配置项 **/
	'CONFIG_ITEM' => array(
		'1' => '常用配置',
		'2' => 'SEO配置',
	),
	'TMPL_CACHE_ON' => false,
	'HTML_CACHE_ON'=>false,
	'DB_FIELD_CACHE'=>false,
	'VAR_FILTERS'=>'htmlspecialchars',
	'SHOW_PAGE_TRACE' => false,
    'TOKEN_ON' => true, // 是否开启令牌验证
    'TOKEN_NAME' => '__hash__', // 令牌验证的表单隐藏字段名称
    'TOKEN_TYPE' => 'md5', //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET' => FALSE, //令牌验证出错后是否重置令牌 默认为true
	
	/*
     * 以下是RBAC认证配置信息
     */
    'USER_AUTH_ON' => true,
    'USER_AUTH_TYPE' => 2, // 默认认证类型 1 登录认证 2 实时认证
    'USER_AUTH_KEY' => 'authId', // 用户认证SESSION标记
    'USER_AUTH_MODEL' => 'Admin', // 默认验证数据表模型
    'AUTH_PWD_ENCODER' => 'md5', // 用户认证密码加密方式encrypt
    'USER_AUTH_GATEWAY' => '/index.php/Public/index', // 默认认证网关
    'NOT_AUTH_MODULE' => 'Public,Index', // 默认无需认证模块
    'REQUIRE_AUTH_MODULE' => '', // 默认需要认证模块
    'NOT_AUTH_ACTION' => '', // 默认无需认证操作
    'REQUIRE_AUTH_ACTION' => '', // 默认需要认证操作
    'GUEST_AUTH_ON' => false, // 是否开启游客授权访问
    'GUEST_AUTH_ID' => 0, // 游客的用户ID
    'RBAC_ROLE_TABLE' => $DB_PREFIX . 'role',
    'RBAC_USER_TABLE' => $DB_PREFIX . 'role_user',
    'RBAC_ACCESS_TABLE' => $DB_PREFIX . 'access',
    'RBAC_NODE_TABLE' => $DB_PREFIX . 'node',
	
	'TMPL_PARSE_STRING'  =>array(
        '__Link__' => 'http://static.cms.com/uploads/backstage_link/',
		'__News_Img__' => 'http://static.cms.com/uploads/backstage_news_img/',
		'__AD_Img__' => 'http://static.cms.com/uploads/backstage_ad_img/',
		'__Config_Atta__' => 'http://static.cms.com/uploads/config_atta/',
    ),
    /** Db Backup Config  **/
    'sqlFileSize' => 5242880, //该值不可太大，否则会导致内存溢出备份、恢复失败，合理大小在512K~10M间，建议5M一卷
        //10M=1024*1024*10=10485760
        //5M=5*1024*1024=5242880
    'SYSTEM_EMAIL' => array (
        'smtp_host' => 'smtp.qq.com',
        'smtp_port' => '25',
        'from_email' => '109760455@qq.com',
        'from_name' => '***网站',
        'smtp_user' => '109760455@qq.com',
        'smtp_pass' => '',
        'reply_email' => '',
        'reply_name' => '',
        'test_email' => '',
    ),
);
return array_merge($initConfig,$DB_Config,$MEUN_Config);
?>