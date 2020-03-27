<?php
namespace app\base\controller;

use app\base\model\Role as RoleModel;
use app\base\model\Menu as MenuModel;
use app\base\model\Power as PowerModer;
use app\base\model\RoleMenu as RoleMenuModel;
use app\base\model\RolePower as RolePowerModel;
use app\index\controller\Auth as Auth;
use think\Db;

// 角色
class Role extends Auth
{
    // region 主界面
    public function index()
    {
        if (request()->isGet()) {
            return $this->fetch('base@Role/index');
        }

        if (request()->isPost()) {
            $postData = input('post.');
            $output = array();
            $returnData = array();
            $count = 0;

            if (!empty($postData)) {
                $data = json_decode($postData['aoData'], true);
                $key = array('status', 'sName', 'iDisplayLength', 'iDisplayStart');
                $s = formatPostData($data, $key);

                $where = "";

                if (!empty($s['status'])) {
                    if ($s['status'] == '2') {
                        $where .= " and status = 2";
                    } else {
                        $where .= " and status != 2";
                    }
                }

                if (!empty($s['sName'])) {
                    $where .= " and roleName like '%{$s['sName']}%'";
                }

                $sql =
                    "SELECT id, status, roleName, description " .
                    "  FROM tp5_role " .
                    " WHERE 1 = 1 " . $where .
                    " order by CONVERT(roleName USING GBK) " .
                    " limit " . $s['iDisplayStart'] . "," . $s['iDisplayLength'];

                $aList = Db::query($sql);
                if (!empty($aList)) {
                    $tempData = array();
                    foreach ($aList as $aRow) {
                        if ($aRow['status'] == 2) {
                            $status = "<a title=\"停用\" style=\"text-decoration:none\" href=\"javascript:;\" onClick=\"changeStatus(this,'" . $aRow['id'] . "')\"><span class=\"label label-success radius\">已启用</span></a>";
                        } else {
                            $status = "<a title=\"启用\" style=\"text-decoration:none\" href=\"javascript:;\" onClick=\"changeStatus(this,'" . $aRow['id'] . "')\"><span class=\"label radius\">已停用</span></a>";
                        }

                        $tempData[0] = $aRow['id'];
                        $tempData[1] = $aRow['roleName'];
                        $tempData[2] = $aRow['description'];
                        $tempData[3] = $status;

                        $returnData[] = $tempData;
                    }

                    /** 数据数量查询 */
                    $sql =
                        "select count(1) as c " .
                        "  FROM tp5_role " .
                        " where 1 = 1 " . $where;
                    $aList = Db::query($sql);
                    $count = !empty($aList) ? $aList[0]['c'] : 0;
                }
            }

            $output['aaData'] = $returnData;
            $output['iTotalDisplayRecords'] = $count;    //如果有全局搜索，搜索出来的个数
            $output['iTotalRecords'] = $count; //总共有几条数据

            return json($output);
        }
    }
    // endregion

    // region 新增
    public function add()
    {
        if (request()->isGet()) {
            $aList = $this->loadMenu('');
            $this->assign("aList", $aList);

            $power = new PowerModer;
            $cList = $power->where("1 = 1")->field("id, name")->order("name")->select();
            $this->assign("cList", $cList);

            return $this->fetch('base@Role/add');
        }

        if (request()->isPost()) {
            $isTrue = false;
            $data = input('post.');

            if ($data) {
                $model = new RoleModel;

                $data['id'] = getID();
                $header['id'] = $data['id'];
                $header['rolename'] = $data['rolename'];
                $header['description'] = $data['description'];

                if ($model->save($header)) {
                    $isTrue = true;
                }

                // 新增角色菜单
                if ($this->addRoleMenu($data)) {
                    $isTrue = true;
                }

                // 新增角色特殊权限
                if ($this->addRolePower($data)) {
                    $isTrue = true;
                }

                if ($isTrue) {
                    return returnValue(2, '新增成功');
                }
            } else {
                return returnValue(1, '异常提交');
            }
        }
    }
    // endregion

