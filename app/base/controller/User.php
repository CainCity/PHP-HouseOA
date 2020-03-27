<?php
namespace app\base\controller;

use app\base\model\User as UserModel;
use app\base\model\UserOther as UserOtherModel;
use app\base\model\Role as RoleModel;
use app\base\model\Organizational as OrgModel;
use app\base\model\UserRole as UserRoleModel;
use app\base\model\UserOrg as UserOrgModel;
use app\index\controller\Auth as Auth;
use think\Db;

class User extends Auth
{
    // region 主界面
    public function index()
    {
        if (request()->isGet()) {
            // 团队
            $this->assign("aList", _getOrganizationalList("0", 0, ""));

            return $this->fetch('base@User/index');
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

                $where = " and isDelete != 2";

                // 状态
                if (!empty($s['status'])) {
                    if ($s['status'] == '2') {
                        $where .= " and u.status = 2";
                    } else {
                        $where .= " and u.status != 2";
                    }
                }

                // 团队
                if (!empty($s['teamId'])) {
                    $where .= " and u.teamId = '" . $s['teamId'] . "'";
                }

                // 编号
                if (!empty($s['sCode'])) {
                    $where .= " and u.userCode like '%" . $s['sCode'] . "%'";
                }

                // 名称
                if (!empty($s['sName'])) {
                    $where .= " and u.username like '%" . $s['sName'] . "%'";
                }

                $sql =
                    "SELECT u.id, u.userCode, u.username, u.nickname, u.description, u.status, " .
                    "       IF(DATE(u.inTime) = '0000-00-00', NULL, DATE(u.inTime)) AS inTime, " .
                    "       IF(DATE(u.outTime) = '0000-00-00', NULL, DATE(u.outTime)) AS outTime, " .
                    "       o.Name AS teamName " .
                    "  FROM tp5_user u " .
                    "  LEFT JOIN tp5_Organizational o ON u.teamId = o.id " .
                    " WHERE 1 = 1 " . $where .
                    " ORDER BY u.userCode " .
                    " limit " . $s['iDisplayStart'] . "," . $s['iDisplayLength'];
                $list = Db::query($sql);
                if (!empty($list)) {
                    $tempData = array();
                    foreach ($list as $row) {
                        $tempData[0] = $row['id'];
                        $tempData[1] = $row['teamName'];
                        $tempData[2] = $row['userCode'];
                        $tempData[3] = $row['username'];
                        $tempData[4] = $row['nickname'];
                        $tempData[5] = $row['inTime'] == null || $row['inTime'] == "0000-00-00 00:00:00" ? null : date('Y-m-d', strtotime($row['inTime']));
                        $tempData[6] = $row['outTime'] == null || $row['outTime'] == "0000-00-00 00:00:00" ? null : date('Y-m-d', strtotime($row['outTime']));
                        $tempData[7] = $row['description'];

                        $returnData[] = $tempData;
                    }

                    /** 数据数量查询 */
                    $sql =
                        "select count(1) as c " .
                        "  FROM tp5_user u " .
                        "  LEFT JOIN tp5_Organizational o ON u.teamId = o.id " .
                        " where 1 = 1 " . $where;
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

    // region 新增
    public function add()
    {
        if (request()->isGet()) {
            $role = new RoleModel;
            $aList = $role->where("status = 2")->order('roleName')->field("id, roleName")->select();
            $this->assign("aList", $aList);

            // 团队
            $this->assign("bList", _getOrganizationalList("0", 0, ""));

            // 组织机构
            $this->assign("cList", getOrganizationalList("add", '0'));

            return $this->fetch('base@User/add');
        }

        if (request()->isPost()) {
            $isTrue = false;
            $data = input('post.');
            if ($data) {
                $user = new UserModel;

                $data['id'] = getID();
                $header['id'] = $data['id'];
                $header['usercode'] = $data['usercode'];
                $header['username'] = $data['username'];
                $header['password'] = md5("123Qwe");
                $header['nickname'] = $data['nickname'];
                $header['teamid'] = $data['teamid'];
                $header['tel'] = $data['tel'];
                $header['intime'] = $data['intime'];
                $header['outtime'] = $data['outtime'];
                $header['description'] = $data['description'];

                if ($user->save($header)) {
                    $isTrue = true;
                }

                if (true) {
                    $idCard = trim($data['idcard']);
                    $birthday = $data['birthday'];
                    $sex = $data['sex'];
                    if ($idCard != "" && strlen($idCard) == 18) {
                        if ($birthday == '') {
                            $temp = substr($idCard, 6, -4);
                            $birthday =
                                substr($temp, 0, 4) . '-' .
                                substr($temp, 4, 2) . '-' .
                                substr($temp, -2);
                        }
                        if ($sex == '0') {
                            $temp = substr($idCard, -2, -1);
                            $sex = $temp % 2 == 1 ? 1 : 2;
                        }
                    }

                    $otherData['userid'] = $data['id'];
                    $otherData['idcard'] = $idCard;
                    $otherData['sex'] = $sex;
                    $otherData['birthday'] = $birthday;
                    $otherData['email'] = $data['email'];
                    $otherData['qq'] = $data['qq'];
                    $otherData['wechat'] = $data['wechat'];
                    $otherData['bank'] = $data['bank'];
                    $otherData['accountholder'] = $data['accountholder'];
                    $otherData['bankaccount'] = $data['bankaccount'];
                    $otherData['note'] = $data['note'];

                    $other = new UserOtherModel;
                    if ($other->insert($otherData)) {
                        $isTrue = true;
                    }
                }

                // 新增用户角色
                if ($this->addUserRole($data)) {
                    $isTrue = true;
                }

                // 新增用户组织（用于设置可查阅客户范围）
                if ($this->addUserOrg($data)) {
                    $isTrue = true;
                }

                if ($isTrue) {
                    \think\Cache::clear('user'); // 清除缓存
                    return returnValue(2, "新增成功");
                }
            } else {
                return returnValue(1, "提交异常");
            }
        }
    }
    // endregion

    // region 编辑
    public function edit()
    {
        if (request()->isGet()) {
            $data = input('get.');

            $sql =
                "select u.*, " .
                "       o.idcard, o.sex, o.birthday, o.email, o.qq, o.wechat, o.bank, " .
                "       o.accountholder, o.bankaccount, o.note " .
                "  from tp5_user u " .
                "  left join tp5_user_other o on u.id = o.userid " .
                " where id = '" . $data['id'] . "'";
            $list = Db::query($sql)[0];

            $list['intime'] = myFromDate($list['intime']);
            $list['outtime'] = myFromDate($list['outtime']);
            $list['birthday'] = myFromDate($list['birthday']);
            $this->assign('list', $list);

            $sql =
                "select r.id, r.roleName, if(ur.roleId is not null, 2, 1) as isTrue " .
                "  from tp5_role r " .
                "  left join (select * from tp5_user_Role where userId = '" . $data['id'] . "') ur on r.id = ur.roleId " .
                " where r.status = 2 " .
                " order by r.roleName ";
            $aList = Db::query($sql);
            $this->assign("aList", $aList);

            // 团队
            $this->assign("bList", _getOrganizationalList("0", 0, ""));

            // 组织机构
            $this->assign("cList", getOrganizationalList("edit", $data['id']));

            return $this->fetch('base@User/edit');
        }

        if (request()->isPost()) {
            $isTrue = false;
            $data = input('post.');
            if ($data) {
                $header['status'] = $data['status'];
                $header['usercode'] = $data['usercode'];
                $header['username'] = $data['username'];
                $header['nickname'] = $data['nickname'];
                $header['teamid'] = $data['teamid'];
                $header['tel'] = $data['tel'];
                $header['intime'] = $data['intime'];
                $header['outtime'] = $data['outtime'];
                $header['description'] = $data['description'];

                $user = new UserModel;
                if ($user->where('id', $data['id'])->update($header)) {
                    $isTrue = true;
                }

                if (true) {
                    $idCard = trim($data['idcard']);
                    $birthday = $data['birthday'];
                    $sex = $data['sex'];
                    if ($idCard != "" && strlen($idCard) == 18) {
                        if ($birthday == '') {
                            $temp = substr($idCard, 6, -4);
                            $birthday =
                                substr($temp, 0,4) . '-' .
                                substr($temp, 4,2) . '-' .
                                substr($temp, -2);
                        }
                        if ($sex == '0') {
                            $temp = substr($idCard, -2, -1);
                            $sex = $temp % 2 == 1 ? 1 : 2;
                        }
                    }

                    $otherData['userid'] = $data['id'];
                    $otherData['idcard'] = $idCard;
                    $otherData['sex'] = $sex;
                    $otherData['birthday'] = $birthday;
                    $otherData['email'] = $data['email'];
                    $otherData['qq'] = $data['qq'];
                    $otherData['wechat'] = $data['wechat'];
                    $otherData['bank'] = $data['bank'];
                    $otherData['accountholder'] = $data['accountholder'];
                    $otherData['bankaccount'] = $data['bankaccount'];
                    $otherData['note'] = $data['note'];

                    $other = new UserOtherModel;
                    if ($other->where('userid', $otherData['userid'])->find()) {
                        if ($other->where('userid', $otherData['userid'])->update($otherData)) {
                            $isTrue = true;
                        }
                    } else {
                        if ($other->insert($otherData)) {
                            $isTrue = true;
                        }
                    }
                }

                // 新增用户角色
                if ($this->addUserRole($data)) {
                    $isTrue = true;
                }

                // 新增用户组织（用于设置可查阅客户范围）
                if ($this->addUserOrg($data)) {
                    $isTrue = true;
                }

                if ($isTrue) {
                    \think\Cache::clear('user'); // 清除缓存
                    return returnValue(2, "编辑成功");
                }
            } else {
                return returnValue(1, "提交异常");
            }
        }
    }
    // endregion


    // region 查看界面
    public function see()
    {
        if (request()->isGet()) {
            $data = input('get.');

            $sql =
                "SELECT u.*, o.name AS teamName " .
                "  FROM tp5_user u " .
                "  LEFT JOIN tp5_Organizational o ON u.teamId = o.id " .
                " WHERE u.id = '" . $data['id'] . "'";
            $this->assign('list', Db::query($sql)[0]);

            return $this->fetch('base@User/see');
        }

        if (request()->isPost()) {
            $data = input('post.');
            if (!empty($data)) {
                $user = new UserModel;
                if ($user->where('id', $data['id'])->update($data)) {
                    return returnValue(2, "编辑成功");
                } else {
                    return returnValue(1, "提交异常");
                }
            } else {
                return returnValue(1, "提交异常");
            }
        }
    }
    // endregion

    // region 修改密码
    public function changePassword()
    {
        if (request()->isGet()) {
            $data = input('get.');

            $user = new UserModel;
            $data = $user->where('id', $data['id'])->field(['id', 'userCode', 'username'])->find();
            $this->assign('data', $data);

            return $this->fetch('base@User/changePassword');
        }

        if (request()->isPost()) {
            $data = input('post.');

            if (!empty($data)) {
                $data['newpassword'] = md5($data['newpassword']);

                $user = new UserModel;
                if ($user->where('id', $data['id'])->update(['password' => $data['newpassword']])) {
                    addAction("修改密码", 'Web', "修改成功");
                    return returnValue(2, "密码修改成功");
                } else {
                    return returnValue(1, "密码修改失败");
                }
            } else {
                return returnValue(1, "提交异常");
            }
        }
    }
    // endregion


    // region 新增用户角色
    private function addUserRole($data)
    {
        /** **************** 初始化 **************** */
        $lines = array();
        $isTrue = false;
        $uOfr = new UserRoleModel;
        $role = new RoleModel;
        /** ************************************************ */


        /** **************** 删除旧数据 **************** */
        if ($uOfr->where("userId", $data['id'])->delete()) {
            $isTrue = true;
        }
        /** ************************************************ */


        /** **************** 插入新数据 **************** */
        $role = $role->field('id')->order('')->select();
        foreach ($role as $row) {
            $temp = array();

            if (array_check($row['id'], $data)) {
                $temp['roleid'] = $row['id'];
                $temp['userid'] = $data['id'];
                $lines[] = $temp;
            }
        }

        if (!empty($lines)) {
            if ($uOfr->insertAll($lines)) {
                $isTrue = true;
            }
        }
        /** ************************************************ */

        return $isTrue;
    }
    // endregion

    // region 新增用户组织（用于设置可查阅客户范围）
    private function addUserOrg($data)
    {
        /** **************** 初始化 **************** */
        $lines = array();
        $isTrue = false;
        $uOfo = new UserOrgModel;
        $org = new OrgModel;
        /** ************************************************ */

        /** **************** 删除旧数据 **************** */
        if ($uOfo->where("userId", $data['id'])->delete()) {
            $isTrue = true;
        }
        /** ************************************************ */

        /** **************** 插入新数据 **************** */
        $role = $org->field('id')->order('')->select();
        foreach ($role as $row) {
            $temp = array();

            if (array_check($row['id'], $data)) {
                $temp['orgid'] = $row['id'];
                $temp['userid'] = $data['id'];
                $temp['atype'] = 1; // 客户查询权限

                $lines[] = $temp;
            }
        }

        if (!empty($lines)) {
            if ($uOfo->insertAll($lines)) {
                $isTrue = true;
            }
        }
        /** ************************************************ */

        return $isTrue;
    }
    // endregion

    public function getUser() {
        if (request()->isPost()) {
            $userList = array();

            $postData = input('post.');
            if (array_check('team', $postData)) {
                $sql =
                    "select id, concat(userCode, '-', nickname) as name " .
                    "  from tp5_user " .
                    " where status = 2 and teamId = '{$postData['team']}' " .
                    " order by userCode ";
                $userList =  Db::query($sql);
            }

            return $userList;
        }
    }
}