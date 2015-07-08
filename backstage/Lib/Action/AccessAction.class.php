<?php
/**
 * Access Manage Class
 * @author linxinliang<109760455@qq.com>
 */

class AccessAction extends CommonAction {
	/** TABNAME **/
	var $_tableName = 'access';
	var $_tableNodeName = 'node';
	var $_tableRoleName = 'role';
	/**
   	 * Node List Page View Function
   	 * @author linxinliang<109760455@qq.com>
   	 */
    public function index() {
    	import("Category");
        $cat = new Category('node', array('id', 'pid', 'title', 'fullname'));
        $temp = $cat->getList();               //获取分类结构
        $level = array("1" => "项目（GROUP_NAME）", "2" => "模块(MODEL_NAME)", "3" => "操作（ACTION_NAME）");
        foreach ($temp as $k => $v) {
            $temp[$k]['statusTxt'] = $v['status'] == 1 ? "启用" : "禁用";
            $temp[$k]['chStatusTxt'] = $v['status'] == 0 ? "启用" : "禁用";
            $temp[$k]['level'] = $level[$v['level']];
            $list[$v['id']] = $temp[$k];
        }
        unset($temp);
        $this->assign("list", $list);
        $this->display("nodeList");
    }
    /**
   	 * Role List Page View Function
   	 * @author linxinliang<109760455@qq.com>
   	 */
    public function roleList() {
        $list = M($this->_tableRoleName)->field('id,name,pid,status,remark')->select();
        foreach ($list as $k => $v) {
            $list[$k]['statusTxt'] = $v['status'] == 1 ? "启用" : "禁用";
            $list[$k]['chStatusTxt'] = $v['status'] == 0 ? "启用" : "禁用";
        }
        $this->assign("list", $list);
        $this->display();
    }
    /**
   	 * Role ADD Function
   	 * @author linxinliang<109760455@qq.com>
   	 */
    public function addRole() {
        if (IS_POST) {  
        	$_D = array();
            $_D['name'] = trim($_POST['name']);
            $_D['status'] = intval($_POST['status']);
            $_D['pid'] = intval($_POST['pid']);
            $_D['sort'] = intval($_POST['sort']);
            $_D['remark'] = trim($_POST['remark']);
        	if (M($this->_tableRoleName)->where("`name`='" . $_D['name'] . "'")->count() > 0) {
	            $_JsonArray =  array('status' => 0, 'info' => "已经存在该角色");
	            die(json_encode($_JsonArray));
	        }
	        if(!empty($_D['name']) && !empty($_D['pid'])){
	        	$rs = M($this->_tableRoleName)->add($_D);
	        	if($rs!==false){
	        		$this->Log('添加角色 ID：'.$rs);
	        		echo json_encode(array('status' => 1, 'info' => '添加角色信息成功', 'url' => U("Access/roleList")));
	        	}else{
	        		echo json_encode(array('status' => 0, 'info' => "添加失败，请重试"));
	        	}
	        }else{
	        	echo json_encode(array('status' => 0, 'info' => "角色组名称或父级组ID不能为空"));
	        }
        } else {
            $this->assign("info", $this->getRole());
            $this->display("add_edit_role");
        }
    }
    /**
   	 * Role Edit Function
   	 * @author linxinliang<109760455@qq.com>
   	 */
    public function editRole() {
        if (IS_POST) {  
        	/** Params_Verify **/
	    	$this->Params_Verify($_POST['id'],$_POST['v'],U("Access/roleList"));          
        	$_D = array();
            $_D['name'] = trim($_POST['name']);
            $_D['status'] = intval($_POST['status']);
            $_D['pid'] = intval($_POST['pid']);
            $_D['sort'] = intval($_POST['sort']);
            $_D['remark'] = trim($_POST['remark']);
       		if(M($this->_tableRoleName)->where('id='.(int) $_POST['id'])->save($_D)!==false){
				$this->Log('修改角色 ID：'.$_POST['id']);
       			echo json_encode(array('status' => 1, 'info' => "更新角色信息成功",'url' => "javascript:self.location=document.referrer;"));
			}else{
				echo json_encode(array('status' => 0, 'info' => "更新失败，请重试"));
			}
        } else {
        	/** Params_Verify **/
	    	$this->Params_Verify($_GET['id'],$_GET['v'],U("Access/roleList"));
            $info = M($this->_tableRoleName)->where("id=" . (int) $_GET['id'])->find();
            if (empty($info['id'])) {
                $this->error("不存在该角色", U('Access/roleList'));
            }
            $this->assign("info", $this->getRole($info));
            $this->display("add_edit_role");
        }
    }
	/**
     * Change Node Status
     * @author linxinliang<109760455@qq.com>
     */
    public function opNodeStatus() {
        header('Content-Type:application/json; charset=utf-8');
        /** Params_Verify **/
	    $this->Params_Verify($_GET['id'],$_GET['v'],U("Access/index"));
        $_D['status'] = $_GET["status"] == 1 ? 0 : 1;
        $_MES = $_D['status'] == 1 ? '开启':'禁用';
        if (M($this->_tableNodeName)->where("id=". (int) $_GET['id'])->save($_D)!==false) {
        	$_JsonArray = array('status' => 1, 'info' => $_MES."成功",'data' => array("status" => $datas['status'], "txt" => $datas['status'] == 1 ? "禁用" : "启动"));
			$this->Log($_MES.'节点 ID：'.$_GET['id']);
			echo json_encode($_JsonArray);
        } else {
			$_JsonArray = array('status' => 0, 'info' => $_MES."失败");
        	echo json_encode($_JsonArray);
        }
    }