    // region 编辑
    public function edit()
    {
        if (request()->isGet()) {
            $data = input('get.');

            $model = new RoleModel;
            $list = $model->where('id', $data['id'])->find();
            $this->assign('list', $list);

            $aList = $this->loadMenu($data['id']);
            $this->assign("aList", $aList);

            $sql =
                "SELECT p.id, p.name, IF(ifNull(rp.powerId, '') = '', 1, 2) AS isTrue " .
                "  FROM tp5_power p " .
                "  LEFT JOIN (SELECT * FROM tp5_Role_Power WHERE roleId = '" . $data['id'] . "') rp ON p.id = rp.powerId " .
                " WHERE p.status = 2 " .
                " ORDER BY p.name ";
            $cList = Db::query($sql);
            $this->assign("cList", $cList);

            return $this->fetch('base@Role/edit');
        }

        if (request()->isPost()) {
            $isTrue = false;
            $data = input('post.');

            if ($data) {
                $header['rolename'] = $data['rolename'];
                $header['description'] = $data['description'];

                $model = new RoleModel;
                if ($model->where('id', $data['id'])->update($header)) {
                    $isTrue = true;
                }

                // 新增角色菜单
                if ($this->addRoleMenu($data)) {
                    $isTrue = true;
                }

                // 新增角色特殊权限
                if ($this->addRolePower($data)) {
                    $isTrue = true;
                }

                if ($isTrue) {
                    return returnValue(2, '编辑成功');
                }
            } else {
                return returnValue(1, '提交异常');
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
        return _changeStatus(new RoleModel);
    }
    // endregion


    // region 新增角色菜单
    private function addRoleMenu($data)
    {
        /** **************** 初始化 **************** */
        $lines = array();
        $isTrue = false;
        $rOfm = new RoleMenuModel;
        $menu = new MenuModel;
        /** ************************************************ */


        /** **************** 删除旧数据 **************** */
        if ($rOfm->where("roleId", $data['id'])->delete()) {
            $isTrue = true;
        }
        /** ************************************************ */


        /** **************** 插入新数据 **************** */
        $menu = $menu->field('id')->order('')->select();
        foreach ($menu as $row) {
            if (array_check($row['id'] . '_a', $data) ||
                array_check($row['id'] . '_d', $data) ||
                array_check($row['id'] . '_e', $data) ||
                array_check($row['id'] . '_s', $data)) {
                $temp = array();

                // 增
                $temp['a'] = array_check($row['id'] . '_a', $data) ? 2 : 1;
                // 删
                $temp['d'] = array_check($row['id'] . '_d', $data) ? 2 : 1;
                // 改
                $temp['e'] = array_check($row['id'] . '_e', $data) ? 2 : 1;
                // 查
                $temp['s'] = array_check($row['id'] . '_s', $data) ? 2 : 1;

                $temp['roleid'] = $data['id'];
                $temp['menuid'] = $row['id'];
                $lines[] = $temp;
            }
        }

        if (!empty($lines)) {
            if ($rOfm->insertAll($lines)) {
                $isTrue = true;
            }
        }
        /** ************************************************ */

        return $isTrue;
    }
    // endregion

    // region 新增角色特殊权限
    private function addRolePower($data)
    {
        /** **************** 初始化 **************** */
        $lines = array();
        $isTrue = false;
        $rOfp = new RolePowerModel;
        $power = new PowerModer;
        /** ************************************************ */


        /** **************** 删除旧数据 **************** */
        if ($rOfp->where("roleId", $data['id'])->delete()) {
            $isTrue = true;
        }
        /** ************************************************ */


        /** **************** 插入新数据 **************** */
        $power = $power->field('id')->order('')->select();
        foreach ($power as $row) {
            $temp = array();

            if (array_check($row['id'], $data)) {
                $temp['roleid'] = $data['id'];
                $temp['powerid'] = $row['id'];
                $lines[] = $temp;
            }
        }

        if (!empty($lines)) {
            if ($rOfp->insertAll($lines)) {
                $isTrue = true;
            }
        }
        /** ************************************************ */

        return $isTrue;
    }
    // endregion

    protected function loadMenu($roleId, $pid = '0')
    {
        if ($roleId == '') {
            $sql =
                "select m.id, m.menuName as name, " .
                "       '' as s, '' as a, '' as e, '' as d, '' as roleId " .
                "  from tp5_menu m " .
                " where m.status = 2 and m.pid = '{$pid}' " .
                " order by m.mSort ";
        } else {
            $sql =
                "select m.id, m.menuName as name, rm.s, rm.a, rm.e, rm.d, rm.roleId " .
                "  from tp5_menu m " .
                "  left join (select * from tp5_Role_Menu where roleId = '{$roleId}') rm on m.id = rm.menuId " .
                " where m.status = 2 and m.pid = '{$pid}' " .
                " order by m.mSort ";
        }
        $data = Db::query($sql);
        if (!empty($data)) {
            $returnData = Array();

            foreach ($data as $row) {
                $tempData['id'] = $row['id'];
                $tempData['name'] = $row['name'];
                $tempData['s'] = $row['s'];
                $tempData['a'] = $row['a'];
                $tempData['e'] = $row['e'];
                $tempData['d'] = $row['d'];
                $tempData['roleId'] = $row['roleId'];
                $tempData['data'] = $this->loadMenu($roleId, $row['id']);

                $returnData[] = $tempData;
            }

            return $returnData;
        } else {
            return '';
        }
    }
}