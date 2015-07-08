/*
SQLyog Ultimate v8.63 
MySQL - 5.5.27 : Database - wms
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`wms` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `wms`;

/*Table structure for table `wms_access` */

DROP TABLE IF EXISTS `wms_access`;

CREATE TABLE `wms_access` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主id',
  `role_id` smallint(6) unsigned NOT NULL COMMENT '角色id',
  `node_id` smallint(6) unsigned NOT NULL COMMENT '节点id',
  `level` tinyint(1) NOT NULL COMMENT '级别',
  `pid` smallint(6) DEFAULT NULL COMMENT '父id',
  `module` varchar(50) DEFAULT NULL COMMENT '模块',
  PRIMARY KEY (`id`),
  KEY `idx_role_id` (`role_id`),
  KEY `idx_node_id` (`node_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限分配';

/*Data for the table `wms_access` */

/*Table structure for table `wms_ad` */

DROP TABLE IF EXISTS `wms_ad`;

CREATE TABLE `wms_ad` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主id',
  `name` varchar(40) NOT NULL DEFAULT '' COMMENT '广告位名称',
  `mark` varchar(30) NOT NULL DEFAULT '' COMMENT '广告位标识',
  `platform` tinyint(1) NOT NULL DEFAULT '0' COMMENT '设备平台[见配置文件]',
  `ad_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=代码2=文字3=图片',
  `time_limit` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1=永不过期2=时间限制',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `normal_content` text NOT NULL COMMENT '正常显示内容',
  `overdue_content` text NOT NULL COMMENT '过期显示内容',
  `status` tinyint(1) DEFAULT '1' COMMENT '1为可用 2为禁用',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=正常 2=删除',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_time_limit` (`time_limit`),
  KEY `idx_mark` (`mark`),
  KEY `idx_start_time` (`start_time`),
  KEY `idx_end_time` (`end_time`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='广告';

/*Data for the table `wms_ad` */

/*Table structure for table `wms_admin` */

DROP TABLE IF EXISTS `wms_admin`;

CREATE TABLE `wms_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键递增',
  `username` varchar(50) DEFAULT '' COMMENT '登录账号',
  `password` char(32) DEFAULT '' COMMENT '登录密码',
  `status` tinyint(1) DEFAULT '1' COMMENT '账号状态 1为可用 2为禁用',
  `note` varchar(255) DEFAULT '' COMMENT '备注信息',
  `last_login_time` int(11) NOT NULL DEFAULT '0' COMMENT '上次登录时间',
  `last_login_ip` varchar(20) NOT NULL DEFAULT '' COMMENT '上次登录ip',
  `login_count` int(11) NOT NULL DEFAULT '0' COMMENT '登陆次数',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=正常 2=删除',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='管理员';

/*Data for the table `wms_admin` */

insert  into `wms_admin`(`id`,`username`,`password`,`status`,`note`,`last_login_time`,`last_login_ip`,`login_count`,`is_deleted`,`create_time`,`update_time`) values (1,'admin','21232f297a57a5a743894a0e4a801fc3',1,'这是Admin账号',1406110389,'127.0.0.1',150,1,1402383367,1402560640);

/*Table structure for table `wms_config` */

DROP TABLE IF EXISTS `wms_config`;

CREATE TABLE `wms_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键递增',
  `field_name` varchar(30) NOT NULL DEFAULT '' COMMENT '字段名称',
  `field_value` varchar(150) NOT NULL DEFAULT '' COMMENT '字段内容',
  `field_desc` varchar(30) NOT NULL DEFAULT '' COMMENT '字段描述',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '类型',
  `atta_type` tinyint(1) NOT NULL DEFAULT '2' COMMENT '1=有附件2=无',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_filed_name` (`field_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='网站配置';

/*Data for the table `wms_config` */

/*Table structure for table `wms_link` */

DROP TABLE IF EXISTS `wms_link`;

CREATE TABLE `wms_link` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键递增',
  `title` varchar(30) NOT NULL DEFAULT '' COMMENT '标题',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=友情链接 2=合作伙伴',
  `icon` varchar(60) NOT NULL DEFAULT '' COMMENT '图片',
  `url` varchar(60) NOT NULL DEFAULT '' COMMENT '链接地址',
  `order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=启用 2=禁用',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=正常 2=删除',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='友情链接';

/*Data for the table `wms_link` */

/*Table structure for table `wms_news` */

DROP TABLE IF EXISTS `wms_news`;

CREATE TABLE `wms_news` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键递增',
  `title` varchar(30) NOT NULL DEFAULT '' COMMENT '标题',
  `category_id` int(10) NOT NULL DEFAULT '0' COMMENT '类别ID',
  `content` text NOT NULL COMMENT '内容',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=启用 2=禁用',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=正常 2=删除',
  `publish_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发布时间',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_category_id` (`category_id`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='最新公告';

/*Data for the table `wms_news` */

/*Table structure for table `wms_news_category` */

DROP TABLE IF EXISTS `wms_news_category`;

CREATE TABLE `wms_news_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '分类名称',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级ID',
  `order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=正常 2=删除',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='资讯类别';

/*Data for the table `wms_news_category` */

/*Table structure for table `wms_node` */

DROP TABLE IF EXISTS `wms_node`;

CREATE TABLE `wms_node` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT '主id',
  `name` varchar(20) NOT NULL COMMENT '名字',
  `title` varchar(50) DEFAULT NULL COMMENT '标题',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `sort` smallint(6) unsigned DEFAULT NULL COMMENT '排序',
  `pid` smallint(6) unsigned NOT NULL COMMENT '父id',
  `level` tinyint(1) unsigned NOT NULL COMMENT '级别',
  PRIMARY KEY (`id`),
  KEY `idx_level` (`level`),
  KEY `idx_pid` (`pid`),
  KEY `idx_status` (`status`),
  KEY `idx_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8 COMMENT='权限节点';

/*Data for the table `wms_node` */

insert  into `wms_node`(`id`,`name`,`title`,`status`,`remark`,`sort`,`pid`,`level`) values (1,'Admin','后台管理',1,'WMS后台管理系统',0,0,1),(2,'News','资讯管理',1,'',0,1,2),(3,'index','资讯列表',1,'',0,2,3),(4,'add','添加',1,'',0,2,3),(5,'edit','编辑',1,'',0,2,3),(6,'status','启用/禁用',1,'',0,2,3),(7,'del','删除',1,'',0,2,3),(8,'category','资讯分类',1,'',0,2,3),(9,'Link','链接管理',1,'',0,1,2),(10,'index','链接列表',1,'',0,9,3),(11,'add','添加',1,'',0,9,3),(12,'edit','编辑',1,'',0,9,3),(13,'status','启用/禁用',1,'',0,9,3),(14,'del','删除',1,'',0,9,3),(15,'details','详情',1,'',0,9,3),(16,'Ad','广告管理',1,'',0,1,2),(17,'index','广告列表',1,'',0,16,3),(18,'add','添加',1,'',0,16,3),(19,'edit','编辑',1,'',0,16,3),(20,'status','启用/禁用',1,'',0,16,3),(21,'del','删除',1,'',0,16,3),(22,'details','详情',1,'',0,16,3),(23,'User','用户管理',1,'',0,1,2),(24,'index','用户列表',1,'',0,23,3),(26,'del','删除',1,'',0,23,3),(27,'details','详情',1,'',0,23,3),(28,'Admin','管理员管理',1,'',0,1,2),(29,'index','管理员列表',1,'',0,28,3),(30,'add','添加',1,'',0,28,3),(31,'edit','编辑',1,'',0,28,3),(32,'status','启用/禁用',1,'',0,28,3),(33,'del','删除',1,'',0,28,3),(34,'details','详情',1,'',0,28,3),(35,'Config','配置管理',1,'',0,1,2),(36,'index','列表+编辑',1,'',0,35,3),(37,'add','添加',1,'',0,35,3),(38,'Access','权限管理',1,'',0,1,2),(39,'index','节点列表',1,'',0,38,3),(40,'addNode','添加节点',1,'',0,38,3),(41,'editNode','编辑节点',1,'',0,38,3),(42,'opSort','节点排序',1,'',0,38,3),(43,'opNodeStatus','启用/禁用(节点)',1,'',0,38,3),(44,'delNode','删除节点',1,'',0,38,3),(45,'roleList','角色列表',1,'',0,38,3),(46,'addRole','添加角色',1,'',0,38,3),(47,'editRole','编辑角色',1,'',0,38,3),(48,'opRoleStatus','启用/禁用(角色)',1,'',0,38,3),(49,'changeRole','分配权限',1,'',0,38,3),(50,'Db','数据库管理',1,'',0,1,2),(51,'index','数据表列表',1,'',0,50,3),(52,'restore','导入列表',1,'',0,50,3),(53,'zipList','压缩包列表',1,'',0,50,3),(54,'repair','优化与修复',1,'',0,50,3),(55,'status','禁用/启用',1,'',0,23,3),(56,'restoreData','导入',1,'',0,50,3),(57,'delSqlFiles','删除SQL文件',1,'',0,50,3),(58,'sendSql','发送邮箱',1,'',0,50,3),(59,'zipSql','压缩为ZIP',1,'',0,50,3),(60,'unzipSqlfile','解压缩为SQL',1,'',0,50,3),(61,'delZipFiles','删除ZIP文件',1,'',0,50,3),(62,'downFile','下载文件',1,'',0,50,3),(63,'Template','模板管理',1,'',0,1,2),(64,'index','文件列表',1,'',0,63,3),(65,'downFile','下载文件',1,'',0,63,3),(66,'edit','编辑',1,'',0,63,3),(67,'mkdir','创建文件夹',1,'',0,63,3),(68,'reName','重命名',1,'',0,63,3),(69,'delFile','删除文件',1,'',0,63,3),(70,'Oplog','日志管理',1,'',0,1,2),(71,'index','日志列表',1,'',0,70,3);

/*Table structure for table `wms_oplog` */

DROP TABLE IF EXISTS `wms_oplog`;

CREATE TABLE `wms_oplog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主id',
  `model` varchar(30) NOT NULL DEFAULT '' COMMENT '模块',
  `action` varchar(30) NOT NULL DEFAULT '' COMMENT '动作',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `admin_name` varchar(50) DEFAULT '' COMMENT '用户名',
  `role_id` int(10) NOT NULL DEFAULT '0' COMMENT '管理角色id',
  `role_name` varchar(50) NOT NULL DEFAULT '' COMMENT '管理角色',
  `bak` varchar(256) NOT NULL DEFAULT '' COMMENT '备注',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='后台操作表';

/*Data for the table `wms_oplog` */

insert  into `wms_oplog`(`id`,`model`,`action`,`admin_id`,`admin_name`,`role_id`,`role_name`,`bak`,`create_time`) values (1,'Public','loginout',1,'admin',1,'管理员','退出',1406110377),(2,'Public','index',1,'admin',1,'超级管理员','登录',1406110389);

/*Table structure for table `wms_role` */

DROP TABLE IF EXISTS `wms_role`;

CREATE TABLE `wms_role` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT '主id',
  `name` varchar(20) NOT NULL COMMENT '名字',
  `pid` smallint(6) DEFAULT NULL COMMENT '父id',
  `status` tinyint(1) unsigned DEFAULT NULL COMMENT '状态',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `idx_pid` (`pid`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='角色组';

/*Data for the table `wms_role` */

insert  into `wms_role`(`id`,`name`,`pid`,`status`,`remark`) values (1,'超级管理员',NULL,1,'拥有系统所有权限');

/*Table structure for table `wms_role_user` */

DROP TABLE IF EXISTS `wms_role_user`;

CREATE TABLE `wms_role_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主id',
  `role_id` mediumint(9) unsigned DEFAULT NULL COMMENT '角色id',
  `user_id` char(32) DEFAULT NULL COMMENT '用户id',
  PRIMARY KEY (`id`),
  KEY `idx_role_id` (`role_id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='用户角色';

/*Data for the table `wms_role_user` */

insert  into `wms_role_user`(`id`,`role_id`,`user_id`) values (1,1,'1');

/*Table structure for table `wms_user` */

DROP TABLE IF EXISTS `wms_user`;

CREATE TABLE `wms_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键递增',
  `nickname` varchar(20) DEFAULT '' COMMENT '昵称',
  `username` varchar(50) DEFAULT '' COMMENT '登录账号',
  `usertype` tinyint(1) DEFAULT '1' COMMENT '用户类型 ',
  `mobile` char(11) DEFAULT '' COMMENT '手机号',
  `email` varchar(50) DEFAULT '' COMMENT '邮箱',
  `password` char(32) DEFAULT '' COMMENT '登录密码',
  `status` tinyint(1) DEFAULT '1' COMMENT '账号状态 1=可用 2=禁用',
  `note` varchar(255) DEFAULT '' COMMENT '备注信息',
  `auth_mobile` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=未验证 2=已验证',
  `auth_email` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=未验证 2=已验证',
  `reg_type` tinyint(1) NOT NULL DEFAULT '3' COMMENT '1=android ,2=ios ,3=PC端 ,4=其它',
  `last_login_time` int(11) NOT NULL DEFAULT '0' COMMENT '上次登录时间',
  `last_login_ip` varchar(20) NOT NULL DEFAULT '' COMMENT '上次登录ip',
  `login_count` int(11) NOT NULL DEFAULT '0' COMMENT '登陆次数',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=正常 2=删除',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `Idx_create_time` (`create_time`),
  KEY `Idx_login_count` (`login_count`),
  KEY `Idx_mobile` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户';

/*Data for the table `wms_user` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
