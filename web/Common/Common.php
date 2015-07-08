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
 +----------------------------------------------------------
 * 字符串去除html
 +----------------------------------------------------------
 * @static
 * @access public
 +----------------------------------------------------------
 * @param string $string 需要转换的字符串
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function triptags($string)
{
	$str = strip_tags(trim($string,"\""));
	return $str;
}
function timeAgo($datetime='', $nowtime = 0) {
	$datetime = strtotime($datetime);
	if (empty($nowtime)) {
		$nowtime = time();
	}
	$timediff = $nowtime - $datetime;
	$timediff = $timediff >= 0 ? $timediff : $datetime - $nowtime;
	// 秒
	if ($timediff < 60) {
		return $timediff . '秒前';
	}
	// 分
	if ($timediff < 3600 && $timediff >= 60) {
		return intval($timediff / 60) . '分钟前';
	}
	// 今天
	$today = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
	if ($datetime >= $today) {
		return date('今天 H:i', $datetime);
	}
	// 昨天
	$yestoday = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
	if ($datetime >= $yestoday) {
		return date('昨天 H:i', $datetime);
	}
	// 今年月份
	$this_year = mktime(0, 0, 0, 1, 1, date('Y'));
	if ($datetime >= $this_year) {
		return date('m月d日 H:i', $datetime);
	}
	// 往年
	return date('Y年m月d日', $datetime);
}

//获取真实IP
function get_real_ip() {
	$ip = false;
	if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
		$ip = $_SERVER["HTTP_CLIENT_IP"];
	}
	if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
		if ($ip) {
			array_unshift($ips, $ip);
			$ip = FALSE;
		}
		for ($i = 0; $i < count($ips); $i++) {
			if (!eregi("^(10│172.16│192.168).", $ips[$i])) {
				$ip = $ips[$i];
				break;
			}
		}
	}
	if ($ip) {
		return $ip;
	} else {
		if (isset($_SERVER['REMOTE_ADDR'])) {
			return $_SERVER['REMOTE_ADDR'];
		} else {
			return 'unknown';
		}
	}
}
function getMsg($msg=''){
	$str = '<div class="alert alert-danger alert-dismissible" style="margin-top:10px;" role="alert">
	<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
	<strong>错误：</strong> '.$msg.'
	</div>';
	return $str;
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
