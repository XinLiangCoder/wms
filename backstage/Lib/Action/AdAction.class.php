<?php
/**
 * Ad Manage Class
 * @author linxinliang<109760455@qq.com>
 */

class AdAction extends CommonAction {
	/** TABNAME **/
	var $_tableName = 'ad';
	
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
    	$where = $s_data = array();
    	if(!empty($_GET['s_type'])){
	    	switch($_GET['s_type']){
				case '1':
				  $field = 'name';
				  break;  
				case '2':
				  $field = 'mark';
				  break;
			}
			$s_data['s_type'] = $_GET['s_type'];
    	}
    	if(!empty($_GET['s_key'])&&!empty($field)){
    		if($_GET['s_type']=='2'){
    			$where[$field] = array('like','%'.urldecode(strtoupper($_GET['s_key'])).'%');
    		}else{
    			$where[$field] = array('like','%'.urldecode($_GET['s_key']).'%');
    		}
    		$s_data['s_key'] = $_GET['s_key'];
    	}
		if(!empty($_GET['s_platform'])){
			$where['platform'] = $_GET['s_platform'];
			$s_data['s_platform'] = $_GET['s_platform'];
		}
		if(!empty($_GET['s_status'])){
			$where['status'] = $_GET['s_status'];
			$s_data['s_status'] = $_GET['s_status'];
		}
    	if(!empty($_GET['s_time_limit'])){
	    	$where['time_limit'] = $_GET['s_time_limit'];
			$s_data['s_time_limit'] = $_GET['s_time_limit'];
    	}
    	if(!empty($_GET['s_time_type'])){
	    	switch($_GET['s_time_type']){
				case '1':
				  $timeField = 'create_time';
				  break;  
				case '2':
				  $timeField = 'update_time';
				  break;
			}
			$s_data['s_time_type'] = $_GET['s_time_type'];	
    	}
		if(!empty($_GET['s_start_time']) && !empty($_GET['s_end_time'])){
			$_s = strtotime($_GET['s_start_time']."00:00:00");
			$_e = strtotime($_GET['s_end_time']."23:59:59");
			$where[$timeField] = array('between',"{$_s},{$_e}");
			$s_data['s_start_time'] = $_GET['s_start_time'];
			$s_data['s_end_time'] = $_GET['s_end_time'];
		}else{
			if(!empty($_GET['s_start_time'])){
	    		$where[$timeField] = array('egt',strtotime($_GET['s_start_time']."00:00:00"));
	    		$s_data['s_start_time'] = $_GET['s_start_time'];
	    	}
	    	if(!empty($_GET['s_end_time'])){
	    		$where[$timeField] = array('elt',strtotime($_GET['s_end_time']."23:59:59"));
	    		$s_data['s_end_time'] = $_GET['s_end_time'];
	    	}
		}
    	$sort = 'id DESC';
    	$where['is_deleted'] = '1';
    	$FIELDS = 'id,name,mark,platform,time_limit,status,create_time,update_time';
    	$M = M($this->_tableName);
    	$count = $M->where($where)->count('id');
    	import("ORG.Util.Page");
        $page = new Page($count, 16,$s_data);
        $showPage = $page->show();
        $_list = $M->where($where)->field($FIELDS)->order($sort)->limit("$page->firstRow, $page->listRows")->select();
        $this->assign("page", $showPage);
        $this->assign("list", $_list);
        $this->assign("s",$s_data);
        /** 设备平台类型 **/
        $this->assign('ad_fltype',C('PLATFORM_TYPE'));
        $this->display();
    }
    /**
     * Ad Add Function
     * @author linxinliang<109760455@qq.com>
     */
    public function add(){
	    if (IS_POST) {            
            header('Content-Type:application/json; charset=utf-8');
            $_D = array();
        	$_D['name'] = trim($_POST['name']);
        	$_D['mark'] = strtoupper(trim($_POST['mark']));
        	$_D['time_limit'] = intval($_POST['time_limit']);
        	$_D['platform'] = intval($_POST['platform']);
        	$_D['ad_type'] = intval($_POST['ad_type']);
        	$_D['status'] = intval($_POST['status']);
			switch($_D['ad_type']){
				case 1:
				  $_D['normal_content'] = htmlspecialchars($_POST['code']);
				  break;
				case 2:
				  $_D['text_content'] = $_POST['text_content'];
				  if(!$this->Is_Url($_POST['text_link']) && !empty($_POST['text_link'])){
            		die(json_encode(array('status' => 0, 'info' => "文字链接地址不正确")));
            	  }
				  $_D['text_link'] = !empty($_POST['text_link']) ? $_POST['text_link'] : 'javascript:void(0)' ;
				  $_D['text_color'] = !empty($_POST['text_color']) ? $_POST['text_color'] : 'black';
				  $_D['text_size'] = !empty($_POST['text_size']) ? $_POST['text_size'] : '12px' ;
				  $_CONTENT_2 = '<a target="_blank" style="font-size:'.$_D['text_size'].';color:'.$_D['text_color'].';" href="'.$_D['text_link'].'">'.$_D['text_content'].'</a>';
				  $_D['normal_content'] = htmlspecialchars($_CONTENT_2);
				  break;
				case 3:
				  $imgs = $_POST['imageList'];
				  $target = $_POST['target'];
				  $url = $_POST['url'];
				  $title = $_POST['title'];
				  $count = count($imgs);
				  for($i=0;$i<$count;$i++){
				  	if(!$this->Is_Url($url[$i]) && !empty($url[$i])){
            			die(json_encode(array('status' => 0, 'info' => "Url地址不正确")));
            		}else{
            			$_Arr[$i] = array('IMG'=>$imgs[$i],'TAR'=>$target[$i],'URL'=>$url[$i],'TITLE'=>$title[$i]);
            		}
				  }
				  $_D['normal_content'] = json_encode($_Arr);
				  break;
			}
        	if($_D['time_limit']=='2'){
        		$_D['start_time'] = !strstr($_POST['start_time'],":") ? strtotime($_POST['start_time'].'00:00:00') : strtotime($_POST['start_time']) ;
        		$_D['end_time'] = !strstr($_POST['end_time'],":") ? strtotime($_POST['end_time'].'23:59:59') : strtotime($_POST['end_time']) ;
        		if($_D['end_time']<$_D['start_time']){
        			die(json_encode(array('status' => 0, 'info' => "结束时间不能小于开始时间")));
        		}
        		$_D['overdue_content'] = htmlspecialchars($_POST['overdue_content']);
        	}
        	$_D['is_deleted'] = '1';
        	$_D['create_time'] = time();
		    if (M($this->_tableName)->where("`mark`='" . $_D['mark'] . "'")->count() > 0) {
	            $_JsonArray =  array('status' => 0, 'info' => "已经存在该广告位标识");
	            die(json_encode($_JsonArray));
	        }
	        if(!empty($_D['name']) && !empty($_D['mark']) && !empty($_D['platform'])){
		        $rs= M($this->_tableName)->add($_D);
	        	if($rs!==false){
	        		$this->Log('添加广告位 ID：'.$rs);
	        		echo json_encode(array('status' => 1, 'info' => '添加广告位成功', 'url' => U("Ad/index")));
	        	}else{
	        		echo json_encode(array('status' => 0, 'info' => "添加广告位失败，请重试"));
	        	}
	        }else{
	        	echo json_encode(array('status' => 0, 'info' => "名称或标识或平台信息不完整"));
	        }
        }else{
        	/** 设备平台类型 **/
        	$this->assign('ad_fltype',C('PLATFORM_TYPE'));
            $this->display();
        }
    }
    /**
     * Ad Edit Function
     * @author linxinliang<109760455@qq.com>
     */
    public function edit(){
	    if (IS_POST) {         
	    	header('Content-Type:application/json; charset=utf-8');
	    	/** Params_Verify **/
	    	$this->Params_Verify($_POST['id'],$_POST['v'],U("Ad/index"));   
	        $_D = array();
        	$_D['name'] = trim($_POST['name']);
        	$_D['mark'] = strtoupper(trim($_POST['mark']));
        	$_D['platform'] = intval($_POST['platform']);
        	$_D['time_limit'] = intval($_POST['time_limit']);
        	$_D['ad_type'] = intval($_POST['ad_type']);
        	$_D['status'] = intval($_POST['status']);
			switch($_D['ad_type']){
				case 1:
				  $_D['normal_content'] = htmlspecialchars($_POST['code']);
				  break;
				case 2:
				  if(!empty($_POST['text_data'])){
				  	 $_D['normal_content'] = htmlspecialchars($_POST['text_data']);
				  }else{
				  	 $_D['text_content'] = $_POST['text_content'];
					 if(!$this->Is_Url($_POST['text_link']) && !empty($_POST['text_link'])){
	            		die(json_encode(array('status' => 0, 'info' => "文字链接地址不正确")));
	            	 }
					 $_D['text_link'] = !empty($_POST['text_link']) ? $_POST['text_link'] : 'javascript:void(0)' ;
					 $_D['text_color'] = !empty($_POST['text_color']) ? $_POST['text_color'] : 'black';
					 $_D['text_size'] = !empty($_POST['text_size']) ? $_POST['text_size'] : '12px' ;
					 $_CONTENT_2 = '<a target="_blank" style="font-size:'.$_D['text_size'].';color:'.$_D['text_color'].';" href="'.$_D['text_link'].'">'.$_D['text_content'].'</a>';
					 $_D['normal_content'] = htmlspecialchars($_CONTENT_2);
				  }
				  break;
				case 3:
				  $imgs = $_POST['imageList'];
				  $target = $_POST['target'];
				  $url = $_POST['url'];
				  $title = $_POST['title'];
				  $count = count($imgs);
				  for($i=0;$i<$count;$i++){
				  	if(!$this->Is_Url($url[$i]) && !empty($url[$i])){
            			die(json_encode(array('status' => 0, 'info' => "Url地址不正确")));
            		}else{
            			$_Arr[$i] = array('IMG'=>$imgs[$i],'TAR'=>$target[$i],'URL'=>$url[$i],'TITLE'=>$title[$i]);
            		}
				  }
				  $_D['normal_content'] = json_encode($_Arr);
				  break;
			}
        	if($_D['time_limit']=='2'){
        		$_D['start_time'] = !strstr($_POST['start_time'],":") ? strtotime($_POST['start_time'].'00:00:00') : strtotime($_POST['start_time']) ;
        		$_D['end_time'] = !strstr($_POST['end_time'],":") ? strtotime($_POST['end_time'].'23:59:59') : strtotime($_POST['end_time']) ;
        		if($_D['end_time']<$_D['start_time']){
        			die(json_encode(array('status' => 0, 'info' => "结束时间不能小于开始时间")));
        		}
        		$_D['overdue_content'] = htmlspecialchars($_POST['overdue_content']);
        	}
        	$_D['update_time'] = time();
			if(M($this->_tableName)->where('id='.$_POST['id'])->save($_D)!==false){
				$_JsonArray = array('status' => 1, 'info' => "更新成功",'url' => "javascript:self.location=document.referrer;");
				$this->Log('修改广告位 ID：'.$_POST['id']);
				echo json_encode($_JsonArray);
			}else{
				$_JsonArray = array('status' => 0, 'info' => "更新失败，请重试");
				echo json_encode($_JsonArray);
			}
	    }else{
	    	/** Params_Verify **/
	    	$this->Params_Verify($_GET['id'],$_GET['v'],U("Ad/index"));
	        $id = (int) $_GET['id'];
	        $info = M($this->_tableName)->where('id='.$_GET['id'])->find();
	        if($info['ad_type']=='3'){
	        	$_data = json_decode($info['normal_content'],true);
	        }
	        $this->assign("data",$_data);
	        $this->assign("info",$info);
	        /** 设备平台类型 **/
        	$this->assign('ad_fltype',C('PLATFORM_TYPE'));
	        $this->display();
	    }
    }
    /**
     * Ad Details Function
     * @author linxinliang<109760455@qq.com>
     */    
    public function details(){
    	/** Params_Verify **/
	    $this->Params_Verify($_GET['id'],$_GET['v'],U("Ad/index"));
    	$info = M($this->_tableName)->where("id=".$_GET['id'])->find();
    	if($info['ad_type']=='3'){
        	$_data = json_decode($info['normal_content'],true);
        }else{
        	$_data = '';
        }
        $info['normal_content'] = htmlspecialchars_decode($info['normal_content']);
        $info['overdue_content'] = htmlspecialchars_decode($info['overdue_content']);
        $this->assign("data",$_data);
    	$this->assign("info", $info);
    	/** 设备平台类型 **/
        $this->assign('ad_fltype',C('PLATFORM_TYPE'));
	    $this->display();
    }
	/**
     * Change Status
     * @author linxinliang<109760455@qq.com>
     */
    public function status(){
    	$this->updateStatus('广告');
    }
}