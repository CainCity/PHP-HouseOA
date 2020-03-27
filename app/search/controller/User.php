<?php
namespace app\search\controller;

use app\base\model\Organizational as OrgModel;
use think\Db;
use app\index\controller\Auth as Auth;

class User extends Auth
{
    // region 查询弹出窗体：用户
    public function index()
    {
        if (request()->isGet()) {
            // 团队
            if (session('login_sale_team')) {
                $aList = new OrgModel;
                $aList = $aList->where("id", session('login_team'))->field("id, name")->order("mSort")->select();
            } else {
                $aList = getOrganizationalList('add', '0');
            }

            $this->assign("aList", $aList);

            return $this->fetch('search@User/index');
        }

        if (request()->isPost()) {
            $postData = input('post.');
            $output = array();
            $returnData = array();
            $count = 0;

            if (!empty($postData)) {
                $data = json_decode($postData['aoData'], true);
                $key = array('status', 'teamId', 'sCode', 'sName', 'iDisplayLength', 'iDisplayStart');
                $s = formatPostData($data, $key);

                $where = "1 = 1 and isDelete != 2";

                //状态
                if (!empty($s['status'])) {
                    if ($s['status'] == '2') {
                        $where .= " and u.status = 2";
                    } else {
                        $where .= " and u.status != 2";
                    }
                }
                //团队
                if (!empty($s['teamId'])) {
                    $where .= " and u.teamId = '{$s['teamId']}'";
                }
                //编号
                if (!empty($s['sCode'])) {
                    $where .= " and u.userCode like '%{$s['sCode']}%'";
                }
                //名称
                if (!empty($s['sName'])) {
                    $where .= " and u.userName like '%{$s['sName']}%'";
                }

                /**
                 * 根据当前用户所属团队判断用户查询权限
                 * 1、当前用户归属销售团队时，仅能查询本团队用户资料
                 * 2、当前用户非销售团队时，则能查询全部用户资料
                 */
                if (session('login_sale_team')) {
                    $where .= " and u.teamId = '" . session('login_team') . "'";
                }

                $sql =
                    "SELECT u.id, u.userCode, u.userName, u.nickName, u.description, o.Name AS teamName " .
                    "  FROM tp5_user u " .
                    "  LEFT JOIN tp5_Organizational o ON u.teamId = o.id " .
                    " WHERE " . $where .
                    " ORDER BY u.userCode " .
                    " limit " . $s['iDisplayStart'] . "," . $s['iDisplayLength'];

                $list = Db::query($sql);
                if (!empty($list)) {
                    $tempData = array();
                    foreach ($list as $row) {
                        $tempData[0] = $row['id'];
                        $tempData[1] = $row['userCode'];
                        $tempData[2] = $row['nickName'];
                        $tempData[3] = $row['teamName'];
                        $tempData[4] = "";
                        $returnData[] = $tempData;
                    }

                    $sql =
                        "select count(1) as c " .
                        "  FROM tp5_user u " .
                        "  LEFT JOIN tp5_wordbook wb ON u.teamId = wb.id " .
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