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
 * Memcache缓存驱动
 * @category   Extend
 * @package  Extend
 * @subpackage  Driver.Cache
 * @author    liu21st <liu21st@gmail.com>
 */
class CacheMemcached extends Cache {

    /**
     * 架构函数
     * @param array $options 缓存参数
     * @access public
     */
    function __construct($options=array()) {
        if ( !extension_loaded('memcached') ) {
            throw_exception(L('_NOT_SUPPERT_').':memcached');
        }
        if(empty($options)) {
            $options = array (
                'host'			=>	C('MEMCACHE_HOST') ? C('MEMCACHE_HOST') : '127.0.0.1',
                'port'			=>	C('MEMCACHE_PORT') ? C('MEMCACHE_PORT') : 11211,
                'weight'		=>	C('MEMCACHE_WEIGHT') ? C('MEMCACHE_WEIGHT') : 0,
                'timeout'		=>	C('DATA_CACHE_TIMEOUT') ? C('DATA_CACHE_TIMEOUT') : false,
                'compress'	=>	C('DATA_CACHE_COMPRESS') ? C('DATA_CACHE_COMPRESS') : false,
                'persistent'	=>	false,
            );
        }
        $this->options				=	$options;
        $this->options['expire']	=	isset($options['expire'])?  $options['expire']  :   C('DATA_CACHE_TIME');
        $this->options['prefix']	=	isset($options['prefix'])?  $options['prefix']  :   C('DATA_CACHE_PREFIX');        
        $this->options['length']	=	isset($options['length'])?  $options['length']  :   0;        
        $func               =   $options['persistent'] ? 'pconnect' : 'connect';
		
		$key = $this->options['host'].$this->options['port'];
        $this->handler      =   new Memcached($key);
		
		if(!count($this->handler->getServerList())) {
			//This code block will only execute if we are setting up a new EG(persistent_list) entry
			$this->handler->setOption(Memcached::OPT_RECV_TIMEOUT, $this->options['timeout']);
			$this->handler->setOption(Memcached::OPT_SEND_TIMEOUT, $this->options['timeout']);
			$this->handler->setOption(Memcached::OPT_COMPRESSION , $this->options['compress']);
			$this->handler->setOption(Memcached::OPT_TCP_NODELAY, true);
			$this->handler->setOption(Memcached::OPT_PREFIX_KEY, $this->options['prefix']);
			$this->connected = $this->handler->addServer($this->options['host'], $this->options['port']);
		}
    }

    /**
     * 是否连接
     * @access private
     * @return boolen
     */
    private function isConnected() {
        return $this->connected;
    }

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @return mixed
     */
    public function get($name) {
        N('cache_read',1);
        return $this->handler->get($name);
    }

    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     * @param integer $expire  有效时间（秒）
     * @return boolen
     */
    public function set($name, $value, $expire = null) {
        N('cache_write',1);
        if(is_null($expire)) {
            $expire  =  time() + $this->options['expire'];
        }else if($expire > 0){
            $expire  =  time() + $expire;
		}
        if($this->handler->set($name, $value, $expire)) {
            if($this->options['length']>0) {
                // 记录缓存队列
                $this->queue($name);
            }
            return true;
        }
        return false;
    }

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolen
     */
    public function rm($name, $ttl = false) {
        return $ttl === false ?
            $this->handler->delete($name) :
            $this->handler->delete($name, $ttl);
    }

    /**
     * 清除缓存
     * @access public
     * @return boolen
     */
    public function clear() {
        return $this->handler->flush();
    }
}