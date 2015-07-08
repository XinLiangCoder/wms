<?php
/**
 * Public Class
 * @author linxinliang<109760455@qq.com>
 *
 */
class PublicAction extends Action {

    public $loginMarked;
    
    public function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
        header('Content-Type:application/json; charset=utf-8');
        $loginMarked = C("TOKEN");
        $this->loginMarked = md5($loginMarked['admin_marked']);
    }
    
	/**
	 * Method Doesn't Exist To Do
     * @author linxinliang<109760455@qq.com>
	 */
	public function _empty(){
		$this->error('Unauthorized access');
	}
    
    /**
     * Login Function
     * @author linxinliang<109760455@qq.com>
     */
    public function index() {
        if (IS_POST) {
	        if ($_SESSION['verify'] != md5($_POST['verify_code'])) {
	            die(json_encode(array('status' => 0, 'info' => "验证码错误啦，再输入吧")));
	        }
	        $where['is_deleted'] = '1';
	        $where['username'] = $_POST['username'];
	        if (M('admin')->where($where)->count() >= 1) {
	        	$info = M('admin')->where("`username`='" . $_POST['username'] . "'")->find();
		        if ($info['status'] == 2) {
		        	$_JsonArray = array('status' => 0, 'info' => "你的账号被禁用，有疑问联系管理员吧");
	                die(json_encode($_JsonArray));
	            }
	            if ($info['password'] == md5($_POST['password'])) {
	                $shell = $info['id'] . md5($info['password'] . C('AUTH_CODE'));
	                //set session
	                $_SESSION[$this->loginMarked] = $shell;
	                //set cookie
	                setcookie($this->loginMarked, $shell."_" .time(), 0, "/");
	                //set sessionInfo
	                $_ROLE = M("role_user")->where("user_id=" . $info['id'])->join(C('DB_PREFIX') . "role ON " . C('DB_PREFIX') . "role_user.role_id = " . C('DB_PREFIX') . "role.id")->find();
	                $info['role_id']= $_ROLE['role_id'];
					$info['role_name']= $_ROLE['name'];
	            	$_SESSION['my_info'] = $info;
	            	$map = array();
	                $map['username'] = $this->_post('username');
	                import('ORG.Util.RBAC');
	                $authInfo = RBAC::authenticate($map,'admin');
	                $_SESSION[C('USER_AUTH_KEY')] = $authInfo['id'];
	                $_SESSION['username'] = $authInfo['username'];
	                if ($authInfo['username'] == C('ADMIN_AUTH_KEY')) {
	                    $_SESSION[C('ADMIN_AUTH_KEY')] = true;
	                }
	                // 缓存访问权限
	                RBAC::saveAccessList();
	            	//set log
					D('Oplog')->addLog('登录');
					$login_data['last_login_time'] = time();
					$login_data['last_login_ip'] = $this->getClientIP();
					$login_data['login_count'] = $info['login_count'] + 1;
					M('admin')->where('id='.$info['id'])->save($login_data);
	            	$_JsonArray = array('status' => 1,'info' => "登录成功",'url' => U("Index/index"));
	                echo json_encode($_JsonArray);
	            }else{
	            	$_JsonArray = array('status' => 0, 'info' => "账号或密码错误");
	                echo json_encode($_JsonArray);
	            }
	        }else{
            	$_JsonArray = array('status' => 0, 'info' => "不存在账号为：" . $_POST['username'] . '的管理员账号！');
            	echo json_encode($_JsonArray);
	        }
        } else {
            if (isset($_COOKIE[$this->loginMarked])) {
                $this->redirect("Index/index");
            }
            $systemConfig = include WEB_ROOT . 'Conf/config.php';
            $this->assign("site", $systemConfig);
            $this->display("Common:login");
        }
    }
	/**
	 * LoginOut Function 
	 * @author linxinliang<109760455@qq.com>
	 */
    public function loginout() {
		D('Oplog')->addLog('退出');
        setcookie("$this->loginMarked", NULL, -3600, "/");
        unset($_SESSION[$this->loginMarked],$_COOKIE[$this->loginMarked]);
    	if (isset($_SESSION[C('USER_AUTH_KEY')])) {
            unset($_SESSION[C('USER_AUTH_KEY')]);
            unset($_SESSION);
            session_destroy();
        }
        $this->redirect("Index/index");
    }
    /**
	 * Show verifycode  Function 
	 * @author linxinliang<109760455@qq.com>
	 */
 	public function verifycode() {
        $w = isset($_GET['w']) ? (int) $_GET['w'] : 50;
        $h = isset($_GET['h']) ? (int) $_GET['h'] : 30;
        import("ORG.Util.Image");
        Image::buildImageVerify(4, 1, 'png', $w, $h);
    }
    /**
     * Get the client IP Function
     * @author linxinliang<109760455@qq.com>
     */
    public function getClientIP(){
    	if (getenv("HTTP_CLIENT_IP"))
			$ip = getenv("HTTP_CLIENT_IP");
		else if(getenv("HTTP_X_FORWARDED_FOR"))
			$ip = getenv("HTTP_X_FORWARDED_FOR");
		else if(getenv("REMOTE_ADDR"))
			$ip = getenv("REMOTE_ADDR");
		else $ip = "Unknow";
		
		return $ip;
    }
     /**
     * Flash Upload_File Function
     * @author linxinliang<109760455@qq.com>
     */
	public function Upload_File(){
		switch ($_GET['t']){
			case 'ad_imgs':
			  $_t = "ad_imgs";
			  $path = C('UPLOAD_PATH.AD');
			  $allowExts = array('jpg','gif','png','jpeg');
			  $saveRule = 'uniqid';
			  $uploadReplace = false;
			  break;  
			case 'config_atta':
			  $_t = "config_atta";
			  $path = C('UPLOAD_PATH.Config_Atta');
			  $uploadReplace = true;
			  $allowExts = array('jpg','gif','png','jpeg','docx','pdf','apk','xlsx');
			  $saveRule = '';
			  break;
			case 'template':
			  $_t = "template";
			  $path = Tpl_ROOT.str_replace("=","/",$_GET['p']);
			  $uploadReplace = true;
			  //$allowExts = array('jpg','gif','png','jpeg','docx','pdf','apk','xlsx');
			  $saveRule = '';
			  break;
		}
		/** 针对36 环境 因为有两层 firsthouse-static**/
		if(strstr($path,'firsthouse-back/')){
			$path = str_replace('firsthouse-back/','firsthouse-static/',$path);
		}
		mkdir($path);
	    import("ORG.Net.UploadFile");
	    $Upload = new UploadFile();
	    $Upload->maxSize = (20*1048576);
	    $Upload->allowExts = $allowExts;
	    $Upload->savePath = $path.'/';
	    $Upload->saveRule = $saveRule;
	    $Upload->uploadReplace = $uploadReplace;
		if($Upload->upload()){
			$info = $Upload->getUploadFileInfo();
			if(empty($_GET['id'])){
				$void = array('err'=>0,'url'=>$info[0]['savename']);
			}else{
				$void = array('err'=>0,'url'=>$info[0]['savename'],'id'=>intval($_GET['id']));
			}
		}else{
			$void = array('err'=>1,'message'=>$Upload->getErrorMsg());
		}
		die(json_encode($void));
  	}
  	/**
     * Del Images Function
     * @author linxinliang<109760455@qq.com>
     */
  	public function Del_Images(){
  		if(IS_POST){
			header('Content-Type:application/json; charset=utf-8');
			$type = $this->_post('type');
			$name = $this->_post('name');
			if($type=="ad"){
				$path = C('UPLOAD_PATH.AD');
			}
	  		/** 针对36 环境 因为有两层 firsthouse-static**/
			if(strstr($path,'firsthouse-back/')){
				$path = str_replace('firsthouse-back/','firsthouse-static/',$path);
			}
			if(!unlink($path.'/'.$name)){
				die(json_encode(array('error'=>'删除失败')));
			}else{
				die(json_encode(array('success'=>'ok')));
			}
		}
  	}
}