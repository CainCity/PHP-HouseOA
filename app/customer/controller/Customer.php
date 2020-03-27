<?php
namespace app\customer\controller;

use think\Db;
use app\index\controller\Auth as Auth;
use app\customer\model\Customer as CustomerModel;

// 客户信息
class Customer extends Auth
{
    // region 主界面
    public function index()
    {
        if (request()->isGet()) {
            $list = array();
            $list['aType'] = "";
            $list['aTeamId'] = "";
            $data = input('get.');
            if (!empty($data)) {
                if (array_check('aType', $data)) {
                    $list['aType'] = $data['aType'];
                }
                if (array_check('aTeamId', $data)) {
                    $list['aTeamId'] = $data['aTeamId'];
                }
            }
            $this->assign("list", $list);

            // 类型
            $this->assign("aList", cacheData('wordbook', config('data.客户类型')));
            // 来源
            $this->assign("bList", cacheData('wordbook', config('data.客户来源')));
            // 客户池
            $this->assign("dList", cacheData('wordbook', config('data.客户池')));
            // 团队
            if (session('login_sale_team')) {
                $sql = "select id, name from tp5_organizational where id = '" . session('login_team') . "'";
                $dList = Db::query($sql);
            } else {
                $sql = "select id, name from tp5_organizational where sign = 9 order by msort";
                $dList = Db::query($sql);
            }
            $this->assign("eList", $dList);

            return $this->fetch('customer@Customer/index');
        }

        if (request()->isPost()) {
            // 查询条件
            $RData = $this->createSqlWhere(input('post.'));
            $returnData = array();

            if (true) {
                /** 数据查询 */
                $sql =
                    "select c.id, c.name, c.description, c.uTime, " .
                    "       if(c.cTime >= date_sub(now(), interval 2 day), '新客户', '') as isNew, " .
                    "       wb1.name as aTypeName, " .
                    "       c.itemId, c.uid, c.aid " .
                    "  from tp5_customer c " .
                    "  left join tp5_wordbook wb1 on c.aType = wb1.id " .
                    " where " . $RData['where'] .
                    " order by c.uTime desc " .
                    " limit " . $RData['iDisplayStart'] . "," . $RData['iDisplayLength'];

                $aList = Db::query($sql);
                if (!empty($aList)) {
                    $tempData = array();
                    foreach ($aList as $aRow) {
                        $description = $aRow['description'];
                        if (strlen($aRow['isNew']) > 0) {
                            $isNew = "<span class='c-red'>【" . $aRow['isNew'] . "】</span>";

                            if (strlen($description) > 60) {
                                $description = mb_substr($description, 0, 20, 'utf-8') . '...';
                            }

                            $description = $isNew . $description;
                        } else {
                            if (strlen($description) > 75) {
                                $description = mb_substr($description, 0, 25, 'utf-8') . '...';
                            }
                        }

                        $item = "";
                        if ($aRow['itemId'] != "0" && $aRow['itemId'] != "") {
                            $item = cacheData('item', $aRow['itemId'], true);
                            $item = $item[$aRow['itemId']];
                        }

                        $userData = cacheData('user','', true);
                        $aUserName = "";
                        if (!empty($aRow['aid'])) {
                            $aUserName = $userData[$aRow['aid']];
                        }
                        $uUserName = "";
                        if (!empty($aRow['uid'])) {
                            $uUserName = $userData[$aRow['uid']];
                        }

                        $tempData[0] = $aRow['id'];
                        $tempData[1] = $aRow['aTypeName'] . " " . $aUserName;
                        $tempData[2] = $aRow['name'];
                        $tempData[3] = $item;
                        $tempData[4] = $description;
                        $tempData[5] = $aRow['uTime'] . " " . $uUserName;

                        $returnData[] = $tempData;
                    }
                }

                /** 数据数量查询 */
                if ($RData['iDisplayStart'] == 0) {
                    $sql =
                        "select count(1) as c " .
                        "  from tp5_customer c " .
                        "  left join tp5_wordbook wb1 on c.aType = wb1.id " .
                        " where " . $RData['where'];
                    $aList = Db::query($sql);
                    $RData['count'] = !empty($aList) ? $aList[0]['c'] : 0;
                }
            }

            $output['aaData'] = $returnData;
            $output['iTotalDisplayRecords'] = $RData['count'];    //如果有全局搜索，搜索出来的个数
            $output['iTotalRecords'] = $RData['count']; //总共有几条数据

            return json($output);
        }
    }

