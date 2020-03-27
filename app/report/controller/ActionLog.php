<?php
namespace app\report\controller;

use app\index\controller\Auth as Auth;
use think\Db;

// 报表：操作日志
class ActionLog extends Auth
{
    protected $DBConfig = "DBConfig_00"; // 数据仓库

    public function index()
    {
        if (request()->isGet()) {
            return $this->fetch('report@ActionLog/index');
        }

        if (request()->isPost()) {
            $data = input('post.');
            $s = array();

            //----------------设置查询条件----------------
            $where = "1 = 1";
            if ($data) {
                $data = json_decode($data['aoData'], true);

                foreach ($data as $rol) {
                    switch ($rol['name']) {
                        case "sdatetype":
                            $s['sdatetype'] = $rol['value'];
                            break;
                        case "dateMin":
                            $s['dateMin'] = $rol['value'];
                            break;
                        case "dateMax":
                            $s['dateMax'] = $rol['value'];
                            break;
                        case "stype":
                            $s['stype'] = $rol['value'];
                            break;
                        case "susername":
                            $s['susername'] = $rol['value'];
                            break;
                        case "sdescription":
                            $s['sdescription'] = $rol['value'];
                            break;
                        case "count":
                            $s['count'] = $rol['value'];
                            break;


                        case "iDisplayLength":
                            $s['iDisplayLength'] = $rol['value'];
                            break;
                        case "iDisplayStart":
                            $s['iDisplayStart'] = $rol['value'];
                            break;
                    }
                }

                /** 日期类型 */
                $DateType = "date(a.ctime)";

                /** 日期范围 */
                if ($s['dateMin'] != '' && $s['dateMax'] != '') {
                    $d1 = date('Y-m-d', strtotime($s['dateMin']));
                    $d2 = date('Y-m-d', strtotime($s['dateMax']));
                    if ($d1 < $d2) {
                        $where .= " and " . $DateType . " between '" . $d1 . "' and '" . $d2 . "'";
                    } else {
                        $where .= " and " . $DateType . " between '" . $d2 . "' and '" . $d1 . "'";
                    }
                } else {
                    if ($s['dateMin'] != '') {
                        $d1 = date('Y-m-d', strtotime($s['dateMin']));
                        $where .= " and " . $DateType . " >= '" . $d1 . "'";
                    } elseif ($s['dateMax'] != '') {
                        $d2 = date('Y-m-d', strtotime($s['dateMax']));
                        $where .= " and " . $DateType . " <= '" . $d2 . "'";
                    }
                }

                /** 操作类型 */
                if ($s['stype'] != '0') {
                    $where .= " and a.atype = '" . $s['stype'] . "'";
                }

                /** 操作用户 */
                if (!empty($s['susername'])) {
                    $controller = "base";
                    $model = 'user';
                    $where = " nickname like '%" . $s['susername'] . "%'";
                    $value = $s['susername'];
                    $ids = cacheQueryCriteria($controller, $model, $where, $value);

                    if (!empty($ids)) {
                        $where .= " and c.cid in ({$ids})";
                    } else {
                        $where .= " and 1 <> 1";
                    }
                }

                /** 操作内容 */
                if ($s['sdescription'] != '') {
                    $where .= " and a.description like '%" . $s['sdescription'] . "%'";
                }
            }
            //------------------------------------------------

            //----------------数据处理----------------
            $aaData = array();
            $count = $s['count'];

            $sql =
                "select a.* " .
                "  from tp5_action a " .
                " where " . $where .
                " order by ctime desc " .
                " limit " . $s['iDisplayStart'] . "," . $s['iDisplayLength'];
            $aList = Db::query($sql);

            if (!empty($aList)) {
                $aa = array();
                $userData = cacheData('user','', true);

                foreach ($aList as $aRow) {
                    $username = "";
                    if (!empty($aRow['cid'])) {
                        $username = $userData[$aRow['cid']];
                    }

                    $aa[0] = $aRow['source'];
                    $aa[1] = $aRow['ip'];
                    $aa[2] = $aRow['atype'];
                    $aa[3] = $aRow['description'];
                    $aa[4] = $username;
                    $aa[5] = $aRow['ctime'];

                    $aaData[] = $aa;
                }

                if ($s['iDisplayStart'] == 0) {
                    $sql = "select count(1) as c from tp5_action a where " . $where;
                    $aList = Db::query($sql);
                    $count = !empty($aList) ? $aList[0]['c'] : 0;
                }
            }

            $output['aaData'] = $aaData;
            $output['iTotalDisplayRecords'] = $count;    //如果有全局搜索，搜索出来的个数
            $output['iTotalRecords'] = $count; //总共有几条数据
            //------------------------------------------------

            return json($output);
        }
    }
}