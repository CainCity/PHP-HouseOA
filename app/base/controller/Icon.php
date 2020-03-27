<?php
namespace app\base\controller;

use app\index\controller\Auth as Auth;
use think\Db;

// ICON图标
class Icon extends Auth
{
    public function index()
    {
        if (request()->isGet()) {
            $aList = controller('base/Icon', 'model');
            $aList = $aList->where("pid = 0")->field("id, name")->order("id")->select();
            $this->assign("aList", $aList);

            return $this->fetch('base@Icon/index');
        }

        if (request()->isPost()) {
            $postData = input('post.');
            $output = array();
            $returnData = array();
            $count = 0;

            if (!empty($postData)) {
                $data = json_decode($postData['aoData'], true);
                $key = array('TypeId', 'iDisplayLength', 'iDisplayStart');
                $s = formatPostData($data, $key);

                $where = "";

                if (!empty($s['TypeId'])) {
                    $where .= " and i2.pid = " . $s['TypeId'];
                }

                $sql =
                    "select i2.id, i2.code, i2.name, i1.name as pName" .
                    "  from (select * from tp5_icon where pid = 0) i1" .
                    "  left join tp5_icon i2 on i1.id = i2.pid" .
                    " where 1 = 1" . $where .
                    " order by i1.id, i2.id" .
                    " limit " . $s['iDisplayStart'] . "," . $s['iDisplayLength'];

                $aList = Db::query($sql);
                if (!empty($aList)) {
                    $aa = array();
                    foreach ($aList as $aRow) {
                        $aa[0] = $aRow['pName'];
                        $aa[1] = "<i class=\"icon Hui-iconfont\">" . $aRow['code'] . "</i>";
                        $aa[2] = $aRow['name'];
                        $aa[3] = "";
                        $returnData[] = $aa;
                    }

                    /** 数据数量查询 */
                    $sql =
                        "select count(1) as c " .
                        "  from (select * from tp5_icon where pid = 0) i1" .
                        "  left join tp5_icon i2 on i1.id = i2.pid" .
                        " where 1 = 1" . $where;
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
}