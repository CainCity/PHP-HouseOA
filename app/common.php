<?php
// 应用公共文件
use think\Db;
use app\customer\model\Customer as CustomerModel;
use app\customer\model\Followup as FollowupModel;
use app\base\model\Action as ActionModel;
use app\base\model\Attachment as AttachmentModel;
use app\base\model\UserOrg as UserOrgModel;
use app\base\model\Organizational as OrganizationalModel;

// +----------------------------------------------------------------------
// | 新增数据
// +----------------------------------------------------------------------

# region 新增数据
/**
 * <p>新增：客户</p>
 *
 * @param array $data <p>客户信息</p>
 * @return bool <p>true成功 false失败</p>
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function addCustomer($data)
{
    if (is_numeric($data['tel'])) {
        $model = new CustomerModel;
        if ($model->where('tel', $data['tel'])->find()) {
            return false;
        } else {
            $time = date('Y-m-d H:i:s', time());
            $name = trim($data['name']); // 去空格
            $tel = $data['tel'];
            $description = trim($data['description']);

            // 特殊处理：处理客户名为电话号码全号情况
            if ($tel == $name || $name == '') {
                $name = substr($tel, 0, 3) . '****' . substr($tel, -4);
            }

            // 特殊处理：处理操作人ID
            if (session('?login_id')) {
                $userId = session('login_id');
            } else {
                $userId = $data['userId'];
            }

            $cData['id'] = getID();
            $cData['name'] = $name;
            $cData['tel'] = $tel; // 电话
            $cData['source'] = $data['source'];
            $cData['atype'] = $data['atype'];
            $cData['btype'] = $data['btype'];
            $cData['description'] = $description;
            $cData['cid'] = $userId; // 创建人
            $cData['ctime'] = $time; // 创建时间
            $cData['uid'] = $userId; // 最后修改人
            $cData['utime'] = $time; // 最后修改时间

            if ($model->save($cData)) {
                // 新增跟进
                if (addFollowup($cData)) {
                    return true;
                }

                return true;
            }
        }
    }

    return false;
}

/**
 * <p>新增：跟进</p>
 *
 * @param array $dataCustomer <p>客户数据</p>
 * @param string $followupTypeId <p>跟进类型ID</p>
 * @return bool <p>true成功 false失败</p>
 */
function addFollowup($dataCustomer, $followupTypeId = '')
{
    if (trim($dataCustomer['description']) != "") {
        // 如果没有指定跟进类型的情况是，默认跟进类型为电话
        if ($followupTypeId == '') {
            $followupTypeId = config('data.去电');
        }

        $orgId = "";

        if ($orgId == "") {
            if (array_check("orgid", $dataCustomer)) {
                $orgId = $dataCustomer['orgid'] != "" ? $dataCustomer['orgid'] : "";
            }
        }

        if ($orgId == "") {
            if (session('login_sale_team')) {
                $orgId = session('login_team');
            }
        }

        if ($orgId == "") {
            if (session('login_customer_org')) {
                $orgId = explode(',', session('login_customer_org'))[0];
            }
        }

        $model = new FollowupModel;
        $data['id'] = getID(); // 生成新ID
        $data['atype'] = $followupTypeId;
        $data['hid'] = $dataCustomer['id'];
        $data['fid'] = $dataCustomer['uid'];
        $data['ftime'] = $dataCustomer['utime'];
        $data['description'] = $dataCustomer['description'];
        $data['orgid'] = $orgId;

        if ($model->save($data)) {
            return true;
        }
    }

    return false;
}

/**
 * <p>修改：客户备注</p>
 *
 * @param string $id <p>客户ID</p>
 * @param string $description <p>跟进内容</p>
 * @return bool <p>true成功 false失败</p>
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function editCustomerNote($id, $description) {
    if ($id != "") {
        $model = new CustomerModel;
        $list = $model->where("id = '".$id."'")->field('description')->find();

        $description = trim($description);
        if ($description != $list['description']) {
            $data['description'] = $description;
        }

        $data['id'] = $id;
        $data['uid'] = session('login_id'); // 最后修改人
        $data['utime'] = date('Y-m-d H:i:s', time()); // 最后修改时间
        $model->save($data, ['id' => $data['id']]);
    }

    return true;
}
# endregion

// +----------------------------------------------------------------------
// | 特殊权限
// +----------------------------------------------------------------------

# region 特殊权限
/**
 * <p>获取当前用户特殊权限</p>
 *
 * @return mixed
 */
function getUserPower()
{
    $sql =
        "SELECT p.code " .
        "  FROM tp5_user_Role uf " .
        "  LEFT JOIN tp5_role_power rf ON uf.roleId = rf.roleId " .
        "  LEFT JOIN tp5_power p ON rf.powerId = p.id " .
        " WHERE uf.userId = '" . session('login_id') . "' " .
        "   AND p.id IS NOT NULL " .
        " ORDER BY p.code ";
    return Db::query($sql);
}

/**
 * <p>获取指定用户的客户查询权限</p>
 *
 * @param string $userId <p>用户ID</p>
 * @return string <p>权限ID</p>
 */
function getUserOrg($userId)
{
    $orgIds = "";

    $sql = "select orgId as id from tp5_User_Org where userId = '{$userId}' ";
    $List = Db::query($sql);
    if ($List) {
        foreach ($List as $Row) {
            $orgIds = $orgIds . ',' . getOrgList($Row['id']);
        }
    }

    if ($orgIds != "") {
        $arrOrgId = explode(',', $orgIds);
        $orgIds = "";
        for ($i = 0; $i < count($arrOrgId); $i++) {
            if ($arrOrgId[$i] == "") { continue; }

            $sql = "select id from tp5_organizational where sign = 9 and id = '" . $arrOrgId[$i] . "'";
            if (count(Db::query($sql)) == 0) { continue; }

            if ($orgIds != "") { $orgIds .= ","; }
            if (!strpos($orgIds, $arrOrgId[$i])) { $orgIds .= $arrOrgId[$i]; }
        }
    }

    return $orgIds;
}
function getOrgList($orgId)
{
    $orgIds = $orgId;

    $sql = "select id from tp5_organizational where pid = '" . $orgId . "' ";
    $List = Db::query($sql);
    if ($List) {
        foreach ($List as $Row) {
            $orgIds = $orgIds . ',' . getOrgList($Row['id']);
        }
    }

    return $orgIds;
}

