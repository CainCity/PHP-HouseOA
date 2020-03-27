<?php
namespace app\base\controller;

use app\base\model\District as DistrictModel;
use app\base\model\City as CityModel;
use app\base\model\Province as ProvinceModel;
use app\index\controller\Auth as Auth;
use think\Db;

// 地域：区级信息
class District extends Auth
{
    // region 主界面
    public function index()
    {
        if (request()->isGet()) {
            $aList = new ProvinceModel;
            $aList = $aList->where("status = 2")->field("id, name")->order("code")->select();
            $this->assign("aList", $aList);

            return $this->fetch('base@District/index');
        }

        if (request()->isPost()) {
            $postData = input('post.');
            $output = array();
            $returnData = array();
            $count = 0;

            if (!empty($postData)) {
                $data = json_decode($postData['aoData'], true);
                $key = array('status', 'province', 'city', 'sName', 'iDisplayLength', 'iDisplayStart');
                $s = formatPostData($data, $key);

                $where = "p.status = 2";

                if (!empty($s['status'])) {
                    if ($s['status'] == '2') {
                        $where .= " and a.status = 2";
                    } else {
                        $where .= " and a.status != 2";
                    }
                }

                if (!empty($s['province'])) {$where .= " and c.pid = '{$s['province']}'";}

                if (!empty($s['city'])) {$where .= " and a.pid = '{$s['city']}'";}

                if (!empty($s['sName'])) {$where .= " and a.name like '%{$s['sName']}%'";}

                $sql =
                    "select p.name as provinceName, c.name as cityName, a.name, a.description, a.status, a.id" .
                    "  from tp5_District a" .
                    "  left join tp5_City c on a.pid = c.id" .
                    "  left join tp5_province p on c.pid = p.id" .
                    " where " . $where .
                    " order by p.code, c.code, a.code" .
                    " limit " . $s['iDisplayStart'] . "," . $s['iDisplayLength'];

                $aList = Db::query($sql);
                if (!empty($aList)) {
                    $tempData = array();
                    foreach ($aList as $aRow) {
                        if ($aRow['status'] == 2) {
                            $status = "<a title=\"停用\" style=\"text-decoration:none\" href=\"javascript:;\" onClick=\"changeStatus(this,'" . $aRow['id'] . "')\"><span class=\"label label-success radius\">已启用</span></a>";
                        } else {
                            $status = "<a title=\"启用\" style=\"text-decoration:none\" href=\"javascript:;\" onClick=\"changeStatus(this,'" . $aRow['id'] . "')\"><span class=\"label radius\">已停用</span></a>";
                        }

                        $tempData[0] = $aRow['id'];
                        $tempData[1] = $aRow['provinceName'];
                        $tempData[2] = $aRow['cityName'];
                        $tempData[3] = $aRow['name'];
                        $tempData[4] = $aRow['description'];
                        $tempData[5] = $status;

                        $returnData[] = $tempData;
                    }

                    $sql =
                        "select count(1) as c " .
                        "  from tp5_District a" .
                        "  left join tp5_City c on a.pid = c.id" .
                        "  left join tp5_province p on c.pid = p.id" .
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

    // region 新增方法
    public function add()
    {
        if (request()->isGet()) {
            $aList = new ProvinceModel;
            $aList = $aList->where("status = 2")->field("id, name")->order("code")->select();
            $this->assign("aList", $aList);

            return $this->fetch('base@District/add');
        }

        if (request()->isPost()) {
            $data = input('post.');
            if (!empty($data)) {
                $model = new DistrictModel;
                //$data['id'] = getID();
                if ($model->save($data)) {
                    \think\Cache::clear('district'); // 清除缓存
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

    // region 编辑方法
    public function edit()
    {
        if (request()->isGet()) {
            $data = input('get.');

            $model = new DistrictModel;
            $list = $model->where('id', $data['id'])->find();
            $temp = new CityModel;
            $temp = $temp->where("status = 2 and id = '{$list['pid']}'")->field("pid as province")->select();
            foreach ($temp as $row) {
                $list['province'] = $row['province'];
            }
            $this->assign('list', $list);

            $aList = new ProvinceModel;
            $aList = $aList->where("status = 2")->field("id, name")->order("code")->select();
            $this->assign("aList", $aList);

            $bList = new CityModel;
            $bList = $bList->where("status = 2 and pid = '{$list['province']}'")->field("id, name")->order("code")->select();
            $this->assign("bList", $bList);

            return $this->fetch('base@District/edit');
        }

        if (request()->isPost()) {
            $data = input('post.');
            if (!empty($data)) {
                $model = new DistrictModel;
                if ($model->save($data, ['id' => $data['id']])) {
                    \think\Cache::clear('district'); // 清除缓存
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
     */
    public function changeStatus()
    {
        return _changeStatus(new DistrictModel);
    }
    // endregion

    // region 区(查询方法)
    public function getDistrict()
    {
        $returnData = array();

        if (request()->isPost()) {
            $data = input('post.');
            if (array_check('id', $data)) {
                $returnData = cacheDistrict($data['id']);
            }
        }

        return $returnData;
    }
    // endregion
}