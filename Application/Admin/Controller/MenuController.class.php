<?php
/**
 * Created for takahashi.
 * User: Qi
 * Date: 2016.7.6
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
use Think\Exception;

class MenuController extends Controller
{
    public function index()
    {
        $data = array();
        if (isset($_REQUEST['type']) && in_array($_REQUEST['type'], array(0, 1))) {
            $data['type'] = intval($_REQUEST['type']);
            $this->assign('type', $data['type']);
        }
        //分页操作
        $page = isset($_REQUEST['p']) ? $_REQUEST['p'] : 1;
        $pageSize = isset($_REQUEST['pageSize']) ? $_REQUEST['pageSize'] : 5;
        $menus = D('Menu')->getMenus($data, $page, $pageSize);
        $menusCount = D('Menu')->getMenuCount($data);
        $res = new \Admin\Vendor\Page($menusCount, $pageSize);
        $pageRes = $res->show();
        $this->assign('pageRes', $pageRes);
        $this->assign('menus', $menus);
        $this->display();
    }
    public function add()
    {
        if ($_POST) {
            if (!isset($_POST['name']) || !$_POST['name']) {
                return show('0', '菜单名不可为空');
            }
            if (!isset($_POST['m']) || !$_POST['m']) {
                return show('0', '模块名不可为空');
            }
            if (!isset($_POST['c']) || !$_POST['c']) {
                return show('0', '控制器不可为空');
            }
            if (!isset($_POST['f']) || !$_POST['f']) {
                return show('0', '方法名不可为空');
            }
            if ($_POST['menu_id']) {
                return $this->save($_POST);
            }
            $menuId = D("Menu")->insert($_POST);
            if ($menuId) {
                return show(1, '添加成功', $menuId);
            }
            return show(0, '添加失败', $menuId);
        } else {
            $this->display();
        }
    }
    public function edit()
    {
        $menuId = $_GET['id'];
        $menu = D("Menu")->find($menuId);
        $this->assign('menu', $menu);
        $this->display();
    }
    public function save($data)
    {
        $menuId = $data['menu_id'];
        unset($data['menu_id']);
        try {
            $id = D('Menu')->updateMenuById($menuId, $data);
            if ($id == false) {
                return show(0, '更新失败');
            }
            return show(1, '更新成功');
        } catch (Exception $e) {
            return show(0, $e->getMessage());
        }
    }
    public function setStatus()
    {
        try {
            if ($_POST) {
                $id = $_POST['id'];
                $status = $_POST['status'];
                $res = D('Menu')->updateStatusById($id, $status);
                if ($res) {
                    return show(1, '操作成功');
                } else {
                    return show(0, '操作失败');
                }
            }
        } catch(Exception $e) {
            return show(0, $e->getMessage());
        }
        return show(0, '未提交数据');
    }
    public function listOrder()
    {
        $listorder = $_POST['listorder'];
        $errors = array();
        $jumpUrl = $_SERVER['HTTP_REFERER'];
        if ($listorder) {
            try {
                foreach($listorder as $menuId => $v) {
                    //执行更新
                    $id = D('Menu') -> updateMenuListOrderById($menuId, $v);
                    if ($id === false) {
                        $errors[] = $menuId;
                    }
                }
            }catch(Exception $e) {
                return show(0, $e ->getMessage(), array('jump_url' => $jumpUrl));
            }
            if ($errors) {
                return show(0, '排序失败-'.implode(',', $errors), array('jump_url' => $jumpUrl));
            }
            return show(1, '排序成功', array('jump_url' => $jumpUrl));
        }
        return show(0, '排序数据失败', array('jump_url' => $jumpUrl));
    }
}