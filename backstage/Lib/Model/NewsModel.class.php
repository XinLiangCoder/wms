<?php
/**
 * News Manage Model
 * @author linxinliang<109760455@qq.com>
 */
class NewsModel extends Model {
	/**
     * News category Function
     * @author linxinliang<109760455@qq.com>
     */
    public function category() {
        if (IS_POST) {
            $act = $_POST['act'];
            $data = $_POST['data'];
            $data['name'] = addslashes(trim($data['name']));
            $data['order'] = intval($data['order']);
            $M = M("news_category");
            /** ADD **/
            if ($act == "add") {
				if(empty($data['name']))  return array('status' => 0, 'info' => '分类名字不能为空');
                unset($data['id']);
				$_WHERE_D['name'] = $data['name'];
                if ($M->where($_WHERE_D)->count() == 0) {
                	$data['is_deleted'] = '1';
                	$data['create_time'] = time();
					$_RS = $M->add($data);
					/** LOG **/
					if($_RS) D('Oplog')->addLog('添加资讯分类 ID:'.$_RS);
                    return ($_RS) ? array('status' => 1, 'info' => '分类 ' . $data['name'] . ' 已经成功添加到系统中', 'url' => U('News/category', array('time' => time()))) : array('status' => 0, 'info' => '分类 ' . $data['name'] . ' 添加失败');
                } else {
                    return array('status' => 0, 'info' => '系统中已经存在分类：' . $data['name']);
                }
            /** EDIT **/
            } else if ($act == "edit") {
                if (empty($data['name'])) {
                    unset($data['name']);
					return array('status' => 0, 'info' => '分类名字不能为空');
                }
                if ($data['parent_id'] == $data['id']) {
                    unset($data['parent_id']);
                }
                $data['update_time'] = time();
                $_RS = $M->save($data);
                /** LOG **/
                if($_RS) D('Oplog')->addLog('修改资讯分类 ID:'.$data['id']);
                return $_RS ? array('status' => 1, 'info' => '分类 ' . $data['name'] . ' 已经成功更新', 'url' => U('News/category', array('time' => time()))) : array('status' => 0, 'info' => '分类 ' . $data['name'] . ' 更新失败');
           	/** DEL **/
            } else if ($act == "del") {
                unset($data['parent_id'], $data['name']);
                /** LOG **/
                $_RS = $M->where($data)->delete();
                if($_RS) D('Oplog')->addLog('删除资讯分类 ID:'.$data['id']);
                return $_RS ? array('status' => 1, 'info' => '分类 ' . $data['name'] . ' 已经成功删除', 'url' => U('News/category', array('time' => time()))) : array('status' => 0, 'info' => '分类 ' . $data['name'] . ' 删除失败');
            }
        } else {
            import("Category");
            $cat = new Category('news_category', array('id', 'parent_id', 'name', 'fullname'));
            return $cat->getList();
        }
    }
}

?>
