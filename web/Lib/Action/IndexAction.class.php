<?php
/**
 * Index Page Class
 */
class IndexAction extends BaseAction {
	public function __construct(){
		parent::__construct();
	} 
    public function index(){
    	$this->display();
    }
	/**
	 * Method Doesn't Exist To Do
	 */
	public function _empty(){
		header("HTTP/1.0 404 Not Found");
        C("LAYOUT_ON",false);
        $this->assign('title', '404 Not Found');
        $this->display("Public:404");
	}
} 