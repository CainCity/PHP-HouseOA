<?php
namespace app\search\controller;

use think\Db;
use app\index\controller\Auth as Auth;

class Item extends Auth
{
    // region 查询弹出窗体：项目
    public function index()
    {
        if (request()->isGet()) {
            $this->assign("aList", cacheData('wordbook', config('data.项目性质')));

            return $this->fetch('search@Item/index');
        }

        if (request()->isPost()) {
            $postData = input('post.');
            $output = array();
            $returnData = array();
            $count = 0;

            if (!empty($postData)) {
                $data = json_decode($postData['aoData'], true);
                $key = array('status', 'itemType', 'sName', "iDisplayLength", "iDisplayStart");
                $s = formatPostData($data, $key);

                $where = "1 = 1";

                if (!empty($s['status'])) {
                    if ($s['status'] == '2') {
                        $where .= " and i.status = 2";
                    } else {
                        $where .= " and i.status != 2";
                    }
                }

                if (!empty($s['itemType'])) {
                    $where .= " and i.itemType = '{$s['itemType']}'";
                }

                if (!empty($s['sName'])) {
                    $where .= " and i.itemName like '%{$s['sName']}%'";
                }

                $sql =
                    "select i.id, " .
                    "       concat(p.name, '·', c.name, '·', d.name, '·', i.address) as address, " .
                    "       concat('【', wb.name, '】', i.itemName) as item " .
                    "  from tp5_item i " .
                    "  left join (select id, name from tp5_wordbook where pid = '" . config('data.项目性质') . "') wb on i.itemType = wb.id " .
                    "  left join tp5_province p on i.provinceId = p.id " .
                    "  left join tp5_city c on i.cityId = c.id " .
                    "  left join tp5_district d on i.districtId = d.id " .
                    " where " . $where .
                    " order by CONVERT(p.name USING GBK), CONVERT(c.name USING GBK), CONVERT(d.name USING GBK), CONVERT(i.itemName USING GBK) " .
                    " limit " . $s['iDisplayStart'] . "," . $s['iDisplayLength'];

                $list = Db::query($sql);
                if (!empty($list)) {
                    $tempData = array();
                    foreach ($list as $aRow) {
                        $tempData[0] = $aRow['id'];
                        $tempData[1] = $aRow['item'];
                        $tempData[2] = $aRow['address'];
                        $returnData[] = $tempData;
                    }

                    $sql =
                        "select count(1) as c " .
                        "  from tp5_item i " .
                        "  left join (select id, name from tp5_wordbook where pid = '" . config('data.项目性质') . "') wb on i.itemType = wb.id " .
                        "  left join tp5_province p on i.provinceId = p.id " .
                        "  left join tp5_city c on i.cityId = c.id " .
                        "  left join tp5_district d on i.districtId = d.id " .
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