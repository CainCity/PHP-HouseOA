<?php
namespace app\base\controller;

use app\base\model\Menu as MenuModel;
use app\index\controller\Auth as Auth;
use think\Db;

class Menu extends Auth
{
    // region 主界面
    public function index()
    {
        if (request()->isGet()) {
            return $this->fetch('base@Menu/index');
        }

        if (request()->isPost()) {
            $postData = input('post.');
            $output = array();
            $returnData = array();
            $count = 0;

            if (!empty($postData)) {
                $data = json_decode($postData['aoData'], true);
                $key = array('iDisplayLength', 'iDisplayStart');
                $s = formatPostData($data, $key);

                $returnData = $this->loadMenu();
                $count = count($returnData);
            }

            $output['aaData'] = $returnData;
            $output['iTotalDisplayRecords'] = $count;    //如果有全局搜索，搜索出来的个数
            $output['iTotalRecords'] = $count; //总共有几条数据

            return json($output);
        }
    }

    protected function loadMenu($pid = '0', $level = 0)
    {
        $sql =
            "select m.id, ifNull(i.code, '') as icon, m.menuName as name, m.menuUrl as url, " .
            "       m.description, m.msort, m.status" .
            "  from tp5_menu m " .
            "  left join tp5_icon i on m.menuicon = i.id " .
            " where m.pid = '{$pid}' " .
            " order by m.msort ";
        $data = Db::query($sql);
        if (!empty($data)) {
            $returnData = Array();

            $sign = "";
            if ($level > 0) {
                for ($i = 0; $i < $level; $i++) {
                    $sign .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                }
                $sign .= "└";
            }

            foreach ($data as $row) {
                if ($row['status'] == 2) {
                    $status = "<a title=\"停用\" style=\"text-decoration:none\" href=\"javascript:;\" onClick=\"changeStatus(this,'" . $row['id'] . "')\"><span class=\"label label-success radius\">已启用</span></a>";
                } else {
                    $status = "<a title=\"启用\" style=\"text-decoration:none\" href=\"javascript:;\" onClick=\"changeStatus(this,'" . $row['id'] . "')\"><span class=\"label radius\">已停用</span></a>";
                }

                $tempData[0] = $row['id'];
                $tempData[1] = "<i class=\"Hui-iconfont\">" . $row['icon'] . "</i>";
                $tempData[2] = $sign . $row['name'];
                $tempData[3] = $row['url'];
                $tempData[4] = $row['description'];
                $tempData[5] = $row['msort'];
                $tempData[6] = $status;

                $returnData[] = $tempData;

                $lData = $this->loadMenu($row['id'], $level + 1);
                if (!empty($lData)) {
                    $returnData = array_merge($returnData, $lData);
                }
            }

            return $returnData;
        } else {
            return '';
        }
    }
    // endregion

    // region 新增
    public function add()
    {
        if (request()->isGet()) {
            $menu = new MenuModel;
            $aList = $menu->where('Pid = "0"')->field("id, menuName as name")->order('mSort')->select();
            $this->assign('aList', $aList);

            return $this->fetch('base@Menu/add');
        }

        if (request()->isPost()) {
            $postData = input('post.');
            if (!empty($postData)) {
                $data = json_decode($postData['aoData'], true);
                $key = array("pid1", "pid2", "menuName", "menuIcon", "menuUrl", "mSort", "description");
                $s = formatPostData($data, $key);

                $menu = new MenuModel;
                $aData['id'] = getID();
                $aData['pid'] = $s['pid2'] != '0' ? $s['pid2'] : $s['pid1'];
                $aData['menuname'] = $s['menuName'];
                $aData['menuicon'] = $s['menuIcon'];
                $aData['menuurl'] = $s['menuUrl'];
                $aData['msort'] = $s['mSort'];
                $aData['description'] = $s['description'];

                if ($menu->save($aData)) {
                    return returnValue(2, "添加成功");
                } else {
                    return returnValue(1, $menu->getError());
                }
            } else {
                return returnValue(1, "提交异常");
            }
        }
    }
    // endregion

    // region 编辑
    public function edit()
    {
        if (request()->isGet()) {
            $data = input('get.');

            /** 主信息 **/
            $sql =
                "SELECT m.*, i.code as iconcode " .
                "  FROM tp5_menu m " .
                "  LEFT JOIN tp5_icon i ON m.menuicon = i.id " .
                " where m.id = '" . $data['id'] . "'";
            $list = Db::query($sql)[0];
            if ($list['pid'] == '0') {
                $list['pid1'] = '0';
                $list['pid2'] = '0';
            } else {
                $sql = "select pid from tp5_menu where id = '" . $list['pid'] . "'";
                $pid = Db::query($sql)[0]['pid'];
                if ($pid == '0') {
                    $list['pid1'] = $list['pid'];
                    $list['pid2'] = '0';
                } else {
                    $list['pid1'] = $pid;
                    $list['pid2'] = $list['pid'];
                }
            }
            $this->assign('list', $list);

            /** 一级菜单 **/
            $menu = new MenuModel;
            $aList = $menu->where('pid = "0"')->field('id, menuName as name')->order('mSort')->select();
            $this->assign('aList', $aList);

            /** 二级菜单 **/
            $bList = array();
            if ($list['pid1'] != '0') {
                $menu = new MenuModel;
                $bList = $menu->where("pid = '{$list['pid1']}'")->field('id, menuName as name')->order('mSort')->select();
            }
            $this->assign('bList', $bList);

            return $this->fetch('base@Menu/edit');
        }

        if (request()->isPost()) {
            $postData = input('post.');
            if (!empty($postData)) {
                $data = json_decode($postData['aoData'], true);
                $key = array("id", "pid1", "pid2", "menuName", "menuIcon", "menuUrl", "mSort", "description");
                $s = formatPostData($data, $key);

                $menu = new MenuModel;
                $aData['id'] = $s['id'];
                $aData['pid'] = $s['pid2'] != '0' ? $s['pid2'] : $s['pid1'];
                $aData['menuname'] = $s['menuName'];
                $aData['menuicon'] = $s['menuIcon'];
                $aData['menuurl'] = $s['menuUrl'];
                $aData['msort'] = $s['mSort'];
                $aData['description'] = $s['description'];

                if ($menu->where('id', $aData['id'])->update($aData)) {
                    return returnValue(2, "编辑成功");
                } else {
                    return returnValue(1, $menu->getError());
                }
            } else {
                return returnValue(1, "提交异常");
            }
        }
    }
    // endregion

    // region 更新状态
    /**
     * 更新状态
     * return（ 0：未找到对应数据；1：状态修改为停用；2：状态修改为启用。）
     */
    public function changeStatus()
    {
        return _changeStatus(new MenuModel);
    }
    // endregion

    public function getMenuTwo()
    {
        if (request()->isPost()) {
            $getData = input('post.');
            $list = array();

            if ($getData['id'] != '0' && $getData['id'] != '') {
                $sql =
                    "select id, menuName as name " .
                    "  from tp5_menu " .
                    " where pid = '{$getData['id']}' " .
                    " order by mSort ";
                $list = Db::query($sql);
            }

            return json($list);
        }
    }
}