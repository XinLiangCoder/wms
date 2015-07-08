<?php
/**
 * Db Manage Class
 * @author linxinliang<109760455@qq.com>
 */
class DbAction extends CommonAction {
	/**
	 * Method Doesn't Exist To Do
     * @author linxinliang<109760455@qq.com>
	 */
	public function _empty(){
		$this->error('Unauthorized access');
	}
	
	/**
   	 * List Tables Page View Function
   	 * @author linxinliang<109760455@qq.com>
   	 */
    public function index() {
    	echo $this->_DatabaseBackDir;
        $tabs = M()->query('SHOW TABLE STATUS');
        $total = 0;
        foreach ($tabs as $k => $v) {
            $tabs[$k]['size'] = byteFormat($v['Data_length'] + $v['Index_length']);
            $total+=$v['Data_length'] + $v['Index_length'];
        }
        $this->assign("list", $tabs);
        $this->assign("total", byteFormat($total));
        $this->assign("tables", count($tabs));
        $this->display();
    }
	/**
     * Restore Page View Function
     * @author linxinliang<109760455@qq.com>
     */  
    public function restore() {
        $data = $this->getSqlFilesList();
        $this->assign("list", $data['list']);
        $this->assign("total", $data['size']);
        $this->assign("files", count($data['list']));
        $this->display();
    }
	/**
   	 * List Zip File Page View Function
   	 * @author linxinliang<109760455@qq.com>
   	 */
    public function zipList() {
        $data = $this->getZipFilesList();
        $this->assign("list", $data['list']);
        $this->assign("total", $data['size']);
        $this->assign("files", count($data['list']));
        $this->display();
    }
    /**
   	 * List Table To repair Page View Function
   	 * @author linxinliang<109760455@qq.com>
   	 */
	public function repair() {
        $M = M();
        if (IS_POST) {
            if (empty($_POST['table']) || count($_POST['table']) == 0) {
                die(json_encode(array("status" => 0, "info" => "请选择要处理的表")));
            }
            $table = implode(',', $_POST['table']);
            if ($_POST['act'] == 'repair') {
                if ($M->query("REPAIR TABLE {$table} ")){
						$this->Log('修复表'.$table);
						die(json_encode(array("status" => 1, "info" => "修复表成功", 'url' => U('Db/repair'))));
					}
            } elseif ($_POST['act'] == 'optimize') {
                if ($M->query("OPTIMIZE TABLE $table")){
					$this->Log('优化表'.$table);
                    die(json_encode(array("status" => 1, "info" => "优化表成功", 'url' => U('Db/repair'))));
				}
            }
            die(json_encode(array("status" => 0, "info" => "请选择操作")));
        } else {
            $tabs = $M->query('SHOW TABLE STATUS');
            $totalsize = array('table' => 0, 'index' => 0, 'data' => 0, 'free' => 0);
            $tables = array();
            foreach ($tabs as $k => $table) {
                $table['size'] = byteFormat($table['Data_length'] + $table['Index_length']);
                $totalsize['table'] += $table['Data_length'] + $table['Index_length'];
                $totalsize['data']+=$table['Data_length'];
                $table['Data_length'] = byteFormat($table['Data_length']);
                $totalsize['index']+=$table['Index_length'];
                $table['Index_length'] = byteFormat($table['Index_length']);
                $totalsize['free']+=$table['Data_free'];
                $table['Data_free'] = byteFormat($table['Data_free']);
                $tables[] = $table;
            }
            $this->assign("list", $tables);
            $this->assign("totalsize", $totalsize);
            $this->display();
        }
    }
    
