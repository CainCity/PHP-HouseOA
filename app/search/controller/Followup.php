<?php
namespace app\search\controller;

use think\Db;
use app\index\controller\Auth as Auth;

class Followup extends Auth
{
    // region 查询弹出窗体：客户跟进
    public function index()
    {
        if (request()->isPost()) {
            $postData = input('post.');
            $output = array();
            $returnData = array();
            $count = 0;

            if (!empty($postData)) {
                $data = json_decode($postData['aoData'], true);
                $key = array("hid", 'dateMin', 'dateMax', 'aType', "iDisplayLength", "iDisplayStart");
                $s = formatPostData($data, $key);

                $where = "1 = 1";

                $dateType = 'date(fTime)';

                if (!empty($s['dateMin']) && !empty($s['dateMax'])) {
                    $d1 = date('Y-m-d', strtotime($s['dateMin']));
                    $d2 = date('Y-m-d', strtotime($s['dateMax']));
                    if ($d1 < $d2) {
                        $where .= " and {$dateType} between '{$d1}' and '{$d2}'";
                    } else {
                        $where .= " and {$dateType} between '{$d2}' and '{$d1}'";
                    }
                } else {
                    if (!empty($s['dateMin'])) {
                        $d1 = date('Y-m-d', strtotime($s['dateMin']));
                        $where .= " and {$dateType} >= '{$d1}'";
                    } elseif (!empty($s['dateMax'])) {
                        $d2 = date('Y-m-d', strtotime($s['dateMax']));
                        $where .= " and {$dateType} <= '{$d2}'";
                    }
                }

                if (array_check('aType', $s)) {
                    if (!empty($s['aType'])) {
                        $where .= " and aType like '%{$s['aType']}%'";
                    }
                }

                if (array_check('hid', $s)) {
                    if (!empty($s['hid'])) {
                        $where .= " and cf.hId = '{$s['hid']}'";
                    }
                }

                $orgIds = session('login_customer_org');
                if (!empty($orgIds)) {
                    $whereTemp = "";

                    $arrOrgId = explode(',', $orgIds);
                    for ($i = 0; $i < count($arrOrgId); $i++) {
                        if ($arrOrgId[$i] != "") {
                            if ($whereTemp != "") {
                                $whereTemp .= " or ";
                            }
                            if (!strpos($whereTemp, $arrOrgId[$i])) {
                                $whereTemp .= " cf.orgId = '{$arrOrgId[$i]}'";
                            }
                        }
                    }

                    $where .= " and ({$whereTemp}) ";
                }

                $sql =
                    "select cf.id, cf.description, cf.fTime, " .
                    "       c.name AS custName, wb.name AS aTypeName, u.nickname as username " .
                    "  from tp5_followup cf " .
                    "  left join tp5_customer c on cf.hId = c.id " .
                    "  left join tp5_wordbook wb on cf.aType = wb.id " .
                    "  left join tp5_user u on cf.fId = u.id " .
                    " where " . $where .
                    " order by cf.fTime desc " .
                    " limit " . $s['iDisplayStart'] . "," . $s['iDisplayLength'];

                $aList = Db::query($sql);
                if (!empty($aList)) {
                    $tempData = array();
                    foreach ($aList as $aRow) {
                        $tempData[0] = $aRow['id'];
                        $tempData[1] = $aRow['aTypeName'];
                        $tempData[2] = $aRow['description'];
                        $tempData[3] = $aRow['username'];
                        $tempData[4] = $aRow['fTime'];
                        $returnData[] = $tempData;
                    }

                    $sql =
                        "select count(1) as c " .
                        "  from tp5_followup cf " .
                        "  left join tp5_customer c on cf.hId = c.id " .
                        "  left join tp5_wordbook wb on cf.aType = wb.id " .
                        "  left join tp5_user u on cf.fId = u.id " .
                        " where " . $where;
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
}