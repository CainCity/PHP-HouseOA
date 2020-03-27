<?php
namespace app\base\controller;

use app\base\model\Province as ProvinceModel;
use app\index\controller\Auth as Auth;

// 地域：省级信息
class Province extends Auth
{
    // region 主界面
    public function index()
    {
        if (request()->isGet()) {
            return $this->fetch('base@Province/index');
        }

        if (request()->isPost()) {
            $postData = input('post.');
            $output = array();
            $returnData = array();
            $count = 0;

            if (!empty($postData)) {
                $data = json_decode($postData['aoData'], true);
                $key = array('status', 'sName', 'iDisplayLength', 'iDisplayStart');
                $s = formatPostData($data, $key);

                $where = "1 = 1";

                if (!empty($s['status'])) {
                    if ($s['status'] == '2') {
                        $where .= " and status = 2";
                    } else {
                        $where .= " and status != 2";
                    }
                }

                if (!empty($s['sName'])) {
                    $where .= " and name like '%{$s['sName']}%'";
                }

                $model = new ProvinceModel;
                $aList = $model->where($where)->order('code')->limit($s['iDisplayStart'], $s['iDisplayLength'])->select();
                if (!empty($aList)) {
                    $tempData = array();
                    foreach ($aList as $aRow) {
                        if ($aRow['status'] == 2) {
                            $status = "<a title=\"停用\" style=\"text-decoration:none\" href=\"javascript:;\" onClick=\"changeStatus(this,'" . $aRow['id'] . "')\"><span class=\"label label-success radius\">已启用</span></a>";
                        } else {
                            $status = "<a title=\"启用\" style=\"text-decoration:none\" href=\"javascript:;\" onClick=\"changeStatus(this,'" . $aRow['id'] . "')\"><span class=\"label radius\">已停用</span></a>";
                        }

                        $tempData[0] = $aRow['id'];
                        $tempData[1] = $aRow['name'];
                        $tempData[2] = $aRow['description'];
                        $tempData[3] = $status;

                        $returnData[] = $tempData;
                    }

                    /** 数据数量查询 */
                    $count = $model->where($where)->count();
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
            return $this->fetch('base@Province/add');
        }

        if (request()->isPost()) {
            $data = input('get.');
            if ($data) {
                $model = new ProvinceModel;
                //$data['id'] = getID();
                if ($model->save($data)) {
                    \think\Cache::clear('province'); // 清除缓存
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

            $model = new ProvinceModel;
            $list = $model->where('id', $data['id'])->find();
            $this->assign('list', $list);

            return $this->fetch('base@Province/edit');
        }

        if (request()->isPost()) {
            $data = input('post.');
            if (!empty($data)) {
                $model = new ProvinceModel;
                if ($model->save($data, ['id' => $data['id']])) {
                    \think\Cache::clear('province'); // 清除缓存
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
        return _changeStatus(new ProvinceModel);
    }
    // endregion

    // region 省(查询方法)
    public function getProvince()
    {
        $returnData = array();

        if (request()->isPost()) {
            $data = input('post.');
            if (array_check('id', $data)) {
                $returnData = cacheProvince($data['id']);
            }
        }

        return $returnData;
    }
    // endregion
}