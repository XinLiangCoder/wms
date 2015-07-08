<?php
ini_set('date.timezone', 'Asia/Shanghai');
define('THINK_PATH', './ThinkPHP/');
define('APP_NAME', 'Admin');
define("WEB_ROOT", dirname(__FILE__) . "/");
define('WEB_CACHE_PATH', WEB_ROOT."Runtime/Cache/");
define("RUNTIME_PATH", WEB_ROOT . "Runtime/Cache/Admin/");
define("DatabaseBackDir", WEB_ROOT . "Cache/Databases/"); //系统备份数据库文件存放目录
define('APP_DEBUG', true);
require(THINK_PATH . "ThinkPHP.php");
?>