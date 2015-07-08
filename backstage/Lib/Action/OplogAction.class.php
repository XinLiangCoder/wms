<?php
/**
 * operation log Class
 * @author linxinliang<109760455@qq.com>
 *
 */
class OplogAction extends CommonAction {
	
	/**
	 * Method Doesn't Exist To Do
     * @author linxinliang<109760455@qq.com>
	 */
	public function _empty(){
		$this->error('Unauthorized access');
	}
	
	/**
	 * List View Page Function
	 * @author linxinliang<109760455@qq.com>
	 */
    public function index() {
        $M = D("Oplog");
        $where = $data = array();
		if(!empty($_GET['admin_id'])) {
			$where['admin_id'] = $_GET['admin_id'];
			$data['admin_id'] = $_GET['admin_id'];
		}
		if(!empty($_GET['skey'])) {
			$where['bak'] = array('like','%'.urldecode($_GET['skey']).'%');
			$data['skey'] = urldecode($_GET['skey']);
		}
        $count = $M->where($where)->count('id');
        import("ORG.Util.Page");
        $page = new Page($count, 16,$data);
        $showPage = $page->show();
        $list = $M->where($where)->field('*')->order("`id` DESC")->limit("$page->firstRow, $page->listRows")->select();
        $users = M('admin')->field('id,username')->where('is_deleted=1')->order('id desc')->select();
		$this->assign("par", $data);
        $this->assign("page", $showPage);
        $this->assign("list", $list);
		$this->assign("users", $users);
        $this->display();
    }
}