    // 根据提交内容，创建查询条件
    protected function createSqlWhere($data)
    {
        $RData['where'] = "1 = 1";
        $RData['count'] = 0;
        $RData['iDisplayStart'] = 0;
        $RData['iDisplayLength'] = 0;

        if (!empty($data)) {
            $aData = json_decode($data['aoData'], true);
            $keys = array(
                'DateType', 'dateMin', 'dateMax', 'Source', 'Source1',
                'Tel', 'Name', 'aType', 'bType', 'isTrue', 'Description', 'Item',
                'CreateName', 'UpdateName', 'aName', 'count', 'teamId',
                'iDisplayLength', 'iDisplayStart'
            );
            $s = formatPostData($aData, $keys);

            // 查询条件：日期范围
            $dateType = "date(c.{$s['DateType']})";
            if (!empty($s['dateMin']) && !empty($s['dateMax'])) {
                $d1 = date('Y-m-d', strtotime($s['dateMin']));
                $d2 = date('Y-m-d', strtotime($s['dateMax']));
                if ($d1 < $d2) {
                    $RData['where'] .= " and {$dateType} between '{$d1}' and '{$d2}'";
                } else {
                    $RData['where'] .= " and {$dateType} between '{$d2}' and '{$d1}'";
                }
            } else {
                if (!empty($s['dateMin'])) {
                    $d1 = date('Y-m-d', strtotime($s['dateMin']));
                    $RData['where'] .= " and {$dateType} >= '{$d1}'";
                } elseif (!empty($s['dateMax'])) {
                    $d2 = date('Y-m-d', strtotime($s['dateMax']));
                    $RData['where'] .= " and {$dateType} <= '{$d2}'";
                }
            }

            // 查询条件：综合查询
            if (array_check('Description', $s)) {
                if (!empty($s['Description'])) {
                    $customerIDs = "";

                    $sql =
                        "select distinct hId as CustomerID " .
                        "  from tp5_followup " .
                        " where description like '%{$s['Description']}%'";
                    $List = Db::query($sql);
                    if ($List) {
                        foreach ($List as $Row) {
                            if ($customerIDs != "") {
                                $customerIDs .= ",";
                            }
                            $customerIDs .= "'{$Row['CustomerID']}'";
                        }
                    }

                    $tempWhere = '';
                    $tempWhere .= " itemName like '%{$s['Description']}%'";
                    $controller = "item";
                    $model = 'item';
                    $where = $tempWhere;
                    $value = $s['Description'];
                    $itemIDs = cacheQueryCriteria($controller, $model, $where, $value);

                    $RData['where'] .= " and (";
                    $RData['where'] .= " c.name like '%{$s['Description']}%' ";
                    $RData['where'] .= " or c.description like '%{$s['Description']}%' ";
                    if (!empty($itemIDs)) {$RData['where'] .= " or c.itemId in ({$itemIDs})";}
                    if (!empty($customerIDs)) {$RData['where'] .= " or c.id in ({$customerIDs})";}
                    $RData['where'] .= ")";
                }
            }

            // 查询条件：项目
            if (array_check('Item', $s)) {
                if (!empty($s['Item'])) {
                    $tempWhere = '';
                    if ($s['Item'] == '空') {
                        $tempWhere .= " ifNull(ItemName, '') = ''";
                    } else {
                        $tempWhere .= " itemName like '%{$s['Item']}%'";
                    }

                    $controller = "item";
                    $model = 'item';
                    $where = $tempWhere;
                    $value = $s['Item'];
                    $ids = cacheQueryCriteria($controller, $model, $where, $value);

                    if (!empty($ids)){
                        $RData['where'] .= " and c.itemId in ({$ids})";
                    } else {
                        $RData['where'] .= " and 1 <> 1";
                    }
                }
            }

            if (array_check('level', $s)) {
                if (!empty($s['level'])) { $RData['where'] .= " and c.level = '{$s['level']}'"; }
            }

            if (array_check('Source', $s)) {
                if (!empty($s['Source'])) { $RData['where'] .= " and c.source like '%{$s['Source']}%'"; }
            }

            if (array_check('Tel', $s)) {
                if (mb_strlen($s['Tel'], 'utf-8') >= 4) {
                    if (is_numeric($s['Tel'])) {
                        $RData['where'] .= " and c.tel like '%{$s['Tel']}%'";
                    } else {
                        $temp = preg_replace('/[\D]/', '_', $s['Tel']);
                        $RData['where'] .= " and c.tel like '{$temp}'";
                    }
                } elseif ($s['Tel'] <> '') {
                    $RData['where'] .= " and 1 <> 1 ";
                }
            }

            if (array_check('Name', $s)) {
                if (!empty($s['Name'])) { $RData['where'] .= " and c.name like '%{$s['Name']}%'"; }
            }

            if (array_check('aType', $s)) {
                if (!empty($s['aType'])) { $RData['where'] .= " and c.aType = '{$s['aType']}'"; }
            }

            if (array_check('bType', $s)) {
                if (!empty($s['bType'])) { $RData['where'] .= " and c.bType = '{$s['bType']}'"; }
            }

            if (array_check('isTrue', $s)) {
                if (!empty($s['isTrue'])) { $RData['where'] .= " and wb1.temp1 = {$s['isTrue']}"; }
            }

            // 查询条件：录入人
            if (array_check('CreateName', $s)) {
                /** 操作用户 */
                if (!empty($s['CreateName'])) {
                    $controller = "base";
                    $model = 'user';
                    $where = " nickname like '%{$s['CreateName']}%'";
                    $value = $s['CreateName'];
                    $ids = cacheQueryCriteria($controller, $model, $where, $value);

                    if (!empty($ids)){
                        $RData['where'] .= " and c.cid in ({$ids})";
                    } else {
                        $RData['where'] .= " and 1 <> 1";
                    }
                }
            }

            // 查询条件：最后修改人
            if (array_check('UpdateName', $s)) {
                /** 操作用户 */
                if (!empty($s['UpdateName'])) {
                    $controller = "base";
                    $model = 'user';
                    $where = " nickname like '%{$s['UpdateName']}%'";
                    $value = $s['UpdateName'];
                    $ids = cacheQueryCriteria($controller, $model, $where, $value);

                    if (!empty($ids)){
                        $RData['where'] .= " and c.uid in ({$ids})";
                    } else {
                        $RData['where'] .= " and 1 <> 1";
                    }
                }
            }

            // 查询条件：归属人
            if (array_check('aName', $s)) {
                /** 操作用户 */
                if (!empty($s['aName'])) {
                    $controller = "base";
                    $model = 'user';
                    $where = " nickname like '%{$s['aName']}%'";
                    $value = $s['aName'];
                    $ids = cacheQueryCriteria($controller, $model, $where, $value);

                    if (!empty($ids)){
                        $RData['where'] .= " and c.aid in ({$ids})";
                    } else {
                        $RData['where'] .= " and 1 <> 1";
                    }
                }
            }

            // 查询条件：客户所属团队
            if (array_check('teamId', $s)) {
                if (!empty($s['teamId'])) { $RData['where'] .= " and c.orgId = '{$s['teamId']}'"; }
            }

            if (array_check('iDisplayStart', $s)) { $RData['iDisplayStart'] = $s['iDisplayStart']; }
            if (array_check('iDisplayLength', $s)) { $RData['iDisplayLength'] = $s['iDisplayLength']; }
            if (array_check('count', $s)) { $RData['count'] = $s['count']; }
        }

        $powerList = getUserPower();
        if (!empty($powerList)) {
            foreach ($powerList as $aRow) {
                switch (trim($aRow['code'])) {
                    case "TSQX17040001": // 查看全部客户
                        $RData['where'] .= "";
                        break;
                    case "TSQX17040003": // 查看自己的客户
                        $RData['where'] .= " and c.aid = '" . session('login_id') . "'";
                        break;
                    case "TSQX17040002": // 查看自己私客及全部公客
                        $RData['where'] .=
                            " and (" .
                            "c.aType <> '" . config('data.私客') . "' or " .
                            "c.aid = '" . session('login_id') . "'" .
                            ")";
                        break;
                    case "TSQX17120001": // 查看自己私客及电销公客
                        $RData['where'] .=
                            " and (" .
                            "c.bType = '" . config('data.电销客户') . "' or " .
                            "c.aid = '" . session('login_id') . "'" .
                            ")";
                        break;
                    case "TSQX19050001": // 销售经理：查看本团队的全部客户资料
                        $RData['where'] .= "";
                        break;
                }
            }
        }

        $orgIds = session('login_customer_org');
        if (!empty($orgIds)) {
            $where = "";

            $arrOrgId = explode(',', $orgIds);
            for ($i = 0; $i < count($arrOrgId); $i++) {
                if ($arrOrgId[$i] != "") {
                    if ($where != "") { $where .= " or "; }
                    if (!strpos($where, $arrOrgId[$i])) { $where .= " c.orgId = '" . $arrOrgId[$i] . "'"; }
                }
            }

            $RData['where'] .= " and (" . $where . ") ";
        }

        return $RData;
    }
    // endregion