/**
 * <p>用户是否有查看客户联系电话全号权限</p>
 *
 * @return bool <p>true有权限 false无权限</p>
 */
function isPowerOfAllTel() {
    $isTrue = false;

    $powerList = getUserPower();
    if (!empty($powerList)) {
        foreach ($powerList as $power) {
            if ($power['code'] == 'TSQX18040001') {
                $isTrue = true;
                break;
            }
        }
    }

    return $isTrue;
}
# endregion

// +----------------------------------------------------------------------
// | 公共方法
// +----------------------------------------------------------------------

# region 公共方法
/**
 * <p>获取最新ID</p>
 *
 * @return string
 */
function getID()
{
    $data = Db::query('select uuid() as ID');
    return $data[0]['ID'];
}

/**
 * <p>新增操作记录</p>
 *
 * @param string $aType <p>类型</p>
 * @param string $source <p>数据来源</p>
 * @param string $description <p>备注</p>
 */
function addAction($aType, $source, $description)
{
    $model = new ActionModel;
    $model['atype'] = $aType;
    $model['source'] = $source;
    $model['ip'] = getIP();
    $model['description'] = $description;
    $model->save();

    if ($aType == '登录') {
        $sql =
            "update tp5_user_other " .
            "   set lastip = '" . $model['ip'] . "', " .
            "       lasttime = '" . date('Y-m-d H:i:s', time()) . "' " .
            " where userid = '" . session('login_id') . "' ";
        Db::execute($sql);
    }
}

/**
 * <p>设置单据编码 (例：H 1704 00001)</p>
 *
 * @param $model <p>数据模型</p>
 * @param string $sign1 <p>单据类型标识1</p>
 * @param string $sign2 <p>单据类型标识2</p>
 * @param int $len <p>流水号长度</p>
 * @param string $codeName <p>字段名</p>
 * @return string
 * */
function createCode($model, $sign1, $sign2, $len, $codeName = 'code')
{
    $num = 1; // 初始化流水号值
    $code = $sign1 . date($sign2); // 构建标识 (例：H 1704)

    // 查询符合编码规则的最大编码
    $model = $model
        ->where("{$codeName} like '{$code}%' and length({$codeName}) = length('{$code}') + {$len}")
        ->max("{$codeName}");

    // 如果存在，则对应流水号加一，生成最新编码
    if (!empty($model)) {
        $num = intval(substr($model, -1 * $len, $len), 10) + 1;
    }

    // 按流水号长度自动补0
    while (strlen($num) < $len) {
        $num = "0" . $num;
    }

    return $code . $num; // 返回最新单据编码
}

/**
 * <p>检查菜单相应权限</p>
 *
 * @param string $url <p>菜单地址</p>
 * @param string $p <p>权限</p>
 * @return bool
 */
function checkPower($url, $p)
{
    $returnData = false;

    $sql =
        "select rm.*" .
        "  from tp5_user u" .
        "  left join tp5_user_Role uf on u.id = uf.userId" .
        "  left join tp5_role r on  uf.roleId = r.id" .
        "  left join tp5_role_menu rm on r.id = rm.roleId" .
        "  left join tp5_menu m on rm.menuId = m.id" .
        " where u.id = '" . session('login_id') . "'" .
        "   and upper(m.menuUrl) = upper('" . $url . "')";
    $list = Db::query($sql);

    if (!empty($list)) {
        foreach ($list as $row) {
            if ($p == 'a' || $p == 'd' || $p == 's' || $p == 'e') {
                if ($row[$p] == 2) {
                    $returnData = true;
                    break;
                }
            } else {
                $returnData = true;
                break;
            }
        }
    }

    return $returnData;
}

/**
 * <p>校验数组索引是否存在</p>
 *
 * @param string $key <p>键</p>
 * @param array $array <p>数组</p>
 * @return bool
 * */
function array_check($key, $array)
{
    $isTrue = true;
    if (is_array($key)) {
        foreach ($key as $k) {
            if (!isset($array[$k]) && !array_key_exists($k, $array)) {
                $isTrue = false;
                break;
            }
        }
    } else {
        if (!isset($array[$key]) && !array_key_exists($key, $array)) {
            $isTrue = false;
        }
    }

    return $isTrue;
}

/**
 * <p>判断字符串是否经过编码方法</p>
 *
 * @param $str <p>字符串</p>
 * @return bool
 */
function isBase64($str)
{
    if ($str == base64_encode(base64_decode($str))) {
        return true;
    } else {
        return false;
    }
}

/**
 * <p>日期类型显示前处理</p>
 *
 * @param $d <p>日期</p>
 * @return datetime
 */
function myFromDate($d)
{
    return $d == null || $d == "0000-00-00 00:00:00" || $d == "0000-00-00" ? null : date('Y-m-d', strtotime($d));
}

/**
 * <p>数组操作：删除指定Key</p>
 *
 * @param $data <p>数组</p>
 * @param $key <p>需要删除的键</p>
 * @return mixed <p>数组</p>
 */
function array_remove($data, $key){
    if(!array_key_exists($key, $data)){
        return $data;
    }
    $keys = array_keys($data);
    $index = array_search($key, $keys);
    if($index !== FALSE){
        array_splice($data, $index, 1);
    }
    return $data;
}

/**
 * <p>查看全号权限</p>
 *
 * @param $tel
 * @return bool
 */
