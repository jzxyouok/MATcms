<?php
/**
 * @ desc: 推荐位管理
 * @ project: takahashi
 * @ Author: Qi
 * @ Date: 2016-07-23 19:42:26
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
namespace Admin\Controller;
use Think\Controller;

class PositionController extends Controller
{
    public function index()
    {
        $page = $_REQUEST['p'] ? $_REQUEST['p'] : 1;
        $pageSize = 5;
        $position = D('Position')->getPosition($conds, $page, $pageSize);
        $count = D('Position')->getPositionCount();
        //dump($count);die;
        $res = new \Think\Page($count, $pageSize);
        $pageres = $res->show();
        $this->assign(array(
            'pageres'     => $pageres,
            'position'    => $position,
            'conds'       => $conds,
            ));
        $this->display();
    }
    public function setStatus()
    {
        try {
            if ($_POST) {
                $id = $_POST['id'];
                $status = $_POST['status'];
                if (!$id) {
                    return show(0, 'id不存在');
                }
                $res = D('Position')->updateStatusById($id, $status);
                if ($res) {
                    return show(1, '操作成功');
                } else {
                    return show(0, '操作失败');
                }
            }
            return show(0, '未提交内容');
        } catch(Expection $e) {
            return show(0, $e->getMessage());
        }
    }
}