<?php
class BaseAction extends Action {
    function __construct() {
        if(!function_exists("msubstr")){
            load("@.Common");
        }
		$this->assign('device',$this->check_device());
		if($_SESSION['loginstatus']=='1'){
			$this->assign('username',$_SESSION['username']);
		}
		if($this->is_weixin()){
			$this->assign('weixin','1');
		}
    }
	/**
	 * Function Check Computer Or Mobile To Jump
	 */
	public function check_device(){
    	if(preg_match("/(windows 98|windows me|windows 2000|windows xp|windows nt|ubuntu|macintosh|baiduspider|googlebot|ipad)/i", strtolower($_SERVER['HTTP_USER_AGENT']))){
		    /** Computer access **/
		    return '1';
		}else{ 
			/** Mobile access **/
			return '2';
		}
    }
    public function is_weixin(){ 
		if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
				return true;
		}	
		return false;
	}
	/**
	 *
	 * Params_Verify
	 * @param string $v  this is value
	 * @param string $code this is md5code string
	 */
	public function Params_Verify($v='',$code=''){
		//截取前五位
		$md5_str = md5($v.C('PARAMS_AUTH_CODE'));
		if(substr($md5_str,0,6)!=$code){
			return getMsg('Illegal request');
		}
	}
	/**
	 * CommonVerify Function
	 */
	public function V(){
		import("@.ORG.CommonVerify");
		$V = new CommonVerify();
		return $V;
	}
}