<?php
/**
 * Index Class
 * @author linxinliang<109760455@qq.com>
 *
 */
class IndexAction extends CommonAction {

	/**
	 * Method Doesn't Exist To Do
     * @author linxinliang<109760455@qq.com>
	 */
	public function _empty(){
		$this->error('Unauthorized access');
	}
	
	/**
     * Index Function Server information
     * @author linxinliang<109760455@qq.com>
     */
	public function index() {
        if (function_exists('gd_info')) {
            $gd = gd_info();
            $gd = $gd['GD Version'];
        } else {
            $gd = "不支持";
        }
        $info = array(
            '操作系统' => PHP_OS,
            '主机名IP端口' => $_SERVER['SERVER_NAME'] . ' (' . $_SERVER['SERVER_ADDR'] . ':' . $_SERVER['SERVER_PORT'] . ')',
            '运行环境' => $_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式' => php_sapi_name(),
            '程序目录' => WEB_ROOT,
            'MYSQL版本' => function_exists("mysql_close") ? mysql_get_client_info() : '不支持',
            'GD库版本' => $gd,
            '上传附件限制' => ini_get('upload_max_filesize'),
            '执行时间限制' => ini_get('max_execution_time') . "秒",
            '剩余空间' => round((@disk_free_space(".") / (1024 * 1024)), 2) . 'M',
            '服务器时间' => date("Y年n月j日 H:i:s"),
            '北京时间' => gmdate("Y年n月j日 H:i:s", time() + 8 * 3600),
            '采集函数检测' => ini_get('allow_url_fopen') ? '支持' : '不支持',
            'register_globals' => get_cfg_var("register_globals") == "1" ? "ON" : "OFF",
            '字符串处理' => (1 === get_magic_quotes_gpc()) ? 'magic_quotes_gpc=yes' : 'magic_quotes_gpc=on',
            'magic_quotes_runtime' => (1 === get_magic_quotes_runtime()) ? 'YES' : 'NO',
        );
        $this->assign('data',M('admin')->where('id='.$_SESSION['my_info']['id'])->find());
        $this->assign('server_info', $info);
        $this->display();
    }

    /**
     * change password Function
     * @author linxinliang<109760455@qq.com>
     */
    public function changepwd() {
        if (IS_POST) {
	        if (md5($_POST['password0']) != $_SESSION['my_info']['password']) {
	           die (json_encode( $_JsonArray =  array('status' => 0, 'info' => "原始密码输入错误")));
	        }
	        if (trim($_POST['password']) == '') {
	            die (json_encode($_JsonArray =  array('status' => 0, 'info' => "新密码不能为空")));
	        }
	        if (trim($_POST['password']) != trim($_POST['password1'])) {
	            die (json_encode($_JsonArray =  array('status' => 0, 'info' => "确认密码和新密码不一致")));
	        }
	        $data['password'] = md5($_POST['password']);
	        if (M("admin")->where('id='.$_SESSION['my_info']['id'])->save($data)!=false) {
	            setcookie("$this->loginMarked", NULL, -3600, "/");
	            unset($_SESSION["$this->loginMarked"], $_COOKIE["$this->loginMarked"]);
	            D("Oplog")->addLog('修改密码 ID：'.$_SESSION['my_info']['id']);
	            die (json_encode($_JsonArray =  array('status' => 1, 'info' => "你的密码已经成功修改，请重新登录",'url'=>U('Public/index'))));
	        } else {
	            die (json_encode($_JsonArray =  array('status' => 0, 'info' => "密码修改失败")));
	        }
        } else {
            $this->display();
        }
    }
}