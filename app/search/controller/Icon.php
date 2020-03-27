<?php
namespace app\search\controller;

use app\base\model\Icon as IconModel;
use think\Db;
use app\index\controller\Auth as Auth;

class Icon extends Auth
{
    // region 查询弹出窗体：ICON
    public function index()
    {
        if (request()->isGet()) {
            $aList = new IconModel;
            $aList = $aList->where("pid = 0")->field("id, name")->order("id")->select();
            $this->assign("aList", $aList);

            return $this->fetch('search@Icon/index');
        }

        if (request()->isPost()) {
            $postData = input('post.');
            $output = array();
            $returnData = array();
            $count = 0;

            if (!empty($postData)) {
                $data = json_decode($postData['aoData'], true);
                $key = array('sTypeId', "iDisplayLength", "iDisplayStart");
                $s = formatPostData($data, $key);

                $where = "1 = 1";

                if (!empty($s['sTypeId'])) {
                    $where .= " and i2.pid = '{$s['sTypeId']}'";
                }

                $sql =
                    "select i2.id, i2.code, i2.name, i1.name as pname" .
                    "  from (select * from tp5_icon where pid = 0) i1" .
                    "  left join tp5_icon i2 on i1.id = i2.pid" .
                    " where " . $where .
                    " order by i1.id, i2.id" .
                    " limit " . $s['iDisplayStart'] . "," . $s['iDisplayLength'];

                $list = Db::query($sql);
                if (!empty($list)) {
                    $tempData = array();
                    foreach ($list as $aRow) {
                        $tempData[0] = $aRow['id'];
                        $tempData[1] = $aRow['pname'];
                        $tempData[2] = "<i class=\"icon Hui-iconfont\">" . $aRow['code'] . "</i>";
                        $tempData[3] = $aRow['name'];
                        $tempData[4] = "";
                        $returnData[] = $tempData;
                    }

                    $sql =
                        "select count(1) as c " .
                        "  from (select * from tp5_icon where pid = 0) i1" .
                        "  left join tp5_icon i2 on i1.id = i2.pid" .
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