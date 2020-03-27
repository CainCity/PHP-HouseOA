<?php
namespace app\base\controller;

use app\base\model\Wordbook as WordbookModel;
use app\index\controller\Auth as Auth;
use think\Db;

// 数据字典
class Wordbook extends Auth
{
    // region 主界面
    public function index()
    {
        if (request()->isGet()) {
            $this->assign('aList', cacheData('wordbook', '0'));

            return $this->fetch('base@Wordbook/index');
        }

        if (request()->isPost()) {
            $postData = input('post.');
            $output = array();
            $returnData = array();
            $count = 0;

            if (!empty($postData)) {
                $data = json_decode($postData['aoData'], true);
                $key = array('pid', 'status', 'iDisplayLength', 'iDisplayStart');
                $s = formatPostData($data, $key);

                $where = "1 = 1";

                if (!empty($s['status'])) {
                    if ($s['status'] == '2') {
                        $where .= " and v.status = 2";
                    } else {
                        $where .= " and v.status != 2";
                    }
                }

                if (!empty($s['pid'])) {
                    $where .= " and v.id1 = '{$s['pid']}'";
                }

                $sql =
                    "SELECT id, wordbookName1, wordbookName2, status, IF(wordbookName2 = '', mSort1, mSort2) AS mSort, temp1, description " .
                    "  FROM ( " .
                    "SELECT wb.id AS id1, wb.id, " .
                    "       wb.name AS wordbookName1, '' AS wordbookName2, " .
                    "       wb.status, " .
                    "       wb.mSort AS mSort1, -99 AS mSort2, " .
                    "       wb.temp1, " .
                    "       wb.description " .
                    "  FROM tp5_wordbook wb " .
                    " WHERE wb.pid = '0' " .
                    " UNION ALL " .
                    "SELECT wb1.id AS id1, wb2.id, " .
                    "       wb1.name AS wordbookName1, wb2.name AS wordbookName2, " .
                    "       wb2.status, " .
                    "       wb1.mSort AS mSort1, wb2.mSort AS mSort2, " .
                    "       wb2.temp1, " .
                    "       wb2.description " .
                    "  FROM tp5_wordbook wb1 " .
                    "  LEFT JOIN tp5_wordbook wb2 ON wb1.id = wb2.pid " .
                    " WHERE wb1.pid = '0') v " .
                    " WHERE " . $where .
                    " ORDER BY v.mSort1, v.mSort2 " .
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
                        $tempData[1] = $aRow['wordbookName1'];
                        $tempData[2] = $aRow['wordbookName2'];
                        $tempData[3] = $aRow['description'];
                        $tempData[4] = $aRow['mSort'];
                        $tempData[5] = $aRow['temp1'];
                        $tempData[6] = $status;

                        $returnData[] = $tempData;
                    }

                    /** 数据数量查询 */
                    $sql =
                        "select count(1) as c " .
                        "  FROM ( " .
                        "SELECT wb.id AS id1, wb.id, " .
                        "       wb.name AS wordbookName1, '' AS wordbookName2, " .
                        "       wb.status, " .
                        "       wb.mSort AS mSort1, -99 AS mSort2, " .
                        "       wb.temp1, " .
                        "       wb.description " .
                        "  FROM tp5_wordbook wb " .
                        " WHERE wb.pid = '0' " .
                        " UNION ALL " .
                        "SELECT wb1.id AS id1, wb2.id, " .
                        "       wb1.name AS wordbookName1, wb2.name AS wordbookName2, " .
                        "       wb2.status, " .
                        "       wb1.mSort AS mSort1, wb2.mSort AS mSort2, " .
                        "       wb2.temp1, " .
                        "       wb2.description " .
                        "  FROM tp5_wordbook wb1 " .
                        "  LEFT JOIN tp5_wordbook wb2 ON wb1.id = wb2.pid " .
                        " WHERE wb1.pid = '0') v " .
                        " where " . $where;
                    $aList = Db::query($sql);
                    $count = !empty($aList) ? $aList[0]['c'] : 0;
                }

                $output['aaData'] = $returnData;
                $output['iTotalDisplayRecords'] = $count;    //如果有全局搜索，搜索出来的个数
                $output['iTotalRecords'] = $count; //总共有几条数据

                return json($output);
            }
        }
    }
    // endregion

    // region 新增
    public function add()
    {
        if (request()->isGet()) {
            $this->assign('aList', cacheData('wordbook', '0'));

            return $this->fetch('base@Wordbook/add');
        }

        if (request()->isPost()) {
            $data = input('post.');
            if ($data) {
                $model = new WordbookModel;
                $data['id'] = getID();
                if ($model->save($data)) {
                    \think\Cache::clear('wordbook'); // 清除缓存
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

            $this->assign('aList', cacheData('wordbook', '0'));

            $model = new WordbookModel;
            $list = $model->where('id', $data['id'])->find();
            $this->assign('list', $list);

            return $this->fetch('base@Wordbook/edit');
        }

        if (request()->isPost()) {
            $data = input('post.');

            if (!empty($data)) {
                $model = new WordbookModel;
                if ($model->where('id', $data['id'])->update($data)) {
                    \think\Cache::clear('wordbook'); // 清除缓存
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
        return _changeStatus(new WordbookModel);
    }
    // endregion
}