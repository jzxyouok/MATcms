<?php
/**
 * 文章内容model操作
 * Created for takahashi.
 * Author: Qi
 * Date: 2016.7.22
 *                        . .
 *                      '.-:-.`  
 *                      '  :  `
 *                   .-----:     
 *                 .'       `.
 *           ,    /       (o) \   
 *           \`._/          ,__)
 *       ~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *       
 *       Code is far away from bug with the dolphin protecting
 *                       海豚保佑,代码无bug
 */
namespace Common\Model;
use Think\Model;

class NewsModel extends Model
{
    private $_db = '';
    public function __construct()
    {
        $this->_db = M('news');
    }
    public function insert($data = array())
    {
        if (!is_array($data) || !$data) {
            return 0;
        }
        $data['create_time'] = time();
        $data['username'] = getLoginUsername();
        return $this->_db->add($data);
    }
    public function getNews($data, $page, $pageSize = 10)
    {
        $conditions = $data;
        if (isset($data['title']) && $data['title']) {
            $conditions['title'] = array('like','%' . $data['title'] . '%');
        }
        if (isset($data['catid']) && $data['catid']) {
            $conditions['catid'] = array('like','%' . $data['catid'] . '%');
        }
        $conditions['status'] = array('neq', -1);
        $offset = ($page - 1) * $pageSize;
        $list = $this->_db
            ->where($conditions)
            ->order('listorder desc,news_id desc')
            ->limit($offset, $pageSize)
            ->select();
        return $list;
    }
    public function getNewsCount($data = array())
    {
        $data['status'] = array('neq', -1);
        return $this->_db->where($data)->count();
    }
    public function find($id)
    {
        $data = $this->_db->where('news_id=' . $id)->find();
        return $data;
    }
    public function updateById($id, $data)
    {
        unset($id);
        if (!$id || !is_numeric($id)) {
            throw_exception('id不合法');
        }
        if (!$data || !is_array($data)) {
            throw_exception('更新数据不合法');
        }
        return $this->_db->where('news_id=' . $id)->save($data);
    }
    public function updateStatusById($id ,$status)
    {
        if (!is_numeric($status)) {
            throw_exception('status不合法');
        }
        if (!$id || !is_numeric($id)) {
            throw_exception('id不合法');
        }
        $data['status'] = $status;
        return $this->_db->where('news_id=' . $id)->save($data);
    }
    public function updateNewsListorderById($id, $listorder)
    {
        if (!$id || !is_numeric($id)) {
            throw_exception('ID不合法');
        }
        $data = array('listorder' => intval($listorder));
        return $this->_db->where('news_id=' . $id)->save($data);
    }
}