function checkSeeTel($tel)
{
    // 非销售团队
    if (true) {
        if (session('?login_team')) {
            if (session('login_team') != config('data.销售团队')) {
                return true;
            }
        }
    }

    // 非本人的私客
    if (true) {
        $sql =
            "select count(1) as qq " .
            "  from tp5_Customer " .
            " where tel = '" . $tel . "' " .
            "   and ifNull(aid, '') <> '' " .
            "   and aid <> '" . session('login_id') . "' ";
        if (Db::query($sql)[0]['qq'] > 0) {
            return false;
        }
    }

    // 本日已查阅的客户客户全号
    if (true) {
        $sql =
            "select count(1) as qq " .
            "  from tp5_action " .
            " where atype = '查看全号' " .
            "   and cid = '" . session('login_id') . "' " .
            "   and description = '" . $tel . "' " .
            "   and date(cTime) = date(now()) ";
        if (Db::query($sql)[0]['qq'] > 0) {
            return true;
        }
    }

    // 本日可无条件查阅10组客户电话
    if (true) {
        $sql =
            "select if((sum(a) - 9) > sum(b), 1, 0) as qq " .
            "  from ( " .
            "select 1 as a, 0 as b " .
            "  from tp5_action " .
            " where atype = '查看全号' " .
            "   and cid = '" . session('login_id') . "' " .
            "   and date(cTime) = date(now()) " .
            " group by description " .
            " union all " .
            "select 0 as a, 1 as b " .
            "  from tp5_followup f " .
            " where fId = '" . session('login_id') . "' " .
            "   and date(fTime) = date(now()) " .
            " group by f.hId) v ";
        if (Db::query($sql)[0]['qq'] == 0) {
            return true;
        }
    }

    return false;
}

/**
 * <p>获取用户IP</p>
 *
 * @return array|false|string
 */
