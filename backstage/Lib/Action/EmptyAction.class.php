<?php
/**
 * Action Doesn't Exist To Do
 * @author linxinliang<109760455@qq.com>
 */
class EmptyAction extends Action { 
    function _empty(){
        $this->error('Unauthorized access');
    }
}
