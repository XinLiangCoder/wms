<?php
/**
 * Link Manage Class
 * @author linxinliang<109760455@qq.com>
 */

class LinkAction extends CommonAction {
	/** TABNAME **/
	var $_tableName = 'link'; 
	
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
    public function index($partner='') {
    	$where = $s_data  = array();
    	if(!empty($_GET['s_type_id'])){
	    	switch($_GET['s_type_id']){
				case '1':
				  $field = 'title';
				  break;  
				case '2':
				  $field = 'url';
				  break;
			}
			$s_data['s_type_id'] = $_GET['s_type_id'];
    	}
    	if(!empty($_GET['s_key'])&&!empty($field)){
    		$where[$field] = array('like','%'.urldecode($_GET['s_key']).'%');
    		$s_data['s_key'] = $_GET['s_key'];
    	}
    	if(!empty($_GET['s_type'])){
			$where['type'] = $_GET['s_type'];
			$s_data['s_type'] = $_GET['s_type'];
		}
    	if(!empty($_GET['s_status'])){
			$where['status'] = $_GET['s_status'];
			$s_data['s_status'] = $_GET['s_status'];
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
    	$where['is_deleted'] = '1';
    	$M = M($this->_tableName);
    	$count = $M->where($where)->count('id');
    	import("ORG.Util.Page");
        $page = new Page($count, 16,$s_data);
        $showPage = $page->show();
        $FIELDS = 'id,title,type,icon,url,order,status,create_time';
        $_list = $M->where($where)->field($FIELDS)->order($sort)->limit("$page->firstRow, $page->listRows")->select();
        $this->assign("page", $showPage);
        $this->assign("list", $_list);
        $this->assign("s",$s_data);
        $this->display('Link:index');
    }
    /**
     * Link Add Function
     * @author linxinliang<109760455@qq.com>
     */
    public function add(){
	    if (IS_POST) {            
            header('Content-Type:application/json; charset=utf-8');
	    	$_D['url'] = trim($_POST['url']);
            if(!empty($_D['url'])){
            	if(!$this->Is_Url($_D['url'])){
            		$_JsonArray =  array('status' => 0, 'info' => "链接地址不正确");
	            	die(json_encode($_JsonArray));
            	}
            }
        	$_D['title'] = trim($_POST['title']);
        	$_D['type'] = intval($_POST['type']);
        	if(!empty($_FILES['icon']['tmp_name'])){
        		/** FILE NAME **/
        		$_D['icon'] = $this->Upload_File(C('UPLOAD_PATH.Link'));
        	}
        	$_D['order'] = intval($_POST['order']) ? intval($_POST['order']) : 0 ;
        	$_D['status'] = intval($_POST['status']);
        	$_D['is_deleted'] = '1';
        	$_D['create_time'] = time();
		    if (M($this->_tableName)->where("`title`='" . $_D['title'] . "'")->count() > 0) {
	            $_JsonArray =  array('status' => 0, 'info' => "已经存在该链接");
	            die(json_encode($_JsonArray));
	        }
	        if(!empty($_D['title']) && !empty($_D['url']) && !empty($_D['type'])){
		        $_RS= M($this->_tableName)->add($_D);
	        	if($_RS!==false){
	        		$this->Log('添加链接 ID：'.$_RS);
	        		$_JsonArray =  array('status' => 1, 'info' => '添加链接成功', 'url' => U("Link/index"));
	        		echo json_encode($_JsonArray);
	        	}else{
	        		$_JsonArray = array('status' => 0, 'info' => "添加链接失败，请重试");
	        		echo json_encode($_JsonArray);
	        	}
	        }else{
	        	$_JsonArray = array('status' => 0, 'info' => "名称或链接地址或类别信息不完整");
	        	echo json_encode($_JsonArray);
	        }
        }else{
            $this->display('add_edit');
        }
    }
    /**
     * Link Edit Function
     * @author linxinliang<109760455@qq.com>
     */
    public function edit(){
    	/** Params_Verify **/
	    $this->Params_Verify($_REQUEST['id'],$_REQUEST['v'],U("Link/index"));
	    if (IS_POST) {            
	        header('Content-Type:application/json; charset=utf-8');
	        $_D['url'] = trim($_POST['url']);
	    	if(!empty($_D['url'])){
            	if(!$this->Is_Url($_D['url'])){
            		$_JsonArray =  array('status' => 0, 'info' => "链接地址不正确");
	            	die(json_encode($_JsonArray));
            	}
            }
	    	if(!empty($_FILES['icon']['tmp_name'])){
        		/** FILE NAME **/
        		$_D['icon'] = $this->Upload_File(C('UPLOAD_PATH.Link'));
        	}
        	$_D['title'] = trim($_POST['title']);
        	$_D['type'] = intval($_POST['type']);
        	$_D['order'] = intval($_POST['order']) ? intval($_POST['order']) : 0 ;
        	$_D['status'] = intval($_POST['status']);
        	$_D['is_deleted'] = '1';
			$_D['update_time'] = time();
			if(M($this->_tableName)->where('id='.$_POST['id'])->save($_D)!==false){
				$_JsonArray = array('status' => 1, 'info' => "成功更新",'url' => "javascript:self.location=document.referrer;");
				$this->Log('修改链接 ID：'.$_POST['id']);
				echo json_encode($_JsonArray);
			}else{
				$_JsonArray = array('status' => 1, 'info' => "更新失败，请重试");
				echo json_encode($_JsonArray);
			}
	    }else{
	        $FIELDS = 'id,title,type,icon,url,order,status';
	        $info = M($this->_tableName)->field($FIELDS)->where("id=".$_GET['id'])->find();
	        $this->assign("info", $info);
	        $this->display('add_edit');
	    }
    }
	/**
     * Link Details Function
     * @author linxinliang<109760455@qq.com>
     */    
    public function details(){
    	/** Params_Verify **/
	    $this->Params_Verify($_GET['id'],$_GET['v'],U("Link/index"));
    	$info = M($this->_tableName)->where("id=".$_GET['id'])->find();
    	$this->assign("info", $info);
	    $this->display();
    }
	/**
     * Change Status
     * @author linxinliang<109760455@qq.com>
     */
    public function status(){
    	$this->updateStatus('链接');
    }
}