    // region 新增
    public function add()
    {
        if (request()->isGet()) {
            $list = array();
            $list['aTeamId'] = "";
            $data = input('get.');
            if ($data) {
                if (array_check('aTeamId', $data)) { $list['aTeamId'] = $data['aTeamId']; }
            }
            $this->assign("list", $list);

            // 类型
            $this->assign("aList", cacheData('wordbook', config('data.客户类型')));
            // 来源
            $this->assign("bList", cacheData('wordbook', config('data.客户来源')));
            // 等级
            $this->assign("cList", cacheData('wordbook', config('data.客户等级')));

            // 组织
            if (session('login_sale_team')) {
                $sql = "select id, name from tp5_organizational where id = '" . session('login_team') . "'";
                $dList = Db::query($sql);
            } else {
                $sql = "select id, name from tp5_organizational where sign = 9 order by msort";
                $dList = Db::query($sql);
            }
            $this->assign("dList", $dList);

            return $this->fetch('customer@Customer/add');
        }

        if (request()->isPost()) {
            $data = input('post.');
            if (!empty($data)) {
                $model = new CustomerModel;

                // 校验私客归属人逻辑
                if ($data['atype'] != config('data.私客')) {
                    $data['aid'] = "";
                } else {
                    if ($data['aid'] == "") {
                        // return array("code" => 1, "msg" => "请选择归属人");
                        $data['aid'] = session("login_id");
                    }
                }

                $data['tel'] = trim($data['tel']);

                // 判断号码是否重复
                if (existTel($data['tel'], $data['orgid'])) {
                    return array("code" => 1, "msg" => $data['tel'] . "号码已存在！");
                } else {
                    $data['name'] = trim($data['name']); // 去空格

                    // 特殊处理：处理客户名为电话号码全号情况
                    if ($data['tel'] == $data['name'] || $data['name'] == '') {
                        $data['name'] = substr($data['tel'], 0, 3) . '****' . substr($data['tel'], -4);
                    }

                    $data['id'] = getID();
                    $data['btype'] = config('data.精准客户'); // 精准客户
                    $data['description'] = trim($data['description']);
                    $data['cid'] = session('login_id'); // 创建人
                    $data['ctime'] = date('Y-m-d H:i:s', time()); // 创建时间
                    $data['uid'] = session('login_id'); // 最后修改人
                    $data['utime'] = date('Y-m-d H:i:s', time()); // 最后修改时间

                    if ($model->save($data)) {
                        // 新增客户跟进
                        addFollowup($data);

                        return returnValue(2, "添加成功");
                    } else {
                        return returnValue(1, $model->getError());
                    }
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

            $b1 = false;

            $arrPower = getUserPower();
            if (!empty($arrPower)) {
                foreach ($arrPower as $p) {
                    if ("TSQX17040001" == $p['code']) { // 查看全部客户
                        $b1 = true;
                        break;
                    }
                }
            }

            // 主信息
            $sql =
                "select c.id, c.name, c.atype, c.source, c.sex, c.description, c.aid, c.itemid, ".
                "       c.level, CONCAT(LEFT(c.tel, 3), '****', RIGHT(c.tel,4)) AS tel, ".
                "       u.nickname as aname, u1.nickname as cname, c.ctime, ".
                "       i.itemname, o.name as orgName, i.districtId " .
                "  from tp5_customer c ".
                "  left join tp5_user u on c.aid = u.id ".
                "  left join tp5_item i on c.itemId = i.id ".
                "  left join tp5_user u1 on c.cid = u1.id ".
                "  left join tp5_organizational o on c.orgId = o.id ".
                " where c.id = '" . $data['id'] . "'";
            $info = Db::query($sql)[0];

            $cityData = cacheCityList();
            if (!empty($info['districtId'])) {
                $info['itemname'] = $cityData[$info['districtId']] . "·" . $info['itemname'];
            }

            // 客户
            $this->assign('list', $info);
            // 类型
            $this->assign("aList", cacheData('wordbook', config('data.客户类型')));
            // 来源
            $this->assign("bList", cacheData('wordbook', config('data.客户来源')));
            // 跟进类型
            $this->assign("cList", cacheData('wordbook', config('data.跟进类型')));
            // 等级
            $this->assign("dList", cacheData('wordbook', config('data.客户等级')));

            if (strlen($info['aid']) >= 36 && $info['aid'] != session('login_id') && !$b1) {
                // echo('该客户已为【' . $info['aname'] . '】的私客，不允许编辑！');
                return $this->fetch('customer@Customer/view');
            } else {
                return $this->fetch('customer@Customer/edit');
            }
        }

        if (request()->isPost()) {
            $data = input('post.');
            if (!empty($data)) {
                $model = new CustomerModel;

                // 校验私客归属人逻辑
                if ($data['atype'] != config('data.私客')) {
                    $data['aid'] = "";
                } else {
                    if ($data['aid'] == "") {
                        // return array("code" => 1, "msg" => "请选择归属人");
                        $data['aid'] = session("login_id");
                    }
                }

                $data['description'] = trim($data['description']);

                // 验证
                if (true) {
                    $isTrue = true;
                    $oldData = $model->where("id = '" . $data['id'] . "'")->find();

                    if ($oldData['source'] != $data['source'] ||
                        $oldData['atype'] != $data['atype'] ||
                        $oldData['level'] != $data['level'] ||
                        $oldData['name'] != $data['name'] ||
                        $oldData['sex'] != $data['sex'] ||
                        $oldData['aid'] != $data['aid'] ||
                        $oldData['itemid'] != $data['itemid'] ||
                        $oldData['description'] != $data['description']) {
                        $isTrue = false;
                    }

                    if ($isTrue) {
                        return array("code" => 2, "msg" => "未做任何修改");
                    }
                }

                // 校验电话号码是否已存在
                if (array_check('tel', $data)) {
                    if ($model->where("tel = '" . $data['tel'] . "' and id <> '" . $data['id'] . "'")->count() > 1) {
                        return array("code" => 1, "msg" => $data['tel'] . "号码已存在！");
                    }
                }

                // 潜在客户转精准客户
                if ($oldData['btype'] != config("data.精准客户")) {
                    if ($data['atype'] == config("data.私客")) {
                        $data['btype'] = config("data.精准客户");
                    }
                }

                $data['uid'] = session('login_id'); // 最后修改人
                $data['utime'] = date('Y-m-d H:i:s', time()); // 最后修改时间

                // 备注为空，则不修改原有备注
                if ($data['description'] == "") {
                    unset($data['description']);
                }

                // 数据处理
                if ($model->save($data, ['id' => $data['id']])) {
                    // 新增跟进
                    if (array_check('description', $data)) {
                        if ($oldData['description'] != $data['description'] && $data['description'] != "") {
                            addFollowup($data);
                        }
                    }

                    return returnValue(2, "编辑成功");
                } else {
                    return returnValue(1, $model->getError());
                }
            } else {
                return returnValue(1, "提交异常");
            }
        }
    }
    // endregion


    // region 查看全号
    public function getTel()
    {
        if (request()->isPost()) {
            if (isPowerOfAllTel()) {
                $data = input('post.');
                if (!empty($data)) {
                    $sql = "SELECT tel FROM tp5_customer WHERE id = '" . $data['id'] . "'";
                    $data = Db::query($sql)[0];

                    if (checkSeeTel($data['tel'])) {
                        addAction("查看全号", 'Web', $data['tel']);
                        return $data;
                    }
                }
            }
        }

        return "";
    }
    // endregion
}