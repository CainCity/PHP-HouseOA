<?php
namespace app\base\controller;

use app\base\model\City as CityModel;
use app\base\model\Province as ProvinceModel;
use app\index\controller\Auth as Auth;
use think\Db;

// 地域：市级信息
class City extends Auth
{
    // region 主界面
    public function index()
    {
        if (request()->isGet()) {
            $aList = new ProvinceModel;
            $aList = $aList->where("status = 2")->field("id, name")->order("code")->select();
            $this->assign("aList", $aList);

            return $this->fetch('base@City/index');
        }

        if (request()->isPost()) {
            $postData = input('post.');
            $output = array();
            $returnData = array();
            $count = 0;

            if (!empty($postData)) {
                $data = json_decode($postData['aoData'], true);
                $key = array('status', 'sName', 'province', 'iDisplayLength', 'iDisplayStart');
                $s = formatPostData($data, $key);

                $where = " and p.status = 2";

                if (!empty($s['status'])) {
                    if ($s['status'] == '2') {
                        $where .= " and c.status = 2";
                    } else {
                        $where .= " and c.status != 2";
                    }
                }

                if (!empty($s['province'])) {
                    $where .= " and c.pid = '{$s['province']}'";
                }

                if (!empty($s['sName'])) {
                    $where .= " and c.name like '%{$s['sName']}%'";
                }

                $sql =
                    "select p.name as provinceName, c.name, c.description, c.status, c.id" .
                    "  from tp5_City c" .
                    "  left join tp5_province p on c.pid = p.id" .
                    " where 1 = 1" . $where .
                    " order by p.code, c.code" .
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
                        $tempData[2] = $aRow['name'];
                        $tempData[3] = $aRow['description'];
                        $tempData[4] = $status;

                        $returnData[] = $tempData;
                    }

                    /** 数据数量查询 */
                    $sql =
                        "select count(1) as c " .
                        "  from tp5_City c" .
                        "  left join tp5_province p on c.pid = p.id" .
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
    // endregion

    // region 新增
    public function add()
    {
        if (request()->isGet()) {
            $aList = new ProvinceModel;
            $aList = $aList->where("status = 2")->field("id, name")->order("code")->select();
            $this->assign("aList", $aList);

            return $this->fetch('base@City/add');
        }

        if (request()->isPost()) {
            $data = input('post.');
            if ($data) {
                $model = new CityModel;
                //$data['id'] = getID();
                if ($model->save($data)) {
                    \think\Cache::clear('city'); // 清除缓存
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

            $model = new CityModel;
            $list = $model->where('id', $data['id'])->find();
            $this->assign('list', $list);

            $aList = new ProvinceModel;
            $aList = $aList->where("status = 2")->field("id, name")->order("code")->select();
            $this->assign("aList", $aList);

            return $this->fetch('base@City/edit');
        }

        if (request()->isPost()) {
            $data = input('post.');
            if (!empty($data)) {
                $model = new CityModel;
                if ($model->save($data, ['id' => $data['id']])) {
                    \think\Cache::clear('city'); // 清除缓存
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
        return _changeStatus(new CityModel);
    }
    // endregion

    // region 市(查询方法)
    public function getCity()
    {
        $returnData = array();

        if (request()->isPost()) {
            $data = input('post.');
            if (array_check('id', $data)) {
                $returnData = cacheCity($data['id']);
            }
        }

        return $returnData;
    }
    // endregion
}