function getIP()
{
    global $ip;
    if (getenv("HTTP_CLIENT_IP"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR"))
        $ip = getenv("REMOTE_ADDR");
    else $ip = "Unknow IP";
    return $ip;
}

/**
 * <p>效验电话是否已存在</p>
 *
 * @param $tel
 * @param $orgId
 * @return bool
 */
function existTel($tel, $orgId)
{
    if ($tel == "") {
        return true;
    }

    if ($orgId == "") {
        $sql = "select c.tel from tp5_Customer c where c.tel = '" . $tel ."' ";
        if (count(Db::query($sql)) > 0) { return true; }
    } else {
        $sql =
            "select c.tel, c.orgId " .
            "  from tp5_Customer c " .
            " where c.tel = '" . $tel ."' " .
            "   and c.orgId = '" . $orgId ."' ";
        if (count(Db::query($sql)) > 0) { return true; }
    }

    return false;
}

/**
 * <p>效验是否销售团队</p>
 *
 * @param $teamId
 * @return bool
 */
function existSaleTeam($teamId)
{
    $sql = "select sign from tp5_organizational where id = '" . $teamId . "'";
    if (Db::query($sql)[0]['sign'] == 9) {
        return true;
    }

    return false;
}

/**
 * <p>获取IP地理位置等信息</p>
 *
 * @param string $IP <p>IP地址</p>
 * @return array
 * @throws \think\Exception
 * @throws \think\exception\PDOException
 */
function IPInformation($IP = '')
{
    $returnValue = array();

    if (filter_var($IP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE)) {
        $sql =
            "select isp, country, province, city, district " .
            "  from z_visit " .
            " where ip = '{$IP}' " .
            "   and date(from_unixTime(ctime)) > date(subDate(now(), interval 7 day)) " .
            " order by ctime desc " .
            " limit 1 ";
        $data = Db::connect('DBConfig_00')->query($sql);
        if (!empty($data)) {
            $returnValue['country'] = $data[0]['country']; // 国家
            $returnValue['region'] = $data[0]['province']; // 省、地区
            $returnValue['city'] = $data[0]['city']; // 城市
            $returnValue['district'] = $data[0]['district']; // 区、县
            $returnValue['isp'] = $data[0]['isp']; // 运营商
        }

        if (empty($returnValue)) {
            $extend = new \SmallTool\IP();
            $returnValue = $extend->IPInformation($IP);
        }
    }

    if (empty($returnValue)) {
        $returnValue['country'] = '未知';
        $returnValue['region'] = '未知';
        $returnValue['city'] = '未知';
        $returnValue['isp'] = '未知';
    }

    if (array_check('district', $returnValue)) {
        $returnValue['district'] = '未知';
    }

    return $returnValue;
}

/**
 * <p>获取组织机构列表（树型）</p>
 *
 * @param $Sign
 * @param $id
 * @return array
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function getOrganizationalList($Sign, $id)
{
    $tempList = _getOrganizationalList("0", 0, "");
    $cList = array();
    foreach ($tempList as $aRow) {
        $tempData['id'] = $aRow['id'];
        $tempData['name'] = $aRow['status'] == 2 ? $aRow['name'] : $aRow['name'] . "[已禁用]";
        $tempData['isTrue'] = 1;
        $cList[] = $tempData;
    }

    if ($Sign == "edit") {
        $tempModel = new UserOrgModel;
        $tempList = $tempModel->where("userId", $id)->field("orgId")->select();
        if ($tempList) {
            foreach ($tempList as $aRow) {
                for ($i = 0; $i < count($cList); $i++) {
                    if ($aRow['orgId'] == $cList[$i]['id']) {
                        $cList[$i]['isTrue'] = 2;
                        break;
                    }
                }
            }
        }
    }

    return $cList;
}
function _getOrganizationalList($Id = "0", $level = 0, $w = "")
{
    $aaData = array();

    $sign = "";
    $num = $level * 2;
    for ($i = 0; $i < $num; $i++) {
        $sign .= $i == $num - 1 ? "└─" : "&nbsp;&nbsp;";
    }

    $moder = new OrganizationalModel();
    $where = "pId = '" . $Id . "'" . $w;
    $field = "id, name, mSort, status, pid, description";
    $order = "mSort";
    $aList = $moder->where($where)->field($field)->order($order)->select();

    if ($aList) {
        $level++;
        foreach ($aList as $aRow) {
            $aRow['name'] = $sign . $aRow['name'];
            $aaData[] = $aRow;
            $aaData = array_merge($aaData, _getOrganizationalList($aRow["id"], $level, $w));
        }
    }

    return $aaData;
}

/**
 * <p>格式化提交数据</p>
 *
 * @param $data <p>类型：string 备注：数组</p>
 * @param $Key <p>类型：array or string 备注：键</p>
 * @return array 备注：包含指定键值的数组
 */
function formatPostData($data, $Key) {
    $s = array();

    if (is_array($Key)) {
        $arrKey = $Key;
        foreach ($arrKey as $key) {
            $isTrue = true;
            foreach ($data as $rol) {
                if ($rol['name'] == $key) {
                    $s[$key] = array_check('value', $rol) ? trim($rol['value']) : "";
                    $isTrue = false;
                    break;
                }
            }
            if ($isTrue) { $s[$key] = ""; }
        }
    } else {
        foreach ($data as $rol) {
            $isTrue = true;
            if ($rol['name'] == $Key) {
                $s[$Key] = array_check('value', $rol) ? trim($rol['value']) : "";
                $isTrue = false;
            }
            if ($isTrue) { $s[$Key] = ""; }
        }
    }

    return $s;
}
# endregion

// +----------------------------------------------------------------------
// | 附件
// +----------------------------------------------------------------------

# region 附件
/**
 * <p>附件：上传</p>
 *
 * @param $dataId string <p>数据表ID</p>
 * @param $tableName string <p>数据表名</p>
 * @param $path string <p>存储路径</p>
 * @return boolean <p>是否上传成功</p>
 */
function attachmentUpload($dataId, $tableName, $path = '')
{
    if ($dataId != "" && $tableName != "") {
        if ($path != '') { $path .= DS; }

        // 获取表单上传文件
        $file = request()->file('file');

        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file
            ->validate(['size' => 4194304, 'ext' => 'jpg,png,gif'])
            ->move(ROOT_PATH . 'public' . DS . $path . 'uploads');

        if ($info) {
            $model = new AttachmentModel;

            $tLen = strlen($info->getInfo('')['name']);
            $sLen = strlen($info->getExtension()) + 1;

            $dataTemp['id'] = getID();
            $dataTemp['linkid'] = $dataId;
            $dataTemp['linktable'] = $tableName;
            $dataTemp['suffix'] = $info->getExtension();
            $dataTemp['oldname'] = substr($info->getInfo('')['name'], 0, $tLen - $sLen);
            $dataTemp['newname'] = $info->getFilename();
            $dataTemp['path'] = '/uploads/' . str_replace("\\", "/", $info->getSaveName());

            $model->save($dataTemp);
        }
    }

    return true;
}

/**
 * <p>附件：查询</p>
 *
 * @param string $linkTable <p>关联表名</p>
 * @param string $linkID <p>关联单据ID</p>
 * @param string $suffix <p>附件后缀</p>
 * @param string $path <p>附件路径</p>
 * @return string <p>附件信息</p>
 *
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function getAttachmentList($linkTable, $linkID, $suffix, $path = '')
{
    $info = "";
    if ($path != '') {
        $path = DS . $path;
    }

    $model = new AttachmentModel;
    $where = " linkTable = '" . $linkTable . "' and linkID = '" . $linkID . "' and suffix = '" . $suffix . "'";
    $list = $model
        ->where($where)
        ->orderRaw("CONVERT(oldName USING GBK)")
        ->select();

    if (!empty($list)) {
        switch ($suffix) {
            case 'jpg': // 图片
                foreach ($list as $row) {
                    $info .=
                        "<li class='item'>" .
                        "  <div class='portfoliobox'>" .
                        "    <input class='checkbox' name='stline' type='checkbox' value='" . $row['id'] . "'>" .
                        "    <div class='picbox'>" .
                        "      <a href='.." . $path . $row['path'] . "' data-lightbox='gallery' data-title='" . $row['oldname'] . "'><img src='.." . $path . $row['path'] . "'></a>" .
                        "    </div>" .
                        "    <div class='textbox'>" . $row['oldname'] . "</div>" .
                        "  </div>" .
                        "</li>";
                }
                break;
        }
    }

    return $info;
}

/**
 * <p>附件：删除</p>
 *
 * @param string $arrDataId <p>数据表ID</p>
 * @param string $tableName <p>数据表名</p>
 * @param string $path <p>数据路径</p>
 * @return bool <p>是否删除成功</p>
 * @throws \think\Exception
 *
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function attachmentDelete($arrDataId, $tableName, $path = '')
{
    if (!empty($arrDataId) && $tableName != "") {
        $model = new AttachmentModel;
        $ids = "";

        if ($path != '') { $path = DS . $path; }

        foreach ($arrDataId as $id) {
            if ($ids != "") {
                $ids = $ids . "," . "'" . $id . "'";
            } else {
                $ids = $ids . "'" . $id . "'";
            }
        }
        $where = " linkTable = '" . $tableName . "'" . " and id in (" . $ids . ")";

        /* ******** 删除文件 ******** */
        $aList = $model->where($where)->order('newName')->select();
        foreach ($aList as $row) {
            if (is_file(ROOT_PATH . 'public' . $path . $row['path'])) {
                unlink(ROOT_PATH . 'public' . $path . $row['path']);
            }
        }

        /* ******** 删除数据库数据 ******** */
        if ($model->where($where)->delete()) {
            //$this->success('添加成功');
            return true;
        } else {
            //return $model->getError();
            return false;
        }
    }

    return false;
}
# endregion

// +----------------------------------------------------------------------
// | 计算业务员提成、计算销售经理统提
// +----------------------------------------------------------------------

# region 计算提成
/**
 * <p>计算提成(普通销售业务员)</p>
 *
 * @param int $year <p>年度</p>
 * @param int $month <p>月度</p>
 * @param string $team <p>团队</p>
 * @param float $amount <p>业绩</p>
 * @return float|int <p>提成金额</p>
 */
