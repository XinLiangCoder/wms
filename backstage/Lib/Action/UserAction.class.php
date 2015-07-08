<?php
/**
 * User Manage Class
 * @author linxinliang<109760455@qq.com>
 */

class UserAction extends CommonAction {
	/** TABNAME **/
	var $_tableName = 'user';
	
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
    	$M = M($this->_tableName);
    	$where = $s_data = array();
    	if(!empty($_GET['s_type'])){
	    	switch($_GET['s_type']){
				case '1':
				  $field = 'nickname';
				  break;  
				case '2':
				  $field = 'username';
				  break;
				case '3':
				  $field = 'mobile';
				  break;
				case '4':
				  $field = 'email';
				  break;
			}	
			$s_data['s_type'] = $_GET['s_type'];
    	}
    	if(!empty($_GET['s_key'])&&!empty($field)){
    		$where[$field] = array('like','%'.urldecode($_GET['s_key']).'%');
    		$s_data['s_key'] = $_GET['s_key'];
    	}
		if(!empty($_GET['s_plat'])){
			$where['reg_type'] = $_GET['s_plat'];
			$s_data['s_plat'] = $_GET['s_plat'];
		}
		if(!empty($_GET['s_status'])){
			$where['status'] = $_GET['s_status'];
			$s_data['s_status'] = $_GET['s_status'];
		}
		if(!empty($_GET['s_auth_mobile'])){
			$where['auth_mobile'] = $_GET['s_auth_mobile'];
			$s_data['s_auth_mobile'] = $_GET['s_auth_mobile'];
		}
    	if(!empty($_GET['s_auth_email'])){
			$where['auth_email'] = $_GET['s_auth_email'];
			$s_data['s_auth_email'] = $_GET['s_auth_email'];
		}
		if(!empty($_GET['s_start_time']) && !empty($_GET['s_end_time'])){
			$_s = strtotime($_GET['s_start_time']."00:00:00");
			$_e = strtotime($_GET['s_end_time']."23:59:59");
			$where['create_time'] = array('between',"{$_s},{$_e}");
			$s_data['s_start_time'] = $_GET['s_start_time'];
			$s_data['s_end_time'] = $_GET['s_end_time'];
		}else{
			if(!empty($_GET['s_start_time'])){
	    		$where['create_time'] = array('egt',strtotime($_GET['s_start_time']."00:00:00"));
	    		$s_data['s_start_time'] = $_GET['s_start_time'];
	    	}
	    	if(!empty($_GET['s_end_time'])){
	    		$where['create_time'] = array('elt',strtotime($_GET['s_end_time']."23:59:59"));
	    		$s_data['s_end_time'] = $_GET['s_end_time'];
	    	}
		}
    	$sort = 'id DESC';
		if(!empty($_GET['s_login_count'])){
			$s_data['s_login_count'] = $_GET['s_login_count'];
			$sort = $_GET['s_login_count']=='1' ? 'login_count ASC' : 'login_count DESC' ;
		}   	
		$where['is_deleted'] = '1';
    	$count = $M->where($where)->count('id');
    	import("ORG.Util.Page");
        $page = new Page($count, 16,$s_data);
        $showPage = $page->show();
        $_list = $M->where($where)->field('*')->order($sort)->limit("$page->firstRow, $page->listRows")->select();
    	$this->assign("page", $showPage);
        $this->assign("list", $_list);
        $this->assign("s",$s_data);
        $this->display();
    }
    /**
     * Change User Login Status
     * @author linxinliang<109760455@qq.com>
     */
    public function status(){
    	$this->updateStatus('用户');
    }
    /**
     * User Details Function
     * @author linxinliang<109760455@qq.com>
     */    
    public function details(){
    	/** Params_Verify **/
	    $this->Params_Verify($_GET['id'],$_GET['v'],U("User/index"));
    	$info = M($this->_tableName)->where("id=".$_GET['id'])->find();
    	$this->assign("info", $info);
	    $this->display();
    }
}