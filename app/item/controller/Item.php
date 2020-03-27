<?php
namespace app\item\controller;

use app\item\model\Item as ItemModel;
use app\index\controller\Auth as Auth;
use think\Db;

// 项目信息
class Item extends Auth
{
    // region 主界面
    public function index()
    {
        if (request()->isGet()) {
            return $this->fetch('item@Item/index');
        }

        if (request()->isPost()) {
            $postData = input('post.');
            $output = array();
            $returnData = array();
            $count = 0;

            if (!empty($postData)) {
                $data = json_decode($postData['aoData'], true);
                $key = array('status', 'sName', 'ProvinceName', 'CityName', 'DistrictName', 'iDisplayLength', 'iDisplayStart');
                $s = formatPostData($data, $key);

                $where = "1 = 1";

                if (!empty($s['status'])) {
                    if ($s['status'] == '2') {
                        $where .= " and i.status = 2";
                    } else {
                        $where .= " and i.status != 2";
                    }
                }

                if (!empty($s['sName'])) {
                    $itemName = str_replace(array("·", " "), "", $s['sName']);
                    $where .= " and replace(i.itemName,'·','') like '%{$itemName}%'";
                }

                if (!empty($s['ProvinceName'])) {
                    $where .= " and p.name like '%{$s['ProvinceName']}%'";
                }

                if (!empty($s['CityName'])) {
                    $where .= " and c.name like '%{$s['CityName']}%'";
                }

                if (!empty($s['DistrictName'])) {
                    $where .= " and a.name like '%{$s['DistrictName']}%'";
                }

                $sql =
                    "select i.id, i.status, i.itemType, i.itemName, i.description, " .
                    "       concat(p.name, '·', c.name, '·', d.name, '·', i.address) as address " .
                    "  from tp5_item i " .
                    "  left join tp5_province p on i.provinceId = p.id " .
                    "  left join tp5_city c on i.cityId = c.id " .
                    "  left join tp5_district d on i.districtId = d.id " .
                    " where " . $where .
                    " order by CONVERT(p.name USING GBK), CONVERT(c.name USING GBK), CONVERT(d.name USING GBK), CONVERT(i.itemName USING GBK) " .
                    " limit " . $s['iDisplayStart'] . "," . $s['iDisplayLength'];

                $aList = Db::query($sql);
                if (!empty($aList)) {
                    $tempData = array();
                    $itemType = cacheData('wordbook', config('data.项目性质'), true);

                    foreach ($aList as $aRow) {
                        $item = "【" . $itemType[$aRow['itemType']] . "】 " . $aRow['itemName'];

                        if ($aRow['status'] == 2) {
                            $status = "<a title=\"停用\" style=\"text-decoration:none\" href=\"javascript:;\" onClick=\"changeStatus(this,'" . $aRow['id'] . "')\"><span class=\"label label-success radius\">已启用</span></a>";
                        } else {
                            $status = "<a title=\"启用\" style=\"text-decoration:none\" href=\"javascript:;\" onClick=\"changeStatus(this,'" . $aRow['id'] . "')\"><span class=\"label radius\">已停用</span></a>";
                        }

                        $tempData[0] = $aRow['id'];
                        $tempData[1] = $item;
                        $tempData[2] = $aRow['address'];
                        $tempData[3] = $aRow['description'];
                        $tempData[4] = $status;

                        $returnData[] = $tempData;
                    }

                    /** 数据数量查询 */
                    $sql =
                        "select count(1) as c " .
                        "  from tp5_item i " .
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

    // region 新增
    public function add()
    {
        if (request()->isGet()) {
            $this->assign("aList", cacheData('wordbook', config('data.项目性质')));
            $this->assign("provinceList", cacheProvince());

            return $this->fetch('item@Item/add');
        }

        if (request()->isPost()) {
            $data = input('post.');
            if (!empty($data)) {
                $data['itemname'] = trim($data['itemname']);

                if ($this->checkItem($data, false)) {
                    return returnValue(1, "项目[" . $data['itemname'] . "]已存在");
                }

                $data['id'] = getID();

                $model = new ItemModel;
                if ($model->save($data)) {
                    \think\Cache::clear('item'); // 清除缓存
                    return returnValue(2, "添加成功");
                } else {
                    return returnValue(1, $model->getError());
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
            $model = new ItemModel;
            $list = $model->where("id = '{$data['id']}'")->find();
            $this->assign('list', $list);

            // 省
            $this->assign("provinceList", cacheProvince());
            // 市
            $this->assign("cityList", cacheCity($list['provinceid']));
            // 区
            $this->assign("districtList", cacheDistrict($list['cityid']));

            // 项目类型
            $this->assign("type1List", cacheData('wordbook', config('data.项目性质')));

            return $this->fetch('item@Item/edit');
        }

        if (request()->isPost()) {
            $postData = input('post.');
            if (!empty($postData)) {
                $data = json_decode($postData['aoData'], true);

                $model = new ItemModel;
                $key = array('id', 'itemtype', 'itemname', 'provinceid', 'cityid', 'districtid', 'address', 'itemtype', 'developer', 'description');
                $headData = formatPostData($data, $key);
                if ($this->checkItem($headData, true)) {
                    return returnValue(1, "项目[" . $data['itemname'] . "]已存在");
                }

                if ($model->save($headData, ['id' => $headData['id']])) {
                    \think\Cache::clear('item'); // 清除缓存
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


    // region 更新状态
    /**
     * 更新状态
     * return（ 0：未找到对应数据；1：状态修改为停用；2：状态修改为启用。）
     * */
    public function changeStatus()
    {
        return _changeStatus(new ItemModel);
    }
    // endregion

    // region 校验:项目是否存在
    public function checkItem($data, $sign = false)
    {
        $model = new ItemModel;
        $where =
            "1 = 1" .
            " and itemname = '{$data['itemname']}'" .
            " and districtid = '{$data['districtid']}'" .
            " and itemtype = '{$data['itemtype']}'";

        if ($sign) {
            $where .= " and id <> '{$data['id']}'";
        }

        $count = $model->where($where)->count();

        if ($count != 0) {
            return true;
        }
        return false;
    }
    // endregion
}