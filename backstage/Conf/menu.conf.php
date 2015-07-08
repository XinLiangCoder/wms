<?php	return array (
	'admin_big_menu' => array(
        'Index' => '首页',
		'News' => '资讯',
		'Link' => '链接', 
		'Ad'=>'广告',       
		'User' => '用户',
		'Admin'=>'管理员',
		'Config'=>'配置',
		'Access'=>'权限',
		'Db'=>'数据库',
		'Template'=>'模板',
		'Oplog' => '日志',
    ),
    'admin_sub_menu' => array(
        'Index' => array(
            'changepwd' => '修改密码',
        ),
        'News' => array(
        	'index' => '资讯列表',
        	'category' => '资讯分类'
        ),
        'Link' => array(
        	'index' => '链接列表',
        ),
        'Ad' => array(
            'index' => '广告列表',
        ),
        'User' => array(
            'index' => '用户列表',
        ),
        'Admin' => array(
            'index' => '管理员列表',
        ),
        'Config' => array(
            'index' => '配置列表',
        ),
        'Access' => array(
            'index' => '节点管理',
        	'roleList' => '角色管理',
        ),
        'Db' => array(
            'index' => '数据库备份',
	        'restore' => '数据库导入',
	        'zipList' => '数据库压缩包',
	        'repair' => '数据库优化修复',
        ),
        'Template' => array(
            'index' => '文件列表',
        ),
        'Oplog' => array(
            'index' => '日志列表',
        ),
	),
);?>