    /**
   	 * Backup DataBase Function
   	 * @author linxinliang<109760455@qq.com>
   	 */
    public function backup() {
        if (!IS_POST)
            $this->error("访问出错啦");
        header('Content-Type:application/json; charset=utf-8');
        $M = M();
        /** 防止备份数据过程超时 **/
        function_exists('set_time_limit') && set_time_limit(0); 
        $tables = empty($_POST['table']) ? array() : $_POST['table'];
        if (count($tables) == 0 && !isset($_POST['systemBackup'])) {
            die(json_encode(array("status" => 0, "info" => "请先选择要备份的表")));
        }
        $time = time();
        if (isset($_POST['systemBackup'])) {
            if ($_SESSION['username'] != C("ADMIN_AUTH_KEY")) {
                die(json_encode(array("status" => 0, "info" => "只有超级管理员账号登录后方可自动备份操作")));
            }
            $type = "系统自动备份";
            $tables = $this->getAllTableName();
            $path = DatabaseBackDir . "/SYSTEM_" . date("Ym");
            if (file_exists($path . "_1.sql")) {
                die(json_encode(array("status" => 0, "info" => "本月度系统已经进行了自动备份操作")));
            }
        } else {
            $type = "管理员后台手动备份";
            $path = DatabaseBackDir . "/CUSTOM_" . date("Ymd") . "_" . randCode(5);
        }
        $pre = "# -----------------------------------------------------------\n" .
                "# PHP database backup files\n" .
                "# Blog: http://lin100.com\n" .
                "# Type: {$type}\n";
		/** 取得表结构信息 **/
        $bdTable = $this->bakupTable($tables);
        $outPut = "";
        $file_n = 1;
        $backedTable = array();
        foreach ($tables as $table) {
            $backedTable[] = $table;
            $outPut.="\n\n# 数据库表：{$table} 数据信息\n";
            $tableInfo = $M->query("SHOW TABLE STATUS LIKE '{$table}'");
            $page = ceil($tableInfo[0]['Rows'] / 10000) - 1;
            for ($i = 0; $i <= $page; $i++) {
                $query = $M->query("SELECT * FROM {$table} LIMIT " . ($i * 10000) . ", 10000");
                foreach ($query as $val) {
                    $temSql = "";
                    $tn = 0;
                    $temSql = '';
                    foreach ($val as $v) {
                        $temSql.=$tn == 0 ? "" : ",";
                        $temSql.=$v == '' ? "''" : "'{$v}'";
                        $tn++;
                    }
                    $temSql = "INSERT INTO `{$table}` VALUES ({$temSql});\n";

                    $sqlNo = "\n# Time: " . date("Y-m-d H:i:s") . "\n" .
                            "# -----------------------------------------------------------\n" .
                            "# 当前SQL卷标：#{$file_n}\n# -----------------------------------------------------------\n\n\n";
                    if ($file_n == 1) {
                        $sqlNo = "# Description:当前SQL文件包含了表：" . implode("、", $tables) . "的结构信息，表：" . implode("、", $backedTable) . "的数据" . $sqlNo;
                    } else {
                        $sqlNo = "# Description:当前SQL文件包含了表：" . implode("、", $backedTable) . "的数据" . $sqlNo;
                    }
                    if (strlen($pre) + strlen($sqlNo) + strlen($bdTable) + strlen($outPut) + strlen($temSql) > C("sqlFileSize")) {
                        $file = $path . "_" . $file_n . ".sql";
                        $outPut = $file_n == 1 ? $pre . $sqlNo . $bdTable . $outPut : $pre . $sqlNo . $outPut;
                        file_put_contents($file, $outPut, FILE_APPEND);
                        $bdTable = $outPut = "";
                        $backedTable = array();
                        $backedTable[] = $table;
                        $file_n++;
                    }
                    $outPut.=$temSql;
                }
            }
        }
        if (strlen($bdTable . $outPut) > 0) {
            $sqlNo = "\n# Time: " . date("Y-m-d H:i:s") . "\n" .
                    "# -----------------------------------------------------------\n" .
                    "# 当前SQL卷标：#{$file_n}\n# -----------------------------------------------------------\n\n\n";
            if ($file_n == 1) {
                $sqlNo = "# Description:当前SQL文件包含了表：" . implode("、", $tables) . "的结构信息，表：" . implode("、", $backedTable) . "的数据" . $sqlNo;
            } else {
                $sqlNo = "# Description:当前SQL文件包含了表：" . implode("、", $backedTable) . "的数据" . $sqlNo;
            }
            $file = $path . "_" . $file_n . ".sql";
            $outPut = $file_n == 1 ? $pre . $sqlNo . $bdTable . $outPut : $pre . $sqlNo . $outPut;
            file_put_contents($file, $outPut, FILE_APPEND);
            $file_n++;
        }
        $time = time() - $time;
        $this->Log('数据库备份');
        echo json_encode(array("status" => 1, "info" => "成功备份所选数据库表结构和数据，本次备份共生成了" . ($file_n - 1) . "个SQL文件。耗时：{$time} 秒", "url" => U('Db/restore')));
    }

