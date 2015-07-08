<?php
/**
 * AdminUser Manage Class
 * @author linxinliang<109760455@qq.com>
 */

class AdminAction extends CommonAction {
	/** TABNAME **/
	var $_tableName = 'admin'; 
	
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
    	$FIELDS = 'id,username,status,note,last_login_time,last_login_ip,login_count,create_time,update_time';
    	$where['is_deleted'] = '1';
    	$_list = M($this->_tableName)->field($FIELDS)->where($where)->select();
        foreach ($_list as $k => $v) {
			$role = array();
			if($v['id'] == 1){
				$role['name']='超级管理员';			
			}else{
				$roleuser = M("role_user")->where('user_id='.$v['id'])->find();
				$role = M("role")->where('id='.$roleuser['role_id'])->find();
			}
			$_list[$k]['rolegroup']=$role['name'];
        }
        $this->assign("list", $_list);
        $this->display();
    }
    /**
     * AdminUser Add Function
     * @author linxinliang<109760455@qq.com>
     */
    public function add(){
	    if (IS_POST) {            
            header('Content-Type:application/json; charset=utf-8');
        	$data['username'] = trim($_POST['username']);
        	$data['password'] = md5(trim($_POST['password']));
        	$data['status'] = trim($_POST['status']);
        	$data['note'] = trim($_POST['note']);
        	$data['is_deleted'] = '1';
        	$data['create_time'] = time();
		    if (M($this->_tableName)->where("`username`='" . $data['username'] . "'")->count() > 0) {
	            $_JsonArray =  array('status' => 0, 'info' => "已经存在该账号");
	            die(json_encode($_JsonArray));
	        }
	        if(!empty($data['username']) && !empty($data['password'])){
		        $rs= M($this->_tableName)->add($data);
	        	if($rs!==false){
	        		M("role_user")->add(array('user_id' => $rs, 'role_id' => (int) $_POST['role_id']));
	        		$this->Log('添加管理员 ID：'.$rs);
	        		$_JsonArray =  array('status' => 1, 'info' => '账号已开通，请通知相关人员', 'url' => U("Admin/index"));
	        		echo json_encode($_JsonArray);
	        	}else{
	        		$_JsonArray = array('status' => 0, 'info' => "添加新账号失败，请重试");
	        		echo json_encode($_JsonArray);
	        	}
	        }else{
	        	$_JsonArray = array('status' => 0, 'info' => "用户名或密码信息不完整");
	        	echo json_encode($_JsonArray);
	        }
        }else{
        	$this->assign("info", $this->getRoleListOption(array('role_id' => 0)));
            $this->display('add_edit');
        }
    }
    /**
     * AdminUser Edit Function
     * @author linxinliang<109760455@qq.com>
     */
    public function edit(){
	    if (IS_POST) {         
	    	/** Params_Verify **/
	    	$this->Params_Verify($_POST['id'],$_POST['v'],U("Admin/index"));   
	        header('Content-Type:application/json; charset=utf-8');
	        if (!empty($_POST['password'])) {
	            $data['password'] = md5(trim($_POST['password']));
	        } else {
	            unset($_POST['password']);
	        }
        	$roleStatus = M("role_user")->where("`user_id`=".$_POST['id'])->save(array('role_id' => $_POST['role_id']));
			$data['status'] = $_POST['status'];
			$data['note'] = $_POST['note'];
			$data['update_time'] = time();
			if(M($this->_tableName)->where('id='.$_POST['id'])->save($data)!==false){
				$_JsonArray = array('status' => 1, 'info' => "成功更新",'url' => U("Admin/index"));
				$this->Log('修改管理员 ID：'.$_POST['id']);
				echo json_encode($_JsonArray);
			}else{
				$_JsonArray = array('status' => 0, 'info' => "更新失败，请重试");
				echo json_encode($_JsonArray);
			}
	    }else{
	    	/** Params_Verify **/
	    	$this->Params_Verify($_GET['id'],$_GET['v'],U("Admin/index"));
	        $id = (int) $_GET['id'];
	        $FIELDS = C('DB_PREFIX').$this->_tableName.".id,username,status,note,role_id,user_id";
	        $info = M($this->_tableName)->where(C('DB_PREFIX').$this->_tableName.".id=" . $id)->field($FIELDS)->join(C('DB_PREFIX') . "role_user ON " . C('DB_PREFIX') . "admin.id = " . C('DB_PREFIX') . "role_user.user_id")->find();
	        if (empty($info['id'])) {
	            $this->error("不存在该管理员ID", U('Admin/index'));
	            exit;
	        }
	        if ($info['username'] == C('ADMIN_AUTH_KEY')) {
	            $this->error("超级管理员信息不允许操作", U("Admin/index"));
	            exit;
	        }
	        $this->assign("info", $this->getRoleListOption($info));
	        $this->display('add_edit');
	    }
    }
    /**
     * AdminUser Details Function
     * @author linxinliang<109760455@qq.com>
     */    
    public function details(){
    	/** Params_Verify **/
	    $this->Params_Verify($_GET['id'],$_GET['v'],U("Admin/index"));
    	$info = M($this->_tableName)->where("id=".$_GET['id'])->find();
    	$this->assign("info", $info);
	    $this->display();
    }
	/**
     * Change Status
     * @author linxinliang<109760455@qq.com>
     */
    public function status(){
    	$this->updateStatus('管理员');
    }
    /**
     * Get RoleListOption Function
     * @author linxinliang<109760455@qq.com>
     */
	private function getRoleListOption($info = array()) {
        import("Category");
        $cat = new Category('role', array('id', 'pid', 'name', 'fullname'));
        $list = $cat->getList();               //获取分类结构
        $info['roleOption'] = "";
        foreach ($list as $v) {
            $disabled = $v['id'] == 1 ? ' disabled="disabled"' : "";
            $selected = $v['id'] == $info['role_id'] ? ' selected="selected"' : "";
            $info['roleOption'].='<option value="' . $v['id'] . '"' . $selected . $disabled . '>' . $v['fullname'] . '</option>';
        }
        return $info;
    }
}