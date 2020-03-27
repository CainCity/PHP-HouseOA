<?php
namespace app\base\controller;

use app\base\model\Power as PowerModel;
use app\index\controller\Auth as Auth;
use think\Db;

// 特殊权限
class Power extends Auth
{
    // region 主界面
    public function index()
    {
        if (request()->isGet()) {
            return $this->fetch('base@Power/index');
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
                        $where .= " and p.status = 2";
                    } else {
                        $where .= " and p.status != 2";
                    }
                }

                if (!empty($s['sName'])) {
                    $where .= " and p.name like '%{$s['sName']}%'";
                }

                $sql =
                    "SELECT p.* " .
                    "  FROM tp5_power p " .
                    " WHERE 1 = 1 " . $where .
                    " order by CONVERT(p.name USING GBK) " .
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
                        $tempData[1] = $aRow['code'];
                        $tempData[2] = $aRow['name'];
                        $tempData[3] = $aRow['description'];
                        $tempData[4] = $status;

                        $returnData[] = $tempData;
                    }

                    /** 数据数量查询 */
                    $sql =
                        "select count(1) as c " .
                        "  FROM tp5_power p " .
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
            return $this->fetch('base@Power/add');
        }

        if (request()->isPost()) {
            $data = input('post.');
            if ($data) {
                $model = new PowerModel;
                $data['id'] = getID();
                if ($model->save($data)) {
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

            $model = new PowerModel;
            $list = $model->where('id', $data['id'])->find();
            $this->assign('list', $list);

            return $this->fetch('base@Power/edit');
        }

        if (request()->isPost()) {
            $data = input('post.');

            if (!empty($data)) {
                $model = new PowerModel;
                if ($model->where('id', $data['id'])->update($data)) {
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
        return _changeStatus(new PowerModel);
    }
    // endregion
}