    /**
     * Change Role Status
     * @author linxinliang<109760455@qq.com>
     */
    public function opRoleStatus() {
    	header('Content-Type:application/json; charset=utf-8');
    	/** Params_Verify **/
	    $this->Params_Verify($_GET['id'],$_GET['v'],U("Access/roleList"));
        $_D['status'] = $_GET["status"] == 1 ? 0 : 1;
        $_MES = $_D['status'] == 1 ? '开启':'禁用';
        if (M($this->_tableRoleName)->where("id=". (int) $_GET['id'])->save($_D)!==false) {
        	$this->Log($_MES.'角色 ID：'.$_GET['id']);
        	echo json_encode($_JsonArray = array('status' => 1, 'info' => $_MES."成功",'data' => array("status" => $datas['status'], "txt" => $datas['status'] == 1 ? "禁用" : "启动")));
        } else {
			echo json_encode(array('status' => 0, 'info' => $_MES."失败"));
        }
    }
	/**
     * Change Node Sort
     * @author linxinliang<109760455@qq.com>
     */
    public function opSort() {
    	header('Content-Type:application/json; charset=utf-8');
    	$_D = array();
        $_D['sort'] = (int) $this->_post("sort");
        if (M($this->_tableNodeName)->where('id='.(int) $_POST['id'])->save($_D)!==false) {
        	$this->Log('修改权限节点排序 ID：'.$_POST['id']);
            echo json_encode(array('status' => 1, 'info' => "处理成功"));
        } else {
            echo json_encode(array('status' => 0, 'info' => "处理失败"));
        }
    }

