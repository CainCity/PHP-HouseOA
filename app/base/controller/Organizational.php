<?php
namespace app\base\controller;

use app\base\model\Organizational as OrganizationalModel;
use app\index\controller\Auth as Auth;
use think\Db;

class Organizational extends Auth
{
    // region 主界面
    public function index()
    {
        if (request()->isGet()) {
            return $this->fetch('base@Organizational/index');
        }

        if (request()->isPost()) {
            $returnData = array();
            $count = 0;

            $aList = _getOrganizationalList('0', 0, '');

            $sql =
                "select uoo.orgId, group_concat(u.userCode, '-', u.nickname) as userNames " .
                "  from tp5_User_Org uoo " .
                "  left join tp5_user u on uoo.userId = u.id " .
                " where u.status = 2 " .
                " group by uoo.orgId ";

            $bList = Db::query($sql);
            if (!empty($aList)) {
                $tempData = array();
                foreach ($aList as $aRow) {

                    if ($aRow['status'] == 2) {
                        $status = "<a title=\"停用\" style=\"text-decoration:none\" href=\"javascript:;\" onClick=\"changeStatus(this,'" . $aRow['id'] . "')\"><span class=\"label label-success radius\">已启用</span></a>";
                    } else {
                        $status = "<a title=\"启用\" style=\"text-decoration:none\" href=\"javascript:;\" onClick=\"changeStatus(this,'" . $aRow['id'] . "')\"><span class=\"label radius\">已停用</span></a>";
                    }

                    $description = "";
                    foreach ($bList as $bRow) {
                        if ($aRow['id'] == $bRow['orgId']) {
                            $description .= "【{$bRow['userNames']}】";
                        }
                    }
                    if (!empty($aRow['description'])) {
                        $description .= $aRow['description'];
                    }

                    $tempData[0] = $aRow['id'];
                    $tempData[1] = $aRow['name'];
                    $tempData[2] = $aRow['mSort'];
                    $tempData[3] = $description;
                    $tempData[4] = $status;

                    $returnData[] = $tempData;
                }

                $count = !empty($aList) ? count($aList) : 0;
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
            /** 组织机构 */
            $aList = _getOrganizationalList('0', 0, " and status = '2'");
            $this->assign("aList", $aList);

            return $this->fetch('base@Organizational/add');
        }

        if (request()->isPost()) {
            $data = input('post.');
            if ($data) {
                $model = new OrganizationalModel;
                $data['id'] = getID();
                if ($model->save($data)) {
                    \think\Cache::clear('team'); // 清除缓存
                    return returnValue(2, "添加成功");
                } else {
                    return returnValue(1, $model->getError());
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

            $model = new OrganizationalModel;
            $list = $model->where('id', $data['id'])->find();
            $this->assign('list', $list);

            /** 组织机构 */
            $aList = _getOrganizationalList('0', 0, " and status = '2'");
            if ($list['status'] != 2) {
                $tempData = $model->where("id = '" . $list['pid'] . "' and status <> '2'")->field("id, name")->find();
                if ($tempData) {
                    $aData['id'] = $tempData['id'];
                    $aData['name'] = $tempData['name']."[已禁用]";
                    $aList[] = $aData;
                }
            }
            $this->assign("aList", $aList);

            return $this->fetch('base@Organizational/edit');
        }

        if (request()->isPost()) {
            $data = input('post.');
            if ($data) {
                $model = new OrganizationalModel;

                // 数据处理
                if ($model->save($data, ['id' => $data['id']])) {
                    \think\Cache::clear('team'); // 清除缓存
                    return returnValue(2, "编辑成功");
                } else {
                    return returnValue(1, $model->getError());
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
        $post = input('post.');
        $model = new OrganizationalModel;
        $data = $model->where('id', $post['id'])->select();
        if ($data) {
            foreach ($data as $vo) {
                $Ids = "'" . $vo['id'] . "'";
                $IdData = $this->changeTreeStatusById($vo['id']);
                if (count($IdData) > 0) {
                    foreach ($IdData as $aRow) {
                        if ($Ids != "") {
                            $Ids .= ",";
                        }
                        $Ids .= "'" . $aRow['id'] . "'";
                    }
                }
                $where = "id in (" . $Ids . ")";

                if ($vo['status'] == 2) {
                    $model->where($where)->update(['status' => 1]);
                    return (1);
                } else {
                    $model->where($where)->update(['status' => 2]);
                    return (2);
                }
            }
        }
        return (0);
    }

    protected function changeTreeStatusById($Id)
    {
        $aaData = array();

        $moder = new OrganizationalModel();
        $where = "pId = '" . $Id . "'";
        $field = "id";
        $aList = $moder->where($where)->field($field)->select();

        if ($aList) {
            foreach ($aList as $aRow) {
                $aaData[] = $aRow;
                $aaData = array_merge($aaData, $this->changeTreeStatusById($aRow["id"]));
            }
        }

        return $aaData;
    }
    // endregion
}