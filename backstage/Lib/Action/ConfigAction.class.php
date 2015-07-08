<?php
/**
 * WebConfig Manage Class
 * @author linxinliang<109760455@qq.com>
 */

class ConfigAction extends CommonAction {
	/** TABNAME **/
	var $_tableName = 'config'; 
	
	/**
	 * Method Doesn't Exist To Do
     * @author linxinliang<109760455@qq.com>
	 */
	public function _empty(){
		$this->error('Unauthorized access');
	}
	
   	/**
   	 * List Config View Function
   	 * @author linxinliang<109760455@qq.com>
   	 */
    public function index() {
    	if(IS_POST){
    		header('Content-Type:application/json; charset=utf-8');
            $_D_Count = count($_POST['id']);
            for($i=0;$i<$_D_Count;$i++){
            	/** Params_Verify **/
	    		$this->Params_Verify($_POST['id'][$i],$_POST['v'][$i],U("Config/index"));
            	$_Value = M($this->_tableName)->field('field_value,field_name,type,atta_type')->where('id='.$_POST['id'][$i])->find();
	    		if($_Value['field_value']!=$_POST['field_value'][$i] || $_Value['type']!=$_POST['type'][$i] || $_Value['atta_type']!=$_POST['atta_type'][$i]){
	    			$_U_D['field_value'] = $_POST['field_value'][$i];
	    			$_U_D['type'] = $_POST['type'][$i];
	    			$_U_D['atta_type'] = $_POST['atta_type'][$i];
	    			$_U_D['update_time'] = time();
	    			M($this->_tableName)->where('id='.$_POST['id'][$i])->save($_U_D);
	    			$this->Log('编辑网站参数：'.$_Value['field_name']);
	    		}
            }
            $_JsonArray =  array('status' => 1, 'info' => "保存成功",'url' => "javascript:self.location=location.href;");
	        die(json_encode($_JsonArray));
    	}else{
    		if(!empty($_GET['type'])){
    			/** Params_Verify **/
	    		//$this->Params_Verify($_GET['type'],$_GET['v'],U("Config/index"));
    		}
    		$_TYPE = intval($_GET['type']) ? intval($_GET['type'])  : 1 ;
    		$FIELDS = 'id,field_name,field_value,field_desc,type,atta_type';
    		$_list = M($this->_tableName)->field($FIELDS)->where('type='.$_TYPE)->order('id desc')->select();
    		$this->assign('list',$_list);
    		$this->assign('type',$_TYPE);
    		/** 配置项 **/
        	$this->assign('item',C('CONFIG_ITEM'));
        	$this->display();
    	}
    }
    /**
     * WebConfig Add Function
     * @author linxinliang<109760455@qq.com>
     */
    public function add(){
	    if (IS_POST) {            
            header('Content-Type:application/json; charset=utf-8');
            $_D['type'] = intval($_POST['type']);
            $_D['field_name'] = trim($_POST['field_name']);
            $_D['field_value'] = trim($_POST['field_value']);
            $_D['field_desc'] = trim($_POST['field_desc']);
            if(M($this->_tableName)->where("`field_name`='" . $_D['field_name'] . "'")->count() > 0){
            	$_JsonArray =  array('status' => 0, 'info' => "已存在该字段名称");
	            die(json_encode($_JsonArray));
            }
            if(!empty($_D['field_name']) && !empty($_D['field_value']) && !empty($_D['field_desc'])){
            	$_D['create_time'] = time();
            	$rs= M($this->_tableName)->add($_D);
            	if($rs!==false){
	        		$this->Log('添加网站参数：'.$_D['field_name']);
	        		$_JsonArray =  array('status' => 1, 'info' => '添加成功', 'url' => U("Config/index/",array('type'=>$_D['type'],'v'=>$this->Params_Encry($_D['type']))));
	        		echo json_encode($_JsonArray);
	        	}else{
	        		$_JsonArray = array('status' => 0, 'info' => "添加失败，请重试");
	        		echo json_encode($_JsonArray);
	        	}
            }else{
            	$_JsonArray = array('status' => 0, 'info' => "输入信息不完整");
	        	echo json_encode($_JsonArray);
            }
        }else{
        	/** 配置项 **/
        	$this->assign('item',C('CONFIG_ITEM'));
            $this->display();
        }
    }
}