    /**
   	 * Node Edit Function
   	 * @author linxinliang<109760455@qq.com>
   	 */
    public function editNode() {
        if (IS_POST) {
            header('Content-Type:application/json; charset=utf-8');
            /** Params_Verify **/
	    	$this->Params_Verify($_POST['id'],$_POST['v'],U("Access/index"));  
            $_D = array();
            $_D['name'] = trim($_POST['name']);
			$_D['title'] = trim($_POST['title']);
			$_D['remark'] = trim($_POST['remark']);
			$_D['sort'] = intval($_POST['sort']);
			$_D['pid'] = intval($_POST['pid']);
			$_D['status'] = intval($_POST['status']);
			$_D['level'] = intval($_POST['level']);
       		if(M($this->_tableNodeName)->where('id='.(int) $_POST['id'])->save($_D)!==false){
				$_JsonArray = array('status' => 1, 'info' => "更新节点信息成功",'url' => "javascript:self.location=document.referrer;");
				$this->Log('修改节点 ID：'.$_POST['id']);
				echo json_encode($_JsonArray);
			}else{
				$_JsonArray = array('status' => 0, 'info' => "更新失败，请重试");
				echo json_encode($_JsonArray);
			}
        } else {
        	/** Params_Verify **/
	    	$this->Params_Verify($_GET['id'],$_GET['v'],U("Access/index")); 
            $info = M($this->_tableNodeName)->field('id,name,title,remark,sort,pid,status,level')->where("id=" . (int) $_GET['id'])->find();
        	if (empty($info['id'])) {
                $this->error("不存在该节点", U('Access/nodeList'));
            }
            $this->assign("info", $this->getPid($info));
            $this->display("add_edit_node");
        }
    }