function calcCommission($year = 2019, $month = 8, $team = '', $amount = 0.00) {
    $commission = 0;

    if ($amount >= 0) {
        $amount = $amount;
        $where = "1 = 1 and status = 2 and aType = 1";

        if ($month < 10) {
            $month = '0' . $month;
        }
        $date = $year . '-' . $month . '-01';
        if ($date != "") {
            $where .= " and sDate <= date('{$date}') ";
        }

        if ($team != "") {
            $where .= " and teamId in ('{$team}', '0') ";
        }

        $sql =
            "select id " .
            "  from tp5_commission_rate " .
            " where " . $where .
            " order by sDate desc " .
            " limit 1 ";
        $headData = Db::query($sql);
        if (!empty($headData)) {
            $sql =
                "select minAmount, maxAmount, " .
                "       round(rate / 100, 2) as rate " .
                "  from tp5_commission_rate_line " .
                " where hid = '{$headData[0]['id']}' " .
                " order by maxAmount ";
            $list = Db::query($sql);
            if (!empty($list)) {
                foreach ($list as $row) {
                    if ($row['minAmount'] < $amount) {
                        if ($row['maxAmount'] >= $amount) {
                            $commission += ($amount - $row['minAmount']) * $row['rate'];
                        } else {
                            $commission += ($row['maxAmount'] - $row['minAmount']) * $row['rate'];
                        }
                    } else {
                        break;
                    }
                }
            }
        }
    }

    return $commission;
}

/**
 * <p>计算统提(销售管理层特有)</p>
 *
 * @param int $year <p>年度</p>
 * @param int $month <p>月度</p>
 * @param string $team <p>所属团队</p>
 * @param float $amount <p>实际业绩</p>
 * @param float $target <p>目标业绩</p>
 * @return float|int <p>统提金额</p>
 */
function calcCommissionByManager($year = 2019, $month = 8, $team = '', $amount = 0.00, $target= 0.00) {
    $commission = 0.00;

    //实际业绩、所属团队必须有效
    if ($amount >= 0 && $team != '') {
        // 如果传入的目标业绩小于等于0，则在数据库中查询对应月份团队的目标业绩；反之，则不需要查询数据库。
        if ($target <= 0) {
            $sql =
                "select num " .
                "  from tp5_target " .
                " where teamId = '{$team}' and year = '{$year}' and month = '{$month}' ";
            $list = Db::query($sql);
            if (!empty($list)) {
                $target = $list[0]['num'];
            }
        }

        // 如果目标业绩仍然小于等于0，则统提直接为0。
        if ($target > 0) {
            $where = "1 = 1 and status = 2 and aType = 3";

            if ($month < 10) {
                $month = '0' . $month;
            }
            $date = $year . '-' . $month . '-01';
            if ($date != "") {
                $where .= " and sDate <= date('{$date}') ";
            }

            if ($team != "") {
                $where .= " and teamId in ('{$team}', '0') ";
            }

            $sql =
                "select id " .
                "  from tp5_commission_rate " .
                " where " . $where .
                " order by sDate desc " .
                " limit 1 ";
            $headData = Db::query($sql);
            if (!empty($headData)) {
                $sql =
                    "select round(minAmount / 100 * {$target}, 2) as minAmount, " .
                    "       round(maxAmount / 100 * {$target}, 2) as maxAmount, " .
                    "       round(rate / 100, 2) as rate " .
                    "  from tp5_commission_rate_line " .
                    " where hid = '{$headData[0]['id']}' " .
                    " order by maxAmount ";
                $list = Db::query($sql);
                if (!empty($list)) {
                    foreach ($list as $row) {
                        if ($row['maxAmount'] == 0.00) {
                            if ($amount == 0.00) {
                                $commission = $target * $row['rate'];
                                break;
                            }
                        } else {
                            if ($row['minAmount'] <= $amount) {
                                if ($row['maxAmount'] >= $amount) {
                                    $commission += ($amount - $row['minAmount']) * $row['rate'];
                                } else {
                                    $commission += ($row['maxAmount'] - $row['minAmount']) * $row['rate'];
                                }
                            } else {
                                break;
                            }
                        }
                    }
                }
            }
        }
    }

    return $commission;
}
# endregion

// +----------------------------------------------------------------------
// | 缓存数据
// +----------------------------------------------------------------------

# region 缓存数据
/**
 * <p>缓存：缓存数据转为数组
 * 例如：
 * 缓存数据，例如：{'aaaa':'bbbb'}
 * 转为
 * 数组数据，例如：{'id':'aaaa', 'name':'bbbb'}</p>
 *
 * @param $data
 * @return array
 */
function cacheToData($data) {
    $returnData = array();

    if (!empty($data)) {
        foreach ($data as $key => $value) {
            $tempData['id'] = $key;
            $tempData['name'] = $value;
            $returnData[] = $tempData;
        }
    }

    return $returnData;
}

/**
 * <p>缓存：数组转为缓存数据
 * 例如：
 * 数组数据，例如：{'id':'one', 'name':'two'}
 * 转为
 * 缓存数据，例如：{'one':'two'}</p>
 *
 * @param $data
 * @return array
 */
function dataToCache($data) {
    $returnData = array();

    if (!empty($data)) {
        foreach ($data as $row) {
            $returnData[$row['id']] = $row['name'];
        }
    }

    return $returnData;
}

/**
 * <p>缓存：获取省信息</p>
 *
 * @param string $id <p>省ID</p>
 * @param bool $sign
 * @return array|mixed
 */
function cacheProvince($id = '', $sign = false)
{
    $tag = strtolower('province');
    $key = $tag . ':' . $id;

    $cache = cache($key);
    if (empty($cache)) {
        $where = "";

        if (request()->isPost()) {
            $data = input('post.');
            if (!empty($data)) {
                if (array_check('id', $data)) {
                    if ($data['id'] != '0' && $data['id'] != '') {
                        $where = " and id = '{$data['id']}'";
                    }
                }
            }
        }

        if ($id != '') { $where = " and id = '{$id}'"; }

        $sql = "select id, name from tp5_province where status = 2 {$where} order by code ";
        $data = Db::query($sql);

        \think\Cache::tag($tag)->set($key, dataToCache($data));
        if (!$sign) {
            $returnData = $data;
        } else {
            $returnData = cache($key);
        }
    } else {
        if (!$sign) {
            $returnData = cacheToData($cache);
        } else {
            $returnData = $cache;
        }
    }

    return $returnData;
}

