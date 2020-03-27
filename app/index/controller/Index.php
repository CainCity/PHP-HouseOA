<?php
namespace app\index\controller;

use think\Db;

// 主框架
class Index extends Auth
{
    protected $DBConfig = "DBConfig_00"; // 数据仓库

    // region index
    public function index()
    {
        $list = $this->loadMenu('0');
        $this->assign('list', $list);

        $userinfo['id'] = session('login_id');
        $userinfo['code'] = session('login_code');
        $userinfo['name'] = session('login_name');
        $userinfo['nickname'] = session('login_nickname');
        $this->assign('userinfo', $userinfo);

        return $this->fetch();
    }
    protected function loadMenu($pid = '0')
    {
        $userId = session('login_id');
        $sql =
            "select m.id, ifNull(i.code, '') as icon, m.menuName as name, m.menuUrl as url " .
            "  from tp5_user_role ur " .
            "  left join tp5_role_menu rm on ur.roleid = rm.roleid " .
            "  left join tp5_menu m on rm.menuid = m.id " .
            "  left join tp5_icon i on m.menuicon = i.id " .
            " where ur.userid = '{$userId}' " .
            "   and m.pid = '{$pid}' " .
            "   and m.status = 2 " .
            " order by m.msort ";
        $data = Db::query($sql);
        if (!empty($data)) {
            $returnData = Array();

            foreach ($data as $row) {
                $tempData['icon'] = $row['icon'];
                $tempData['name'] = $row['name'];
                $tempData['url'] = $row['url'];
                $tempData['data'] = $this->loadMenu($row['id']);

                $returnData[] = $tempData;
            }

            return $returnData;
        } else {
            return '';
        }
    }
    // endregion

    // region home
    public function home()
    {
        $aData['name'] = session('login_nickname');
        $aData['ip'] = "";
        $aData['ip_last'] = "";
        $aData['cTime'] = "";
        $aData['cTime_last'] = "";
        $aData['count'] = "";

        $sql =
            "select description, cTime " .
            "  from tp5_action " .
            " where aType = '登录' and cid = '" . session("login_id") . "' " .
            " order by cTime desc ";
        $data = Db::query($sql);
        if (count($data) > 1) {
            $i = 0;
            foreach ($data as $row) {
                if ($i > 0) {
                    $aData['ip'] = $row['description'];
                    $aData['cTime'] = $row['cTime'];
                    break;
                } else {
                    $aData['ip_last'] = $row['description'];
                    $aData['cTime_last'] = $row['cTime'];
                    $i++;
                }
            }
        } else {
            foreach ($data as $row) {
                $aData['ip_last'] = $row['description'];
                $aData['cTime_last'] = $row['cTime'];
            }
        }

        $aData['count'] = count($data);

        $this->assign('aData', $aData);

        return $this->fetch();
    }
    // endregion
}
