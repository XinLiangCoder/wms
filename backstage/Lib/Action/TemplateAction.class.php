<?php
/**
 * Template Manage Class
 * @author linxinliang<109760455@qq.com>
 */

class TemplateAction extends CommonAction {
	/**
	 * Method Doesn't Exist To Do
     * @author linxinliang<109760455@qq.com>
	 */
	public function _empty(){
		$this->error('Unauthorized access');
	}
	
   	/**
   	 * List Page View Function
   	 * @author linxinliang<109760455@qq.com>
   	 */
    public function index() {
    	$nowdir = isset($_GET['dir']) ? $_GET['dir']:'';
    	$secondDir = isset($_GET['secondDir']) ? $_GET['secondDir']:'';
    	$_root = Tpl_ROOT;
    	if(!empty($nowdir) && !empty($secondDir)){
    		$root = $_root.$nowdir.$secondDir;
    		$nowdir = $nowdir.$secondDir;
    	} 
    	if(!empty($nowdir) && empty($secondDir)){
    		$root = $_root.$nowdir;
    		$nowdir = $nowdir;
    	};
    	if(empty($nowdir) && !empty($secondDir)){
    		$root = $_root.$secondDir;
    		$nowdir = $secondDir;
    	};
    	$root = empty($root) ? $this->formatPath($_root): $this->formatPath($root);
		$Dirs = scandir($root);
		$list = array();
		foreach($Dirs as $v){
			$filename = rtrim($root,'/').'/'.$v;
			if($v<>'.' && $v<>'..'){
				if(is_dir($filename)){
					$_list['filename']= iconv('gbk' , 'utf-8' , $v );
					$_list['type'] = 'dir';
					$_list['filesize'] = $this->byteFormat($this->getFileSize('1',$root.'/'.$v));
					$b = stat($root.'/'.$v);
					$_list['mtime'] = $b['mtime'];
					$_list['is_readable'] = is_readable($root.'/'.$v);
					$_list['is_writeable'] = is_writeable($root.'/'.$v);
					$_list['nowdir'] = empty($nowdir) ? ''  : $nowdir.'=';
				}else{
					$_list['filename']= iconv('gbk' , 'utf-8' , $v );
					$_list['type'] = 'file';
					$_list['ext'] = substr(strrchr($v,'.'),1);
					$b = stat($root.'/'.$v);
					$_list['mtime'] = $b['mtime'];
					$_list['filesize'] = $this->byteFormat($this->getFileSize('2',$root.'/'.$v));
					$_list['is_readable'] = is_readable($root.'/'.$v);
					$_list['is_writeable'] = is_writeable($root.'/'.$v);
					$_list['nowdir'] = empty($nowdir) ? ''  : $nowdir.'=';
				}
				$list[] = $_list;
			}
		}
		/** Now Dir **/
		$_nowdir = empty($nowdir) ? ''  : $this->formatPath($nowdir);
		$this->assign('NowDir',$_nowdir);
		/** Up Dir **/
		$dirArr = explode('/',$_nowdir);
		/** Deletes the last element **/
		array_pop($dirArr);
		$UpDir = implode("=",$dirArr);
		$this->assign('UpDir',$UpDir);
		/** UploadFile Path **/
		$this->assign('path',$nowdir);
		$this->assign('list',$list);
        $this->display();
    }
	/**
     * downFile Function
     * @author linxinliang<109760455@qq.com>
     */
    public function downFile() {
    	$file = $this->formatPath($_GET['filedir']).$this->formatFileName($_GET['filename']);
    	$_filePath = $this->_GetFilePath($file,$_GET['type'],array("html","css","js","jpg","jpeg","gif","png","txt","php","xml"));
        $longPath = $this->formatFileName($_filePath['longPath']);
        /** 中文 no support need to incov GB2312 **/
        if (!file_exists(iconv('UTF-8','GB2312',$longPath))) {
            $this->error("该文件不存在，可能是被删除");
        }
		$this->Log('下载文件名称：'.$this->formatFileName($_filePath['shortPath']));
        $filename = basename($longPath);
        header("Content-type: application/octet-stream");
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header("Content-Length: " . filesize($longPath));
        readfile($longPath);
    }
    /**
     * EditFile Function
     * @author linxinliang<109760455@qq.com>
     */
    public function edit(){
    	if (IS_POST) {            
          	header('Content-Type:application/json; charset=utf-8');
          	/** Params_Verify **/
          	$this->Params_Verify($_POST['filename'],$_POST['filename_v'],U("Template/index"));
          	$this->Params_Verify($_POST['filedir'],$_POST['filedir_v'],U("Template/index"));
          	$this->Params_Verify($_POST['type'],$_POST['type_v'],U("Template/index"));
          	
          	$file = $this->formatPath($_POST['filedir']).$this->formatFileName($_POST['filename']);
	    	$_filePath = $this->_GetFilePath($file,$_POST['type'],array("html","css","js","jpg","jpeg","gif","png","txt","php","xml"));
	        $longPath = $this->formatFileName($_filePath['longPath']);
          	$_D['content'] = trim($_POST['content']);
          	$_FP=fopen($longPath,"w+");
	        if(fwrite($_FP,$_D['content'])){
	        	$this->Log('编辑文件名称：'.$this->formatFileName($_filePath['shortPath']));
	        	die(json_encode(array('status' => 1, 'info' => '编辑成功','url' => "javascript:self.location=document.referrer;")));
	        }else{
	        	die(json_encode(array('status' => 0, 'info' => '编辑失败,请检查权限')));
	        }
	        fclose($_FP);
    	}else{
    		$file = $this->formatPath($_GET['filedir']).$this->formatFileName($_GET['filename']);
	    	$_filePath = $this->_GetFilePath($file,$_GET['type'],array("html","css","js","jpg","jpeg","gif","png","txt","php","xml"));
	        $longPath = $this->formatFileName($_filePath['longPath']);
	    	$info['content'] = $this->readFileContent($this->formatFileName($longPath));
	    	$info['filedir'] = $_GET['filedir'];
	    	$info['filename'] = $_GET['filename'];
	    	$info['type'] = $_GET['type'];
	    	$this->assign("info",$info);
	    	$this->display();
    	}
    }
    /**
     * MakeDir Function
     * @author linxinliang<109760455@qq.com>
     */
    public function mkdir(){
    	if(IS_POST){
    		header('Content-Type:application/json; charset=utf-8');
    		/** Params_Verify **/
          	$this->Params_Verify($_POST['filedir'],$_POST['filedir_v'],U("Template/index"));
          	if(!preg_match("/^[a-zA-Z0-9_]{1,}$/",$_POST['dirname'])){
          		die(json_encode(array("status" => 0, "info" => '只能输入数字、字母、下划线')));
          	}
          	$Path = Tpl_ROOT.$_POST['filedir'].'/'.$_POST['dirname'];
          	if(!file_exists($Path)){
          		if(mkdir($Path)){
          			$this->Log('创建目录名称：'.$this->formatFileName($_POST['filedir'].'/'.$_POST['dirname']));
          			die(json_encode(array("status" => 1, "info" => '创建目录成功')));
          		}else{
          			die(json_encode(array("status" => 0, "info" => '创建失败，请检查权限')));
          		}
          	}else{
          		die(json_encode(array("status" => 0, "info" => '文件夹已经存在')));
          	}
    	}else{
    		$this->display();
    	}
    }
    /**
     * reName Function
     * @author linxinliang<109760455@qq.com>
     */
    public function reName(){
    	if(!empty($_POST['newname']) && !empty($_POST['oldname'])){
    		if(substr(strrchr($_POST['newname'],'.'),1) != substr(strrchr($_POST['oldname'],'.'),1)){
    			die(json_encode(array("status" => '0',"info" => '请输入正确的文件后缀名')));
    		}
    		$oldName = $this->formatPath($_POST['dir']).$this->formatFileName($_POST['oldname']);
    		$newName = $this->formatPath($_POST['dir']).$this->formatFileName($_POST['newname']);
    		/** 中文 no support need to incov GB2312 **/
    		if(!rename(iconv('UTF-8','GB2312',Tpl_ROOT.$oldName),iconv('UTF-8','GB2312',Tpl_ROOT.$newName))){
    			die(json_encode(array("status" => '0',"info" => '重命名失败')));
    		}else{
    			die(json_encode(array("status" => '1',"info" => '重命名成功')));
    			$this->Log($oldName." 重名为：".$newName);
    		}
    	}
    }
	/**
   	 * Del File Function
   	 * @author linxinliang<109760455@qq.com>
   	 */
    public function delFile() {
    	$file = $this->formatPath($_GET['filedir']).$this->formatFileName($_GET['filename']);
    	/** 判断中文目录的时候  windows 下：is_dir(Tpl_ROOT.iconv( 'UTF-8','GB18030',$file ))  **/
    	if(is_dir(Tpl_ROOT.iconv( 'UTF-8','GB18030',$file ))){
    		$handle = opendir(Tpl_ROOT.iconv('UTF-8','GB2312',$file));
    		if ($handle) {
		        while (false !== ( $item = readdir($handle) )) {
		            if ($item != "." && $item != ".."){
			            /** 中文 no support need to incov GB2312 **/
				        if (file_exists(Tpl_ROOT.iconv('UTF-8','GB2312',$file).'/'.iconv('UTF-8','GB2312',$item))) {
				            die(json_encode(array("status" => 0, "info" => "只能删除空目录")));
				        }
		            }
		        }
		        closedir($handle);
		        if(rmdir(Tpl_ROOT.iconv('UTF-8','GB2312',$file))){
		        	die(json_encode(array("status" => 1, "info" => "删除成功")));
		        }else{
		        	die(json_encode(array("status" => 0, "info" => "删除失败")));
		        }
    		}
    	}
    	$_filePath = $this->_GetFilePath($file,$_GET['type'],array("html","css","js","jpg","jpeg","gif","png","txt","php","xml"));
        if(delDirAndFile(iconv('UTF-8','GB2312',$_filePath['longPath']))){
        	$this->Log('删除文件:'.$_filePath['shortPath']);
        	die(json_encode(array("status" => 1, "info" => "删除成功")));
        }else{
        	die(json_encode(array("status" => 0, "info" => "删除失败")));
        }
    }
    /**
   	 * GetFilePath Function
   	 * @author linxinliang<109760455@qq.com>
   	 * @filename This Is File Name
   	 * @type This Is File Type
   	 * @range This Is Array demo:array("html","css")
   	 * @return String
   	 */
    private function _GetFilePath($filename='',$type='',$range=''){
    	if (empty($filename) || empty($type) || !in_array($_GET['type'], $range)) {
            $this->error("地址不存在");
       	}
       	$path = Tpl_ROOT;
//    	$path = array(
//       		"html" => Tpl_ROOT,
//    		"css" => Tpl_ROOT,
//    		"png" => Tpl_ROOT,
//    		
//       	);
       	//$filePath = $path[$type].$filename;
       	$filePath = $path.$filename;
        return array(
        	"longPath" => $this->formatPath($filePath),
        	"shortPath" => $this->formatPath($filename),
        );
    }
    /**
     * Get File Size Function
     * @author linxinliang<109760455@qq.com>
     * @type 1 = Dir 2 = File
     * @name FileName OR DirName
     */
	private function getFileSize($type='1',$name=''){
		$size = 0;
		if($type=='1'){
			$dirlist = opendir($name);
			$size = 0;
			while (false !==  ($folderorfile = readdir($dirlist))){ 
				if($folderorfile != "." && $folderorfile != ".."){ 
					if (is_dir("$dir/$folderorfile")){ 
						$size += self::getFileSize("$name/$folderorfile"); 
					}else{ 
						$size += filesize("$name/$folderorfile"); 
					}
				}    
			}
			closedir($dirlist);
		}elseif($type=='2'){
			$size = filesize($name); 
		} 
		return $size;
	}
	/**
     * byte_format Function
     * @author linxinliang<109760455@qq.com>
     */
	private function byteFormat($size, $dec=2){
		$a = array("B", "KB", "MB", "GB", "TB", "PB");
		$pos = 0;
		while ($size >= 1024) {
			 $size /= 1024;
			   $pos++;
		}
		return round($size,$dec)." ".$a[$pos];
	}
	/**
	 * formatPath Function 
	 * @param String $path
	 * @author linxinliang<109760455@qq.com>
	 */
	private function formatPath($path=''){
		return str_replace("=","/",$path);
	}
	/**
	 * formatFileName
	 * @param String CodeString
	 * @desc For 中文 Filename
	 * @author linxinliang<109760455@qq.com>
	 * @return string
	 */
	private function formatFileName($filename='') {
		$filename = urldecode($filename);
		return str_replace("+"," ",$filename);
	}
	/**
	 * Read File(html,js,content) Content
	 * @param unknown_type $filename
	 */
	private function readFileContent($filename){
		/** Get Ext **/
		$ext = substr(strrchr($filename,'.'),1);
		if(!in_array($ext,array("html","js","css"))){
			return '';
		}else{
			$fp=fopen(iconv('UTF-8','GB2312',$filename),"r"); 
			$data=""; 
			while(!feof($fp)){ 
				$data.=fread($fp,4096); 
			} 
			fclose($fp); 
			return $data; 
		}
	} 
}