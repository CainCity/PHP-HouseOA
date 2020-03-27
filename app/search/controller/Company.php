<?php
namespace app\search\controller;

use think\Db;
use app\index\controller\Auth as Auth;

class Company extends Auth
{
    // region 查询弹出窗体：合作企业
    public function index()
    {
        if (request()->isGet()) {
            $this->assign("aList", cacheData('wordbook', config('data.项目性质')));

            return $this->fetch('search@Company/index');
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

                $where = "1 = 1";

                if (!empty($s['status'])) {
                    if ($s['status'] == '2') {
                        $where .= " and status = 2";
                    } else {
                        $where .= " and status != 2";
                    }
                }

                if (!empty($s['sName'])) {
                    $where .= " and itemName like '%{$s['sName']}%'";
                }

                $sql =
                    "select id, company, address " .
                    "  from tp5_company " .
                    " where " . $where .
                    " order by CONVERT(company USING GBK) " .
                    " limit " . $s['iDisplayStart'] . "," . $s['iDisplayLength'];
                $list = Db::query($sql);
                if (!empty($list)) {
                    $tempData = array();
                    foreach ($list as $aRow) {
                        $tempData[0] = $aRow['id'];
                        $tempData[1] = $aRow['company'];
                        $tempData[2] = $aRow['address'];
                        $returnData[] = $tempData;
                    }

                    $sql = "select count(1) as c from tp5_company where " . $where;
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