<?php
/**
 * Action Doesn't Exist To Do
 */
class EmptyAction extends BaseAction{
    function _empty(){
        header("HTTP/1.0 404 Not Found");
        C("LAYOUT_ON",false);
        $this->assign('title', '404 Not Found');
        $this->display("Public:404");
    }
}
