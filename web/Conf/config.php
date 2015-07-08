<?php
/** DB配置 **/
$DB_Config = include "db.conf.php";

$initConfig = array(
    'SHOW_PAGE_TRACE' => false, // 显示页面Trace信息

    'LAYOUT_ON'     => false,
    'LAYOUT_NAME'   => 'Layout/layout',

    'TOKEN_ON' => true,  // 是否开启令牌验证 默认关闭
    'TOKEN_NAME' => '__hash__',    // 令牌验证的表单隐藏字段名称
    'TOKEN_TYPE' => 'md5',  //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET' => true,  //令牌验证出错后是否重置令牌 默认为true
    
    'TMPL_PARSE_STRING'  =>array(
		 '__SITE_URL__' => 'http://www.cms.com',
         '__STATIC_URL__' => 'http://static.cms.com',
		 '__JSTIME__' => '?t=20140822',
		 '__CSSTIME__' => '?t=20140822',
		 '__IMGTIME__' => '?t=20140822',
    ),
    'URL_MODEL' =>	2,
    'URL_ROUTER_ON'     => true,
    'URL_ROUTE_RULES'   => array(
    	//'/^category\/(\d+)-(\d+)$/' =>"Products/category?cateid=:1&cid=:2",//通用分类
    ),
	'PARAMS_AUTH_CODE' => 'q0w4e5r7tWZD',
    /** News模版页面 **/
    'NEWS_TEMP_TYPE' => array(
        '0' => array(
                'TEMP_NAME' => 'index',
                'MOBILE_NAME' => 'mobile',
                'TEMP_DESC' => '普通页面',
               ),
        '1' => array(
                'TEMP_NAME' => 'block',
                'MOBILE_NAME' => 'block_mobile',
                'TEMP_DESC' => '块状页面',
               ),
    ),
);
return array_merge($initConfig, $DB_Config);
?>