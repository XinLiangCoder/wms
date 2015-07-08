<?php
/**
 * News Manage Class
 * @author linxinliang<109760455@qq.com>
 */

class NewsAction extends CommonAction {
	/** TABNAME **/
	var $_tableName = 'news'; 
	
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
    	/** check parent_category_id**/
    	$parent_category_id = M('news_category')->field('id')->where('parent_id='.$_GET['parent_category_id'])->find();
    	$_GET['category_id'] = (empty($parent_category_id)) ? '0' : $_GET['category_id'] ;
    	if(!empty($_GET['parent_category_id']) && !empty($_GET['category_id'])){
    		$where['category_id'] = $_GET['category_id'];
    		$s_data['parent_category_id'] = $_GET['parent_category_id'];
    		$s_data['category_id'] = $_GET['category_id'];
    	}
    	if(!empty($_GET['parent_category_id']) && empty($_GET['category_id'])){
    		$where['category_id'] = $_GET['parent_category_id'];
    		$s_data['parent_category_id'] = $_GET['parent_category_id'];
    	}
    	if(!empty($_GET['s_title'])){
    		$where['title'] = array('like','%'.urldecode($_GET['s_title']).'%');
    		$s_data['s_title'] = $_GET['s_title'];
    	}
    	if(!empty($_GET['s_status'])){
    		$where['status'] = $_GET['s_status'];
    		$s_data['s_status'] = $_GET['s_status'];
    	}
    	$sort = 'id DESC';
    	$where['is_deleted'] = '1';
    	$M = M($this->_tableName);
    	$count = $M->where($where)->count('id');
    	import("ORG.Util.Page");
        $page = new Page($count, 17,$s_data);
        $showPage = $page->show();
        $FIELDS = 'id,title,status,is_deleted,category_id,is_deleted,publish_time,create_time,update_time';
        $_list = $M->where($where)->field($FIELDS)->order($sort)->limit("$page->firstRow, $page->listRows")->select();
        $this->assign("list", $_list);
        $this->assign("page", $showPage);
        $this->assign("s",$s_data);
        $category = M("news_category")->where('parent_id=0')->select();
        $this->assign("category", $category);
        if(!empty($_GET['parent_category_id']) && !empty($_GET['category_id'])){
        	$_son_list = M("news_category")->field('id,name')->where('parent_id='.$_GET['parent_category_id'])->select();
	        $this->assign("son_list", $_son_list);
        }
        $this->display();
    }
    /**
     * News Add Function
     * @author linxinliang<109760455@qq.com>
     */
    public function add(){
	    if (IS_POST) {            
            header('Content-Type:application/json; charset=utf-8');
            $_D['title'] = trim($_POST['title']);
            $_D['category_id'] = intval($_POST['category_id']) ? intval($_POST['category_id']) : intval($_POST['parent_category_id']) ;
            $_D['content'] = htmlspecialchars($_POST['content']);
            if(!empty($_POST['publish_time'])){
            	$_D['publish_time'] = strtotime(trim($_POST['publish_time']));
            }
			$_D['status'] = intval($_POST['status']);
			$_D['is_deleted'] = '1';
			$_D['create_time'] = time();
		    if (M($this->_tableName)->where("`title`='" . $_D['title'] . "'")->count() > 0) {
	            die(json_encode(array('status' => 0, 'info' => "已经存在此文章")));
	        }
	        if(!empty($_D['title']) && !empty($_D['category_id'])){
		        $_RS= M($this->_tableName)->add($_D);
	        	if($_RS!==false){
	        		$this->Log('添加资讯 ID：'.$_RS);
	        		die(json_encode(array('status' => 1, 'info' => '添加文章成功', 'url' => U("News/index"))));
	        	}else{
	        		die(json_encode(array('status' => 0, 'info' => "添加文章失败，请重试")));
	        	}
	        }else{
	        	die(json_encode(array('status' => 0, 'info' => "信息不完整")));
	        }
        }else{
        	$category = M("news_category")->where('parent_id=0')->select();
            $this->assign("list", $category);
            $this->display('add_edit');
        }
    }
    /**
     * News Edit Function
     * @author linxinliang<109760455@qq.com>
     */
    public function edit(){
	    if (IS_POST) {
	    	/** Params_Verify **/
	    	$this->Params_Verify($_POST['id'],$_POST['v'],U("News/index"));
	        header('Content-Type:application/json; charset=utf-8');
	        $_D['title'] = trim($_POST['title']);
            $_D['category_id'] = intval($_POST['category_id']) ? intval($_POST['category_id']) : intval($_POST['parent_category_id']) ;
            $_D['content'] = htmlspecialchars($_POST['content']);
	    	if(!empty($_POST['publish_time'])){
            	$_D['publish_time'] = strtotime(trim($_POST['publish_time']));
            }
			$_D['status'] = intval($_POST['status']);
			$_D['update_time'] = time();
			if(M($this->_tableName)->where('id='.$_POST['id'])->save($_D)!==false){
				$_JsonArray = array('status' => 1, 'info' => "成功更新",'url' => "javascript:self.location=document.referrer;");
				$this->Log('修改资讯 ID：'.$_POST['id']);
				echo json_encode($_JsonArray);
			}else{
				$_JsonArray = array('status' => 1, 'info' => "更新失败，请重试");
				echo json_encode($_JsonArray);
			}
	    }else{
	    	/** Params_Verify **/
	    	$this->Params_Verify($_GET['id'],$_GET['v'],U("News/index"));
	        $info = M($this->_tableName)->where("id=".$_GET['id'])->find();
	        $category = M("news_category")->where('parent_id=0')->select();
	        /** get parent_category_id **/
	        $parent_category_id = M('news_category')->field('parent_id')->where('id='.$info['category_id'])->find();
	        $info['parent_category_id'] = !empty($parent_category_id['parent_id']) ? $parent_category_id['parent_id']: $info['category_id'] ;
	        if(!empty($parent_category_id['parent_id'])){
	        	$_son_list = M("news_category")->field('id,name')->where('parent_id='.$parent_category_id['parent_id'])->select();
	        	$this->assign("son_list", $_son_list);
	        }
            $this->assign("list", $category);
	        $this->assign("info", $info);
	        $this->display('add_edit');
	    }
    }
    /**
     * News category Function
     * @author linxinliang<109760455@qq.com>
     */
    public function category(){
    	if (IS_POST) {
            echo json_encode(D("News")->category());
        } else {
            $this->assign("list", D("News")->category());
            $this->display();
        }
    }
    /**
     * News category Function
     * @author linxinliang<109760455@qq.com>
     */
	public function getSubCat(){
		$parent_id = intval($_POST['fid']);
		if(!empty($parent_id)){
			$data = M('news_category')->field('name,id')->where('parent_id='.$parent_id)->select();
		}else{
			$data = array();
		}
		$this->ajaxReturn($data,"成功",1);
	}
	/**
     * Change Status
     * @author linxinliang<109760455@qq.com>
     */
    public function status(){
   		$this->updateStatus('资讯');
    }
}