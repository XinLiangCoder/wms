<?php
/**
 * operation log Class
 * @author linxinliang<109760455@qq.com>
 *
 */
class OplogModel extends Model {
	/**
	 *  Add Operation Log Function
	 * @author linxinliang<109760455@qq.com>
	 */
	public function addLog($bak) {
		$data['model']=MODULE_NAME;
		$data['action']=ACTION_NAME;
		$data['admin_id']=$_SESSION['my_info']['id'];
		$data['admin_name']=$_SESSION['my_info']['username'];
		$data['role_id']=$_SESSION['my_info']['role_id'];
		$data['role_name']=$_SESSION['my_info']['role_name'];
		$data['bak']=$bak;
		$data['create_time']=time();
        D('oplog')->add($data);
    }
}