/**
 * <p>缓存：获取市信息</p>
 *
 * @param string $pid <p>省ID</p>
 * @param bool $sign
 * @return array|mixed
 */
function cacheCity($pid = '', $sign = false)
{
    $tag = strtolower('city');
    $key = $tag . ':' . $pid;

    $cache = cache($key);
    if (empty($cache)) {
        $where = " and 1 <> 1";

        if (request()->isPost()) {
            $data = input('post.');
            if (!empty($data)) {
                if (array_check('id', $data)) {
                    if ($data['id'] != '0' && $data['id'] != '') {
                        $where = " and pid = '{$data['id']}'";
                    }
                }
            }
        }

        if ($pid != '') { $where = " and pid = '{$pid}'"; }

        $sql = "select id, name from tp5_City where status = 2 {$where} order by code ";
        $data = Db::query($sql);

        \think\Cache::tag($tag)->set($key, dataToCache($data));
        if (!$sign) {
            $returnData = $data;
        } else {
            $returnData = cache($key);
        }
    } else {
        if (!$sign) {
            $returnData = cacheToData($cache);
        } else {
            $returnData = $cache;
        }
    }

    return $returnData;
}

/**
 * <p>缓存：获取区信息</p>
 *
 * @param string $pid <p>市ID</p>
 * @param bool $sign
 * @return array|mixed
 */
function cacheDistrict($pid = '', $sign = false)
{
    $tag = strtolower('district');
    $key = $tag . ':' . $pid;

    $cache = cache($key);
    if (empty($cache)) {
        $where = " and 1 <> 1";

        $data = input('post.');
        if (!empty($data)) {
            if (array_check('id', $data)) {
                if ($data['id'] != '0' && $data['id'] != '') {
                    $where = " and pid = '{$data['id']}'";
                }
            }
        }

        if ($pid != '') { $where = " and pid = '{$pid}'"; }

        $sql = "select id, name from tp5_District where status = 2 {$where} order by code ";
        $data = Db::query($sql);

        \think\Cache::tag($tag)->set($key, dataToCache($data));
        if (!$sign) {
            $returnData = $data;
        } else {
            $returnData = cache($key);
        }
    } else {
        if (!$sign) {
            $returnData = cacheToData($cache);
        } else {
            $returnData = $cache;
        }
    }

    return $returnData;
}

/**
 * <p>缓存：通用</p>
 *
 * @param string $model
 * @param string $id
 * @param bool $sign
 * @return array|mixed
 */
function cacheData($model = "", $id = "", $sign = false)
{
    $tag = strtolower($model);
    $key = $tag . ':' . $id;

    $cache = cache($key);
    if (empty($cache)) {
        $data = array();

        switch ($model) {
            // 项目
            case 'item':
                $sql =
                    "select i.id, concat(p.name, '·', c.name, '·', d.name, '·', i.itemName) as name " .
                    "  from tp5_item i " .
                    "  left join tp5_province p on i.provinceId = p.id " .
                    "  left join tp5_city c on i.cityId = c.id " .
                    "  left join tp5_district d on i.districtId = d.id " .
                    " where i.id = '{$id}' ";
                $data = Db::query($sql);
                break;
            // 用户
            case 'user':
                $where = "";
                if ($id != "") {
                    $where .= " and id = '{$id}'";
                }

                $model = model('base/user');
                $field = "id, concat(userCode , '·', username) as name";
                $where = "1 = 1 {$where}";
                $order = "userCode";
                $data = $model->where($where)->field($field)->order($order)->select();
                break;
            // 团队
            case 'team':
                $where = "";
                if ($id != "") {
                    $where .= " and pid = '{$id}'";
                }

                $model = model('base/organizational');
                $field = "id, name";
                $where = "1 = 1 {$where}";
                $order = "id";
                $data = $model->where($where)->field($field)->order($order)->select();
                break;
            // 数据字典
            case 'wordbook':
                $where = "";
                if ($id != "") {
                    $where .= " and pid = '{$id}'";
                }

                $model = model('base/Wordbook');
                $field = "id, name";
                $where = "status = 2 {$where}";
                $order = "mSort";
                $data = $model->where($where)->field($field)->order($order)->select();
                break;
        }

        \think\Cache::tag($tag)->set($key, dataToCache($data));
        if (!$sign) {
            $returnData = $data;
        } else {
            $returnData = cache($key);
        }
    } else {
        if (!$sign) {
            $returnData = cacheToData($cache);
        } else {
            $returnData = $cache;
        }
    }

    return $returnData;
}

/**
 * <p>缓存：查询条件</p>
 *
 * @param string $controller <p>控制器</p>
 * @param string $model <p>模型</p>
 * @param string $where <p>查询条件</p>
 * @param string $value <p>查询内容</p>
 * @return array|mixed
 */
function cacheQueryCriteria($controller = "", $model = "", $where = "", $value = "")
{
    $tag = strtolower($model);
    $key = $tag . ':QueryCriteria:' . $value;

    $name = '';
    if ($controller != "") {$name .= "{$controller}/";}
    $name .= $model;

    $cache = cache($key);
    if (empty($cache)) {
        $model = controller($name, 'model');
        $data = $model->where($where)->field('group_concat(id) as ids')->find();

        if (!empty($data)) {
            \think\Cache::tag($tag)->set($key, formatValue($data['ids']));
        } else {
            \think\Cache::tag($tag)->set($key, '');
        }
        $returnData = cache($key);
    } else {
        $returnData = $cache;
    }

    return $returnData;
}
# endregion

// +----------------------------------------------------------------------
// | 邮件发送
// +----------------------------------------------------------------------

# region 邮件发送
/**
 * <p>邮件发送</p>
 *
 * @param string $title <p>邮件主题</p>
 * @param string $message <p>正文</p>
 * @param string $email <p>发件人</p>
 * @param string $siteName <p>邮件站点名称</p>
 * @return bool
 */