    /**
   	 * restore Sql Data Function
   	 * @author linxinliang<109760455@qq.com>
   	 */
    public function restoreData() {
    	/** ini_set("memory_limit", "256M"); **/
		/** 防止备份数据过程超时 **/
        function_exists('set_time_limit') && set_time_limit(0);
        /** 取得需要导入的sql文件 **/ 
        $files = isset($_SESSION['cacheRestore']) ? $_SESSION['cacheRestore']['files'] : self::getRestoreFiles();
		/** 取得上次文件导入到sql的句柄位置 **/
        $position = isset($_SESSION['cacheRestore']['position']) ? $_SESSION['cacheRestore']['position'] : 0;
        $M = M();
        $execute = 0;
        foreach ($files as $fileKey => $sqlFile) {
            $file = DatabaseBackDir . $sqlFile;
            if (!file_exists($file))
                continue;
            $file = fopen($file, "r");
            $sql = "";
            /** 将文件指针指向上次位置 **/
            fseek($file, $position);
            while (!feof($file)) {
                $tem = trim(fgets($file));
                /** 过滤掉空行、以#号注释掉的行、以--注释掉的行 **/
                if (empty($tem) || $tem[0] == '#' || ($tem[0] == '-' && $tem[1] == '-'))
                    continue;
				/** 统计一行字符串的长度 **/
                $end = (int) (strlen($tem) - 1);
                /** 检测一行字符串最后有个字符是否是分号，是分号则一条sql语句结束，否则sql还有一部分在下一行中 **/
                if ($tem[$end] == ";") {
                    $sql.=$tem;
                    $M->query($sql);
                    $sql = "";
                    $execute++;
                    if ($execute > 500) {
                        $_SESSION['cacheRestore']['position'] = ftell($file);
                        $imported = isset($_SESSION['cacheRestore']['imported']) ? $_SESSION['cacheRestore']['imported'] : 0;
                        $imported+=$execute;
                        $_SESSION['cacheRestore']['imported'] = $imported;
                        echo json_encode(array("status" => 1, "info" => '如果导入SQL文件卷较大(多)导入时间可能需要几分钟甚至更久，请耐心等待导入完成，导入期间请勿刷新本页，当前导入进度：<font color="red">已经导入' . $imported . '条Sql</font>', "url" => U('Db/restoreData', array(randCode() => randCode()))));
                        exit;
                    }
                } else {
                    $sql.=$tem;
                }
            }
            fclose($file);
            unset($_SESSION['cacheRestore']['files'][$fileKey]);
            $position = 0;
        }
        $time = time() - $_SESSION['cacheRestore']['time'];
        unset($_SESSION['cacheRestore']);
        echo json_encode(array("status" => 1, "info" => "导入成功，耗时：{$time} 秒钟.","url" => U("Db/restore")));
    }

   /**
   	 * Del Sql Files Function
   	 * @author linxinliang<109760455@qq.com>
   	 */
    public function delSqlFiles() {
        if (IS_POST) {
            if (empty($_POST['sqlFiles']) || count($_POST['sqlFiles']) == 0)
                echo json_encode(array("status" => 0, "info" => "请先选择要删除的文件"));

            $files = $_POST['sqlFiles'];
            foreach ($files as $file) {
                delDirAndFile(DatabaseBackDir . $file);
            }
			$this->Log('删除已备份数据库文件');
            echo json_encode(array("status" => 1, "info" => "已删除：" . implode("、", $files), "url" => __URL__ . "/restore?" . time()));
        }
    }
    /**
   	 * Send Sql To EmailAdress Function
   	 * @author linxinliang<109760455@qq.com>
   	 */
    public function sendSql() {
        set_time_limit(0);
        if (IS_POST) {
        	$SYSTEM_EMAIL = C('SYSTEM_EMAIL');
        	if(empty($SYSTEM_EMAIL['smtp_host']) || empty($SYSTEM_EMAIL['smtp_port']) || empty($SYSTEM_EMAIL['from_email']) || empty($SYSTEM_EMAIL['smtp_user']) || empty($SYSTEM_EMAIL['smtp_pass'])){
        		die(json_encode(array("status" => 0, "info" => "发送失败，请完善config.php中SYSTEM_EMAIL参数！")));
        	}
            $files = isset($_SESSION['cacheSendSql']['files']) ? $_SESSION['cacheSendSql']['files'] : self::getSqlFilesGroups();
            $to = $_SESSION['cacheSendSql']['to'];
            $sum = $_SESSION['cacheSendSql']['count'];
            foreach ($files as $k => $zipFiles) {
                $zipOut = $sum == 1 ? "sqlBackup.zip" : "sqlBackup_{$k}.zip";
                if ($this->zip($zipFiles, $zipOut)) {
                    send_mail($to, "", "数据库备份{$k}/{$sum}", "网站：<b>" . $this->site['SITE_INFO']['name'] . "</b> 数据文件备份", WEB_CACHE_PATH . $zipOut);
                    delDirAndFile(WEB_CACHE_PATH . $zipOut);
                    echo json_encode(array("status" => 1, "info" => "如果要发送SQL文件卷较大(多)发送时间可能需要几分钟甚至更久，请耐心等待，发送期间请勿刷新本页。SQL打包成{$sum}个zip包，分{$sum}封邮件发出，<font color=\"red\">当前已经发送完第{$k}封邮件</font>", "url" => U('Db/sendSql', array(randCode() => randCode()))));
                    unset($_SESSION['cacheSendSql']['files'][$k]);
                    exit;
                }
            }
            $time = time() - $_SESSION['cacheSendSql']['time'];
            unset($_SESSION['cacheSendSql']);
            $this->Log("向邮箱：".$to."发送sql文件");
            die(json_encode(array("status" => 1, "info" => "sql文件已发送到你的邮件，请注意查收<br/>耗时：$time 秒")));
        }
        $this->display();
    }

