<?php
namespace app\customer\controller;

use app\index\controller\Auth as Auth;
use think\Db;

// 客户跟进信息
class Followup extends Auth
{
    public function index()
    {
        if (request()->isGet()) {
            // 类型
            $this->assign("aList", cacheData('wordbook', config('data.跟进类型')));
            return $this->fetch('customer@Followup/index');
        }

        if (request()->isPost()) {
            $postData = input('post.');
            $output = array();
            $returnData = array();
            $count = 0;

            if (!empty($postData)) {
                $data = json_decode($postData['aoData'], true);
                $key = array('dateMin', 'dateMax', 'atype', 'CustName', 'aName', 'Description', 'count',
                    'iDisplayLength', 'iDisplayStart');
                $s = formatPostData($data, $key);

                $where = "1 = 1";

                $dateType = 'date(cf.ctime)';

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

                if (array_check('Description', $s)) {
                    if (!empty($s['Description'])) {
                        $where .= " and cf.Description like '%{$s['Description']}%'";
                    }
                }

                if (array_check('atype', $s)) {
                    if (!empty($s['atype'])) {
                        $where .= " and cf.atype like '%{$s['atype']}%'";
                    }
                }

                if (array_check('aName', $s)) {
                    /** 操作用户 */
                    if (!empty($s['aName'])) {
                        $controller = "base";
                        $model = 'user';
                        $tempWhere = " nickname like '%" . $s['aName'] . "%'";
                        $value = $s['aName'];
                        $ids = cacheQueryCriteria($controller, $model, $tempWhere, $value);

                        if (!empty($ids)){
                            $where .= " and cf.fid in ({$ids})";
                        } else {
                            $where .= " and 1 <> 1";
                        }
                    }
                }

                if (array_check('CustName', $s)) {
                    if (!empty($s['CustName'])) {
                        $where .= " and c.name like '%{$s['aName']}%'";
                    }
                }

                /** 数据查询 */
                $sql =
                    "select cf.id, cf.code, cf.description, cf.fTime, c.name AS customerName, cf.atype, cf.fid " .
                    "  from tp5_followup cf " .
                    " INNER JOIN tp5_customer c on cf.hid = c.id " .
                    " where " . $where .
                    " order by cf.fTime desc " .
                    " limit " . $s['iDisplayStart'] . "," . $s['iDisplayLength'];

                $aList = Db::query($sql);
                if (!empty($aList)) {
                    $tempData = array();
                    foreach ($aList as $aRow) {
                        $userData = cacheData('user','', true);
                        $aTypeData = cacheData('wordbook', config('data.跟进类型'), true);
                        $description = $aRow['description'];
                        if (strlen($description) > 75) {
                            $description = mb_substr($description, 0, 25, 'utf-8') . '...';
                        }

                        $tempData[0] = $aRow['id'];
                        $tempData[1] = $aTypeData[$aRow['atype']];
                        $tempData[2] = $aRow['customerName'];
                        $tempData[3] = $description;
                        $tempData[4] = $userData[$aRow['fid']];
                        $tempData[5] = $aRow['fTime'];

                        $returnData[] = $tempData;
                    }

                    if ($s['iDisplayStart'] == 0) {
                        $sql =
                            "select count(1) as c " .
                            "  from tp5_followup cf " .
                            " INNER JOIN tp5_customer c on cf.hid = c.id " .
                            " where " . $where;
                        $aList = Db::query($sql);
                        $count = !empty($aList) ? $aList[0]['c'] : 0;
                    } else {
                        if (array_check('count', $s)) {
                            $count = $s['count'];
                        }
                    }
                }
            }

            $output['aaData'] = $returnData;
            $output['iTotalDisplayRecords'] = $count;    //如果有全局搜索，搜索出来的个数
            $output['iTotalRecords'] = $count; //总共有几条数据

            return json($output);
        }
    }
}