function sendmail($title = '', $message = '', $email = '', $siteName = '')
{
    $returnValue = false;

    $mail = new \PHPMailer\PHPMailer();

    try {
        $siteName = $siteName == '' ? config('company.abbreviation') : $siteName;
        $Host = config('email.server');
        $Username = config('email.user');
        $Password = config('email.password');
        $Port = config('email.port');

        if (!empty($siteName)) {
            $title = "[{$siteName}]$title";
        }

        $mail->isSMTP();                // Send using SMTP
        $mail->SMTPAuth = true;         // Enable SMTP authentication
        //$mail->SMTPDebug = \PHPMailer\SMTP::DEBUG_SERVER; // Enable verbose debug output
        $mail->CharSet = \PHPMailer\PHPMailer::CHARSET_UTF8; // 字符集
        $mail->Encoding = \PHPMailer\PHPMailer::ENCODING_BASE64; // 编码方式
        $mail->SMTPSecure = \PHPMailer\PHPMailer::ENCRYPTION_SMTPS; // 安全验证方式

        //Server settings
        $mail->Host = $Host;            // Set the SMTP server to send through
        $mail->Username = $Username;    // SMTP username
        $mail->Password = $Password;    // SMTP password
        $mail->Port = $Port;            // TCP port to connect to

        //Recipients
        $mail->setFrom($Username);   // 发件人

        // 收件人
        $email = explode(',', $email);
        foreach ($email as $v) {
            if (!empty($v)) {
                $mail->addAddress($v);
            }
        }

        // $mail->addReplyTo('info@example.com');  // 设置回复人信息
        // $mail->addCC('cc@example.com');         // 抄送
        // $mail->addBCC('bcc@example.com');       // 密送

        // Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');         // 附件1
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // 附件2

        // Content
        $mail->isHTML(true);    // Set email format to HTML
        $mail->Subject = $title;
        $mail->Body = $message;
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        if ($mail->Send()) {
            $returnValue = true;
        }
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

    return $returnValue;
}
# endregion

// +----------------------------------------------------------------------
// | 配置文件数据
// +----------------------------------------------------------------------

# region 配置文件数据
/**
 * <p>修改config的函数</p>
 *
 * @param $pat <p>配置前缀</p>
 * @param $rep <p>数据变量</p>
 * @param $file <p>配置文件名(不需要后辍)</p>
 * @return bool <p>返回状态</p>
 */
function setConfig($pat, $rep, $file)
{
    /**
     * 原理就是 打开config配置文件 然后使用正则查找替换 然后在保存文件.
     * 传递的参数为2个数组 前面的为配置 后面的为数值.  正则的匹配为单引号  如果你的是分号 请自行修改为分号
     * $pat[0] = 参数前缀;  例:   default_return_type
     * $rep[0] = 要替换的内容;    例:  json
     */

    if (is_array($pat) and is_array($rep)) {
        for ($i = 0; $i < count($pat); $i++) {
            $pats[$i] = '/\'' . $pat[$i] . '\'(.*?),/';
            $reps[$i] = "'" . $pat[$i] . "'" . "=>" . "'" . $rep[$i] . "',";
        }

        $filename = $file . EXT;
        $filePath = APP_PATH . 'extra/' . $filename;
        //$filePath = APP_PATH . "config.php";

        $string = file_get_contents($filePath); //加载配置文件
        $string = preg_replace($pats, $reps, $string); // 正则查找然后替换
        file_put_contents($filePath, $string); // 写入配置文件
        return true;
    } else {
        return flase;
    }
}

/**
 * <p>修改扩展配置文件</p>
 *
 * @param array  $arr  <p>需要更新或添加的配置</p>
 * @param string $file <p>配置文件名(不需要后辍)</p>
 * @param string $user <p>修改人</p>
 * @return bool
 */
function replaceConfig($arr = [], $file = 'web', $user = 'CainCity')
{
    if (is_array($arr)) {
        $filename = $file . EXT;

        $filepath = APP_PATH . 'extra/' . $filename;
        if (!file_exists($filepath)) {
            $conf = "<?php return [];";
            file_put_contents($filepath, $conf);
        }

        $conf = include $filepath;

        foreach ($arr as $key => $value) {
            $conf[$key] = $value;
        }

        $maxLen = 0;
        foreach ($conf as $key => $value) {
            $len = strlen($key);
            $maxLen = $maxLen > $len ? $maxLen : $len;
        }

        $date = date('Y/m/d');
        $time = date('H:i:s');
        $str =
            "<?php" . "\r\n" .
            "/**" . "\r\n" .
            " * Created by PhpStorm." . "\r\n" .
            " * User: $user" . "\r\n" .
            " * Date: $date" . "\r\n" .
            " * Time: $time" . "\r\n" .
            " */" . "\r\n" .
            "" . "\r\n" .
            "return [" . "\r\n";

        foreach ($conf as $key => $value) {
            $len = strlen($key);
            $diffLen = $maxLen - $len;
            $tempStr = '';
            for ($i = 0; $i < $diffLen + 1; $i++){
                $tempStr .= ' ';
            }

            $str .= "\t//";
            $temp = config("explain.{$file}_{$key}");
            if (!empty($temp)) {$str .= " {$temp}";}
            $str .= "\r\n";

            if (is_string($value)) {
                $str .= "\t'$key'$tempStr => '$value',";
            }
            if (is_array($value)) {
                $temp = json_encode($value);
                $str .= "\t'$key'$tempStr => '$temp',";
            }
            $str .= "\r\n";
        }

        $str .= '];';

        file_put_contents($filepath, $str);

        return true;
    } else {
        return false;
    }
}

/**
 * <p>设置配置文件指定key值</p>
 *
 * @param array|string $Value <p>需要存入指定key的值</p>
 * @return array|string <p>输出处理后的合法样式值</p>
 */
function setConfigValue($Value) {
    $returnValue = '';

    if (empty($returnValue)) {
        if (is_string($Value)) {
            $needle = "\r\n";
            if (strpos($Value, $needle) !== false) {
                $returnValue = explode($needle, $Value);
            } else {
                $returnValue = $Value;
            }
        }
    }

    if (empty($returnValue)) {
        if (is_array($Value)) {
            $returnValue = $Value;
        }
    }

    return $returnValue;
}

/**
 * <p>获取配置文件指定key值</p>
 *
 * @param string $name <p>需要获取的key</p>
 * @return array|string <p>输出获取指定key值</p>
 */
function getConfigValue($name) {
    $returnValue = '';

    $temp = json_decode(config($name), true);
    if (!empty($temp)) {
        if (is_array($temp)) {
            $value = '';
            foreach ($temp as $v) {
                if (!empty($value)) {
                    $value .= "\\r\\n";
                }
                $value .= $v;
            }
            $returnValue .= $value;
        } else {
            $returnValue .= $temp;
        }
    } else {
        $returnValue .= config($name);
    }

    return $returnValue;
}
# endregion

// +----------------------------------------------------------------------
// | 其它
// +----------------------------------------------------------------------

# region 其它
/**
 * <p>快捷修改记录状态</p>
 *
 * @param $model
 * @return int
 */
function _changeStatus($model)
{
    $post = input('post.');
    $data = $model->where('id', $post['id'])->select();
    if (!empty($data)) {
        foreach ($data as $vo) {
            if ($vo['status'] == 2) {
                $model->where('id', $vo['id'])->update(['status' => 1]);
                return (1);
            } else {
                $model->where('id', $vo['id'])->update(['status' => 2]);
                return (2);
            }
        }
    }
    return (0);
}

/**
 * <p>格式化返回数据</p>
 *
 * @param $code <p>类型：int 备注：0失败；1成功</p>
 * @param $message <p>类型：string 备注：主内容（包含错误提示、成功结果等）</p>
 * @return array 备注：包含指定键值的数组
 */
function returnValue($code, $message) {
    $s['code'] = $code;
    $s['message'] = $message;

    return $s;
}

/**
 * <p>获取当前用户可查询的客户归属团队ID</p>
 *
 * @return string
 */
function getMyTeams()
{
    return formatValue(session('login_customer_org'));
}

/**
 * <p>日期相减：相差年数、月数、日数</p>
 *
 * @param $sDate date <p>起始日期</p>
 * @param $eDate date <p>截止日期</p>
 * @param $sign string <p>年:y 月:m 日:d</p>
 * @return int 相差数
 */
function dateSubtraction($sDate, $eDate, $sign = "m")
{
    $z = 0;

    if ($sDate > $eDate) { $temp = $sDate; $sDate = $eDate; $eDate = $temp; }

    $sDate_stamp = strtotime($sDate);
    $eDate_stamp = strtotime($eDate);
    list($date_1['y'], $date_1['m'], $date_1['d']) = explode("-", date('Y-m-d', $sDate_stamp));
    list($date_2['y'], $date_2['m'], $date_2['d']) = explode("-", date('Y-m-d', $eDate_stamp));

    switch ($sign) {
        case "y":
            $z = abs($date_1['y'] - $date_2['y']);
            break;
        case "m":
            $z = abs($date_1['y'] - $date_2['y']) * 12 + $date_2['m'] - $date_1['m'];
            break;
        case "d":
            $z = abs(($sDate_stamp - $eDate_stamp) / 86400);
            break;
    }

    return $z;
}

/**
 * <p>自动生成连续日期：按年度、月度、日</p>
 *
 * @param $sDate date <p>起始日期</p>
 * @param $eDate date <p>截止日期</p>
 * @param $sign string <p>年:y 月:m 日:d</p>
 * @return array 连续日期
 */
function dateContinuous($sDate, $eDate, $sign = "m")
{
    $arrDate = Array();

    if ($sDate > $eDate) { $temp = $sDate; $sDate = $eDate; $eDate = $temp; }

    $n = dateSubtraction($sDate, $eDate, $sign);

    switch ($sign) {
        case "y":
            for ($i = 0; $i <= $n; $i++) {
                $arrDate[] = date("Y-m-d", strtotime("+" . $i . " years", strtotime($sDate)));
            }
            break;
        case "m":
            for ($i = 0; $i <= $n; $i++) {
                $arrDate[] = date("Y-m-d", strtotime("+" . $i . " months", strtotime($sDate)));
            }
            break;
        case "d":
            for ($i = 0; $i <= $n; $i++) {
                $arrDate[] = date("Y-m-d", strtotime("+" . $i . " days", strtotime($sDate)));
            }
            break;
    }

    return $arrDate;
}

/**
 * <p>值格式化
 * 举例：
 * 1,2,3,4 => '1','2','3','4'</p>
 *
 * @param $values string <p>值</p>
 * @return string
 */
function formatValue($values)
{
    $returnValues = "";

    if (!empty($values)) {
        $arrValue = explode(',', $values);
        for ($i = 0; $i < count($arrValue); $i++) {
            if ($arrValue[$i] != "") {
                $returnValues .= $returnValues != "" ? "," : "";
                $returnValues .= "'" . $arrValue[$i] . "'";
            }
        }
    }

    return $returnValues;
}

/**
 * <p></p>
 *
 * @param string $str
 * @param array $dictionary
 * @param string $returnType
 * @param string $delimiter
 * @return string|array
 */
function formatNameById($str = "", $dictionary = array(), $returnType = 'string', $delimiter = ',')
{
    switch ($returnType) {
        case 'string':
            $returnValue = '';

            $str = trim($str);
            if ($str != '' && $str != $delimiter) {
                $typeArr = explode($delimiter, $str);
                if (!empty($typeArr)) {
                    foreach ($typeArr as $r) {
                        if ($r == '') continue;

                        if (array_check($r, $dictionary)) {
                            if ($returnValue != '') {
                                $returnValue .= ',';
                            }
                            $returnValue .= $dictionary[$r];
                        }
                    }
                }
            }
            break;
        case 'array':
            $returnValue = array();

            $str = trim($str);
            if ($str != '' && $str != $delimiter) {
                $typeArr = explode($delimiter, $str);
                if (!empty($typeArr)) {
                    foreach ($typeArr as $r) {
                        if ($r == '') continue;

                        if (array_check($r, $dictionary)) {
                            $returnValue[] = $dictionary[$r];
                        }
                    }
                }
            }
            break;
        default:
            $returnValue = '参数异常';
    }

    return $returnValue;
}
# endregion