    /**
   	 * Zip Sql Function
   	 * @author linxinliang<109760455@qq.com>
   	 */
    public function zipSql() {
        if (IS_POST) {
            if (!$_POST['sqlFiles'] || count($_POST['sqlFiles']) == 0)
                die(json_encode(array("status" => 0, "info" => "请选择要打包的sql文件")));
            $files = $_POST['sqlFiles'];
            $toZip = array();
            foreach ($files as $file) {
                $tem = explode("_", $file);
                unset($tem[count($tem) - 1]);
                $toZip[implode("_", $tem)][] = $file;
            }
            foreach ($toZip as $zipOut => $files) {
                if ($this->zip($files, $zipOut . ".zip", DatabaseBackDir . "Zip/")) {
                    foreach ($files as $file) {
                        delDirAndFile(DatabaseBackDir . $file);
                    }
                }
            }
			$this->Log('打包sql文件');
            die(json_encode(array("status" => 1, "info" => "打包的sql文件成功，本次打包" . count($toZip) . "个zip文件", "url" => U('Db/zipList'))));
        }
    }

    /**
     * unzipSqlfile Function
     * @author linxinliang<109760455@qq.com>
     */  
    public function unzipSqlfile() {
        if (!IS_POST)
            return FALSE;
        if ($_SESSION['unzip']) {
            $files = $_SESSION['unzip']['files'];
        } else {
            $_SESSION['unzip']['time'] = time();
            if (!$_POST['zipFiles'] || count($_POST['zipFiles']) == 0)
                die(evaljson_encode(array("status" => 0, "info" => "请选择要解压的zip文件")));
            $files = $_POST['zipFiles'];
            $_SESSION['unzip']['files'] = $files;
            $_SESSION['unzip']['count'] = count($files);
        }
        foreach ($files as $k => $file) {
            $this->unzip($file);
            if (count($files) > 1) {
                echo json_encode(array("status" => 1, "info" => "正在解压缩请耐心等待，解压期间请勿刷新本页 <font color=\"red\">当前已经解压完{$file}</font>", "url" => U('Db/unzipSqlfile', array(randCode() => randCode()))));
                unset($_SESSION['unzip']['files'][$k]);
                exit;
            }
        }

        $time = time() - $_SESSION['unzip']['time'];
        unset($_SESSION['unzip']);
		$this->Log('解压文件');
        die(json_encode(array("status" => 1, "info" => "已解压完成<br/>耗时：$time 秒")));
    }

    /**
   	 * Del ZipSql Files Function
   	 * @author linxinliang<109760455@qq.com>
   	 */
    public function delZipFiles() {
        if (IS_POST) {
            if (!$_POST['zipFiles'] || count($_POST['zipFiles']) == 0)
                die(json_encode(array("status" => 0, "info" => "请选择要删除的zip文件")));
            $files = $_POST['zipFiles'];
            foreach ($files as $file) {
                delDirAndFile(DatabaseBackDir . "Zip/" . $file);
            }
			$this->Log('删除已备份数据库文件');
            echo json_encode(array("status" => 1, "info" => "已删除：" . implode("、", $files), "url" => __URL__ . "/zipList?" . time()));
        }
    }
	/**
     * downFile Function
     * @author linxinliang<109760455@qq.com>
     */
    public function downFile() {
        if (empty($_GET['file']) || empty($_GET['type']) || !in_array($_GET['type'], array("zip", "sql"))) {
            $this->error("下载地址不存在");
        }
        $path = array("zip" => DatabaseBackDir . "Zip/", "sql" => DatabaseBackDir);
        $filePath = $path[$_GET['type']] . $_GET['file'];
        if (!file_exists($filePath)) {
            $this->error("该文件不存在，可能是被删除");
        }
		$this->Log('下载文件名称：'.$_GET['file']);
        $filename = basename($filePath);
        header("Content-type: application/octet-stream");
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header("Content-Length: " . filesize($filePath));
        readfile($filePath);
    }