    /**
   	 * Node ADD Function
   	 * @author linxinliang<109760455@qq.com>
   	 */
    public function addNode() {
        if (IS_POST) { 
            header('Content-Type:application/json; charset=utf-8');
            $_D = array();
            $_D['name'] = trim($_POST['name']);
            $_D['title'] = trim($_POST['title']);
            $_D['status'] = intval($_POST['status']);
            $_D['level'] = intval($_POST['level']);
            $_D['pid'] = intval($_POST['pid']);
            $_D['sort'] = intval($_POST['sort']);
            $_D['remark'] = trim($_POST['remark']);
	        if(!empty($_D['name']) && !empty($_D['title'])){
	        	$rs = M($this->_tableNodeName)->add($_D);
	        	if($rs!==false){
	        		$this->Log('添加节点 ID：'.$rs);
	        		$_JsonArray =  array('status' => 1, 'info' => '添加节点信息成功', 'url' => U("Access/index"));
	        		echo json_encode($_JsonArray);
	        	}else{
	        		$_JsonArray = array('status' => 0, 'info' => "添加失败，请重试");
	        		echo json_encode($_JsonArray);
	        	}
	        }else{
	        	$_JsonArray = array('status' => 0, 'info' => "名称或显示名不能为空");
	        	echo json_encode($_JsonArray);
	        }
        } else {
            $this->assign("info", $this->getPid(array('level' => 1)));
            $this->display("add_edit_node");
        }
    }
    /**
     * Change Role Function
     * @author linxinliang<109760455@qq.com>
     */
    public function changeRole() {
        header('Content-Type:application/json; charset=utf-8');
        if (IS_POST) {
        	/** Params_Verify **/
	    	$this->Params_Verify($_POST['id'],$_POST['v'],U("Access/roleList"));
	        $M = M("Access");
	        $role_id = (int) $_POST['id'];
	        $M->where("role_id=" . $role_id)->delete();
	        $data = $_POST['data'];
	        if (count($data) == 0) {
	            return array('status' => 1, 'info' => "清除所有权限成功", 'url' => U("Access/roleList"));
	        }
	        $datas = array();
	        foreach ($data as $k => $v) {
	            $tem = explode(":", $v);
	            $datas[$k]['role_id'] = $role_id;
	            $datas[$k]['node_id'] = $tem[0];
	            $datas[$k]['level'] = $tem[1];
	            $datas[$k]['pid'] = $tem[2];
	        }
	        if ($M->addAll($datas)) {
	        	$this->Log('权限分配给角色ID：'.$_POST['id']);
	            echo json_encode(array('status' => 1, 'info' => "设置成功", 'url' => U("Access/roleList")));
	        } else {
	            echo json_encode(array('status' => 0, 'info' => "设置失败，请重试"));
	        }
        } else {
        	/** Params_Verify **/
	    	$this->Params_Verify($_GET['id'],$_GET['v'],U("Access/roleList"));
            $M = M("Node");
            $info = M($this->_tableRoleName)->where("id=" . (int) $_GET['id'])->find();
            if (empty($info['id'])) {
                $this->error("不存在该用户组", U('Access/roleList'));die;
            }
            $access = M($this->_tableName)->field("CONCAT(`node_id`,':',`level`,':',`pid`) as val")->where("`role_id`=" . $info['id'])->select();
            $info['access'] = count($access) > 0 ? json_encode($access) : json_encode(array());
            $this->assign("info", $info);
            $datas = $M->where("level=1")->select();
            foreach ($datas as $k => $v) {
                $map['level'] = 2;
                $map['pid'] = $v['id'];
                $datas[$k]['data'] = $M->where($map)->select();
                foreach ($datas[$k]['data'] as $k1 => $v1) {
                    $map['level'] = 3;
                    $map['pid'] = $v1['id'];
                    $datas[$k]['data'][$k1]['data'] = $M->where($map)->select();
                }
            }
            $this->assign("nodeList", $datas);
            $this->display();
        }
    }
	/**
     * Delete Node Function 
     * @author linxinliang<109760455@qq.com>
     */
	public function delNode(){
		if(IS_POST){
			 header('Content-Type:application/json; charset=utf-8');
        	/** Params_Verify **/
	    	$this->Params_Verify($_POST['id'],$_POST['v'],U("Access/index"));
			$id = intval($this->_post('id'));
			$info = M($this->_tableNodeName)->where(array('id'=>$id))->find();
			if(empty($info)) {
				die(json_encode(array('msg'=>'该节点不存在')));
			}
			$access = M('access')->field(array('role_id'))->where(array('node_id'=>$id))->select();
			if(empty($access)){
				if(M($this->_tableNodeName)->where(array('id'=>$id))->delete()!==false){
					$this->Log('删除节点 ID：'.$id);
					echo json_encode(array('success'=>true,'msg'=>'操作成功'));
				}else{
					echo json_encode(array('success'=>false,'msg'=>'操作失败'));
				}
			}else{
				$s = '';
				foreach($access as $v) $s .= ' ' . $v['role_id'];
				echo json_encode(array('msg'=>'角色ID：'.$s.' 对该节点拥有权限，请取消后再删除'));
			}
		}
	}
	/**
     * Get Role Function
     * @author linxinliang<109760455@qq.com>
     */
    private function getRole($info = array()) {
        import("Category");
        $cat = new Category('role', array('id', 'pid', 'name', 'fullname'));
        $list = $cat->getList();               //获取分类结构
        foreach ($list as $k => $v) {
            $disabled = $v['id'] == $info['id'] ? ' disabled="disabled"' : "";
            $selected = $v['id'] == $info['pid'] ? ' selected="selected"' : "";
            $info['pidOption'].='<option value="' . $v['id'] . '"' . $selected . $disabled . '>' . $v['fullname'] . '</option>';
        }
        return $info;
    }
	/**
     * Get PID Function
     * @author linxinliang<109760455@qq.com>
     */
    private function getPid($info) {
        $arr = array("请选择", "项目", "模块", "操作");
        for ($i = 1; $i < 4; $i++) {
            $selected = $info['level'] == $i ? " selected='selected'" : "";
            $info['levelOption'].='<option value="' . $i . '" ' . $selected . '>' . $arr[$i] . '</option>';
        }
        $level = $info['level'] - 1;
        import("Category");
        $cat = new Category('node', array('id', 'pid', 'title', 'fullname'));
        $list = $cat->getList();               //获取分类结构
        $option = $level == 0 ? '<option value="0" level="-1">根节点</option>' : '<option value="0" disabled="disabled">根节点</option>';
        foreach ($list as $k => $v) {
            $disabled = $v['level'] == $level ? "" : ' disabled="disabled"';
            $selected = $v['id'] != $info['pid'] ? "" : ' selected="selected"';
            $option.='<option value="' . $v['id'] . '"' . $disabled . $selected . '  level="' . $v['level'] . '">' . $v['fullname'] . '</option>';
        }
        $info['pidOption'] = $option;
        return $info;
    }
}