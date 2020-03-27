<?php
namespace app\search\controller;

use think\Db;
use app\index\controller\Auth as Auth;

class Transaction extends Auth
{
    // region 查询弹出窗体：交易
    public function index()
    {
        if (request()->isGet()) {
            // 交易状态
            $this->assign("aList", cacheData('wordbook', config('data.交易状态')));

            return $this->fetch('search@Transaction/index');
        }

        if (request()->isPost()) {
            $postData = input('post.');
            $output = array();
            $returnData = array();
            $count = 0;

            if (!empty($postData)) {
                $data = json_decode($postData['aoData'], true);
                $key = array("aType", "sItemName", "sCode", "sCustName", "iDisplayLength", "iDisplayStart");
                $s = formatPostData($data, $key);

                $where = "1 = 1";

                if (!empty($s['aType'])) {
                    $where .= " and t.aType = '{$s['aType']}'";
                }

                if (!empty($s['sItemName'])) {
                    $where .= " and i.itemName like '%{$s['sItemName']}%'";
                }

                if (!empty($s['sCode'])) {
                    $where .= " and t.transactionsCode like '%{$s['sCode']}%'";
                }

                if (!empty($s['sCustName'])) {
                    $where .= " and t.custName like '%{$s['sCustName']}%'";
                }

                $sql =
                    "select t.id, t.transactionsCode, t.date1, t.date2, t.date3, t.description, wb.name as aTypeName, " .
                    "       concat(i.itemName, '·', t.room, '·', t.custName) as itemName " .
                    "  from tp5_transactions t " .
                    "  left join (select id, name, temp1 from tp5_wordbook where pid = '" . config('data.交易状态') . "') wb on t.aType = wb.id " .
                    "  left join tp5_item i on t.itemId = i.id " .
                    " where " . $where .
                    " order by t.date1 desc, t.cTime desc " .
                    " limit " . $s['iDisplayStart'] . "," . $s['iDisplayLength'];
                $list = Db::query($sql);
                if (!empty($list)) {
                    $tempData = array();
                    foreach ($list as $aRow) {
                        $tempData[0] = $aRow['id'];
                        $tempData[1] = $aRow['transactionsCode'];
                        $tempData[2] = $aRow['aTypeName'];
                        $tempData[3] = $aRow['itemName'];
                        $tempData[4] = myFromDate($aRow['date2']);
                        $tempData[5] = myFromDate($aRow['date3']);
                        $returnData[] = $tempData;
                    }

                    $sql =
                        "select count(1) as c " .
                        "  from tp5_transactions t " .
                        "  left join (select id, name, temp1 from tp5_wordbook where pid = '" . config('data.交易状态') . "') wb on t.aType = wb.id " .
                        "  left join tp5_item i on t.itemId = i.id " .
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