    /**
     * BackupTable Function
     * @author linxinliang<109760455@qq.com>
     */  
	private function bakupTable($table_list) {
		/** 1，表示表名和字段名会用``包着的,0 则不用`` **/
        M()->query("SET SQL_QUOTE_SHOW_CREATE = 1");
        $outPut = '';
        if (!is_array($table_list) || empty($table_list)) {
            return false;
        }
        foreach ($table_list as $table) {
            $outPut.="# 数据库表：{$table} 结构信息\n";
            $outPut .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $tmp = M()->query("SHOW CREATE TABLE {$table}");
            $outPut .= $tmp[0]['Create Table'] . " ;\n\n";
        }
        return $outPut;
    }
    /**
     * Read SQL file list
     * @author linxinliang<109760455@qq.com>
     * @return array
     */ 
    private function getSqlFilesList() {
        $list = array();
        $size = 0;
        $handle = opendir(DatabaseBackDir);
        while ($file = readdir($handle)) {
            if (preg_match('#\.sql$#i', $file)) {
                $fp = fopen(DatabaseBackDir . "/$file", 'rb');
                $bakinfo = fread($fp, 2000);
                fclose($fp);
                $detail = explode("\n", $bakinfo);
                $bk = array();
                $bk['name'] = $file;
                $bk['url'] = substr($detail[2], 7);
                $bk['type'] = substr($detail[3], 8);
                $bk['description'] = substr($detail[4], 14);
                $bk['time'] = substr($detail[5], 8);
                $_size = filesize(DatabaseBackDir . "/$file");
                $bk['size'] = byteFormat($_size);
                $size+=$_size;
                $bk['pre'] = substr($file, 0, strrpos($file, '_'));
                $bk['num'] = substr($file, strrpos($file, '_') + 1, strrpos($file, '.') - 1 - strrpos($file, '_'));
                $mtime = filemtime(DatabaseBackDir . "/$file");
                $list[$mtime][$file] = $bk;
            }
        }
        closedir($handle);
        /** 按备份时间倒序排列 **/
        krsort($list);
        $newArr = array();
        foreach ($list as $k => $array) {
        	/** 按备份文件名称顺序排列 **/
            ksort($array);
            foreach ($array as $arr) {
                $newArr[] = $arr;
            }
        }
        unset($list);
        return array("list" => $newArr, "size" => byteFormat($size));
    }
    /**
     * Make ZIP File Set in  WEB_CACHE_PATH dir
     * @author linxinliang<109760455@qq.com>
     * @param $files        array   Need a zip file
     * @param $filename     string  Output zip file name demo:test.zip
     * @param $path         string  The file directory
     * @param $outDir       string  Output file directory
     * @return array
     */ 
    private function zip($files, $filename, $outDir = WEB_CACHE_PATH, $path = DatabaseBackDir) {
        $zip = new ZipArchive;
        makeDir($outDir);
        $res = $zip->open($outDir . "\\" . $filename, ZipArchive::CREATE);
        if ($res === TRUE) {
            foreach ($files as $file) {
                $zip->addFile($path . $file, str_replace('/', '', $file));
            }
            $zip->close();
            return TRUE;
        } else {
            return FALSE;
        }
    }
    /**
     * Read backup ZIP SQL file list
     * @author linxinliang<109760455@qq.com>
     * @return array
     */ 
	private function getZipFilesList() {
        $list = array();
        $size = 0;
        $handle = opendir(DatabaseBackDir . "Zip/");
        while ($file = readdir($handle)) {
            if ($file != "." && $file != "..") {
                $tem = array();
                $tem['file'] = $file; //  checkCharset($file);
                $_size = filesize(DatabaseBackDir . "Zip/$file");
                $tem['size'] = byteFormat($_size);
                $tem['time'] = date("Y-m-d H:i:s", filectime(DatabaseBackDir . "Zip/$file"));
                $size+=$_size;
                $list[] = $tem;
            }
        }
        return array("list" => $list, "size" => byteFormat($size));
    }
    /**
     * Read backup ZIP SQL file list
     * @author linxinliang<109760455@qq.com>
     * @param $file         string   Need a zip file
     * @param $outDir       string   Output the files directory
     * @return array
     */ 
    private function unzip($file, $outDir = DatabaseBackDir) {
        $zip = new ZipArchive();
        if ($zip->open(DatabaseBackDir . "Zip/" . $file) !== TRUE)
            return FALSE;
        $zip->extractTo($outDir);
        $zip->close();
        return TRUE;
    }
	/**
   	 * 读取要导入的sql文件列表并排序后插入SESSION  Function
   	 * @author linxinliang<109760455@qq.com>
   	 */
    static private function getRestoreFiles() {
        $_SESSION['cacheRestore']['time'] = time();
        if (empty($_GET['sqlPre']))
            die(json_encode(array("status" => 0, "info" => "错误的请求")));
        $sqlPre = $_GET['sqlPre'];
        $handle = opendir(DatabaseBackDir);
        $sqlFiles = array();
        while ($file = readdir($handle)) {
		/** 获取以$sqlPre为前缀的所有sql文件  **/
            if (preg_match('#\.sql$#i', $file) && preg_match('#' . $sqlPre . '#i', $file))
                $sqlFiles[] = $file;
        }
        closedir($handle);
        if (count($sqlFiles) == 0)
            die(json_encode(array("status" => 0, "info" => "错误的请求，不存在对应的SQL文件")));
        /** 将要还原的sql文件按顺序组成数组，防止先导入不带表结构的sql文件 **/
        $files = array();
        foreach ($sqlFiles as $sqlFile) {
            $k = str_replace(".sql", "", str_replace($sqlPre . "_", "", $sqlFile));
            $files[$k] = $sqlFile;
        }
        unset($sqlFiles, $sqlPre);
        ksort($files);
        $_SESSION['cacheRestore']['files'] = $files;
        return $files;
    }
	/**
   	 * Function:将待通过邮件发送的sql文件按卷标升序排序并按sql文件大小分组为多个待压缩组
   	 * @author linxinliang<109760455@qq.com>
   	 */
    static private function getSqlFilesGroups() {
        $_SESSION['cacheSendSql']['time'] = time();
        if (!$_POST['sqlFiles'] || count($_POST['sqlFiles']) == 0)
            die(json_encode(array("status" => 0, "info" => "请选择要发送到邮件的sql文件")));

        if (is_email($_POST['to'])) {
            $_SESSION['cacheSendSql']['to'] = $_POST['to'];
        } else {
            die(json_encode(array("status" => 0, "info" => "接受SQL文件的邮件地址格式错误")));
        }
        $sqlFiles = array();
        foreach ($_POST['sqlFiles'] as $sqlFile) {
            $k = explode("_", $sqlFile);
            $k = explode(".", end($k));
            $sqlFiles[$k[0]] = $sqlFile;
        }
        unset($k);
        /** 对数组按key排序  **/
        ksort($sqlFiles);
        $temSize = 0;
        $n = 1;
        /** 计算待发送到邮件的附件大小，并分成多个压缩文件组 **/
        foreach ($sqlFiles as $flie) {
            $path = DatabaseBackDir . $flie;
            if (file_exists($path)) {
            	/** 50M = 50*1024*1024=52428800 **/
                if (filesize($path) > 52428800) {
                    $files[$n][] = $flie;
                    $temSize = 0;
                    $n++;
                } else {
                    $temSize+=filesize($path);
                    if ($temSize < 52428800) {
                        $files[$n][] = $flie;
                    } else {
                        $temSize = 0;
                        $temSize+=filesize($path);
                        $files[$n][] = $flie;
                        $n++;
                    }
                }
            }
        }
        unset($_POST, $sqlFiles, $temSize);
        $_SESSION['cacheSendSql']['count'] = count($files);
        return $_SESSION['cacheSendSql']['files'] = $files;
    }
    /**
   	 * Get All Table Name For BackUp Function
   	 * @author linxinliang<109760455@qq.com>
   	 * @return array
   	 */
    private function getAllTableName() {
        $tabs = M()->query('SHOW TABLE STATUS');
        $arr = array();
        foreach ($tabs as $tab) {
            $arr[] = $tab['Name'];
        }
        unset($tabs);
        return $arr;
    }
}