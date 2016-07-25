<?php
/**
 * 文章管理
 * Created for takahashi.
 * Author: Qi
 * Date: 2016.7.21
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

class ContentController extends Controller
{
    public function index()
    {
        $conds = array();
        if ($_GET['title']) {
            $conds['title'] = $_GET['title'];
        }
        if ($_GET['catid']) {
            $conds['catid'] = intval($_GET['catid']);
        }
        $page = $_REQUEST['p'] ? $_REQUEST['p'] : 1;
        $pageSize = 5;
        $news = D('News')->getNews($conds, $page, $pageSize);
        $count = D('News')->getNewsCount($conds);
        $res = new \Admin\Vendor\Page($count, $pageSize);
        $pageres = $res->show();
        $positions = D('Position')->getNormalPositions();
        $this->assign(array(
            'webSiteMenu' => D('Menu')->getBarMenus(),
            'pageres'     => $pageres,
            'news'        => $news,
            'conds'       => $conds,
            'positions'   => $positions,
            ));
        $this->display();
    }
    public function add()
    {
        if ($_POST) {
            if (!isset($_POST['title']) || !$_POST['title']) {
                return show(0, '标题不存在');
            }
            if (!isset($_POST['small_title']) || !$_POST['small_title']) {
                return show(0, '短标题不存在');
            }
            if (!isset($_POST['catid']) || !$_POST['catid']) {
                return show(0, '文章栏目不存在');
            }
            if (!isset($_POST['keywords']) || !$_POST['keywords']) {
                return show(0, '关键字不存在');
            }
            if (!isset($_POST['content']) || !$_POST['content']) {
                return show(0, 'content不存在');
            }
            if ($_POST['news_id']) {
                $this->save($_POST);
            }
            $newId = D('News')->insert($_POST);
            if ($newId) {
                $newsContentData['content'] = $_POST['content'];
                $newsContentData['news_id'] = $newId;
                $cId = D('NewsContent')->insert($newsContentData);
                if ($cId) {
                    return show(1, '新增成功');
                } else {
                    return show(1, '主表插入成功，附表插入失败');
                }
            } else {
                return show(0, '新增失败');
            }
        } else {
            $webSiteMenu = D('Menu')->getBarMenus();
            $titleFontColor = C('TITLE_FONT_COLOR');
            $copyFrom = C('COPY_FROM');
            $this->assign(array(
                'webSiteMenu' => $webSiteMenu,
                'titleFontColor' => $titleFontColor,
                'copyFrom' => $copyFrom,
            ));
            $this->display();
        }
    }
    public function edit()
    {
        $newId = $_GET['id'];
        if (!$newId) {
            $this->redirect(U('index'));
        }
        $news = D('News')->find($newId);
        if (!$news) {
            $this->redirect(U('index'));
        }
        $newsContent = D('NewsContent')->find($newId);
        if ($newsContent) {
            $news['content'] = $newsContent['content'];
        }
        $webSiteMenu = D('Menu')->getBarMenus();
        $this->assign(array(
            'webSiteMenu'    => $webSiteMenu,
            'titleFontColor' => C("TITLE_FONT_COLOR"),
            'copyFrom'       => C("COPY_FROM"),
            'news'           => $news,
            ));
        $this->display();
    }
    public function save($data)
    {
        $newsId = $data['news_id'];
        unset($data['news_id']);
        try {
            $id = D('News')->updateById($newsId, $data);
            $newsContentData['content'] = $data['content'];
            $conId = D('NewsContent')->updateNewsById($newsId, $newsContentData);
            if ($id === false ||$conId === false) {
                return show(0, '更新失败');
            } else {
                return show(1, '更新成功');
            }
        } catch(\Expection $e) {
            return show(0, $e->getMessage());
        }

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
                $res = D('News')->updateStatusById($id, $status);
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
    public function listorder()
    {
        $listorder = $_POST['listorder'];
        $jump_url = $_SERVER['HTTP_REFERER'];
        $errers = array();
        //dump($_POST);die;
        try {
            if ($listorder) {
               foreach ($listorder as $newsId => $v) {
                   $id = D('News')->updateNewsListorderById($newsId, $v);
                   if ($id === false) {
                        $errers[] = $newId;
                   }
               }
               if ($errers) {
                   return show(0, '排序失败-' . implode(',', $errers), array('jump_url' => $jump_url));
               }
               return show(1, '排序成功', array('jump_url' => $jump_url));
            }
        } catch(Expection $e) {
            return show(0, $e->getMessage());
        }
        return show(0, '排序数据失败', array('jump_url' => $jump_url));
    }
}