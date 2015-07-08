<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

defined('THINK_PATH') or exit();
/**
 * 系统行为扩展：模板内容输出替换
 * @category   Think
 * @package  Think
 * @subpackage  Behavior
 * @author   liu21st <liu21st@gmail.com>
 */
class ContentReplaceBehavior extends Behavior {
    // 行为参数定义
    protected $options   =  array(
        'TMPL_PARSE_STRING' =>  array(),
    );

    // 行为扩展的执行入口必须是run
    public function run(&$content){
        $content = $this->templateContentReplace($content);
    }

    
    /**
     * 模板内容替换
     * @access protected
     * @param string $content 模板内容
     * @return string
     */
    protected function templateContentReplace($content) {
        // 系统默认的特殊变量替换
        $replace =  array(
            '__ROOT__'      =>  __ROOT__,       // 当前网站地址
            '__APP__'       =>  __APP__,        // 当前应用地址
            '__MODULE__'    =>  __MODULE__,
            '__ACTION__'    =>  __ACTION__,     // 当前操作地址
            '__SELF__'      =>  __SELF__,       // 当前页面地址
            '__CONTROLLER__'=>  __CONTROLLER__,
            '__URL__'       =>  __CONTROLLER__,
            '__PUBLIC__'    =>  __ROOT__.'/Public',// 站点公共目录
        );
        // 允许用户自定义模板的字符串替换
        if(is_array(C('TMPL_PARSE_STRING')) )
            $replace =  array_merge($replace,C('TMPL_PARSE_STRING'));
        $content = str_replace(array_keys($replace),array_values($replace),$content);
        return $content;
    }
    
    /**
     * 模板内容替换
     * @access protected
     * @param string $content 模板内容
     * @return string
     */
    protected function templateContentReplace_bak($content) {
        // 系统默认的特殊变量替换
        $replace =  array(
            '__TMPL__'      =>  APP_TMPL_PATH,  // 项目模板目录
            '__ROOT__'      =>  __ROOT__,       // 当前网站地址
            '__APP__'       =>  __APP__,        // 当前项目地址
            '__GROUP__'     =>  defined('GROUP_NAME')?__GROUP__:__APP__,
            '__ACTION__'    =>  __ACTION__,     // 当前操作地址
            '__SELF__'      =>  __SELF__,       // 当前页面地址
            '__URL__'       =>  __URL__,
            '../Public'     =>  APP_TMPL_PATH.'Public',// 项目公共模板目录
            '__PUBLIC__'    =>  __ROOT__.'/Public',// 站点公共目录
        );
        // 允许用户自定义模板的字符串替换
        if(is_array(C('TMPL_PARSE_STRING')) )
            $replace =  array_merge($replace,C('TMPL_PARSE_STRING'));
		
		unset($replace['__PUBLIC__']);
        $content = str_replace(array_keys($replace),array_values($replace),$content);
		$content = preg_replace_callback("/__PUBLIC__/",create_function('$matches',"return 'http://'.C('CDNSERVERHEADER').rand(C('CDNSERVERSTART'),C('CDNSERVEREND')).C('CDNSERVERFOOTER');"),$content);

		//http://dev.yjr1.ncfstatic.com/js/(widget/lib.v1.js|widget/select/select.v1.js|widget/tab/tab.v1.js|widget/slider/slider.v1.js|widget/share/share.v1.js|widget/dialog/dialog.v1.js|widget/datepicker/datepicker.v1.js|widget/validate/validate.v1.js|index_v1.js|global.js|tab.js|?t=20140213)
		//js 合并
		//js css 重复加载判断
		$allowJs = $allowCss = array();
		preg_match_all("/\(.+js\|\?t=[0-9]+\)/",$content,$matches);
		if(isset($matches['0']) && $matches = $matches['0']){
			$jsDest = C('JS_DEST_DIR_COMBINE');
			$jsSource = C('JS_SOURCE_DIR');
			foreach($matches as $matche){
				$destFileName = md5($matche).'.js';

				$sourceFiles = explode('|',str_replace(array('(',')'),array('',''),$matche));
				$timeStep = array_pop($sourceFiles);
				if(!file_exists($jsSource.$jsDest.$destFileName)){
					$sourceFiles = array_diff($sourceFiles,$allowJs);
					$res = combine($destFileName,$sourceFiles,$jsSource.$jsDest,$jsSource,'js');
					$allowJs = array_merge($allowJs,$sourceFiles);
				}
				$content = str_replace($matche,$jsDest.$destFileName.$timeStep,$content);
			}
		}

		//css 合并
		preg_match_all("/\(.+css\|\?t=[0-9]+\)/",$content,$matches);
		if(isset($matches['0']) && $matches = $matches['0']){
			$cssDest = C('CSS_DEST_DI_COMBINE');
			$cssSource = C('CSS_SOURCE_DIR');
			foreach($matches as $matche){
				$destFileName = md5($matche).'.css';
				$sourceFiles = explode('|',str_replace(array('(',')'),array('',''),$matche));
				$timeStep = array_pop($sourceFiles);
				if(!file_exists($cssSource.$cssDest.$destFileName)){
					$sourceFiles = array_diff($sourceFiles,$allowCss);
					$res = combine($destFileName,$sourceFiles,$cssSource.$cssDest,$cssSource,'css');
					$allowCss = array_merge($allowCss,$sourceFiles);
				}
				$content = str_replace($matche,$cssDest.$destFileName.$timeStep,$content);
			}
		}
        return $content;
    }
}