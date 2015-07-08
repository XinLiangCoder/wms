<?php
/**
 * Common Class
 * @author linxinliang<109760455@qq.com>
 *
 */
class CommonAction extends Action {

    public $loginMarked;

    /**
     * 初始化
     * 如果 继承本类的类自身也需要初始化那么需要在使用本继承类的类里使用parent::_initialize();
     */
    public function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
        header('Content-Type:application/json; charset=utf-8');
		if(!function_exists("msubstr")){
            load("@.Common");
        }
        $systemConfig = include WEB_ROOT . 'Conf/config.php';
        $this->loginMarked = md5($systemConfig['TOKEN']['admin_marked']);
        $this->checkLogin();
        /** The user permission check **/
        if (C('USER_AUTH_ON') && !in_array(MODULE_NAME, explode(',', C('NOT_AUTH_MODULE')))) {
            import('ORG.Util.RBAC');
            if (!RBAC::AccessDecision()) {
                //检查认证识别号
                if (!$_SESSION [C('USER_AUTH_KEY')]) {
                    //跳转到认证网关
                    redirect(C('USER_AUTH_GATEWAY'));
                }
                // 没有权限 抛出错误
                if (C('RBAC_ERROR_PAGE')) {
                    // 定义权限错误页面
                    redirect(C('RBAC_ERROR_PAGE'));
                } else {
                    if (C('GUEST_AUTH_ON')) {
                        $this->assign('jumpUrl', C('USER_AUTH_GATEWAY'));
                    }
                    // 提示错误信息
                    $this->error(L('_VALID_ACCESS_'));
                }
            }
        }
        $this->assign("menu", $this->show_menu());
        $this->assign("sub_menu", $this->show_sub_menu());
        $this->assign("my_info", $_SESSION['my_info']);
        $this->assign("site", $systemConfig);
    }
    /**
     * Check Login Function
     * @author linxinliang<109760455@qq.com>
     */
    public function checkLogin() {
        if (isset($_COOKIE[$this->loginMarked])) {
            $cookie = explode("_", $_COOKIE[$this->loginMarked]);
            $timeout = C("TOKEN");
            if (time() > (end($cookie) + $timeout['admin_timeout'])) {
                setcookie("$this->loginMarked", NULL, -3600, "/");
                unset($_SESSION[$this->loginMarked], $_COOKIE[$this->loginMarked]);
                $this->error("登录超时，请重新登录", U("Public/index"));
            } else {
                if ($cookie[0] == $_SESSION[$this->loginMarked]) {
                    setcookie("$this->loginMarked", $cookie[0] . "_" . time(), 0, "/");
                } else {
                    setcookie("$this->loginMarked", NULL, -3600, "/");
                    unset($_SESSION[$this->loginMarked], $_COOKIE[$this->loginMarked]);
                    $this->error("帐号异常，请重新登录", U("Public/index"));
                }
            }
        } else {
            $this->redirect("Public/index");
        }
        return TRUE;
    }
    /**
     * show_menu  Function  Display the level menu
     * @author linxinliang<109760455@qq.com>
     */
    private function show_menu() {
    	$cache = C('admin_big_menu');
		$authId = $_SESSION[C('USER_AUTH_KEY')];
		$role = M('role_user')->where("user_id=" . $authId)->join(C('DB_PREFIX') . "role ON " . C('DB_PREFIX') . "role_user.role_id = " . C('DB_PREFIX') . "role.id")->find();
		if($authId != 1){			
        	$access = M('access')->where('role_id='.$role['role_id'])->field('node_id')->select();
			foreach ($access as $k=>$v){
				$accesses[] = $v['node_id'];
			}
			if (empty($accesses)){
				$node =array();
			}else{
				$node = M('node')->where('pid=1 and id in ('.implode(",",$accesses).')')->select();
			}
			foreach ($node as $k=>$v){
				$nodes[$v['name']] = $v['name'];
			}
			$nodes['Index'] = 'Index';
			$cache = array_intersect_key($cache,$nodes);
		}
		$count = count($cache);
        $i = 1;
        $menu = "";
        foreach ($cache as $url => $name) {
            if ($i == 1) {
                $css = $url == MODULE_NAME || !$cache[MODULE_NAME] ? "fisrt_current" : "fisrt";
                $menu.='<li class="' . $css . '"><span><a href="' . U($url . '/index') . '">' . $name . '</a></span></li>';
            } else if ($i == $count) {
                $css = $url == MODULE_NAME ? "end_current" : "end";
                $menu.='<li class="' . $css . '"><span><a href="' . U($url . '/index') . '">' . $name . '</a></span></li>';
            } else {
                $css = $url == MODULE_NAME ? "current" : "";
                $menu.='<li class="' . $css . '"><span><a href="' . U($url . '/index') . '">' . $name . '</a></span></li>';
            }
            $i++;
        }
        return $menu;
    }
    /**
     * show_menu  Function  Display the secondary menu
     * @author linxinliang<109760455@qq.com>
     */
    private function show_sub_menu() {
        $big = MODULE_NAME;
        $cache = C('admin_sub_menu');
    	$authId = $_SESSION[C('USER_AUTH_KEY')];
		$role = M('role_user')->where("user_id=" . $authId)->join(C('DB_PREFIX') . "role ON " . C('DB_PREFIX') . "role_user.role_id = " . C('DB_PREFIX') . "role.id")->find();
		if($authId != 1){			
			$access = M('access')->where('role_id='.$role['role_id'])->field('node_id')->select();
			foreach ($access as $k=>$v){
				$accesses[] = $v['node_id'];
			}
			if (empty($accesses)){
				$node =array();
			}else{
				$node = M('node')->where('level=3 and id in ('.implode(",",$accesses).')')->select();
			}
			foreach ($node as $k=>$v){
				$parent = M('node')->where('id='.$v['pid'])->find();
				$nodes[$parent['name']][] = $v['name'];
			}
		}
        $sub_menu = array();
        if ($cache[$big]) {
            $cache = $cache[$big];
            foreach ($cache as $url => $title) {
				$sub_menu[] = array('url' => U("$url"), 'title' => $title);
            }
            return $sub_menu;
        } else {
            return $sub_menu[] = array('url' => '#', 'title' => "该菜单组不存在");
        }
    }
	/**
	 * 
	 * Params_Verify 
	 * @author linxinliang<109760455@qq.com>
	 * @param string $v  this is value
	 * @param string $code this is md5code string
	 * @param string $url this is jump url
	 */
	public function Params_Verify($v='',$code='',$url=''){
    	//截取前五位
    	$md5_str = md5($v.C('PARAMS_AUTH_CODE'));
    	if(substr($md5_str,0,6)!=$code){
    		$this->error("Illegal request", $url);
    		exit();
    	}
    }
    /**
     * 
     * Params_Encry
     * @author linxinliang<109760455@qq.com>
     * @param $v  this is params value
     * @return md5code string
     */
    public function Params_Encry($v=''){
    	$md5_str = md5($v.C('PARAMS_AUTH_CODE'));
    	return substr($md5_str,0,6);
    }
    /**
     * Add Operation Log Function
     * @author linxinliang<109760455@qq.com>
     * @param String $bak this is log message
     */
    public function Log($bak=''){
    	D('Oplog')->addLog($bak);
    }
	/**
     * Grab the remote file
     * @author linxinliang<109760455@qq.com>
     * @param type $url
     * @param type $charset
     * @return type 
     */
    public function Get_Https($url, $charset = "utf-8"){
        if (extension_loaded('curl')) {
            $file_contents = self::_curl_file_get_contents($url);
        } else {
            $file_contents = @file_get_contents($url);
        }
        $charset = strtolower($charset);
        if ($charset == "utf-8") {
            return $file_contents;
        } elseif ($charset == "gb2312") {
            $file_contents = iconv("gb2312", "UTF-8", $file_contents);
            return $file_contents;
        }
    }
	/**
     * CURL get contents
     * @author linxinliang<109760455@qq.com>
     * @param type $url
     * @param type $timeout
     * @return type
     */
    private function _curl_file_get_contents($url, $timeout = 50){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }
    /**
     * Check URL
     * @author linxinliang<109760455@qq.com>
     * @param type $url String
     * @return bool
     */
    public function Is_Url($url=''){
	    if(!preg_match('/http[s]?:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is',$url)){
	        return false;
	    }
	    return true;
	}
	/**
     * Upload_File
     * @author linxinliang<109760455@qq.com>
     * @param type $path String
     * @return String  IMG NAME
     */
	public function Upload_File($path='',$ext=''){
	    import("ORG.Net.UploadFile");
		mkdir($path);
	    $Upload = new UploadFile();
	    $Upload->maxSize = 3145728*20;
	    $Upload->allowExts = array('jpg','gif','png','jpeg','apk');
	    $Upload->savePath = $path.'/';
	    $Upload->saveRule = 'uniqid';
		if($Upload->upload()){
		   	$_info=$Upload->getUploadFileInfo();
		   	return $_info[0]['savename'];
		}else{
        	$_JsonArray =  array('status' => 0, 'info' => $Upload->getErrorMsg());
	        die(json_encode($_JsonArray));
		}
  	}
	/**
     * Common Del Function
     * @author linxinliang<109760455@qq.com>
     */
    public function del(){
    	if(IS_POST){
    		header('Content-Type:application/json; charset=utf-8');
            $_D_Count = count($_POST['ids']);
            $_Idstr = implode(",",$_POST['ids']);
    		for($i=0;$i<$_D_Count;$i++){
    			$_U_D['is_deleted'] = '2';
    			M($this->_tableName)->where('id='.$_POST['ids'][$i])->save($_U_D);
            }
            $this->Log('批量删除'.htmlspecialchars($_POST['msg']).'ID：'.$_Idstr);
            die(json_encode(array('status' => 1, 'info' => "操作成功",'url' => "javascript:self.location=location.href;")));
    	}
    }
    /**
     * Common updateStatus Function
     * @author linxinliang<109760455@qq.com>
     */
    public function updateStatus($log=''){
    	if(empty($log)){
    		die (json_encode(array('status'=>0,'msg'=>'非法访问')));
    	}
    	/** Params_Verify **/
	    $this->Params_Verify($_GET['id'],$_GET['v'],U(MODULE_NAME.'/index'));
   		$S_D['status'] = intval($_GET['s']);
	    $_MSG = ($S_D['status'] == '1') ? '启用' : '禁用' ;
	    if(M($this->_tableName)->where("id=".$_GET['id'])->save($S_D)!==false){
	    	$this->Log($_MSG.$log.' ID：'.$_GET['id']);
	    	die (json_encode(array('status'=>1,'msg'=>$_MSG.'成功！')));
	    }else{
	    	die (json_encode(array('status'=>0,'msg'=>$_MSG.'失败！')));
	    }
    }
}