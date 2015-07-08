<?php
	/**
	 +----------------------------------------------------------
	 * 字符串截取，支持中文和其他编码
	 +----------------------------------------------------------
	 * @static
	 * @access public
	 +----------------------------------------------------------
	 * @param string $str 需要转换的字符串
	 * @param string $start 开始位置
	 * @param string $length 截取长度
	 * @param string $charset 编码格式
	 * @param string $suffix 截断显示字符
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true)
	{
	    if(function_exists("mb_substr")){
	    if($suffix)
		  return mb_substr($str, $start, $length, $charset)."...";
	      else
	      return mb_substr($str, $start, $length, $charset);
		   }
	    elseif(function_exists('iconv_substr')) {
		    if($suffix)
	       return iconv_substr($str,$start,$length,$charset)."...";
	       else
	       return iconv_substr($str,$start,$length,$charset);
	    }
	    $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
	    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
	    $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
	    $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
	    preg_match_all($re[$charset], $str, $match);
	    $slice = join("",array_slice($match[0], $start, $length));
	    if($suffix) return $slice."…";
	    return $slice;
	}
	/**
     * 
     * Params_Encry
     * @author linxinliang<109760455@qq.com>
     * @param $v  this is params value
     * @return md5code string
     */
    function Params_Encry($v=''){
    	$md5_str = md5($v.C('PARAMS_AUTH_CODE'));
    	return substr($md5_str,0,6);
    }
    /**
     * GetNewsCateName Function 
     * @author linxinliang<109760455@qq.com>
     * @param $id String
     * @return CateName String
     */
    function GetNewsCateName($id=''){
    	$I = M('news_category')->field('name')->where('id='.$id)->find();
    	return $I['name'];
    }
    /**
     * GetUserLoginName Function 
     * @author linxinliang<109760455@qq.com>
     * @param $uid String
     * @return LoginName String
     */
    function GetUserLoginName($uid=''){
    	$I = M('user')->field('username')->where('id='.$uid)->find();
    	return $I['username'];
    }
    /**
     * GetAdminLoginName Function 
     * @author linxinliang<109760455@qq.com>
     * @param $uid String
     * @return LoginName String
     */
    function GetAdminLoginName($uid=''){
    	$I = M('admin')->field('username')->where('id='.$uid)->find();
    	return $I['username'];
    }
    /**
     * SetDefaultValue Function 
     * @author linxinliang<109760455@qq.com>
     * @param $v String
     * @return $v or -- String
     */
    function SetDefaultValue($v=''){
    	return !empty($v) ? $v : '--' ;
    }
    /**
     * NumFormatter Function 
     * @author linxinliang<109760455@qq.com>
     * @param $num decimal
     * @return String
     */
    function NumFormatter($num=''){
    	return number_format($num);
    }
    /**
     * GetProductName Function 
     * @author linxinliang<109760455@qq.com>
     * @param $id int
     * @return ProductName String
     */
    function GetProductName($id=''){
    	$I = M('product')->field('title')->where('id='.$id)->find();
    	return $I['title'];
    }
    function GetIco($filename=''){
    	$range = array("css","exe","flash","gif","html","ico","jpg","js","mp3","php","rm","txt","zip","wmv","xml");
    	$_ext = substr(strrchr($filename,'.'),1);
    	$ext = strtolower($_ext);
    	if(!empty($ext) && in_array($ext,$range)){
    		return "__PUBLIC__/Img/smallico/".$ext.".gif";
    	}elseif(!empty($ext)){
    		return "__PUBLIC__/Img/smallico/aq.gif";
    	}else{
    		return "__PUBLIC__/Img/smallico/dir.gif";
    	}
    	
    }